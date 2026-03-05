<?php

use App\Livewire\Forms\LoginForm;
use App\Models\Restaurant;
use App\Helpers\SubdomainHelper;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')]
class extends Component {
    public LoginForm $form;
    public ?Restaurant $restaurant = null;

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $subdomain = SubdomainHelper::getCurrentSubdomain();
        if ($subdomain) $this->restaurant = Restaurant::where('subdomain', $subdomain)->first();
    }

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $this->redirectIntended(default: RouteServiceProvider::HOME, navigate: true);
    }
}; ?>

<div class="card card-login-enterprise border-0">
    <div class="card-body p-4 p-sm-5">

        <!-- Header: Dynamic based on Restaurant -->
        <div class="text-center mb-5">
            <div
                class="d-inline-flex align-items-center justify-content-center bg-dark text-white rounded-3 mb-3 shadow-sm"
                style="width: 48px; height: 48px;">
                @if($restaurant && $restaurant->logo)
                    <img src="{{ Storage::url($restaurant->logo) }}" alt="{{ $restaurant->name }}" class="rounded-3"
                         style="width: 100%; height: 100%; object-fit: cover;">
                @else
                    <i class="bi bi-layers-fill fs-5"></i>
                @endif
            </div>
            <h4 class="fw-bold mb-1" style="color: #0f172a; letter-spacing: -0.5px;">
                {{ $restaurant ? __('login.title', ['restaurantName' => $restaurant->name]) : __('login.default_title') }}
            </h4>
            <p class="text-muted small">
                {{ $restaurant ? __('login.subtitle', ['restaurantName' => $restaurant->name]) : __('login.default_subtitle') }}
            </p>
        </div>

        <!-- Session Status -->
        @if (session('status'))
            <div class="alert alert-success d-flex align-items-center mb-4 py-2 border-0 rounded-3 small" role="alert"
                 style="background-color: #ecfdf5; color: #047857;">
                <i class="bi bi-check-circle-fill me-2"></i>
                <div>{{ session('status') }}</div>
            </div>
        @endif

        <form wire:submit="login">

            <!-- Email Input -->
            <div class="form-floating mb-3">
                <input wire:model="form.email"
                       type="email"
                       class="form-control form-control-enterprise @error('form.email') is-invalid @enderror"
                       id="email"
                       placeholder="{{ __('login.email_label') }}"
                       required autofocus>
                <label for="email" class="text-muted ps-3">{{ __('login.email_label') }}</label>
                @error('form.email')
                <div class="invalid-feedback ps-1 small">{{ $message }}</div>
                @enderror
            </div>

            <!-- Password Input -->
            <div x-data="{ show: false }" class="mb-4">
                <div class="input-group">
                    <div class="form-floating flex-grow-1">
                        <input wire:model="form.password"
                               :type="show ? 'text' : 'password'"
                               class="form-control form-control-enterprise rounded-end-0 @error('form.password') is-invalid @enderror"
                               id="password"
                               placeholder="{{ __('login.password_label') }}"
                               style="border-right: none;"
                               required>
                        <label for="password" class="text-muted ps-3">{{ __('login.password_label') }}</label>
                    </div>
                    <span class="input-group-text bg-white border-start-0 px-3 rounded-end-3"
                          style="border: 1px solid #e2e8f0; border-radius: 12px; cursor: pointer;"
                          @click="show = !show">
                        <i class="bi" :class="show ? 'bi-eye-slash text-primary' : 'bi-eye text-muted'"></i>
                    </span>
                </div>
                @error('form.password')
                <div class="text-danger small mt-1 ps-1">{{ $message }}</div>
                @enderror
            </div>

            <!-- Remember & Forgot -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="form-check">
                    <input wire:model="form.remember" id="remember" type="checkbox" class="form-check-input"
                           style="cursor: pointer; border-color: #cbd5e1;">
                    <label for="remember" class="form-check-label text-muted small user-select-none"
                           style="cursor: pointer;">
                        {{ __('login.remember_device') }}
                    </label>
                </div>

                @if (Route::has('password.request'))
                    <a class="text-decoration-none small fw-semibold text-primary"
                       href="{{ route('password.request') }}" wire:navigate>
                        {{ __('login.forgot_password') }}
                    </a>
                @endif
            </div>

            <!-- Submit Button -->
            <div class="d-grid mb-4">
                <button type="submit"
                        class="btn btn-enterprise shadow-sm"
                        wire:loading.attr="disabled"
                        wire:target="login">
                    <span wire:loading.remove wire:target="login">{{ __('login.submit_button') }} <i
                            class="bi bi-arrow-right ms-1"></i></span>
                    <span wire:loading wire:target="login">
                        <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                        {{ __('login.authenticating') }}
                    </span>
                </button>
            </div>

            <!-- Register Link -->
            {{--            <div class="text-center">--}}
            {{--                <p class="text-muted small mb-0">--}}
            {{--                    {{ $restaurant ? __('login.new_to_us', ['restaurantName' => $restaurant->name]) : __('login.default_new_to_us') }}--}}
            {{--                    <a href="{{ route('register') }}"--}}
            {{--                       class="text-primary fw-semibold text-decoration-none"--}}
            {{--                       wire:navigate>--}}
            {{--                        {{ __('login.register_outlet') }}--}}
            {{--                    </a>--}}
            {{--                </p>--}}
            {{--            </div>--}}

        </form>

    </div>
</div>
