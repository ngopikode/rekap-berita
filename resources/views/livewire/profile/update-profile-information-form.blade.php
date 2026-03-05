<?php

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;

new class extends Component {
    public string $name = '';
    public string $email = '';

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->name = Auth::user()->name;
        $this->email = Auth::user()->email;
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            // 'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
        ]);

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        $this->dispatch('profile-updated', name: $user->name);
    }

    /**
     * Send an email verification notification to the current user.
     */
    public function sendVerification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: RouteServiceProvider::HOME);

            return;
        }

        $user->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }
}; ?>

<section>
    <header class="mb-4">
        <h5 class="font-serif fw-bold text-dark mb-1">
            {{ __('Profile Information') }}
        </h5>
        <p class="text-muted small">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form wire:submit="updateProfileInformation">

        <!-- Name Input -->
        <div class="form-floating mb-3">
            <input wire:model="name"
                   id="name"
                   name="name"
                   type="text"
                   class="form-control rounded-3 @error('name') is-invalid @enderror"
                   placeholder="Full Name"
                   required autofocus autocomplete="name">
            <label for="name" class="text-muted">
                <i class="bi bi-person me-1"></i> {{ __('Name') }}
            </label>
            @error('name')
            <div class="invalid-feedback ps-2 small">{{ $message }}</div>
            @enderror
        </div>

        <!-- Email Input -->
        <div class="form-floating mb-3">
            <input wire:model="email" @readonly(true)
            id="email"
                   name="email"
                   type="email"
                   class="form-control rounded-3 @error('email') is-invalid @enderror"
                   placeholder="Email Address"
                   required autocomplete="username">
            <label for="email" class="text-muted">
                <i class="bi bi-envelope me-1"></i> {{ __('Email') }}
            </label>
            @error('email')
            <div class="invalid-feedback ps-2 small">{{ $message }}</div>
            @enderror

            <!-- Email Verification Section -->
            @if (auth()->user() instanceof MustVerifyEmail && ! auth()->user()->hasVerifiedEmail())
                <div class="mt-3 p-3 bg-warning bg-opacity-10 rounded-3 border border-warning border-opacity-25">
                    <p class="small text-dark mb-2">
                        {{ __('Your email address is unverified.') }}
                    </p>

                    <button wire:click.prevent="sendVerification" class="btn btn-sm btn-outline-dark">
                        {{ __('Click here to re-send the verification email.') }}
                    </button>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 fw-bold small text-success">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <!-- Action Buttons -->
        <div class="d-flex align-items-center gap-3 mt-4">
            <button type="submit"
                    class="btn btn-brand rounded-pill px-4 shadow-sm">
                {{ __('Save Changes') }}
            </button>

            <x-action-message class="text-success small fw-bold" on="profile-updated">
                <i class="bi bi-check-circle-fill me-1"></i> {{ __('Saved.') }}
            </x-action-message>
        </div>
    </form>
</section>
