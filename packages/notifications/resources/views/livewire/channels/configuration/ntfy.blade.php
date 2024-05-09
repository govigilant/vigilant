<div wire:init="init">
    <x-form.text
        field="settings.server"
        name="NTFY Server"
        description="URL of the NTFY server"
    />

    <x-form.text
        field="settings.topic"
        name="NTFY Topic"
        description="Notification Topic"
    />

    <x-form.select
        field="settings.auth_method"
        name="Authentication Method"
        description="Choose the authentication method"
    >
        <option value="" selected>None</option>
        <option value="username">Username and Password</option>
        <option value="token">Token</option>
    </x-form.select>

    @if (($settings['auth_method'] ?? '') === 'username')
        <x-form.text
                field="settings.username"
                name="Username"
        />

        <x-form.password
                field="settings.password"
                name="Password"
        />
    @endif

    @if (($settings['auth_method'] ?? '') === 'token')
        <x-form.text
                field="settings.token"
                name="Access Token"
        />
    @endif

</div>
