<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('layouts.storefront')]
class Login extends Component
{
    #[Validate('required|email')]
    public string $email = '';

    #[Validate('required|string')]
    public string $password = '';

    public bool $remember = false;

    private function throttleKey(): string
    {
        return Str::lower($this->email).'|'.request()->ip();
    }

    public function authenticate(): void
    {
        $this->validate();

        if (RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            $seconds = RateLimiter::availableIn($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => "Demasiados intentos. Vuelve a intentar en {$seconds} segundos.",
            ]);
        }

        if (! Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            RateLimiter::hit($this->throttleKey(), 60);

            throw ValidationException::withMessages([
                'email' => 'Estas credenciales no coinciden con nuestros registros.',
            ]);
        }

        RateLimiter::clear($this->throttleKey());

        request()->session()->regenerate();

        if (Auth::user()->hasAnyRole(['admin', 'staff'])) {
            $this->redirect(route('admin.dashboard'), navigate: true);

            return;
        }

        $intended = session()->pull('url.intended', route('home'));
        $this->redirect($intended, navigate: true);
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}
