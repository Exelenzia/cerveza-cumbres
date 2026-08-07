<div class="mx-auto flex max-w-md flex-col justify-center px-6 py-20">
    <p class="text-center font-display text-xs font-semibold tracking-widest text-secondary uppercase">Bienvenido de vuelta</p>
    <h1 class="mt-2 text-center font-display text-3xl uppercase tracking-wide text-text" data-reveal>Ingresa a tu cuenta</h1>

    <form wire:submit="authenticate" class="mt-10 space-y-5">
        <div>
            <label for="email" class="block text-sm font-medium text-text/80">Correo electrónico</label>
            <input
                type="email"
                id="email"
                wire:model="email"
                autocomplete="email"
                class="mt-1 w-full border-[2px] border-text bg-surface-muted px-4 py-2.5 text-text placeholder-text-muted focus:outline-none"
                placeholder="tucorreo@ejemplo.com"
            >
            @error('email') <p class="mt-1 text-sm text-rose-400">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-text/80">Contraseña</label>
            <input
                type="password"
                id="password"
                wire:model="password"
                autocomplete="current-password"
                class="mt-1 w-full border-[2px] border-text bg-surface-muted px-4 py-2.5 text-text placeholder-text-muted focus:outline-none"
                placeholder="••••••••"
            >
            @error('password') <p class="mt-1 text-sm text-rose-400">{{ $message }}</p> @enderror
        </div>

        <label class="flex items-center gap-2 text-sm text-text/70">
            <input type="checkbox" wire:model="remember" class="rounded border-border bg-surface-muted text-primary focus:ring-primary">
            Recordarme
        </label>

        <button
            type="submit"
            class="btn-brutal font-display w-full bg-primary py-3 text-sm font-semibold tracking-widest text-primary-on uppercase transition hover:bg-primary-hover"
        >
            Ingresar
        </button>
    </form>

    <p class="mt-8 text-center text-sm text-text/60">
        ¿Aún no tienes cuenta?
        <a href="{{ route('register') }}" wire:navigate class="font-medium text-primary hover:text-primary-hover">Crea una aquí</a>
    </p>
</div>
