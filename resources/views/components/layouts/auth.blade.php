<x-layouts.app :with-footer="false" :with-header="false">
    <div class="min-h-screen flex flex-col sm:justify-center items-center bg-gray-100">
        <a href="/" class="flex items-center gap-2 mb-4 mt-8 sm:mt-0 scale-125">
            <x-logo />
        </a>
        <div class="mt-8 w-full sm:max-w-md px-6 py-8 bg-white shadow-md">
            {{ $slot }}
        </div>
    </div>
</x-layouts.app>
