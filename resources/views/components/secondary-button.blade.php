<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center gap-2 px-6 py-2.5 bg-base-850 dark:bg-base-850 border border-base-700 dark:border-base-700 rounded-lg font-semibold text-sm text-base-200 dark:text-base-200 shadow-md hover:bg-base-800 dark:hover:bg-base-800 hover:shadow-lg hover:-translate-y-0.5 focus:outline-hidden focus:ring-2 focus:ring-red focus:ring-offset-2 dark:focus:ring-offset-base-black disabled:opacity-25 transition-all duration-200']) }}>
    {{ $slot }}
</button>
