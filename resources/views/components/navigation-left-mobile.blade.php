@props([
    'class' => ''
])

@php
    use App\Actions\NavigationActions;
    use App\Data\LunarCollectionData;

    $navigation = NavigationActions::makeNavigationData();
@endphp

<div class="{{ $class }} h-full group relative" x-data="{slideOverOpen: false}">
    <span x-on:click="slideOverOpen = true" class="cursor-pointer">
        <x-icon name="bars-3" />
    </span>

    <template x-teleport="body">
        <div
            x-show="slideOverOpen"
            @keydown.window.escape="slideOverOpen=false"
            class="relative z-[99]">
            <div x-show="slideOverOpen" x-transition.opacity.duration.600ms @click="slideOverOpen = false" class="fixed inset-0 bg-black bg-opacity-10"></div>
            <div class="fixed inset-0 overflow-hidden">
                <div class="absolute inset-0 overflow-hidden">
                    <div class="fixed inset-y-0 right-0 flex max-w-full">
                        <div
                            x-show="slideOverOpen"
                            @click.away="slideOverOpen = false"
                            x-transition:enter="transform transition ease-in-out duration-300 sm:duration-400"
                            x-transition:enter-start="-translate-x-full"
                            x-transition:enter-end="translate-x-0"
                            x-transition:leave="transform transition ease-in-out duration-300 sm:duration-400"
                            x-transition:leave-start="translate-x-0"
                            x-transition:leave-end="-translate-x-full"
                            class="w-screen max-w-full">
                            <div class="flex flex-col h-full py-5 overflow-y-scroll bg-white border-l shadow-lg border-neutral-100/70">
                                <x-navigation-slideover-content />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>
