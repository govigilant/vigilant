<div>
    <x-slot name="header">
        <x-page-header title="Add Site" :back="route('sites')"></x-page-header>
    </x-slot>

    <form wire:submit="save">
        <div class="flex flex-col gap-4">

            <x-form.text class="sm:col-span-2"
                         field="url"
                         name="URL"
                         placeholder="https://vigilant-monitoring.io"/>


            <div>
                <x-form.button type="submit">@lang('Create')</x-form.button>
            </div>

        </div>
    </form>
</div>
