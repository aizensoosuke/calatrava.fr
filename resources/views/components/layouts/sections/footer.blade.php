<div id="footer" class="mt-auto">
    <div class="px-8 lg:px-16 py-8 bg-gray-100">
        <div class="flex flex-col sm:flex-row justify-between items-center gap-6 sm:gap-24">
            <div class="hidden sm:block">
                <x-logo />
            </div>
            <div class="sm:hidden">
                <x-logo-small />
            </div>
            <div class="flex flex-row justify-center sm:justify-end gap-x-8 gap-y-2 flex-wrap">
                <x-link>
                    <a href="mailto:contact@calatrava.fr" class="whitespace-nowrap">
                        contact@calatrava.fr
                    </a>
                </x-link>
                <x-link>
                    <a href="{{ route('legal') }}" class="whitespace-nowrap">
                        Mentions légales
                    </a>
                </x-link>
                <x-link>
                    <a href="{{ route('terms') }}" class="whitespace-nowrap">
                        Conditions générales de vente
                    </a>
                </x-link>
            </div>
        </div>
    </div>
</div>
