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
                    </ul>
                </div>

                <div>
                    <p class="font-display text-sm font-semibold tracking-widest text-gold-500 uppercase">Delivery Perú</p>
                    <p class="mt-4 text-sm text-cream-100/70">Enviamos a Lima en 24h y al interior del país en 24–72h.</p>
                </div>

                <div>
                    <p class="font-display text-sm font-semibold tracking-widest text-gold-500 uppercase">Contacto</p>
                    <ul class="mt-4 space-y-2 text-sm text-cream-100/70">
                        <li>WhatsApp: +51 999 999 999</li>
                        <li>hola@cervezacumbres.pe</li>
                        <li>Lima, Perú</li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-cumbre-700/60 px-6 py-6 text-center text-xs text-cream-100/50">
                &copy; {{ now()->year }} Cerveza Cumbres · Prohibida la venta a menores de 18 años.
            </div>
        </footer>

        @livewireScripts
    </body>
</html>
