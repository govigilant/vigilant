<div>
    <x-slot name="header">
        <x-page-header title="Add Site" :back="route('sites')"></x-page-header>
    </x-slot>

    <form wire:submit="save">
        <div class="flex flex-col gap-4 max-w-7xl mx-auto">

            <x-form.text class="sm:col-span-2"
                         field="url"
                         name="URL"
                         description="The URL of the site that you want to add."
                         placeholder="{{ config('app.url') }}"/>


            <x-form.submit-button/>

        </div>
    </form>
</div>
