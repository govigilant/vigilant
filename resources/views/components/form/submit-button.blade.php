<div class="flex justify-end gap-3 pt-6 border-t border-base-700">
    {{ $slot }}
    <button type="submit"
        {{ $attributes->merge(['class' => 'inline-flex items-center gap-2 rounded-lg bg-gradient-to-r from-red to-red-light hover:from-red-light hover:to-red px-6 py-2.5 text-sm font-semibold text-base-100 shadow-lg shadow-red/20 hover:shadow-xl hover:shadow-red/30 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-light disabled:opacity-50 transition-all duration-200']) }}>
        {{ __($submitText ?? 'Create') }}
    </button>
</div>
