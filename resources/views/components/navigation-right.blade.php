<div class="flex gap-4 lg:items-center text-xs">
    <!--x-navigation-link-icon
        label="Rechercher"
        class="hidden lg:block"
        icon="magnifying-glass" /-->
    @if(auth()->guest())
        <!-- x-navigation-link-icon
            :url="route('login')"
            label="Se connecter"
            class="hidden sm:block"
            icon="user" />
        <x-navigation-link-icon
            :url="route('login')"
            class="sm:hidden"
            icon="user" /-->
    @else
        <!--x-navigation-link-icon
            :url="route('account')"
            label="Mon compte"
            class="hidden sm:block"
            icon="user" />
        <x-navigation-link-icon
            :url="route('account')"
            class="sm:hidden"
            icon="user" /-->
    @endif
    <!--x-navigation-link-icon
        label="Favoris"
        icon="heart"
        class="hidden lg:block" /-->
    <x-cart-popover />
</div>
