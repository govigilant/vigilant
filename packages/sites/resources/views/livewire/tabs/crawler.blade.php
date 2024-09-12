<div>
    <div class="mb-4">
        <x-form.checkbox
            name="Enable Link Crawling"
            description="Crawl your website to find broken links"
            field="enabled"
            dusk="crawler-tab-enabled"
        ></x-form.checkbox>
    </div>

    @if($enabled)
        <livewire:crawler-form
            :crawler="$this->crawler"
            :inline="true"
            :siteId="$siteId"
        />
    @endif
</div>
