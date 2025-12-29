<!--
  This file was part of laravel breeze

  I modified it to use volt without a separate form file
-->

<?php

use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth')] class extends Component
{
    #[Rule('required|string|email')]
    public string $email = '';

    #[Rule('required|string')]
    public string $password = '';

    #[Rule('boolean')]
    public bool $remember = false;

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws ValidationException
     */
    protected function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        if (! Auth::attempt($this->only(['email', 'password']), $this->remember)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the authentication request is not rate limited.
     */
    protected function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
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
        return Str::transliterate(Str::lower($this->email).'|'.request()->ip());
    }

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->authenticate();

        Session::regenerate();

        $this->redirectIntended();
    }
}; ?>

<div>
    <!-- Session Status -->
    @if (session('status'))
        <div class='font-medium text-sm text-green-600 mb-4'>
            {{ session('status') }}
        </div>
    @endif

    <form wire:submit="login">
        <!-- Email Address -->
        <div>
            <label class='block font-medium text-sm text-gray-700' for="email">
                Email
            </label>
            <input
                wire:model="email"
                id="email"
                class="px-3 py-2 block mt-1 w-full border border-gray-300 focus:ring-indigo-500"
                type="email"
                name="email"
                required
                autofocus
                autocomplete="username"/>
            @if($errors->get('email'))
                <ul class='text-sm text-red-600 space-y-1 mt-2'>
                    @foreach ((array) $errors->get('email') as $message)
                        <li>{{ $message }}</li>
                    @endforeach
                </ul>
            @endif
        </div>

        <!-- Password -->
        <div class="mt-4">
            <label class='block font-medium text-sm text-gray-700' for="password">
                Mot de passe
            </label>

            <input wire:model="password"
                   id="password"
                   class="px-3 py-2 block mt-1 w-full border border-gray-300 focus:ring-indigo-500"
                   type="password"
                   name="password"
                   required autocomplete="current-password"/>

            @if($errors->get('password'))
                <ul class='text-sm text-red-600 space-y-1 mt-2'>
                    @foreach ((array) $errors->get('password') as $message)
                        <li>{{ $message }}</li>
                    @endforeach
                </ul>
            @endif
        </div>

        <!-- Remember Me -->
        <div class="block mt-2 flex justify-between">
            <a
                class="text-xs text-gray-600 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                href="{{ route('register') }}"
            >
                Mot de passe oublié?
            </a>
        </div>

        <div class="flex items-center justify-end mt-4">
            <a
                class="text-sm py-1 px-2 text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                href="{{ route('register') }}"
            >
                Créer un compte
            </a>
            <button type='submit'
                    class='ms-4 cursor-pointer font-semibold uppercase px-4 py-2 bg-gray-800 border border-transparent  text-xs text-white tracking-widest hover:bg-gray-700  focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150'>
                Se connecter
            </button>

        </div>
    </form>
</div>
