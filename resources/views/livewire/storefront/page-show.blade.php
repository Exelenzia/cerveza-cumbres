<div>
    @foreach ($blocks as $entry)
        @include('livewire.storefront.blocks.'.$entry['block']->type, ['block' => $entry['block'], 'products' => $entry['products']])
    @endforeach
</div>
