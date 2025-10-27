<div>
    @if (!$inline)
        <x-slot name="header">
            <x-page-header :title="$updating ? 'Edit Certificate Monitor - ' . $certificateMonitor->url : 'Add Certificate Monitor'" :back="$updating
                ? route('certificates.index', ['monitor' => $certificateMonitor])
                : route('certificates')">
            </x-page-header>
        </x-slot>
    @endif

    <form wire:submit="save">
        <div class="max-w-7xl mx-auto">
            <x-card>
                <div class="flex flex-col gap-4">
                    @if (!$inline)
                        <x-form.checkbox field="form.enabled" name="Enabled"
                            description="Enable or disable this certificate monitor" />
                    @endif
                    <x-form.text field="form.domain" name="Domain" description="Domain" />

                    <x-form.number field="form.port" name="Port" description="Port" />

                    @if (!$inline)
                        <x-form.submit-button dusk="submit-button" :submitText="$updating ? 'Save' : 'Create'" />
                    @endif
                </div>
            </x-card>
        </div>
    </form>
</div>
