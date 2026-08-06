<?php

namespace App\Livewire\Admin\Sunat;

use App\Models\DocumentSeries;
use App\Models\SunatSetting;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.admin')]
class Settings extends Component
{
    use WithFileUploads;

    public ?SunatSetting $sunatSetting = null;

    public string $ruc = '';

    public string $razon_social = '';

    public string $nombre_comercial = '';

    public string $ubigeo = '';

    public string $departamento = '';

    public string $provincia = '';

    public string $distrito = '';

    public string $direccion = '';

    public string $sol_usuario = '';

    public string $sol_clave = '';

    public string $certificado_password = '';

    public string $modo = SunatSetting::MODO_BETA;

    public $certificado = null;

    public ?string $certificadoError = null;

    public function mount(): void
    {
        $this->sunatSetting = SunatSetting::current() ?? SunatSetting::query()->latest('id')->first();

        if ($this->sunatSetting) {
            $this->ruc = $this->sunatSetting->ruc;
            $this->razon_social = $this->sunatSetting->razon_social;
            $this->nombre_comercial = (string) $this->sunatSetting->nombre_comercial;
            $this->ubigeo = (string) $this->sunatSetting->ubigeo;
            $this->departamento = (string) $this->sunatSetting->departamento;
            $this->provincia = (string) $this->sunatSetting->provincia;
            $this->distrito = (string) $this->sunatSetting->distrito;
            $this->direccion = (string) $this->sunatSetting->direccion;
            $this->sol_usuario = (string) $this->sunatSetting->sol_usuario;
            $this->modo = $this->sunatSetting->modo;
        }
    }

    public function save(): void
    {
        $this->certificadoError = null;

        $data = $this->validate([
            'ruc' => 'required|digits:11',
            'razon_social' => 'required|string|max:255',
            'nombre_comercial' => 'nullable|string|max:255',
            'ubigeo' => 'nullable|digits:6',
            'departamento' => 'nullable|string|max:100',
            'provincia' => 'nullable|string|max:100',
            'distrito' => 'nullable|string|max:100',
            'direccion' => 'nullable|string|max:255',
            'sol_usuario' => 'required|string|max:100',
            'sol_clave' => 'nullable|string|max:100',
            'certificado_password' => 'nullable|string|max:100',
            'modo' => 'required|in:beta,produccion',
            'certificado' => 'nullable|file|max:4096',
        ]);

        unset($data['certificado']);

        if ($data['sol_clave'] === '') {
            unset($data['sol_clave']);
        }

        if ($data['certificado_password'] === '') {
            unset($data['certificado_password']);
        }

        $data['is_active'] = true;

        if ($this->sunatSetting) {
            $this->sunatSetting->update($data);
        } else {
            $this->sunatSetting = SunatSetting::query()->create($data);
        }

        if ($this->certificado) {
            $extension = strtolower((string) $this->certificado->getClientOriginalExtension());
            $raw = file_get_contents($this->certificado->getRealPath());

            if (in_array($extension, ['pfx', 'p12'], true)) {
                $password = $this->sunatSetting->certificado_password ?? '';

                if (! openssl_pkcs12_read($raw, $certs, $password)) {
                    $this->certificadoError = 'No se pudo leer el certificado .pfx. Verifica que la contraseña sea correcta.';
                    $this->certificado = null;

                    return;
                }

                $pem = $certs['pkey'].$certs['cert'];
            } else {
                $pem = $raw;
            }

            $path = 'sunat/certificados/certificado-'.$this->sunatSetting->id.'.pem';
            Storage::disk('local')->put($path, $pem);
            $this->sunatSetting->update(['certificado_path' => $path]);
            $this->certificado = null;
        }

        $this->sol_clave = '';
        $this->certificado_password = '';

        session()->flash('sunat-settings-saved', true);
    }

    public function render()
    {
        return view('livewire.admin.sunat.settings', [
            'title' => 'Facturación SUNAT',
            'series' => $this->sunatSetting ? DocumentSeries::query()->orderBy('tipo_comprobante')->get() : collect(),
        ]);
    }
}
