<button {{ $attributes->merge(['type' => 'submit', 'class' => 'group inline-flex items-center justify-center gap-2 px-6 py-3 border-2 border-base-700 hover:border-indigo rounded-xl font-semibold text-sm text-base-100 shadow-lg hover:shadow-xl hover:bg-base-800/50 backdrop-blur-sm focus:outline-hidden focus:ring-2 focus:ring-indigo focus:ring-offset-2 focus:ring-offset-base-black disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:border-base-700 disabled:hover:bg-transparent transition-all duration-300']) }}>
    {{ $slot }}
</button>
