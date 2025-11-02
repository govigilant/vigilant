<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-gradient-to-r from-red to-red-light border border-transparent rounded-lg font-semibold text-sm text-base-100 shadow-lg shadow-red/20 hover:from-red-light hover:to-red hover:shadow-xl hover:shadow-red/30 active:from-red-dark active:to-red focus:outline-hidden focus:ring-2 focus:ring-red-light focus:ring-offset-2 dark:focus:ring-offset-base-black transition-all duration-200']) }}>
    {{ $slot }}
</button>
