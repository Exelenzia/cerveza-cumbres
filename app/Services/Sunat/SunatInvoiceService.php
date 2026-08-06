<?php

namespace App\Services\Sunat;

use App\Models\DocumentSeries;
use App\Models\Invoice as InvoiceRecord;
use App\Models\Order;
use App\Models\SunatSetting;
use Greenter\Model\Client\Client;
use Greenter\Model\Company\Address;
use Greenter\Model\Company\Company;
use Greenter\Model\Sale\FormaPagos\FormaPagoContado;
use Greenter\Model\Sale\Invoice as GreenterInvoice;
use Greenter\Model\Sale\Legend;
use Greenter\Model\Sale\SaleDetail;
use Greenter\See;
use Illuminate\Support\Facades\Storage;

class SunatInvoiceService
{
    private const IGV_RATE = 0.18;

    private const BETA_ENDPOINT = 'https://e-beta.sunat.gob.pe/ol-ti-itcpfegem-beta/billService';

    private const PROD_ENDPOINT = 'https://e-factura.sunat.gob.pe/ol-ti-itcpfegem/billService';

    public function issue(Order $order, ?string $tipoComprobante = null): InvoiceRecord
    {
        $settings = SunatSetting::current();

        if (! $settings) {
            throw new SunatInvoiceException('SUNAT no está configurado. Ingresa el RUC, credenciales SOL y certificado en el panel de administración.');
        }

        if (! $order->customer_document_type || ! $order->customer_document_number) {
            throw new SunatInvoiceException('El pedido no tiene un tipo/número de documento de cliente registrado.');
        }

        $tipoComprobante ??= $order->customer_document_type === Order::DOCUMENT_RUC
            ? DocumentSeries::FACTURA
            : DocumentSeries::BOLETA;

        $series = $this->resolveSeries($tipoComprobante);
        $correlativo = $series->nextCorrelativo();

        [$details, $mtoOperGravadas, $mtoIGV] = $this->buildDetails($order);
        $mtoImpVenta = round($mtoOperGravadas + $mtoIGV, 2);

        $document = new GreenterInvoice();
        $document->setUblVersion('2.1')
            ->setTipoOperacion('0101')
            ->setFormaPago(new FormaPagoContado())
            ->setTipoDoc($tipoComprobante)
            ->setSerie($series->serie)
            ->setCorrelativo((string) $correlativo)
            ->setFechaEmision(now())
            ->setTipoMoneda($order->currency)
            ->setCompany($this->buildCompany($settings))
            ->setClient($this->buildClient($order))
            ->setMtoOperGravadas($mtoOperGravadas)
            ->setMtoIGV($mtoIGV)
            ->setTotalImpuestos($mtoIGV)
            ->setValorVenta($mtoOperGravadas)
            ->setSubTotal($mtoImpVenta)
            ->setMtoImpVenta($mtoImpVenta)
            ->setDetails($details)
            ->setLegends([
                (new Legend())->setCode('1000')->setValue(NumeroALetras::convertir($mtoImpVenta, $order->currency === 'USD' ? 'DOLARES' : 'SOLES')),
            ]);

        $see = $this->buildSee($settings);
        $xmlSigned = $see->getXmlSigned($document);
        $result = $see->send($document);

        $invoice = new InvoiceRecord([
            'order_id' => $order->id,
            'document_series_id' => $series->id,
            'tipo_comprobante' => $tipoComprobante,
            'serie' => $series->serie,
            'correlativo' => $correlativo,
            'fecha_emision' => now()->toDateString(),
            'moneda' => $order->currency,
            'op_gravada' => $mtoOperGravadas,
            'igv' => $mtoIGV,
            'total' => $mtoImpVenta,
        ]);

        $basePath = sprintf('invoices/%s/%s-%s', now()->format('Y'), $series->serie, str_pad((string) $correlativo, 8, '0', STR_PAD_LEFT));

        Storage::put("{$basePath}.xml", $xmlSigned);
        $invoice->xml_path = "{$basePath}.xml";

        if ($result && $result->isSuccess()) {
            $cdrResponse = $result->getCdrResponse();
            $invoice->estado = $cdrResponse && $cdrResponse->isAccepted()
                ? InvoiceRecord::ESTADO_ACEPTADO
                : InvoiceRecord::ESTADO_RECHAZADO;
            $invoice->sunat_response_code = $cdrResponse?->getCode();
            $invoice->sunat_response_description = $cdrResponse?->getDescription();

            if ($result->getCdrZip()) {
                Storage::put("{$basePath}-cdr.zip", $result->getCdrZip());
                $invoice->cdr_path = "{$basePath}-cdr.zip";
            }
        } else {
            $error = $result?->getError();
            $invoice->estado = InvoiceRecord::ESTADO_ERROR;
            $invoice->sunat_response_code = $error?->getCode();
            $invoice->sunat_response_description = $error?->getMessage() ?? 'No se pudo enviar el comprobante a SUNAT.';
        }

        $invoice->save();

        return $invoice;
    }

