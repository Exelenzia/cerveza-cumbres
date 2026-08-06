<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Coupon;
use App\Models\Pack;
use App\Models\Product;
use App\Models\Setting;
use App\Models\ShippingZone;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'staff']);

        Setting::firstOrCreate(['key' => 'guest_checkout_enabled'], ['value' => '0']);

        $admin = User::firstOrCreate(
            ['email' => 'admin@cervezacumbres.pe'],
            ['name' => 'Admin Cumbres', 'password' => bcrypt('password')]
        );
        $admin->assignRole('admin');

        $cumbre = Category::firstOrCreate(
            ['slug' => 'linea-cumbre'],
            ['name' => 'Línea Cumbre', 'description' => 'Nuestros clásicos de siempre, equilibrados y fáciles de beber.', 'sort_order' => 1]
        );

        $reserva = Category::firstOrCreate(
            ['slug' => 'linea-reserva'],
            ['name' => 'Línea Reserva', 'description' => 'Ediciones intensas y de carácter, para paladares exigentes.', 'sort_order' => 2]
        );

        $products = [
            [
                'category_id' => $cumbre->id,
                'name' => 'Cumbre IPA',
                'style' => 'IPA',
                'description' => 'India Pale Ale con lúpulos cítricos y un amargor equilibrado.',
                'abv' => 6.2,
                'ibu' => 55,
                'volume_ml' => 355,
                'price' => 12.00,
                'compare_at_price' => null,
                'stock' => 120,
                'is_popular' => true,
                'sort_order' => 1,
            ],
            [
                'category_id' => $reserva->id,
                'name' => 'Huascarán Stout',
                'style' => 'Stout',
                'description' => 'Cerveza negra con notas de café y chocolate, cuerpo denso.',
                'abv' => 7.0,
                'ibu' => 35,
                'volume_ml' => 355,
                'price' => 14.00,
                'compare_at_price' => null,
                'stock' => 80,
                'is_popular' => true,
                'sort_order' => 2,
            ],
            [
                'category_id' => $cumbre->id,
                'name' => 'Cordillera Blonde',
                'style' => 'Blonde Ale',
                'description' => 'Suave y refrescante, con un final ligeramente dulce.',
                'abv' => 4.8,
                'ibu' => 18,
                'volume_ml' => 355,
                'price' => 11.00,
                'compare_at_price' => null,
                'stock' => 150,
                'is_popular' => true,
                'sort_order' => 3,
            ],
            [
                'category_id' => $reserva->id,
                'name' => 'Altura Amber',
                'style' => 'Amber Ale',
                'description' => 'Maltas tostadas con un toque caramelizado y aroma a frutos secos.',
                'abv' => 5.5,
                'ibu' => 28,
                'volume_ml' => 355,
                'price' => 13.00,
                'compare_at_price' => 15.00,
                'stock' => 60,
                'is_popular' => false,
                'sort_order' => 4,
            ],
            [
                'category_id' => $cumbre->id,
                'name' => 'Nevado Wheat',
                'style' => 'Wheat Beer',
                'description' => 'Cerveza de trigo, turbia y refrescante con notas cítricas.',
                'abv' => 5.0,
                'ibu' => 15,
                'volume_ml' => 355,
                'price' => 11.50,
                'compare_at_price' => null,
                'stock' => 90,
                'is_popular' => false,
                'sort_order' => 5,
            ],
        ];

        $created = collect($products)->map(fn (array $data) => Product::firstOrCreate(
            ['slug' => \Illuminate\Support\Str::slug($data['name'])],
            $data + ['is_active' => true]
        ));

        $pack = Pack::firstOrCreate(
            ['slug' => 'pack-explorador'],
            [
                'name' => 'Pack Explorador',
                'description' => 'Una selección de nuestras cervezas más queridas para descubrir Cumbres.',
                'price' => 65.00,
                'compare_at_price' => 72.00,
                'is_active' => true,
                'sort_order' => 1,
            ]
        );

        $pack->products()->sync(
            $created->take(3)->mapWithKeys(fn (Product $product) => [$product->id => ['quantity' => 2]])
        );

        ShippingZone::firstOrCreate(
            ['name' => 'Lima Metropolitana'],
            [
                'cities' => 'Lima, Callao, San Isidro, Miraflores, Surco',
                'price' => 10.00,
                'eta_label' => '24 horas',
                'is_active' => true,
                'sort_order' => 1,
            ]
        );

        ShippingZone::firstOrCreate(
            ['name' => 'Interior del país'],
            [
                'cities' => 'Arequipa, Cusco, Trujillo, Chiclayo, Piura, Huancayo',
                'price' => 20.00,
                'eta_label' => '24 a 72 horas',
                'is_active' => true,
                'sort_order' => 2,
            ]
        );

        Coupon::firstOrCreate(
            ['code' => 'CUMBRES10'],
            [
                'type' => Coupon::TYPE_PERCENTAGE,
                'value' => 10,
                'scope' => Coupon::SCOPE_ORDER,
                'min_order_amount' => 50,
                'is_active' => true,
            ]
        );
    }
}
