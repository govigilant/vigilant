<label class="flex items-center relative">
    <input
        {{
            $attributes
                ->merge(['type' => 'checkbox'])
                ->class([
                    'rounded-md size-5 cursor-pointer shadow-sm appearance-none border peer transition-all duration-200',
                    'focus:outline-none focus:ring-2 focus:ring-red/50',
                    'bg-base-850 checked:bg-red',
                    'border-base-700 focus:border-red checked:border-red',
                ])
        }}
    />
    <span class="absolute text-base-50 opacity-0 peer-checked:opacity-100 top-0.5 left-0.5 transform pointer-events-none transition-opacity duration-200">
        <x-livewire-table::icon icon="check" class="size-4" />
    </span>
</label>
