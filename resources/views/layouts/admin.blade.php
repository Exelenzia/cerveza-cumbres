<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>{{ $title ?? 'Admin' }} · Cumbres</title>

        <script>
            (function () {
                var stored = localStorage.getItem('theme');
                var theme = stored || (window.matchMedia('(prefers-color-scheme: light)').matches ? 'light' : 'dark');
                if (theme === 'light') document.documentElement.classList.add('light');
            })();
        </script>

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @livewireStyles
    </head>
    <body class="min-h-screen bg-surface font-sans text-text antialiased transition-colors duration-300">
        <div class="flex min-h-screen">
            <aside class="flex w-64 shrink-0 flex-col border-r-[3px] border-text bg-surface-muted">
                <div class="border-b-[3px] border-text px-6 py-5">
                    <a href="{{ route('admin.dashboard') }}" wire:navigate class="font-display text-xl font-semibold tracking-wide">
                        <span class="text-text">CUM</span><span class="text-primary">BRES</span>
                    </a>
                    <p class="mt-1 text-xs font-bold tracking-widest text-text-muted uppercase">Panel Admin</p>
                </div>

                <nav class="mt-4 flex flex-col gap-1 px-3 font-display text-sm font-bold tracking-wide">
                    @php
                        $links = [
                            ['route' => 'admin.dashboard', 'label' => 'Dashboard'],
                            ['route' => 'admin.orders.index', 'label' => 'Pedidos'],
                            ['route' => 'admin.reports.index', 'label' => 'Reportes'],
                            ['route' => 'admin.categories.index', 'label' => 'Categorías'],
                            ['route' => 'admin.products.index', 'label' => 'Productos'],
                            ['route' => 'admin.packs.index', 'label' => 'Packs'],
                            ['route' => 'admin.pack-templates.index', 'label' => 'Armador de packs'],
                            ['route' => 'admin.shipping.zones', 'label' => 'Envíos'],
                            ['route' => 'admin.coupons.index', 'label' => 'Cupones'],
                            ['route' => 'admin.cms.pages.index', 'label' => 'Páginas (CMS)'],
                            ['route' => 'admin.sunat.settings', 'label' => 'Facturación SUNAT'],
                            ['route' => 'admin.whatsapp.settings', 'label' => 'WhatsApp'],
                            ['route' => 'admin.settings.index', 'label' => 'Configuración'],
                        ];
                    @endphp

                    @foreach ($links as $link)
                        <a
                            href="{{ route($link['route']) }}"
                            wire:navigate
                            class="border-[2px] px-3 py-2 transition {{ request()->routeIs($link['route']) ? 'border-text bg-primary text-primary-on' : 'border-transparent text-text/80 hover:border-text hover:bg-surface' }}"
                        >
                            {{ $link['label'] }}
                        </a>
                    @endforeach
                </nav>

                <div class="mt-auto border-t-[3px] border-text px-3 py-6">
                    <button
                        type="button"
                        data-theme-toggle
                        class="mb-2 flex w-full items-center gap-2 px-3 py-2 text-sm font-bold text-text/80 transition hover:bg-surface"
                        aria-label="Cambiar tema"
                    >
                        <span data-theme-icon class="grid h-5 w-5 place-items-center rounded-full bg-primary text-[.7rem] text-primary-on">☾</span>
                        <span data-theme-label>Oscuro</span>
                    </button>
                    <a href="{{ route('home') }}" wire:navigate class="block px-3 py-2 text-sm text-text/60 transition hover:bg-surface">← Ver tienda</a>
                    <form method="POST" action="{{ route('logout') }}" class="mt-1">
                        @csrf
                        <button type="submit" class="w-full px-3 py-2 text-left text-sm text-text/60 transition hover:bg-surface">Cerrar sesión</button>
                    </form>
                </div>
            </aside>

            <div class="flex-1">
                <header class="border-b-[3px] border-text bg-surface px-8 py-5">
                    <h1 class="font-display text-lg font-bold tracking-wide uppercase">{{ $title ?? 'Dashboard' }}</h1>
                </header>

                <main class="p-8">
                    {{ $slot }}
                </main>
            </div>
        </div>

        @livewireScripts
    </body>
</html>
