<div class="mx-auto flex max-w-md flex-col justify-center px-6 py-20">
    <p class="text-center font-display text-xs font-semibold tracking-widest text-gold-500 uppercase">Únete a Cumbres</p>
    <h1 class="mt-2 text-center font-display text-3xl font-semibold text-cream-100">Crea tu cuenta</h1>

    <form wire:submit="register" class="mt-10 space-y-5">
        <div>
            <label for="name" class="block text-sm font-medium text-cream-100/80">Nombre completo</label>
            <input
                type="text"
                id="name"
                wire:model="name"
                autocomplete="name"
                class="mt-1 w-full rounded border border-cumbre-700 bg-cumbre-900 px-4 py-2.5 text-cream-100 placeholder-cream-100/30 focus:border-gold-500 focus:ring-gold-500 focus:outline-none"
                placeholder="Tu nombre"
            >
            @error('name') <p class="mt-1 text-sm text-rose-400">{{ $message }}</p> @enderror
        </div>

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
            <label for="phone" class="block text-sm font-medium text-cream-100/80">Teléfono (opcional)</label>
            <input
                type="text"
                id="phone"
                wire:model="phone"
                autocomplete="tel"
                class="mt-1 w-full rounded border border-cumbre-700 bg-cumbre-900 px-4 py-2.5 text-cream-100 placeholder-cream-100/30 focus:border-gold-500 focus:ring-gold-500 focus:outline-none"
                placeholder="+51 999 999 999"
            >
            @error('phone') <p class="mt-1 text-sm text-rose-400">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-cream-100/80">Contraseña</label>
            <input
                type="password"
                id="password"
                wire:model="password"
                autocomplete="new-password"
                class="mt-1 w-full rounded border border-cumbre-700 bg-cumbre-900 px-4 py-2.5 text-cream-100 placeholder-cream-100/30 focus:border-gold-500 focus:ring-gold-500 focus:outline-none"
                placeholder="Mínimo 8 caracteres"
            >
            @error('password') <p class="mt-1 text-sm text-rose-400">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-cream-100/80">Confirmar contraseña</label>
            <input
                type="password"
                id="password_confirmation"
                wire:model="password_confirmation"
                autocomplete="new-password"
                class="mt-1 w-full rounded border border-cumbre-700 bg-cumbre-900 px-4 py-2.5 text-cream-100 placeholder-cream-100/30 focus:border-gold-500 focus:ring-gold-500 focus:outline-none"
                placeholder="Repite tu contraseña"
            >
        </div>

        <button
            type="submit"
            class="font-display w-full rounded bg-gold-500 py-3 text-sm font-semibold tracking-widest text-cumbre-950 uppercase transition hover:bg-gold-400"
        >
            Crear cuenta
        </button>
    </form>

    <p class="mt-8 text-center text-sm text-cream-100/60">
        ¿Ya tienes cuenta?
        <a href="{{ route('login') }}" wire:navigate class="font-medium text-gold-400 hover:text-gold-300">Ingresa aquí</a>
    </p>
</div>
