@props(['field', 'items' => [], 'name' => '', 'placeholder', 'description' => '', 'live' => true])
<div class="grid grid-cols-1 md:grid-cols-2 gap-4" 
     x-data="{ 
         items: @js($items),
         addItem() {
             this.items.push('');
         },
         removeItem(index) {
             this.items.splice(index, 1);
         }
     }"
     x-init="
         // Listen for Livewire updates
         Livewire.hook('commit', ({ component, commit, respond }) => {
             respond(() => {
                 // After Livewire updates, restore Alpine state from backend
                 let newItems = @this.get('{{ $field }}') || [];
                 if (JSON.stringify(this.items) !== JSON.stringify(newItems)) {
                     this.items = newItems;
                 }
             });
         });
     ">
    @if(!blank($name))
        <div class="flex flex-col justify-center">
            <label class="block text-base font-semibold leading-6 text-base-50">@lang($name)</label>
            @if($description)
                <span class="text-base-400 text-sm mt-1">{{ $description }}</span>
            @endif
        </div>
    @endif
    <div class="flex flex-col justify-center">
        <div class="space-y-3 mb-4">
            <template x-for="(item, index) in items" :key="index">
                <div class="flex gap-2 items-start">
                    <div class="flex-1 rounded-lg bg-base-900 ring-1 ring-inset ring-base-700 focus-within:ring-2 focus-within:ring-inset focus-within:ring-red transition-all duration-200">
                        <input type="text"
                               :name="'{{ $field }}[' + index + ']'"
                               x-model="items[index]"
                               {{ $attributes->merge(['class' => 'w-full border-0 bg-transparent py-2.5 px-3 text-base-100 focus:ring-0 sm:text-sm sm:leading-6 placeholder:text-base-500']) }}
                               placeholder="{{ $placeholder ?? '' }}">
                    </div>
                    <button 
                        type="button"
                        @click="removeItem(index)"
                        x-show="items.length > 1"
                        class="flex items-center justify-center w-10 h-10 rounded-lg bg-red/10 border border-red/30 text-red-light hover:bg-red/20 hover:border-red transition-all duration-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </template>
        </div>

        <button 
            @click="addItem()" 
            type="button"
            class="bg-gradient-to-r from-blue to-blue-light text-white px-4 py-2.5 rounded-lg font-medium hover:shadow-lg hover:shadow-blue/30 transition-all duration-200 inline-flex items-center gap-2 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            @lang('Add')
        </button>

        <div>
            @error($field) <span class="text-red text-sm mt-1">@lang($message)</span> @enderror
        </div>
    </div>
</div>
