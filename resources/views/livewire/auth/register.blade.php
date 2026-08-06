<div class="mx-auto flex max-w-md flex-col justify-center px-6 py-20">
    <p class="text-center font-display text-xs font-semibold tracking-widest text-secondary uppercase">Únete a Cumbres</p>
    <h1 class="mt-2 text-center font-display text-3xl font-semibold text-text">Crea tu cuenta</h1>

    <form wire:submit="register" class="mt-10 space-y-5">
        <div>
            <label for="name" class="block text-sm font-medium text-text/80">Nombre completo</label>
            <input
                type="text"
                id="name"
                wire:model="name"
                autocomplete="name"
                class="mt-1 w-full rounded border border-border bg-surface-muted px-4 py-2.5 text-text placeholder-text-muted focus:border-primary focus:ring-primary focus:outline-none"
                placeholder="Tu nombre"
            >
            @error('name') <p class="mt-1 text-sm text-rose-400">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="email" class="block text-sm font-medium text-text/80">Correo electrónico</label>
            <input
                type="email"
                id="email"
                wire:model="email"
                autocomplete="email"
                class="mt-1 w-full rounded border border-border bg-surface-muted px-4 py-2.5 text-text placeholder-text-muted focus:border-primary focus:ring-primary focus:outline-none"
                placeholder="tucorreo@ejemplo.com"
            >
            @error('email') <p class="mt-1 text-sm text-rose-400">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="phone" class="block text-sm font-medium text-text/80">Teléfono (opcional)</label>
            <input
                type="text"
                id="phone"
                wire:model="phone"
                autocomplete="tel"
                class="mt-1 w-full rounded border border-border bg-surface-muted px-4 py-2.5 text-text placeholder-text-muted focus:border-primary focus:ring-primary focus:outline-none"
                placeholder="+51 999 999 999"
            >
            @error('phone') <p class="mt-1 text-sm text-rose-400">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-text/80">Contraseña</label>
            <input
                type="password"
                id="password"
                wire:model="password"
                autocomplete="new-password"
                class="mt-1 w-full rounded border border-border bg-surface-muted px-4 py-2.5 text-text placeholder-text-muted focus:border-primary focus:ring-primary focus:outline-none"
                placeholder="Mínimo 8 caracteres"
            >
            @error('password') <p class="mt-1 text-sm text-rose-400">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-text/80">Confirmar contraseña</label>
            <input
                type="password"
                id="password_confirmation"
                wire:model="password_confirmation"
                autocomplete="new-password"
                class="mt-1 w-full rounded border border-border bg-surface-muted px-4 py-2.5 text-text placeholder-text-muted focus:border-primary focus:ring-primary focus:outline-none"
                placeholder="Repite tu contraseña"
            >
        </div>

        <button
            type="submit"
            class="font-display w-full rounded bg-primary py-3 text-sm font-semibold tracking-widest text-primary-on uppercase transition hover:bg-primary-hover"
        >
            Crear cuenta
        </button>
    </form>

    <p class="mt-8 text-center text-sm text-text/60">
        ¿Ya tienes cuenta?
        <a href="{{ route('login') }}" wire:navigate class="font-medium text-primary hover:text-primary-hover">Ingresa aquí</a>
    </p>
</div>
