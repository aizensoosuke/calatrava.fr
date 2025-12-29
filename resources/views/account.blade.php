<x-layouts.app>
    <div class="py-12">
        <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
            <div class="bg-white p-4 shadow sm:p-8">
                <h2 class="text-lg font-medium text-gray-900 ">
                    Bienvenue, {{ auth()->user()?->name }}
                </h2>
                <p class="mt-1 text-sm text-gray-600">
                    Consultez et modifiez vos informations personnelles.
                </p>

                <div class="mt-6 flex justify-start">
                    <x-button :url="route('logout')">
                        Se déconnecter
                    </x-button>
                    <x-button class="ms-3" :url="route('account.edit')">
                        Éditer le profil
                    </x-button>
                </div>
            </div>

            <div class="bg-white p-4 shadow sm:p-8">
                <h2 class="text-lg font-medium text-gray-900">
                    Commandes passées
                </h2>
                <p class="mt-1 text-sm text-gray-600">
                    Vos commandes passées sur notre sites.
                </p>
            </div>
        </div>
    </div>
</x-layouts.app>
