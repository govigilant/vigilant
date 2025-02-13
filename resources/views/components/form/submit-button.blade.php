<div class="flex justify-end gap-2">
    {{ $slot }}
    <button type="submit"
        {{ $attributes->merge(['class' => 'rounded-full bg-red hover:bg-red-light px-2.5 py-1.5 text-sm font-semibold text-white shadow-sm focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-light disabled:opacity-50']) }}>
        {{ __($submitText ?? 'Create') }}
    </button>
</div>
