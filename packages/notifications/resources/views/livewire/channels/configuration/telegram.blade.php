<div>
    <x-form.text
        field="settings.bot_token"
        name="Bot token"
        description="Telegram Bot token from @BotFather"
    />

    <x-form.text
        field="settings.chat_id"
        name="Chat ID"
        description="Your Telegram chat ID"
    />

    <div class="mt-4 text-sm text-gray-600 dark:text-gray-400">
        <p class="font-medium mb-2">How to get your chat ID:</p>
        <ol class="list-decimal list-inside space-y-1">
            <li>Send a message to your bot on Telegram</li>
            <li>Visit <code class="px-1 py-0.5 bg-gray-100 dark:bg-gray-800 rounded">https://api.telegram.org/bot{your_bot_token}/getUpdates</code></li>
            <li>Look for the <code class="px-1 py-0.5 bg-gray-100 dark:bg-gray-800 rounded">chat</code> → <code class="px-1 py-0.5 bg-gray-100 dark:bg-gray-800 rounded">id</code> field in the response</li>
        </ol>
    </div>
</div>
