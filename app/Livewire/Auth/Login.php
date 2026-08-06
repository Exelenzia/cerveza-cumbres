<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
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

    public function authenticate(): void
    {
        $this->validate();

        if (! Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            throw ValidationException::withMessages([
                'email' => 'Estas credenciales no coinciden con nuestros registros.',
            ]);
        }

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
