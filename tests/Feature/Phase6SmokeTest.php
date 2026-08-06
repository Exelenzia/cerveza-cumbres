<?php

namespace Tests\Feature;

use App\Livewire\Admin\Cms\PageEditor;
use App\Livewire\Admin\Cms\Pages as AdminCmsPages;
use App\Livewire\Storefront\PageShow;
use App\Models\Category;
use App\Models\Page;
use App\Models\PageBlock;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class Phase6SmokeTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsAdmin(): User
    {
        Role::firstOrCreate(['name' => 'admin']);
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $this->actingAs($admin);

        return $admin;
    }

    private function makeProduct(array $overrides = []): Product
    {
        $category = Category::create([
            'name' => 'Línea Cumbre',
            'slug' => 'linea-cumbre-'.uniqid(),
            'sort_order' => 1,
        ]);

        return Product::create(array_merge([
            'category_id' => $category->id,
            'name' => 'Cumbre IPA',
            'slug' => 'cumbre-ipa-'.uniqid(),
            'style' => 'IPA',
            'description' => 'Test',
            'abv' => 6.0,
            'ibu' => 50,
            'volume_ml' => 355,
            'price' => 10.00,
            'stock' => 20,
            'is_active' => true,
            'is_popular' => false,
            'sort_order' => 1,
        ], $overrides));
    }

    public function test_admin_can_create_a_page_and_is_redirected_to_editor(): void
    {
        $this->actingAsAdmin();

        Livewire::test(AdminCmsPages::class)
            ->set('title', 'Nuestra Historia')
            ->call('save');

        $page = Page::first();

        $this->assertNotNull($page);
        $this->assertSame('Nuestra Historia', $page->title);
        $this->assertSame('nuestra-historia', $page->slug);
        $this->assertTrue($page->is_active);
    }

    public function test_admin_can_update_page_meta(): void
    {
        $this->actingAsAdmin();

        $page = Page::create(['title' => 'Nosotros', 'slug' => 'nosotros', 'is_active' => true, 'sort_order' => 1]);

        Livewire::test(PageEditor::class, ['page' => $page])
            ->set('title', 'Sobre Cumbres')
            ->set('slug', 'sobre-cumbres')
            ->set('is_active', false)
            ->call('savePageMeta');

        $page->refresh();

        $this->assertSame('Sobre Cumbres', $page->title);
        $this->assertSame('sobre-cumbres', $page->slug);
        $this->assertFalse($page->is_active);
    }

    public function test_admin_cannot_use_a_reserved_or_duplicate_slug(): void
    {
        $this->actingAsAdmin();

        Page::create(['title' => 'Otra', 'slug' => 'otra', 'is_active' => true, 'sort_order' => 1]);
        $page = Page::create(['title' => 'Nosotros', 'slug' => 'nosotros', 'is_active' => true, 'sort_order' => 2]);

        Livewire::test(PageEditor::class, ['page' => $page])
            ->set('slug', 'otra')
            ->call('savePageMeta')
            ->assertHasErrors(['slug']);

        Livewire::test(PageEditor::class, ['page' => $page])
            ->set('slug', 'tienda')
            ->call('savePageMeta')
            ->assertHasErrors(['slug']);
    }

    public function test_admin_can_add_reorder_and_remove_blocks(): void
    {
        $this->actingAsAdmin();

        $page = Page::create(['title' => 'Nosotros', 'slug' => 'nosotros', 'is_active' => true, 'sort_order' => 1]);

        $component = Livewire::test(PageEditor::class, ['page' => $page])
            ->set('addType', PageBlock::TYPE_HERO)
            ->call('addBlock')
            ->set('addType', PageBlock::TYPE_CTA)
            ->call('addBlock');

        $this->assertSame(2, $page->blocks()->count());

        $heroBlock = $page->blocks()->where('type', PageBlock::TYPE_HERO)->first();
        $ctaBlock = $page->blocks()->where('type', PageBlock::TYPE_CTA)->first();

        $this->assertLessThan($ctaBlock->sort_order, $heroBlock->sort_order);

        $component->call('moveBlock', $ctaBlock->id, 'up');

        $this->assertLessThan($heroBlock->fresh()->sort_order, $ctaBlock->fresh()->sort_order);

        $component->call('removeBlock', $heroBlock->id);

        $this->assertSame(1, $page->blocks()->count());
        $this->assertSame(0, PageBlock::where('id', $heroBlock->id)->count());
    }

    public function test_admin_can_save_hero_block_data_with_image(): void
    {
        $this->actingAsAdmin();

        $page = Page::create(['title' => 'Nosotros', 'slug' => 'nosotros', 'is_active' => true, 'sort_order' => 1]);
        $block = PageBlock::create([
            'page_id' => $page->id,
            'type' => PageBlock::TYPE_HERO,
            'data' => PageBlock::defaultData(PageBlock::TYPE_HERO),
            'sort_order' => 1,
        ]);

        Livewire::test(PageEditor::class, ['page' => $page])
            ->set("blocks.0.data.heading", 'Nuestra pasión')
            ->set("blocks.0.data.subheading", 'Desde las alturas del Perú')
            ->set("newImages.{$block->id}", UploadedFile::fake()->image('hero.jpg'))
            ->call('saveBlockData', $block->id)
            ->assertHasNoErrors();

        $block->refresh();

        $this->assertSame('Nuestra pasión', $block->data['heading']);
        $this->assertSame('Desde las alturas del Perú', $block->data['subheading']);
        $this->assertNotNull($block->image_url);
    }

    public function test_admin_can_manage_testimonial_items(): void
    {
        $this->actingAsAdmin();

        $page = Page::create(['title' => 'Nosotros', 'slug' => 'nosotros', 'is_active' => true, 'sort_order' => 1]);
        $block = PageBlock::create([
            'page_id' => $page->id,
            'type' => PageBlock::TYPE_TESTIMONIALS,
            'data' => PageBlock::defaultData(PageBlock::TYPE_TESTIMONIALS),
            'sort_order' => 1,
        ]);

        Livewire::test(PageEditor::class, ['page' => $page])
            ->call('addTestimonial', $block->id)
            ->set('blocks.0.data.items.0.author', 'Juan Pérez')
            ->set('blocks.0.data.items.0.quote', 'La mejor cerveza artesanal.')
            ->set('blocks.0.data.items.1.author', 'María López')
            ->set('blocks.0.data.items.1.quote', 'Excelente sabor.')
            ->call('saveBlockData', $block->id)
            ->assertHasNoErrors();

        $block->refresh();

        $this->assertCount(2, $block->data['items']);
        $this->assertSame('Juan Pérez', $block->data['items'][0]['author']);
    }

    public function test_admin_page_listing_can_toggle_active_and_delete(): void
    {
        $this->actingAsAdmin();

        $page = Page::create(['title' => 'Nosotros', 'slug' => 'nosotros', 'is_active' => true, 'sort_order' => 1]);

        Livewire::test(AdminCmsPages::class)
            ->call('toggleActive', $page->id);

        $this->assertFalse($page->fresh()->is_active);

        Livewire::test(AdminCmsPages::class)
            ->call('delete', $page->id);

        $this->assertSame(0, Page::where('id', $page->id)->count());
    }

    public function test_storefront_renders_active_page_with_its_blocks(): void
    {
        $product = $this->makeProduct(['is_popular' => true]);

        $page = Page::create(['title' => 'Nuestra Historia', 'slug' => 'nuestra-historia', 'is_active' => true, 'sort_order' => 1]);

        PageBlock::create([
            'page_id' => $page->id,
            'type' => PageBlock::TYPE_HERO,
            'data' => ['heading' => 'Cumbres nació entre montañas', 'subheading' => '', 'button_label' => '', 'button_link' => ''],
            'sort_order' => 1,
        ]);

        PageBlock::create([
            'page_id' => $page->id,
            'type' => PageBlock::TYPE_PRODUCT_GRID,
            'data' => ['heading' => 'Las más pedidas', 'source' => 'popular', 'category_id' => null, 'limit' => 4],
            'sort_order' => 2,
        ]);

        PageBlock::create([
            'page_id' => $page->id,
            'type' => PageBlock::TYPE_CTA,
            'data' => ['heading' => 'Conoce la tienda', 'body' => '', 'button_label' => 'Ver tienda', 'button_link' => '/tienda'],
            'sort_order' => 3,
        ]);

        $response = $this->get(route('pages.show', $page));

        $response->assertOk()
            ->assertSee('Cumbres nació entre montañas')
            ->assertSee($product->name)
            ->assertSee('Conoce la tienda');
    }

    public function test_storefront_returns_404_for_inactive_page(): void
    {
        $page = Page::create(['title' => 'Borrador', 'slug' => 'borrador', 'is_active' => false, 'sort_order' => 1]);

        $this->get(route('pages.show', $page))->assertNotFound();
    }

    public function test_storefront_footer_lists_active_cms_pages(): void
    {
        Page::create(['title' => 'Nuestra Historia', 'slug' => 'nuestra-historia', 'is_active' => true, 'sort_order' => 1]);
        Page::create(['title' => 'Borrador Oculto', 'slug' => 'borrador-oculto', 'is_active' => false, 'sort_order' => 2]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Nuestra Historia')
            ->assertDontSee('Borrador Oculto');
    }
}
