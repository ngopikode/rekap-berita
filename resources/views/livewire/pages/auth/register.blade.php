<?php

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')]
class extends Component {
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        event(new Registered($user = User::create($validated)));

        Auth::login($user);

        $this->redirectRoute('dashboard', navigate: true);
    }
}; ?>

<div class="card card-auth border-0">
    <div class="card-body p-4 p-sm-5">

        <!-- Header: Font Serif (Playfair Display) -->
        <div class="text-center mb-4">
            <h3 class="font-serif fw-bold mb-1" style="color: #1e293b;">Buat Akun Baru</h3>
            <p class="text-muted small">Mulai perjalanan pernikahan impian Anda.</p>
        </div>

        <form wire:submit="register">

            <!-- Name Input -->
            <div class="form-floating mb-3">
                <input wire:model="name"
                       type="text"
                       class="form-control rounded-4 @error('name') is-invalid @enderror"
                       id="name"
                       placeholder="Nama Lengkap"
                       style="background-color: #f8fafc; border: 1px solid #e2e8f0;"
                       required autofocus autocomplete="name">
                <label for="name" class="text-muted">
                    <i class="bi bi-person me-1"></i> Nama Lengkap
                </label>

                @error('name')
                <div class="invalid-feedback ps-2 small">
                    {{ $message }}
                </div>
                @enderror
            </div>

            <!-- Email Input -->
            <div class="form-floating mb-3">
                <input wire:model="email"
                       type="email"
                       class="form-control rounded-4 @error('email') is-invalid @enderror"
                       id="email"
                       placeholder="name@example.com"
                       style="background-color: #f8fafc; border: 1px solid #e2e8f0;"
                       required autocomplete="username">
                <label for="email" class="text-muted">
                    <i class="bi bi-envelope me-1"></i> Alamat Email
                </label>

                @error('email')
                <div class="invalid-feedback ps-2 small">
                    {{ $message }}
                </div>
                @enderror
            </div>

            <!-- Password Input -->
            <div x-data="{ show: false }" class="mb-3">
                <div class="input-group">
                    <div class="form-floating flex-grow-1 position-relative">
                        <input wire:model="password"
                               :type="show ? 'text' : 'password'"
                               class="form-control rounded-4 rounded-end-0 @error('password') is-invalid @enderror"
                               id="password"
                               placeholder="Password"
                               style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-right: none;"
                               required autocomplete="new-password">
                        <label for="password" class="text-muted">
                            <i class="bi bi-lock me-1"></i> Password
                        </label>
                    </div>
                    <span class="input-group-text bg-light border-start-0 rounded-4 rounded-start-0 pe-3"
                          style="background-color: #f8fafc !important; border: 1px solid #e2e8f0; cursor: pointer;"
                          @click="show = !show">
                        <i class="bi" :class="show ? 'bi-eye-slash text-brand' : 'bi-eye text-muted'"></i>
                    </span>
                </div>
                @error('password')
                <div class="text-danger small mt-1 ps-2">{{ $message }}</div>
                @enderror
            </div>

            <!-- Confirm Password Input -->
            <div x-data="{ showConfirm: false }" class="mb-4">
                <div class="input-group">
                    <div class="form-floating flex-grow-1 position-relative">
                        <input wire:model="password_confirmation"
                               :type="showConfirm ? 'text' : 'password'"
                               class="form-control rounded-4 rounded-end-0 @error('password_confirmation') is-invalid @enderror"
                               id="password_confirmation"
                               placeholder="Konfirmasi Password"
                               style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-right: none;"
                               required autocomplete="new-password">
                        <label for="password_confirmation" class="text-muted">
                            <i class="bi bi-lock-fill me-1"></i> Konfirmasi Password
                        </label>
                    </div>
                    <span class="input-group-text bg-light border-start-0 rounded-4 rounded-start-0 pe-3"
                          style="background-color: #f8fafc !important; border: 1px solid #e2e8f0; cursor: pointer;"
                          @click="showConfirm = !showConfirm">
                        <i class="bi" :class="showConfirm ? 'bi-eye-slash text-brand' : 'bi-eye text-muted'"></i>
                    </span>
                </div>
                @error('password_confirmation')
                <div class="text-danger small mt-1 ps-2">{{ $message }}</div>
                @enderror
            </div>

            <!-- Submit Button -->
            <div class="d-grid">
                <button type="submit"
                        class="btn btn-brand py-3 rounded-pill shadow-sm"
                        wire:loading.attr="disabled"
                        wire:target="register">

                    <span wire:loading.remove wire:target="register">
                        Daftar Sekarang <i class="bi bi-arrow-right ms-2"></i>
                    </span>

                    <span wire:loading wire:target="register">
                        <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                        Mendaftarkan...
                    </span>
                </button>
            </div>

            <!-- Login Link -->
            <div class="text-center mt-4">
                <p class="text-muted small mb-0">Sudah punya akun?
                    <a href="{{ route('login') }}" class="text-brand fw-bold text-decoration-none" wire:navigate>
                        Masuk disini
                    </a>
                </p>
            </div>

        </form>
    </div>
</div>
