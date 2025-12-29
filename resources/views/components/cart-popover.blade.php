<div class="relative" x-data="{cartOpen: false}">
    <span x-on:click="cartOpen = !cartOpen">
        <x-navigation-link-icon
            label="Panier"
            icon="shopping-bag"
            class="hidden sm:block" />
        <x-navigation-link-icon
            icon="shopping-bag"
            class="sm:hidden" />
    </span>
    <div
        class="absolute top-10 sm:top-14 right-0"
        x-show="cartOpen"
        x-cloak
        x-on:click.outside="cartOpen = false"
    >
        <livewire:cart />
    </div>
</div>

