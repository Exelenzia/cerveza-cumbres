<?php

namespace App\Livewire\Admin\Shipping;

use App\Models\ShippingZone;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin')]
class Zones extends Component
{
    public bool $showForm = false;

    public ?int $editingId = null;

    public string $name = '';

    public string $cities = '';

    public string $price = '';

    public string $eta_label = '';

    public bool $is_active = true;

    public int $sort_order = 0;

    public function create(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function edit(int $id): void
    {
        $zone = ShippingZone::findOrFail($id);

        $this->editingId = $zone->id;
        $this->name = $zone->name;
        $this->cities = $zone->cities;
        $this->price = (string) $zone->price;
        $this->eta_label = (string) $zone->eta_label;
        $this->is_active = $zone->is_active;
        $this->sort_order = $zone->sort_order;
        $this->showForm = true;
    }

    public function save(): void
    {
        $data = $this->validate([
            'name' => 'required|string|max:255',
            'cities' => 'required|string',
            'price' => 'required|numeric|min:0',
            'eta_label' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

        if ($this->editingId) {
            ShippingZone::findOrFail($this->editingId)->update($data);
        } else {
            ShippingZone::create($data);
        }

        $this->resetForm();
        $this->showForm = false;
    }

    public function delete(int $id): void
    {
        ShippingZone::findOrFail($id)->delete();
    }

    public function cancel(): void
    {
        $this->resetForm();
        $this->showForm = false;
    }

    private function resetForm(): void
    {
        $this->reset(['editingId', 'name', 'cities', 'price', 'eta_label', 'sort_order']);
        $this->is_active = true;
        $this->sort_order = 0;
    }

    public function render()
    {
        return view('livewire.admin.shipping.zones', [
            'title' => 'Zonas de envío',
            'zones' => ShippingZone::orderBy('sort_order')->get(),
        ]);
    }
}
