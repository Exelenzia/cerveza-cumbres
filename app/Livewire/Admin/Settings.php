<?php

namespace App\Livewire\Admin;

use App\Models\Setting;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin')]
class Settings extends Component
{
    public bool $guestCheckoutEnabled = false;

    public bool $bannerEnabled = false;

    public string $bannerText = '';

    public string $bannerLink = '';

    public function mount(): void
    {
        $this->guestCheckoutEnabled = Setting::bool('guest_checkout_enabled');

        $banner = Setting::json('promo_banner');
        $this->bannerEnabled = (bool) ($banner['enabled'] ?? false);
        $this->bannerText = $banner['text'] ?? '';
        $this->bannerLink = $banner['link'] ?? '';
    }

    public function save(): void
    {
        $this->validate([
            'bannerText' => 'nullable|string|max:255',
            'bannerLink' => 'nullable|string|max:255',
        ]);

        Setting::set('guest_checkout_enabled', $this->guestCheckoutEnabled ? '1' : '0');

        Setting::setJson('promo_banner', [
            'enabled' => $this->bannerEnabled,
            'text' => $this->bannerText,
            'link' => $this->bannerLink,
        ]);

        session()->flash('settings-saved', true);
    }

    public function render()
    {
        return view('livewire.admin.settings', [
            'title' => 'Configuración',
        ]);
    }
}
