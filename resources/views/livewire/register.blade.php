<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth')] class extends Component {
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

        event(new Registered(($user = User::create($validated))));

        Auth::login($user);

        $this->redirectIntended(route('home', absolute: false), navigate: true);
    }
}; ?>


<div>
    <!-- Session Status -->
    @if (session('status'))
        <div class='font-medium text-sm text-green-600 mb-4'>
            {{ session('status') }}
        </div>
    @endif

    <form wire:submit="register">
        <!-- Name -->
        <div>
            <label class='block font-medium text-sm text-gray-700' for="name">
                Nom complet
            </label>
            <input
                wire:model="name"
                id="name"
                class="mt-1 px-3 py-2 block w-full border border-gray-300 focus:ring-indigo-500"
                type="text"
                name="name"
                required
                autofocus
                autocomplete="name"/>
            @if($errors->get('name'))
                <ul class='text-sm text-red-600 space-y-1 mt-2'>
                    @foreach ((array) $errors->get('name') as $message)
                        <li>{{ $message }}</li>
                    @endforeach
                </ul>
            @endif
        </div>

        <!-- Email Address -->
        <div class="mt-4">
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
                   required autocomplete="new-password"/>

            @if($errors->get('password'))
                <ul class='text-sm text-red-600 space-y-1 mt-2'>
                    @foreach ((array) $errors->get('password') as $message)
                        <li>{{ $message }}</li>
                    @endforeach
                </ul>
            @endif
        </div>

        <!-- Confirm password -->
        <div class="mt-4">
            <label class='block font-medium text-sm text-gray-700' for="password_confirmation">
                Confirmation du mot de passe
            </label>

            <input wire:model="password_confirmation"
                   id="password_confirmation"
                   class="px-3 py-2 block mt-1 w-full border border-gray-300 focus:ring-indigo-500"
                   type="password"
                   name="password_confirmation"
                   required autocomplete="new-password"/>

            @if($errors->get('password_confirmation'))
                <ul class='text-sm text-red-600 space-y-1 mt-2'>
                    @foreach ((array) $errors->get('password_confirmation') as $message)
                        <li>{{ $message }}</li>
                    @endforeach
                </ul>
            @endif
        </div>

        <div class="flex items-center justify-end mt-4">
            <a
                class="text-sm py-1 px-2 text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                href="{{ route('login') }}"
            >
                Compte existant?
            </a>
            <button type='submit'
                    class='ms-4 cursor-pointer font-semibold uppercase px-4 py-2 bg-gray-800 border border-transparent  text-xs text-white tracking-widest hover:bg-gray-700  focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150'>
                Se connecter
            </button>

        </div>
    </form>
</div>
