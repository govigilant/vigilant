<div>
    <x-slot name="header">
        <x-page-header
            :title="$updating ? __('Edit Crawler :url', ['url' => $crawler->start_url])  : __('Add Crawler')"
            :back="route('crawler.index')">
        </x-page-header>
    </x-slot>

    <form wire:submit="save">
        <div class="flex flex-col gap-4 max-w-7xl mx-auto">

            <x-form.text
                field="form.start_url"
                name="Start URL"
                description="Enter the URL to start crawling"
            />

            <x-form.text-list
                field="form.sitemaps"
                name="Sitemaps"
                description="Sitemaps to retrieve URLs from"
                placeholder="Sitemap URL"
                :items="$form->sitemaps"
            />

            @if(!$inline)
                <div class="flex justify-end gap-4">
                    <x-form.submit-button dusk="submit-button" :submitText="$updating ? 'Save' : 'Create'"/>
                </div>
            @endif
        </div>
    </form>
</div>
