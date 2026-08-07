<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>{{ $title ?? 'Cumbres · Cerveza Artesanal del Perú' }}</title>

        <script>
            (function () {
                var stored = localStorage.getItem('theme');
                var theme = stored || (window.matchMedia('(prefers-color-scheme: light)').matches ? 'light' : 'dark');
                if (theme === 'light') document.documentElement.classList.add('light');
            })();
        </script>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        {{ \Illuminate\Support\Facades\Vite::fonts() }}

        @livewireStyles
    </head>
    <body class="min-h-screen bg-surface font-sans text-text antialiased transition-colors duration-300">
        @php($banner = \App\Models\Setting::json('promo_banner'))
        @if (($banner['enabled'] ?? false) && filled($banner['text'] ?? null))
            @php($bannerContent = trim($banner['text']))
            @if (filled($banner['link'] ?? null))
                <a href="{{ $banner['link'] }}" wire:navigate class="block bg-primary px-4 py-2 text-center text-sm font-semibold text-primary-on hover:bg-primary-hover">
                    {{ $bannerContent }}
                </a>
            @else
                <div class="bg-primary px-4 py-2 text-center text-sm font-semibold text-primary-on">
                    {{ $bannerContent }}
                </div>
            @endif
        @endif

        <header class="sticky top-0 z-40 border-b-[3px] border-text bg-surface/95 backdrop-blur">
            <div class="mx-auto flex max-w-6xl items-center justify-between px-6 py-4">
                <a href="{{ route('home') }}" wire:navigate class="badge-brutal bg-surface-elevated px-3 py-1 font-display text-2xl font-semibold tracking-wide -rotate-1">
                    <span class="text-text">CUM</span><span class="text-primary">BRES</span>
                </a>

                <nav class="hidden items-center gap-10 font-display text-sm font-bold tracking-[0.22em] text-text/90 uppercase md:flex">
                    <a href="{{ route('shop') }}" wire:navigate class="transition hover:text-primary">Tienda</a>
                    <a href="{{ route('home') }}#lineas" class="transition hover:text-primary">Líneas Cerveceras</a>
                    <a href="{{ route('home') }}#historia" class="transition hover:text-primary">Historia</a>
                </nav>

                <div class="flex items-center gap-5 font-display text-sm font-bold tracking-[0.22em] uppercase">
                    <livewire:storefront.cart-counter />
                    @auth
                        @if (auth()->user()->hasAnyRole(['admin', 'staff']))
                            <a href="{{ route('admin.dashboard') }}" wire:navigate class="hidden text-text/80 transition hover:text-primary sm:inline">Admin</a>
                        @endif
                        <a href="{{ route('orders.index') }}" wire:navigate class="hidden text-text/80 transition hover:text-primary sm:inline">Mis pedidos</a>
                        <span class="hidden text-text/60 normal-case sm:inline">Hola, {{ explode(' ', auth()->user()->name)[0] }}</span>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn-brutal bg-surface-elevated px-4 py-2 text-text">Salir</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" wire:navigate class="text-text/80 transition hover:text-primary">Ingresar</a>
                        <a href="{{ route('register') }}" wire:navigate class="btn-brutal bg-primary px-4 py-2 text-primary-on">Crear cuenta</a>
                    @endauth
                    <button
                        type="button"
                        data-theme-toggle
                        class="inline-flex items-center gap-2 border-[3px] border-text bg-surface-elevated px-3 py-1.5 normal-case tracking-normal transition hover:bg-primary hover:text-primary-on"
                        aria-label="Cambiar tema"
                    >
                        <span data-theme-icon class="grid h-5 w-5 place-items-center rounded-full bg-primary text-[.7rem] text-primary-on">☾</span>
                        <span data-theme-label class="hidden text-xs sm:inline">Oscuro</span>
                    </button>
                </div>
            </div>
        </header>

        <main>
            {{ $slot }}
        </main>

        <footer class="border-t-[3px] border-text bg-surface-muted">
            <div class="mx-auto grid max-w-6xl gap-10 px-6 py-14 md:grid-cols-4">
                <div>
                    <p class="font-display text-xl font-semibold">
                        <span class="text-text">CUM</span><span class="text-primary">BRES</span>
                    </p>
                    <p class="mt-2 text-sm text-text-muted">Cerveza Artesanal del Perú</p>
                    <p class="mt-4 text-sm text-text/70">Elaboradas con pasión desde las alturas peruanas. Cada sorbo cuenta una historia.</p>
                </div>

                <div>
                    <p class="font-display text-sm font-bold tracking-widest text-secondary uppercase">Navegación</p>
                    <ul class="mt-4 space-y-2 text-sm text-text/70">
                        <li><a href="{{ route('shop') }}" wire:navigate class="hover:text-primary">Tienda</a></li>
                        <li><a href="{{ route('home') }}#lineas" class="hover:text-primary">Líneas Cerveceras</a></li>
                        <li><a href="{{ route('home') }}#historia" class="hover:text-primary">Nuestra Historia</a></li>
                        @foreach (\App\Models\Page::where('is_active', true)->orderBy('sort_order')->get() as $cmsPage)
                            <li><a href="{{ route('pages.show', $cmsPage) }}" wire:navigate class="hover:text-primary">{{ $cmsPage->title }}</a></li>
                        @endforeach
                    </ul>
                </div>

                <div>
                    <p class="font-display text-sm font-bold tracking-widest text-secondary uppercase">Delivery Perú</p>
                    <p class="mt-4 text-sm text-text/70">Enviamos a Lima en 24h y al interior del país en 24–72h.</p>
                </div>

                <div>
                    <p class="font-display text-sm font-bold tracking-widest text-secondary uppercase">Contacto</p>
                    <ul class="mt-4 space-y-2 text-sm text-text/70">
                        @php($waPhone = \App\Models\WhatsappSetting::current()?->wa_link_phone)
                        @if ($waPhone)
                            <li>
                                <a href="https://wa.me/{{ $waPhone }}?text={{ urlencode('Hola Cumbres, tengo una consulta.') }}" target="_blank" rel="noopener" class="hover:text-primary">
                                    WhatsApp: +{{ $waPhone }}
                                </a>
                            </li>
                        @endif
                        <li>hola@cervezacumbres.pe</li>
                        <li>Lima, Perú</li>
                    </ul>
                </div>
            </div>

            <div class="border-t-[3px] border-text px-6 py-6 text-center text-xs text-text-muted">
                &copy; {{ now()->year }} Cerveza Cumbres · Prohibida la venta a menores de 18 años.
            </div>
        </footer>

        @if ($waPhone)
            <a
                href="https://wa.me/{{ $waPhone }}?text={{ urlencode('Hola Cumbres, tengo una consulta.') }}"
                target="_blank"
                rel="noopener"
                aria-label="Chatear por WhatsApp"
                class="fixed bottom-5 right-5 z-50 flex h-14 w-14 items-center justify-center rounded-full border-[3px] border-text bg-green-500 text-white shadow-brutal-sm transition hover:bg-green-400"
            >
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" class="h-7 w-7" fill="currentColor">
                    <path d="M16.001 3C9.373 3 4 8.373 4 15c0 2.29.638 4.43 1.744 6.256L4 29l7.94-1.7A11.94 11.94 0 0 0 16.001 27C22.629 27 28 21.627 28 15S22.629 3 16.001 3Zm0 21.818c-1.94 0-3.75-.52-5.312-1.428l-.381-.225-4.71 1.01 1.028-4.59-.248-.397A9.77 9.77 0 0 1 5.182 15c0-5.964 4.855-10.818 10.819-10.818S26.818 9.036 26.818 15 21.965 24.818 16.001 24.818Zm5.98-8.14c-.328-.164-1.94-.957-2.241-1.066-.301-.109-.52-.164-.738.164-.219.328-.848 1.066-1.04 1.285-.191.219-.383.246-.71.082-.328-.164-1.386-.51-2.64-1.628-.976-.87-1.635-1.945-1.826-2.273-.191-.328-.02-.505.144-.668.148-.147.328-.383.492-.574.164-.191.219-.328.328-.547.11-.219.055-.41-.027-.574-.082-.164-.738-1.777-1.012-2.434-.267-.64-.539-.554-.738-.564l-.629-.011c-.219 0-.574.082-.875.41-.301.328-1.148 1.121-1.148 2.734s1.176 3.172 1.34 3.391c.164.219 2.316 3.537 5.613 4.958.784.339 1.396.541 1.873.692.787.25 1.503.215 2.07.13.631-.094 1.94-.793 2.213-1.558.273-.766.273-1.422.191-1.559-.082-.137-.301-.219-.629-.383Z" />
                </svg>
            </a>
        @endif

        @livewireScripts
    </body>
</html>
