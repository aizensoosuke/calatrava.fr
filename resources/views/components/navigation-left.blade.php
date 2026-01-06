@props([
    'class' => ''
])

@php
    use App\Actions\NavigationActions;
    use App\Data\LunarCollectionData;

    $navigation = NavigationActions::makeNavigationData();
@endphp

<div class="{{ $class }} h-full group relative" x-data="{openDropdown: undefined}">
    @foreach($navigation as $root)
        @php /** @var LunarCollectionData $root */ @endphp
        <a
            class="uppercase text-sm font-semibold hover:underline"
            x-on:mouseenter="openDropdown = '{{ $root->slug }}'"
            href="{{ $root->url }}"
        >
            {{ $root->name }}
        </a>
    @endforeach

    <template x-teleport="#navigation-dropdown">
        <div
            class="bg-white w-full absolute px-8 lg:px-16 pb-8"
            x-show="openDropdown !== undefined"
            x-on:mouseleave="openDropdown = undefined"
        >
            @foreach($navigation as $root)
                @php /** @var LunarCollectionData $root */ @endphp
                <div
                    class="grid grid-cols-3"
                    x-show="openDropdown == '{{ $root->slug }}'"
                    x-cloak
                >
                    @foreach($root->children->take(3) as $category)
                        <div wire:key="{{ $category->slug }}">
                            <a class="hover:underline text-sm font-semibold capitalize" href="{{ $category->url }}">{{ $category->name }}</a>

                            <div class="flex flex-col mt-2 gap-1">
                                @foreach($category->children as $subcategory)
                                    <a class="hover:underline text-sm capitalize" wire:key="{{ $subcategory->slug }}" href="{{ $category->url }}">{{ $subcategory->name }}</a>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
    </template>
</div>
