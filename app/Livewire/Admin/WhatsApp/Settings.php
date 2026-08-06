<?php

namespace App\Livewire\Admin\WhatsApp;

use App\Models\WhatsappSetting;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin')]
class Settings extends Component
{
    public ?WhatsappSetting $whatsappSetting = null;

    public string $phone_number_id = '';

    public string $access_token = '';

    public string $business_account_id = '';

    public string $wa_link_phone = '';

    public string $template_language = 'es';

    public string $template_confirmacion = '';

    public string $template_enviado = '';

    public string $template_entregado = '';

    public bool $is_active = true;

    public function mount(): void
    {
        $this->whatsappSetting = WhatsappSetting::current() ?? WhatsappSetting::query()->latest('id')->first();

        if ($this->whatsappSetting) {
            $this->phone_number_id = $this->whatsappSetting->phone_number_id;
            $this->business_account_id = (string) $this->whatsappSetting->business_account_id;
            $this->wa_link_phone = (string) $this->whatsappSetting->wa_link_phone;
            $this->template_language = $this->whatsappSetting->template_language;
            $this->template_confirmacion = (string) $this->whatsappSetting->template_confirmacion;
            $this->template_enviado = (string) $this->whatsappSetting->template_enviado;
            $this->template_entregado = (string) $this->whatsappSetting->template_entregado;
            $this->is_active = $this->whatsappSetting->is_active;
        }
    }

    public function save(): void
    {
        $data = $this->validate([
            'phone_number_id' => 'required|string|max:100',
            'access_token' => 'nullable|string|max:1000',
            'business_account_id' => 'nullable|string|max:100',
            'wa_link_phone' => 'nullable|string|max:20',
            'template_language' => 'required|string|max:10',
            'template_confirmacion' => 'nullable|string|max:255',
            'template_enviado' => 'nullable|string|max:255',
            'template_entregado' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        if ($data['access_token'] === '') {
            unset($data['access_token']);
        }

        if ($this->whatsappSetting) {
            $this->whatsappSetting->update($data);
        } else {
            $this->whatsappSetting = WhatsappSetting::query()->create($data);
        }

        $this->access_token = '';

        session()->flash('whatsapp-settings-saved', true);
    }

    public function render()
    {
        return view('livewire.admin.whatsapp.settings', [
            'title' => 'WhatsApp',
        ]);
    }
}
