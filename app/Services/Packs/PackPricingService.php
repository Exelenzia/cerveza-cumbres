<?php

namespace App\Services\Packs;

use App\Models\PackTemplate;
use App\Models\Product;

class PackPricingService
{
    /**
     * @param  array<int, int>  $selections  product id => quantity
     */
    public function price(PackTemplate $template, array $selections): float
    {
        return match ($template->type) {
            PackTemplate::TYPE_FIXED_STYLE => $this->fixedStylePrice($selections),
            PackTemplate::TYPE_MIX => $this->mixPrice($template, $selections),
            PackTemplate::TYPE_GIFT => (float) $template->base_price,
            default => 0.0,
        };
    }

    /**
     * @param  array<int, int>  $selections  product id => quantity
     */
    public function validateSelections(PackTemplate $template, array $selections): ?string
    {
        $selections = array_filter($selections, fn ($qty) => $qty > 0);

        if (empty($selections)) {
            return 'Selecciona al menos un estilo.';
        }

        if ($template->type === PackTemplate::TYPE_FIXED_STYLE && count($selections) !== 1) {
            return 'El Pack 6 Fijo requiere un único estilo.';
        }

        $totalUnits = array_sum($selections);

        if ($totalUnits !== $template->bottle_count) {
            return "Debes seleccionar exactamente {$template->bottle_count} unidades (llevas {$totalUnits}).";
        }

        $eligibleIds = $template->eligibleProducts()->pluck('products.id')->all();
        $invalid = array_diff(array_map('intval', array_keys($selections)), $eligibleIds);

        if (! empty($invalid)) {
            return 'Uno de los estilos elegidos no está disponible para este pack.';
        }

        if ($template->type === PackTemplate::TYPE_FIXED_STYLE) {
            $product = Product::find(array_key_first($selections));

            if (! $product || $product->fixed_pack6_price === null) {
                return 'Ese estilo no tiene un precio de Pack 6 Fijo configurado.';
            }
        }

        return null;
    }

    /**
     * @param  array<int, int>  $selections
     */
    private function fixedStylePrice(array $selections): float
    {
        $product = Product::find(array_key_first($selections));

        return $product ? (float) $product->fixed_pack6_price : 0.0;
    }

    /**
     * @param  array<int, int>  $selections
     */
    private function mixPrice(PackTemplate $template, array $selections): float
    {
        $products = Product::whereIn('id', array_keys($selections))->get()->keyBy('id');

        $surcharge = 0.0;

        foreach ($selections as $productId => $quantity) {
            $product = $products->get((int) $productId);

            if ($product && $product->is_mix_premium) {
                $surcharge += $quantity * (float) $product->mix_surcharge_per_unit;
            }
        }

        return round((float) $template->base_price + $surcharge, 2);
    }
}