    private function resolveSeries(string $tipoComprobante): DocumentSeries
    {
        $serie = $tipoComprobante === DocumentSeries::FACTURA ? 'F001' : 'B001';

        return DocumentSeries::query()->firstOrCreate(
            ['tipo_comprobante' => $tipoComprobante, 'serie' => $serie],
            ['correlativo' => 0, 'is_active' => true],
        );
    }

    private function buildCompany(SunatSetting $settings): Company
    {
        $address = (new Address())
            ->setUbigueo($settings->ubigeo)
            ->setDepartamento($settings->departamento)
            ->setProvincia($settings->provincia)
            ->setDistrito($settings->distrito)
            ->setDireccion($settings->direccion);

        return (new Company())
            ->setRuc($settings->ruc)
            ->setRazonSocial($settings->razon_social)
            ->setNombreComercial($settings->nombre_comercial ?? $settings->razon_social)
            ->setAddress($address);
    }

    private function buildClient(Order $order): Client
    {
        $tipoDoc = $order->customer_document_type === Order::DOCUMENT_RUC ? '6' : '1';

        return (new Client())
            ->setTipoDoc($tipoDoc)
            ->setNumDoc($order->customer_document_number)
            ->setRznSocial($order->customer_name)
            ->setEmail($order->customer_email)
            ->setTelephone($order->customer_phone);
    }

    /**
     * @return array{0: SaleDetail[], 1: float, 2: float}
     */
    private function buildDetails(Order $order): array
    {
        $details = [];
        $mtoOperGravadas = 0.0;
        $mtoIGV = 0.0;

        foreach ($order->items as $item) {
            $detail = $this->buildDetail(
                codProducto: (string) ($item->product_id ?? $item->pack_id ?? $item->id),
                descripcion: $item->name,
                cantidad: (float) $item->quantity,
                precioUnitario: (float) $item->unit_price,
                unidad: $item->unit_code,
                tipAfeIgv: $item->igv_affectation_code,
            );

            $details[] = $detail;
            $mtoOperGravadas += $detail->getMtoBaseIgv();
            $mtoIGV += $detail->getIgv();
        }

        if ((float) $order->shipping_cost > 0) {
            $detail = $this->buildDetail(
                codProducto: 'ENVIO',
                descripcion: 'Gastos de envío',
                cantidad: 1.0,
                precioUnitario: (float) $order->shipping_cost,
                unidad: 'ZZ',
                tipAfeIgv: '10',
            );

            $details[] = $detail;
            $mtoOperGravadas += $detail->getMtoBaseIgv();
            $mtoIGV += $detail->getIgv();
        }

        return [$details, round($mtoOperGravadas, 2), round($mtoIGV, 2)];
    }

    private function buildDetail(string $codProducto, string $descripcion, float $cantidad, float $precioUnitario, string $unidad, string $tipAfeIgv): SaleDetail
    {
        $valorUnitario = round($precioUnitario / (1 + self::IGV_RATE), 4);
        $valorVenta = round($valorUnitario * $cantidad, 2);
        $igv = round($valorVenta * self::IGV_RATE, 2);

        return (new SaleDetail())
            ->setCodProducto($codProducto)
            ->setUnidad($unidad)
            ->setCantidad($cantidad)
            ->setDescripcion($descripcion)
            ->setMtoValorUnitario($valorUnitario)
            ->setMtoPrecioUnitario($precioUnitario)
            ->setMtoValorVenta($valorVenta)
            ->setMtoBaseIgv($valorVenta)
            ->setPorcentajeIgv(self::IGV_RATE * 100)
            ->setIgv($igv)
            ->setTipAfeIgv($tipAfeIgv)
            ->setTotalImpuestos($igv);
    }

    private function buildSee(SunatSetting $settings): See
    {
        $see = new See();
        $see->setCertificate(Storage::get($settings->certificado_path));
        $see->setClaveSOL($settings->ruc, $settings->sol_usuario, $settings->sol_clave);
        $see->setService($settings->isBeta() ? self::BETA_ENDPOINT : self::PROD_ENDPOINT);

        return $see;
    }
}
