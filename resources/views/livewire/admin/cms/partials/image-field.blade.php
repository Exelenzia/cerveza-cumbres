<div>
    <label class="mb-1 block text-sm text-cream-200">Imagen</label>
    @if ($block['image_url'])
        <img src="{{ $block['image_url'] }}" alt="" class="mb-2 h-24 w-24 rounded-lg object-cover">
    @endif
    <input type="file" wire:model="newImages.{{ $block['id'] }}" accept="image/*" class="w-full text-sm text-cream-200">
    @error("newImages.{$block['id']}") <span class="text-sm text-red-400">{{ $message }}</span> @enderror
</div>
