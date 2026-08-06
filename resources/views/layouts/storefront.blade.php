<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>{{ $title ?? 'Cumbres · Cerveza Artesanal del Perú' }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @livewireStyles
    </head>
    <body class="min-h-screen bg-cumbre-950 font-sans text-cream-100 antialiased">
        @php($banner = \App\Models\Setting::json('promo_banner'))
        @if (($banner['enabled'] ?? false) && filled($banner['text'] ?? null))
            @php($bannerContent = trim($banner['text']))
            @if (filled($banner['link'] ?? null))
                <a href="{{ $banner['link'] }}" wire:navigate class="block bg-gold-500 px-4 py-2 text-center text-sm font-semibold text-cumbre-950 hover:bg-gold-400">
                    {{ $bannerContent }}
                </a>
            @else
                <div class="bg-gold-500 px-4 py-2 text-center text-sm font-semibold text-cumbre-950">
                    {{ $bannerContent }}
                </div>
            @endif
        @endif

        <header class="sticky top-0 z-40 border-b border-cumbre-700/60 bg-cumbre-950/95 backdrop-blur">
            <div class="mx-auto flex max-w-6xl items-center justify-between px-6 py-4">
                <a href="{{ route('home') }}" wire:navigate class="font-display text-2xl font-semibold tracking-wide">
                    <span class="text-cream-100">CUM</span><span class="text-gold-500">BRES</span>
                </a>

                <nav class="hidden items-center gap-8 font-display text-sm font-medium tracking-widest text-cream-100/90 uppercase md:flex">
                    <a href="{{ route('shop') }}" wire:navigate class="transition hover:text-gold-400">Tienda</a>
                    <a href="{{ route('home') }}#lineas" class="transition hover:text-gold-400">Líneas Cerveceras</a>
                    <a href="{{ route('home') }}#historia" class="transition hover:text-gold-400">Historia</a>
                </nav>

                <div class="flex items-center gap-4 font-display text-sm font-medium tracking-widest uppercase">
                    <livewire:storefront.cart-counter />
                    @auth
                        @if (auth()->user()->hasAnyRole(['admin', 'staff']))
                            <a href="{{ route('admin.dashboard') }}" wire:navigate class="hidden text-cream-100/80 transition hover:text-gold-400 sm:inline">Admin</a>
                        @endif
                        <a href="{{ route('orders.index') }}" wire:navigate class="hidden text-cream-100/80 transition hover:text-gold-400 sm:inline">Mis pedidos</a>
                        <span class="hidden text-cream-100/60 sm:inline">Hola, {{ explode(' ', auth()->user()->name)[0] }}</span>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="rounded border border-cream-100/20 px-4 py-2 text-cream-100 transition hover:border-gold-500 hover:text-gold-400">Salir</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" wire:navigate class="text-cream-100/80 transition hover:text-gold-400">Ingresar</a>
                        <a href="{{ route('register') }}" wire:navigate class="rounded border border-gold-500 px-4 py-2 text-gold-400 transition hover:bg-gold-500 hover:text-cumbre-950">Crear cuenta</a>
                    @endauth
                </div>
            </div>
        </header>

        <main>
            {{ $slot }}
        </main>

        <footer class="border-t border-cumbre-700/60 bg-cumbre-900">
            <div class="mx-auto grid max-w-6xl gap-10 px-6 py-14 md:grid-cols-4">
                <div>
                    <p class="font-display text-xl font-semibold">
                        <span class="text-cream-100">CUM</span><span class="text-gold-500">BRES</span>
                    </p>
                    <p class="mt-2 text-sm text-cream-100/60">Cerveza Artesanal del Perú</p>
                    <p class="mt-4 text-sm text-cream-100/70">Elaboradas con pasión desde las alturas peruanas. Cada sorbo cuenta una historia.</p>
                </div>

                <div>
                    <p class="font-display text-sm font-semibold tracking-widest text-gold-500 uppercase">Navegación</p>
                    <ul class="mt-4 space-y-2 text-sm text-cream-100/70">
                        <li><a href="{{ route('shop') }}" wire:navigate class="hover:text-gold-400">Tienda</a></li>
                        <li><a href="{{ route('home') }}#lineas" class="hover:text-gold-400">Líneas Cerveceras</a></li>
                        <li><a href="{{ route('home') }}#historia" class="hover:text-gold-400">Nuestra Historia</a></li>
                        @foreach (\App\Models\Page::where('is_active', true)->orderBy('sort_order')->get() as $cmsPage)
                            <li><a href="{{ route('pages.show', $cmsPage) }}" wire:navigate class="hover:text-gold-400">{{ $cmsPage->title }}</a></li>
                        @endforeach
                    </ul>
                </div>

                <div>
                    <p class="font-display text-sm font-semibold tracking-widest text-gold-500 uppercase">Delivery Perú</p>
                    <p class="mt-4 text-sm text-cream-100/70">Enviamos a Lima en 24h y al interior del país en 24–72h.</p>
                </div>

                <div>
                    <p class="font-display text-sm font-semibold tracking-widest text-gold-500 uppercase">Contacto</p>
                    <ul class="mt-4 space-y-2 text-sm text-cream-100/70">
                        @php($waPhone = \App\Models\WhatsappSetting::current()?->wa_link_phone)
                        @if ($waPhone)
                            <li>
                                <a href="https://wa.me/{{ $waPhone }}?text={{ urlencode('Hola Cumbres, tengo una consulta.') }}" target="_blank" rel="noopener" class="hover:text-gold-400">
                                    WhatsApp: +{{ $waPhone }}
                                </a>
                            </li>
                        @endif
                        <li>hola@cervezacumbres.pe</li>
                        <li>Lima, Perú</li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-cumbre-700/60 px-6 py-6 text-center text-xs text-cream-100/50">
                &copy; {{ now()->year }} Cerveza Cumbres · Prohibida la venta a menores de 18 años.
            </div>
        </footer>

        @if ($waPhone)
            <a
                href="https://wa.me/{{ $waPhone }}?text={{ urlencode('Hola Cumbres, tengo una consulta.') }}"
                target="_blank"
                rel="noopener"
                aria-label="Chatear por WhatsApp"
                class="fixed bottom-5 right-5 z-50 flex h-14 w-14 items-center justify-center rounded-full bg-green-500 text-white shadow-lg transition hover:bg-green-400"
            >
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" class="h-7 w-7" fill="currentColor">
                    <path d="M16.001 3C9.373 3 4 8.373 4 15c0 2.29.638 4.43 1.744 6.256L4 29l7.94-1.7A11.94 11.94 0 0 0 16.001 27C22.629 27 28 21.627 28 15S22.629 3 16.001 3Zm0 21.818c-1.94 0-3.75-.52-5.312-1.428l-.381-.225-4.71 1.01 1.028-4.59-.248-.397A9.77 9.77 0 0 1 5.182 15c0-5.964 4.855-10.818 10.819-10.818S26.818 9.036 26.818 15 21.965 24.818 16.001 24.818Zm5.98-8.14c-.328-.164-1.94-.957-2.241-1.066-.301-.109-.52-.164-.738.164-.219.328-.848 1.066-1.04 1.285-.191.219-.383.246-.71.082-.328-.164-1.386-.51-2.64-1.628-.976-.87-1.635-1.945-1.826-2.273-.191-.328-.02-.505.144-.668.148-.147.328-.383.492-.574.164-.191.219-.328.328-.547.11-.219.055-.41-.027-.574-.082-.164-.738-1.777-1.012-2.434-.267-.64-.539-.554-.738-.564l-.629-.011c-.219 0-.574.082-.875.41-.301.328-1.148 1.121-1.148 2.734s1.176 3.172 1.34 3.391c.164.219 2.316 3.537 5.613 4.958.784.339 1.396.541 1.873.692.787.25 1.503.215 2.07.13.631-.094 1.94-.793 2.213-1.558.273-.766.273-1.422.191-1.559-.082-.137-.301-.219-.629-.383Z" />
                </svg>
            </a>
        @endif

        @livewireScripts
    </body>
</html>
