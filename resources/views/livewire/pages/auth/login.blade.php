<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component {
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="card shadow-lg border-0 rounded-4" style="max-width: 400px; width: 100%;">
        <div class="card-body p-5">

            <!-- Header -->
            <div class="text-center mb-4">
                <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3 shadow-sm" style="width: 64px; height: 64px;">
                    <i class="bi bi-newspaper fs-2"></i>
                </div>
                <h3 class="fw-bold text-dark">Rekap Berita</h3>
                <p class="text-muted small">Masuk untuk mengelola laporan media</p>
            </div>

            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form wire:submit="login">
                <!-- Email Address -->
                <div class="form-floating mb-3">
                    <input wire:model="form.email" id="email" type="email"
                           class="form-control @error('form.email') is-invalid @enderror"
                           placeholder="name@example.com" required autofocus autocomplete="username">
                    <label for="email">Email Address</label>
                    @error('form.email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Password -->
                <div class="form-floating mb-3">
                    <input wire:model="form.password" id="password" type="password"
                           class="form-control @error('form.password') is-invalid @enderror"
                           placeholder="Password" required autocomplete="current-password">
                    <label for="password">Password</label>
                    @error('form.password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="form-check mb-3">
                    <input wire:model="form.remember" id="remember_me" type="checkbox" class="form-check-input">
                    <label class="form-check-label text-muted" for="remember_me">
                        Ingat Saya
                    </label>
                </div>

                <!-- Submit Button -->
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg fw-semibold" wire:loading.attr="disabled">
                        <span wire:loading.remove>Masuk</span>
                        <span wire:loading>
                            <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                            Memproses...
                        </span>
                    </button>
                </div>

                {{-- Forgot Password Link (Optional) --}}
                {{--
                <div class="text-center mt-3">
                    @if (Route::has('password.request'))
                        <a class="text-decoration-none small text-muted" href="{{ route('password.request') }}" wire:navigate>
                            Lupa password?
                        </a>
                    @endif
                </div>
                --}}
            </form>
        </div>
    </div>
</div>
