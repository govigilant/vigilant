<div class="w-full border border-base-700 rounded-lg px-4 pt-3 pb-4 bg-base-850" x-data="{ deleteHover: false, addGroupHover: false, addConditionHover: false }"
    :class="deleteHover ? 'ring-2 ring-red/50 border-red/50' : ''">
    <div class="flex flex-wrap gap-3 items-center">
        <div class="flex gap-2 items-center">
            <label for="group-operator{{ $path }}" class="text-sm text-base-200 font-medium">
                @lang('Match')
            </label>
            <div>
                <select
                    @if (blank($path)) wire:model.live="parent.operator"
                    @else
                        wire:model.live="conditions.{{ $path }}.operator" @endif
                    id="group-operator{{ $path }}"
                    class="block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-base-100 bg-base-800 ring-1 ring-inset ring-base-700 focus:ring-2 focus:ring-inset focus:ring-red transition-all duration-200">
                    <option value="any">@lang('Any')</option>
                    <option value="all">@lang('All')</option>
                </select>
            </div>
            <span>
                <label for="group-operator{{ $path }}" class="text-sm text-base-200">@lang('conditions in this group')</label>
            </span>
        </div>

        <div class="flex-1 flex flex-wrap gap-2 justify-end">
            <div class="min-w-[200px]">
                <select wire:model.live="selectedCondition.{{ md5($path) }}"
                    class="block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-base-100 bg-base-800 ring-1 ring-inset ring-base-700 focus:ring-2 focus:ring-inset focus:ring-red transition-all duration-200">
                    @foreach ($conditions as $condition)
                        <option value="{{ $condition }}">{{ $condition::$name }}</option>
                    @endforeach
                </select>
            </div>

            <button type="button"
                wire:click="addCondition('{{ $path }}')" 
                x-on:mouseover="addConditionHover = true"
                x-on:mouseleave="addConditionHover = false"
                class="inline-flex items-center px-4 py-2 bg-green hover:bg-green-light text-base-100 font-medium rounded-md transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green focus:ring-offset-base-900">
                @lang('Add Condition')
            </button>

            <button type="button"
                wire:click="addGroup('{{ $path }}')" 
                x-on:mouseover="addGroupHover = true"
                x-on:mouseleave="addGroupHover = false"
                class="inline-flex items-center px-4 py-2 bg-blue hover:bg-blue-light text-base-100 font-medium rounded-md transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue focus:ring-offset-base-900">
                @lang('Add Group')
            </button>

            @if (!blank($path))
                <button type="button"
                    wire:click="deletePath('{{ $path }}')" 
                    x-on:mouseover="deleteHover = true"
                    x-on:mouseleave="deleteHover = false"
                    class="inline-flex items-center px-4 py-2 bg-red hover:bg-red-light text-base-100 font-medium rounded-md transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red focus:ring-offset-base-900">
                    @lang('Delete Group')
                </button>
            @endif
        </div>
    </div>

    <div class="px-1">
        @foreach ($children as $index => $child)
            @if ($child['type'] === 'group')
                <div class="px-4 pt-4">
                    <x-notifications::condition-builder.group :children="$child['children']" :conditions="$conditions" :path="blank($path) ? $index : $path . '.children.' . $index" />
                </div>
            @endif

            @if ($child['type'] === 'condition')
                <div class="px-2">
                    <x-notifications::condition-builder.condition :condition="$child" :path="blank($path) ? $index : $path . '.children.' . $index" />
                </div>
            @endif
        @endforeach

        <div class="w-full bg-blue/20 border border-blue text-blue-light rounded-md py-2 px-3 mt-3 text-sm font-medium" x-cloak x-show="addGroupHover">
            @lang('New Group')
        </div>
        <div class="w-full bg-green/20 border border-green text-green-light rounded-md py-2 px-3 mt-3 text-sm font-medium" x-cloak x-show="addConditionHover">
            @lang('New Condition')
        </div>

    </div>
</div>
