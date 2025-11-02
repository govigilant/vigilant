<div>
    <x-slot name="header">
        <x-page-header title="Sites">
            <x-frontend::page-header.actions>
                <x-create-button dusk="site-import-button" :href="route('site.import')" model="Vigilant\Sites\Models\Site">
                    @lang('Add Multiple Sites')
                </x-create-button>
                <x-create-button dusk="site-add-button" :href="route('site.create')" model="Vigilant\Sites\Models\Site">
                    @lang('Add Site')
                </x-create-button>

            </x-frontend::page-header.actions>
            <x-frontend::page-header.mobile-actions>
                <x-create-button-dropdown :href="route('site.create')" model="Vigilant\Sites\Models\Site">
                    @lang('Add Site')
                </x-create-button-dropdown>
                <x-create-button-dropdown :href="route('site.import')" model="Vigilant\Sites\Models\Site">
                    @lang('Add Multiple Sites')
                </x-create-button-dropdown>
            </x-frontend::page-header.mobile-actions>
        </x-page-header>
    </x-slot>

    <div class="mx-auto max-w-7xl px-6 lg:px-8 py-8">
        @if ($sites->count() > 0)
            <div class="space-y-4">
                @foreach ($sites as $site)
                    <x-sites::site-card :site="$site" />
                @endforeach
            </div>

            @if ($sites->hasPages())
                <div class="mt-8">
                    {{ $sites->links() }}
                </div>
            @endif
        @else
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-base-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path>
                </svg>
                <h3 class="mt-4 text-lg font-semibold text-base-100">{{ __('No sites yet') }}</h3>
                <p class="mt-2 text-sm text-base-300">{{ __('Get started by adding your first site.') }}</p>
                <div class="mt-6">
                    <x-create-button :href="route('site.create')" model="Vigilant\Sites\Models\Site">
                        @lang('Add Site')
                    </x-create-button>
                </div>
            </div>
        @endif
    </div>

</div>
