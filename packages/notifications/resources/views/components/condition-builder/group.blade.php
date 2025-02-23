<div class="w-full border border-base-200 rounded px-2 pt-2 pb-4" x-data="{ deleteHover: false, addGroupHover: false, addConditionHover: false }"
    :class="deleteHover ? 'bg-red-light/20' : ''">
    <div class="flex space-x-4 items-center">
        <div class="mt-1 flex gap-2 items-center">
            <label for="group-operator{{ $path }}" class="text-sm text-white">
                @lang('Match')
            </label>
            <div>
                <select
                    @if (blank($path)) wire:model.live="parent.operator"
                    @else
                        wire:model.live="conditions.{{ $path }}.operator" @endif
                    id="group-operator{{ $path }}"
                    class="block w-full rounded-md border-0 py-0 pl-3 pr-10 text-white bg-white/5 ring-1 ring-inset ring-white/10 focus-within:ring-2 focus-within:ring-inset focus-within:ring-red">
                    <option value="any">@lang('Any')</option>
                    <option value="all">@lang('All')</option>
                </select>
            </div>
            <span>
                <label for="group-operator{{ $path }}" class="text-sm text-white">@lang('conditions in this group')</label>
            </span>
        </div>

        <div class="flex-1 flex gap-2 justify-end">
            <div>
                <select wire:model.live="selectedCondition.{{ md5($path) }}"
                    class="block w-full rounded-md border-0 py-1 pl-3 pr-10 text-white bg-white/5 ring-1 ring-inset ring-white/10 focus-within:ring-2 focus-within:ring-inset focus-within:ring-red">
                    @foreach ($conditions as $condition)
                        <option value="{{ $condition }}">{{ $condition::$name }}</option>
                    @endforeach
                </select>
            </div>

            <x-form.button class="bg-green hover:bg-green-light" type="button"
                wire:click="addCondition('{{ $path }}')" x-on:mouseover="addConditionHover = true"
                x-on:mouseleave="addConditionHover = false">
                @lang('Add Condition')
            </x-form.button>

            <x-form.button class="bg-blue hover:bg-blue-light" type="button"
                wire:click="addGroup('{{ $path }}')" x-on:mouseover="addGroupHover = true"
                x-on:mouseleave="addGroupHover = false">
                @lang('Add Group')
            </x-form.button>

            @if (!blank($path))
                <x-form.button class="bg-red hover:bg-red-light" type="button"
                    wire:click="deletePath('{{ $path }}')" x-on:mouseover="deleteHover = true"
                    x-on:mouseleave="deleteHover = false">
                    @lang('Delete Group')
                </x-form.button>
            @endif
        </div>
    </div>

    <div class="px-1">
        @foreach ($children as $index => $child)
            @if ($child['type'] === 'group')
                <div class="px-4 pt-4">
                    <x-notifications::condition-builder.group :children="$child['children']" :conditions="$conditions" :path="blank($path) ? '0' : $path . '.children.' . $index" />
                </div>
            @endif

            @if ($child['type'] === 'condition')
                <div class="px-2">
                    <x-notifications::condition-builder.condition :condition="$child" :path="blank($path) ? '0' : $path . '.children.' . $index" />
                </div>
            @endif
        @endforeach

        <div class="w-full bg-blue text-white rounded-md py-1 px-2 mt-2" x-cloak x-show="addGroupHover">
            @lang('New Group')
        </div>
        <div class="w-full bg-green text-white rounded-md py-1 px-2 mt-2" x-cloak x-show="addConditionHover">
            @lang('New Condition')
        </div>

    </div>
</div>
