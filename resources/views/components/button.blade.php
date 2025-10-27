<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center gap-2 px-6 py-2.5 bg-base-800 dark:bg-base-800 border border-base-700 rounded-lg font-semibold text-sm text-base-100 shadow-md hover:bg-base-700 hover:shadow-lg hover:-translate-y-0.5 focus:bg-base-700 active:bg-base-900 focus:outline-hidden focus:ring-2 focus:ring-red focus:ring-offset-2 dark:focus:ring-offset-base-black disabled:opacity-50 disabled:hover:transform-none disabled:hover:shadow-md transition-all duration-200']) }}>
    {{ $slot }}
</button>
