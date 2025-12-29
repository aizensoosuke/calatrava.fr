<div id="header" class="relative z-50">
    <div class="flex items-end sm:grid sm:grid-cols-3 sm:items-center w-full px-8 lg:px-16 py-4 z-1 relative">
        <x-navigation-left class="hidden lg:flex gap-4 items-center justify-start" />
        <x-navigation-left-mobile class="lg:hidden h-full flex items-center" />
        <a class="flex justify-center items-center" href="{{ route('home') }}">
            <div class="hidden sm:block">
                <x-logo/>
            </div>

            <div class="ml-6 sm:hidden">
                <x-logo-small />
            </div>
        </a>
        <div class="flex flex-1 justify-end">
            <x-navigation-right />
        </div>
    </div>
</div>
