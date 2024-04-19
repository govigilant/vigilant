<div class="w-full">
    <div class="flex space-x-4 items-center">
        <div>
            <select
                @if(blank($path))
                    wire:model.live="parent.operator"
                @else
                    wire:model.live="conditions.{{ $path }}.operator"
                @endif
                class="mt-2 block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-white bg-white/5 ring-1 ring-inset ring-white/10 focus-within:ring-2 focus-within:ring-inset focus-within:ring-red">
                <option value="any">@lang('Any')</option>
                <option value="all">@lang('All')</option>
            </select>
        </div>

        <div class="text-white">
            {{ $path }}
        </div>

        <div class="flex-1 flex gap-4 justify-end">
            <div>
                <select
                    wire:model.live="selectedCondition.{{ md5($path) }}"
                    class="mt-2 block w-full rounded-md border-0 py-1 pl-3 pr-10 text-white bg-white/5 ring-1 ring-inset ring-white/10 focus-within:ring-2 focus-within:ring-inset focus-within:ring-red">
                    @foreach($conditions as $condition)
                        <option value="{{ $condition }}">{{ $condition::$name }}</option>
                    @endforeach
                </select>
            </div>

            <x-form.button class="bg-red" type="button" wire:click="addCondition('{{ $path }}')">
                @lang('Add Condition')
            </x-form.button>

            <x-form.button class="bg-blue" type="button" wire:click="addGroup('{{ $path }}')">
                @lang('Add Group')
            </x-form.button>
        </div>
    </div>

    <div>
        @foreach($children as $index => $child)
            @if ($child['type'] === 'group')
                <div class="px-4 pt-4">
                    <x-notifications::condition-builder.group :children="$child['children']"
                                                              :conditions="$conditions"
                                                              :path="blank($path) ? '0' : $path . '.children.' . $index"/>
                </div>
            @endif

            @if ($child['type'] === 'condition')
                <x-notifications::condition-builder.condition :condition="$child"
                                                              :path="blank($path) ? '0' : $path . '.children.' . $index"/>
            @endif
        @endforeach
    </div>
</div>
