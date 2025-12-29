<?php

use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rules\Password;

use function Livewire\Volt\state;
use function Livewire\Volt\action;

state([
    'saved' => false,
])->locked();

state([
    'current_password' => '',
    'password' => '',
    'password_confirmation',
])->modelable();

$save = action(function (): bool {
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

    return true;
});
?>

<div class="bg-white p-4 shadow sm:p-8">
    <h2 class="text-lg font-medium text-gray-900">
        Changez votre mot de passe
    </h2>
    <p class="mt-1 text-sm text-gray-600">
        Assurez-vous d'utiliser un mot de passe long et aléatoire pour sécuriser votre compte.
    </p>

    <form class="mt-6 space-y-6">
        <!-- Current password -->
        <div>
            <label class='block font-medium text-sm text-gray-700' for="current_password">
                Mot de passe actuel
            </label>
            <input
                wire:model="current_password"
                id="current_password"
                class="mt-1 px-3 py-2 block w-full border border-gray-300 focus:ring-indigo-500"
                type="password"
                name="current_password"
                required
                autofocus
                autocomplete="current_password"/>
            @if($errors->get('current_password'))
                <ul class='text-sm text-red-600 space-y-1 mt-2'>
                    @foreach ((array) $errors->get('current_password') as $message)
                        <li>{{ $message }}</li>
                    @endforeach
                </ul>
            @endif
        </div>

        <!-- New password -->
        <div>
            <label class='block font-medium text-sm text-gray-700' for="password">
                Nouveau mot de passe
            </label>
            <input
                wire:model="password"
                id="password"
                class="mt-1 px-3 py-2 block w-full border border-gray-300 focus:ring-indigo-500"
                type="password"
                name="password"
                required
                autofocus
                autocomplete="password"/>
            @if($errors->get('password'))
                <ul class='text-sm text-red-600 space-y-1 mt-2'>
                    @foreach ((array) $errors->get('password') as $message)
                        <li>{{ $message }}</li>
                    @endforeach
                </ul>
            @endif
        </div>

        <!-- Password confirmation -->
        <div>
            <label class='block font-medium text-sm text-gray-700' for="password_confirmation">
                Confirmer le nouveau mot de passe
            </label>
            <input
                wire:model="password_confirmation"
                id="password_confirmation"
                class="mt-1 px-3 py-2 block w-full border border-gray-300 focus:ring-indigo-500"
                type="password"
                name="password_confirmation"
                required
                autofocus
                autocomplete="password_confirmation"/>
            @if($errors->get('password_confirmation'))
                <ul class='text-sm text-red-600 space-y-1 mt-2'>
                    @foreach ((array) $errors->get('password_confirmation') as $message)
                        <li>{{ $message }}</li>
                    @endforeach
                </ul>
            @endif
        </div>

        <div
            class="space-x-4"
            x-data="{
                saved: false,
                timeoutId: undefined
            }"
        >
            <span
                x-on:click.prevent="
                    clearTimeout(timeoutId);
                    saved = await $wire.save();
                    timeoutId = setTimeout(() => saved = false, 1500);
                ">
                <x-button primary>
                    Enregistrer
                </x-button>
            </span>
            <span class="text-sm text-gray-600" x-transition x-cloak x-show.opacity="saved">
                Enregistré.
            </span>
        </div>
    </form>
</div>
