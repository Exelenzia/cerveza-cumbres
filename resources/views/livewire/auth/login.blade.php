<div class="mx-auto flex max-w-md flex-col justify-center px-6 py-20">
    <p class="text-center font-display text-xs font-semibold tracking-widest text-gold-500 uppercase">Bienvenido de vuelta</p>
    <h1 class="mt-2 text-center font-display text-3xl font-semibold text-cream-100">Ingresa a tu cuenta</h1>

    <form wire:submit="authenticate" class="mt-10 space-y-5">
        <div>
            <label for="email" class="block text-sm font-medium text-cream-100/80">Correo electrónico</label>
            <input
                type="email"
                id="email"
                wire:model="email"
                autocomplete="email"
                class="mt-1 w-full rounded border border-cumbre-700 bg-cumbre-900 px-4 py-2.5 text-cream-100 placeholder-cream-100/30 focus:border-gold-500 focus:ring-gold-500 focus:outline-none"
                placeholder="tucorreo@ejemplo.com"
            >
            @error('email') <p class="mt-1 text-sm text-rose-400">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-cream-100/80">Contraseña</label>
            <input
                type="password"
                id="password"
                wire:model="password"
                autocomplete="current-password"
                class="mt-1 w-full rounded border border-cumbre-700 bg-cumbre-900 px-4 py-2.5 text-cream-100 placeholder-cream-100/30 focus:border-gold-500 focus:ring-gold-500 focus:outline-none"
                placeholder="••••••••"
            >
            @error('password') <p class="mt-1 text-sm text-rose-400">{{ $message }}</p> @enderror
        </div>

        <label class="flex items-center gap-2 text-sm text-cream-100/70">
            <input type="checkbox" wire:model="remember" class="rounded border-cumbre-700 bg-cumbre-900 text-gold-500 focus:ring-gold-500">
            Recordarme
        </label>

        <button
            type="submit"
            class="font-display w-full rounded bg-gold-500 py-3 text-sm font-semibold tracking-widest text-cumbre-950 uppercase transition hover:bg-gold-400"
        >
            Ingresar
        </button>
    </form>

    <p class="mt-8 text-center text-sm text-cream-100/60">
        ¿Aún no tienes cuenta?
        <a href="{{ route('register') }}" wire:navigate class="font-medium text-gold-400 hover:text-gold-300">Crea una aquí</a>
    </p>
</div>
