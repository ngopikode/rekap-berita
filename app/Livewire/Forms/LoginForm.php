<?php

namespace App\Livewire\Forms;

use App\Helpers\SubdomainHelper;
use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Validate;
use Livewire\Form;

class LoginForm extends Form
{
    #[Validate('required|string|email')]
    public string $email = '';

    #[Validate('required|string')]
    public string $password = '';

    #[Validate('boolean')]
    public bool $remember = false;

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        if (!Auth::attempt($this->only(['email', 'password']), $this->remember)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'form.email' => trans('auth.failed'),
            ]);
        }

        $this->validateSubdomain();

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Validate if the authenticated user belongs to the current subdomain.
     *
     * @throws ValidationException
     */
    protected function validateSubdomain(): void
    {
        /** @var User $user */
        $user = Auth::user();
        $currentSubdomain = SubdomainHelper::getCurrentSubdomain();

        // If there's no subdomain, or the user is not associated with a restaurant, allow login.
        // This could be for a central admin dashboard.
        if (is_null($currentSubdomain) || is_null($user->restaurant)) {
            return;
        }

        // If the user's restaurant subdomain does not match the current subdomain, block the login.
        if ($user->restaurant->subdomain !== $currentSubdomain) {
            Auth::logout(); // Log the user out immediately

            throw ValidationException::withMessages([
                'form.email' => trans('auth.incorrect_restaurant'),
            ]);
        }
    }


    /**
     * Ensure the authentication request is not rate limited.
     */
    protected function ensureIsNotRateLimited(): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'form.email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the authentication rate limiting throttle key.
     */
    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->email) . '|' . request()->ip());
    }
}
