<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Livewire\Volt\Component;

new class extends Component {
    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Update the password for the currently authenticated user.
     */
    public function updatePassword(): void
    {
        try {
            $validated = $this->validate([
                'current_password' => ['required', 'string', 'current_password'],
                'password' => ['required', 'string', Password::defaults(), 'confirmed'],
            ]);
        } catch (ValidationException $e) {
            $this->reset('current_password', 'password', 'password_confirmation');

            throw $e;
        }

        Auth::user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        $this->reset('current_password', 'password', 'password_confirmation');

        $this->dispatch('password-updated');
    }
}; ?>

<section>
    <header class="mb-4">
        <h5 class="font-serif fw-bold text-dark mb-1">
            {{ __('Update Password') }}
        </h5>
        <p class="text-muted small">
            {{ __('Ensure your account is using a long, random password to stay secure.') }}
        </p>
    </header>

    <form wire:submit="updatePassword">

        <!-- Current Password -->
        <div class="form-floating mb-3">
            <input wire:model="current_password"
                   id="update_password_current_password"
                   name="current_password"
                   type="password"
                   class="form-control rounded-3 @error('current_password') is-invalid @enderror"
                   placeholder="Current Password"
                   autocomplete="current-password">
            <label for="update_password_current_password" class="text-muted">
                <i class="bi bi-key me-1"></i> {{ __('Current Password') }}
            </label>
            @error('current_password')
            <div class="invalid-feedback ps-2 small">{{ $message }}</div>
            @enderror
        </div>

        <!-- New Password -->
        <div class="form-floating mb-3">
            <input wire:model="password"
                   id="update_password_password"
                   name="password"
                   type="password"
                   class="form-control rounded-3 @error('password') is-invalid @enderror"
                   placeholder="New Password"
                   autocomplete="new-password">
            <label for="update_password_password" class="text-muted">
                <i class="bi bi-lock me-1"></i> {{ __('New Password') }}
            </label>
            @error('password')
            <div class="invalid-feedback ps-2 small">{{ $message }}</div>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div class="form-floating mb-4">
            <input wire:model="password_confirmation"
                   id="update_password_password_confirmation"
                   name="password_confirmation"
                   type="password"
                   class="form-control rounded-3 @error('password_confirmation') is-invalid @enderror"
                   placeholder="Confirm Password"
                   autocomplete="new-password">
            <label for="update_password_password_confirmation" class="text-muted">
                <i class="bi bi-lock-fill me-1"></i> {{ __('Confirm Password') }}
            </label>
            @error('password_confirmation')
            <div class="invalid-feedback ps-2 small">{{ $message }}</div>
            @enderror
        </div>

        <!-- Action Buttons -->
        <div class="d-flex align-items-center gap-3">
            <button type="submit"
                    class="btn btn-brand rounded-pill px-4 shadow-sm">
                {{ __('Save') }}
            </button>

            <x-action-message class="text-success small fw-bold" on="password-updated">
                <i class="bi bi-check-circle-fill me-1"></i> {{ __('Saved.') }}
            </x-action-message>
        </div>
    </form>
</section>
