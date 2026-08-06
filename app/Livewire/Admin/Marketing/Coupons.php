<?php

namespace App\Livewire\Admin\Marketing;

use App\Models\Coupon;
use App\Models\Product;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin')]
class Coupons extends Component
{
    public bool $showForm = false;

    public ?int $editingId = null;

    public string $code = '';

    public string $type = Coupon::TYPE_FIXED;

    public string $value = '';

    public string $scope = Coupon::SCOPE_ORDER;

    public ?int $product_id = null;

    public string $min_order_amount = '';

    public string $max_uses = '';

    public string $expires_at = '';

    public bool $is_active = true;

    public function create(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function edit(int $id): void
    {
        $coupon = Coupon::findOrFail($id);

        $this->editingId = $coupon->id;
        $this->code = $coupon->code;
        $this->type = $coupon->type;
        $this->value = (string) $coupon->value;
        $this->scope = $coupon->scope;
        $this->product_id = $coupon->product_id;
        $this->min_order_amount = (string) $coupon->min_order_amount;
        $this->max_uses = (string) $coupon->max_uses;
        $this->expires_at = $coupon->expires_at?->format('Y-m-d') ?? '';
        $this->is_active = $coupon->is_active;
        $this->showForm = true;
    }

    public function save(): void
    {
        $data = $this->validate([
            'code' => 'required|string|max:50',
            'type' => 'required|in:'.Coupon::TYPE_FIXED.','.Coupon::TYPE_PERCENTAGE,
            'value' => 'required|numeric|min:0.01',
            'scope' => 'required|in:'.Coupon::SCOPE_ORDER.','.Coupon::SCOPE_PRODUCT,
            'product_id' => 'nullable|required_if:scope,'.Coupon::SCOPE_PRODUCT.'|exists:products,id',
            'min_order_amount' => 'nullable|numeric|min:0',
            'max_uses' => 'nullable|integer|min:1',
            'expires_at' => 'nullable|date',
            'is_active' => 'boolean',
        ]);

        $data['code'] = Str::upper($data['code']);
        $data['product_id'] = $data['scope'] === Coupon::SCOPE_PRODUCT ? $data['product_id'] : null;
        $data['min_order_amount'] = $data['min_order_amount'] !== '' ? $data['min_order_amount'] : null;
        $data['max_uses'] = $data['max_uses'] !== '' ? $data['max_uses'] : null;
        $data['expires_at'] = $data['expires_at'] !== '' ? $data['expires_at'] : null;

        if ($this->editingId) {
            Coupon::findOrFail($this->editingId)->update($data);
        } else {
            Coupon::create($data);
        }

        $this->resetForm();
        $this->showForm = false;
    }

    public function delete(int $id): void
    {
        Coupon::findOrFail($id)->delete();
    }

    public function cancel(): void
    {
        $this->resetForm();
        $this->showForm = false;
    }

    private function resetForm(): void
    {
        $this->reset([
            'editingId', 'code', 'value', 'product_id', 'min_order_amount', 'max_uses', 'expires_at',
        ]);
        $this->type = Coupon::TYPE_FIXED;
        $this->scope = Coupon::SCOPE_ORDER;
        $this->is_active = true;
    }

    public function render()
    {
        return view('livewire.admin.marketing.coupons', [
            'title' => 'Cupones',
            'coupons' => Coupon::with('product')->latest()->get(),
            'products' => Product::orderBy('name')->get(),
        ]);
    }
}
