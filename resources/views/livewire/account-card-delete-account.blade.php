<?php
use App\Models\User;
use Illuminate\Validation\Rule;

use function Livewire\Volt\state;
use function Livewire\Volt\action;

state([
    'current_password' => '',
])->modelable();

$delete = action(function(): void {
    $this->validate([
        'current_password' => ['required', 'string', 'current_password'],
    ]);

    $user = auth()->user();

    Auth::guard('web')->logout();

    Session::invalidate();
    Session::regenerateToken();

    $user?->delete();

    $this->redirect('/', navigate: true);
});
?>

<div
    class="bg-white p-4 shadow sm:p-8"
    x-data="{modalOpen: false}"
    @keydown.escape.window="modalOpen = false"
>
    <h2 class="text-lg font-medium text-gray-900">
        Supprimer le compte
    </h2>
    <p class="mt-1 text-sm text-gray-600">
        Une fois votre compte supprimé, toutes ses ressources et données seront définitivement supprimées. Avant de supprimer votre compte, veuillez sauvegarder les données ou informations que vous souhaitez conserver.
    </p>


    <div class="mt-4 relative z-50 w-auto h-auto">
        <div x-on:click.prevent="modalOpen = true">
            <x-button danger>
                Supprimer le compte
            </x-button>
        </div>

        <template x-teleport="body">
            <div x-show="modalOpen" class="fixed top-0 left-0 z-[99] flex items-center justify-center w-screen h-screen" x-cloak>
                <div x-show="modalOpen"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-300"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     @click="modalOpen=false" class="absolute inset-0 w-full h-full bg-gray-500/75"></div>
                <div x-show="modalOpen"
                     x-trap.inert.noscroll="modalOpen"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="relative w-full p-6 bg-white sm:max-w-2xl sm:rounded">
                    <div class="flex items-center justify-between pb-2">
                        <h2 class="text-lg font-medium text-gray-900">
                            Êtes-vous sûr.e de vouloir supprimer votre compte?
                        </h2>
                        <button x-on:click="modalOpen=false" class="absolute top-0 right-0 flex items-center justify-center w-8 h-8 mt-5 mr-5 text-gray-600 rounded hover:text-gray-800 hover:bg-gray-50">
                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>
                    <div class="relative w-auto">
                        <p class="mt-1 text-sm text-gray-600">
                            Une fois votre compte supprimé, toutes ses ressources et données seront définitivement supprimées. Avant de supprimer votre compte, veuillez sauvegarder les données ou informations que vous souhaitez conserver.
                        </p>
                    </div>
                    <form class="mt-6 space-y-6">
                        <!-- Current password -->
                        <div>
                            <input
                                wire:model="current_password"
                                id="current_password"
                                class="mt-1 px-3 py-2 block w-full border border-gray-300 focus:ring-indigo-500"
                                type="password"
                                name="current_password"
                                required
                                autofocus
                                placeholder="Mot de passe"
                                autocomplete="current_password"/>
                            @if($errors->get('current_password'))
                                <ul class='text-sm text-red-600 space-y-1 mt-2'>
                                    @foreach ((array) $errors->get('current_password') as $message)
                                        <li>{{ $message }}</li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                        <div
                            class="space-x-4"
                            x-data="{
                                saved: false,
                                timeoutId: undefined,
                            }"
                        >
                            <span x-on:click.prevent="$wire.delete()">
                                <x-button danger>
                                    Supprimer le compte
                                </x-button>
                            </span>
                            <span x-on:click.prevent="modalOpen = false">
                                <x-button>
                                    Annuler
                                </x-button>
                            </span>
                        </div>
                    </form>
                </div>
            </div>
        </template>
    </div>
</div>
