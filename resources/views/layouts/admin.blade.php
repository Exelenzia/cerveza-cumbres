<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>{{ $title ?? 'Admin' }} · Cumbres</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @livewireStyles
    </head>
    <body class="min-h-screen bg-cumbre-950 font-sans text-cream-100 antialiased">
        <div class="flex min-h-screen">
            <aside class="flex w-64 shrink-0 flex-col border-r border-cumbre-700/60 bg-cumbre-900">
                <div class="px-6 py-5">
                    <a href="{{ route('admin.dashboard') }}" wire:navigate class="font-display text-xl font-semibold tracking-wide">
                        <span class="text-cream-100">CUM</span><span class="text-gold-500">BRES</span>
                    </a>
                    <p class="mt-1 text-xs tracking-widest text-cream-100/50 uppercase">Panel Admin</p>
                </div>

                <nav class="mt-4 flex flex-col gap-1 px-3 font-display text-sm font-medium tracking-wide">
                    @php
                        $links = [
                            ['route' => 'admin.dashboard', 'label' => 'Dashboard'],
                            ['route' => 'admin.orders.index', 'label' => 'Pedidos'],
                            ['route' => 'admin.categories.index', 'label' => 'Categorías'],
                            ['route' => 'admin.products.index', 'label' => 'Productos'],
                            ['route' => 'admin.packs.index', 'label' => 'Packs'],
                            ['route' => 'admin.shipping.zones', 'label' => 'Envíos'],
                            ['route' => 'admin.coupons.index', 'label' => 'Cupones'],
                            ['route' => 'admin.settings.index', 'label' => 'Configuración'],
                        ];
                    @endphp

                    @foreach ($links as $link)
                        <a
                            href="{{ route($link['route']) }}"
                            wire:navigate
                            class="rounded px-3 py-2 transition {{ request()->routeIs($link['route']) ? 'bg-gold-500 text-cumbre-950' : 'text-cream-100/80 hover:bg-cumbre-800' }}"
                        >
                            {{ $link['label'] }}
                        </a>
                    @endforeach
                </nav>

                <div class="mt-auto px-3 py-6">
                    <a href="{{ route('home') }}" wire:navigate class="block rounded px-3 py-2 text-sm text-cream-100/60 transition hover:bg-cumbre-800">← Ver tienda</a>
                    <form method="POST" action="{{ route('logout') }}" class="mt-1">
                        @csrf
                        <button type="submit" class="w-full rounded px-3 py-2 text-left text-sm text-cream-100/60 transition hover:bg-cumbre-800">Cerrar sesión</button>
                    </form>
                </div>
            </aside>

            <div class="flex-1">
                <header class="border-b border-cumbre-700/60 bg-cumbre-950 px-8 py-5">
                    <h1 class="font-display text-lg font-semibold tracking-wide">{{ $title ?? 'Dashboard' }}</h1>
                </header>

                <main class="p-8">
                    {{ $slot }}
                </main>
            </div>
        </div>

        @livewireScripts
    </body>
</html>
