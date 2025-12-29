@php
    use App\Actions\NavigationActions;
    use App\Data\LunarCollectionData;

    $navigation = NavigationActions::makeNavigationData();
@endphp

<div x-data="{rootSelected: '{{ $navigation->first()->slug }}'}" class="h-full">
    <div class="px-4 sm:px-5">
        <div class="flex items-start justify-between pb-1">
            <h2 class="text-base font-semibold leading-6 text-gray-900 space-x-4"  id="slide-over-title">
                @foreach($navigation as $root)
                    <span
                        class="uppercase text-sm cursor-pointer"
                        x-bind:class="{
                            'text-gray-500': rootSelected != '{{ $root->slug }}',
                            'underline': rootSelected == '{{ $root->slug }}'
                        }"
                        x-on:click="rootSelected = '{{ $root->slug }}'"
                        x-text="'{{ str($root->name)->toString() }}'"
                    >
                    </span>
                @endforeach
            </h2>
            <div class="flex items-center h-auto ml-3">
                <button @click="slideOverOpen=false" class="absolute top-0 right-0 z-30 flex items-center justify-center px-3 py-2 mt-4 mr-5 space-x-1 text-xs font-medium uppercase border border-neutral-200 text-neutral-600 hover:bg-neutral-100">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                    <span>Close</span>
                </button>
            </div>
        </div>
    </div>
    <div class="relative flex-1 px-4 mt-5 sm:px-5 h-full">
        <div class="absolute inset-0 px-4 sm:px-5 h-full">
            <div class="relative h-full overflow-hidden">
                @foreach($navigation as $root)
                    <div
                        wire:key="{{ $root->slug }}"
                        x-show="rootSelected == '{{ $root->slug }}'"
                        x-cloak
                        class="uppercase font-semibold text-sm h-full space-y-6 mt-4"
                    >
                        <a href="{{ $root->url }}" class="block hover:underline">Voir tout</a>
                        @foreach($root->children as $category)
                            <div wire:key="{{ $category->slug }}">
                                <a href="{{ $category->url }}" class="block hover:underline">
                                    {{ $category->name }}
                                </a>
                                @foreach($category->children as $subcategory)
                                    <a href="{{ $subcategory->url }}" class="mt-1 capitalize block hover:underline font-normal text-lg">
                                        {{ $subcategory->name }}
                                    </a>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
