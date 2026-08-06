<?php

namespace App\Livewire\Admin\Orders;

use App\Models\DocumentSeries;
use App\Models\Order;
use App\Services\Sunat\SunatInvoiceException;
use App\Services\Sunat\SunatInvoiceService;
use App\Services\WhatsApp\WhatsAppService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin')]
class Show extends Component
{
    public Order $order;

    public string $tipoComprobante = DocumentSeries::BOLETA;

    public ?string $invoiceError = null;

    public function mount(Order $order): void
    {
        $this->order = $order;
        $this->tipoComprobante = $order->customer_document_type === Order::DOCUMENT_RUC
            ? DocumentSeries::FACTURA
            : DocumentSeries::BOLETA;
    }

    public function updateStatus(string $status, WhatsAppService $whatsApp): void
    {
        if (! in_array($status, Order::STATUSES, true)) {
            return;
        }

        $this->order->update(['status' => $status]);

        if ($status === Order::STATUS_SHIPPED) {
            $whatsApp->notifyOrderShipped($this->order);
        } elseif ($status === Order::STATUS_DELIVERED) {
            $whatsApp->notifyOrderDelivered($this->order);
        }
    }

    public function issueInvoice(SunatInvoiceService $service): void
    {
        $this->invoiceError = null;

        try {
            $service->issue($this->order, $this->tipoComprobante);
            $this->order->refresh();
        } catch (SunatInvoiceException $e) {
            $this->invoiceError = $e->getMessage();
        }
    }

    public function render()
    {
        return view('livewire.admin.orders.show', [
            'title' => 'Pedido '.$this->order->order_number,
            'items' => $this->order->items()->with(['product', 'pack'])->get(),
            'statuses' => Order::STATUSES,
            'invoices' => $this->order->invoices()->latest()->get(),
        ]);
    }
}
