<?php
use App\Models\User;
use Illuminate\Validation\Rule;

use function Livewire\Volt\state;
use function Livewire\Volt\action;

state([
    'verificationLinkSent' => false,
])->locked();

state([
    'name' => auth()->user()?->name,
    'email' => auth()->user()?->email
])->modelable();

$save = action(function(): bool {
    $user = auth()->user();

    if ($user === null) {
        return false;
    }

    $validated = $this->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'lowercase', 'email', 'max:255',  Rule::unique('users')->ignore($user->id)],
    ]);

    $user->update([
        'name' => $validated['name'],
        'email' => $validated['email']
    ]);

    return true;
});

$sendEmailVerificationLink = action(function(): bool {
    $user = auth()->user();

    if ($user === null || $user->email_verified_at !== null) {
        return false;
    }

    $user->sendEmailVerificationNotification();

    return true;
})
?>

<div class="bg-white p-4 shadow sm:p-8">
    <h2 class="text-lg font-medium text-gray-900">
        Votre profil
    </h2>
    <p class="mt-1 text-sm text-gray-600">
        Mettez à jour les informations de votre profil et votre addresse email
    </p>

    <form class="mt-6 space-y-6">
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

        @if(auth()->user()?->email_verified_at === null)
        <div x-data="{sent: false}">
            <p class="mt-2 text-sm text-gray-800" x-show="! sent">
                Votre address email n'est pas vérifiée.
                <x-link x-click="sent = await $wire.sendEmailVerificationLink()">
                    Cliquer ici pour renvoyer le mail de vérification.
                </x-link>
            </p>

            <div class="mt-2 text-sm text-green-600 dark:text-green-400" x-show="sent" x-cloak>
                Un email de vérification a été envoyé à votre adresse email.
            </div>
        </div>
        @endif

        <div
            class="space-x-4"
            x-data="{
                saved: false,
                timeoutId: undefined,
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
