<div x-data="{currentAlert: {}}" @alert.window="currentAlert = $event.detail[0] ?? {}" x-cloak>

{{--    Same alerts as the components just rendered via a JS event --}}
    <div class="rounded-md bg-green-light p-4" x-show="currentAlert.type == 'success'">
        <div class="flex">
            <div class="flex-shrink-0">
                <x-heroicon-o-check-circle class="h-5 w-5 text-white"/>
            </div>
            <div class="ml-3 flex-1">
                <div class="flex justify-between">
                    <h3 class="text-sm font-medium text-white flex-1" x-text="currentAlert.title"></h3>
                    <span x-on:click="currentAlert = {}">
                    <x-tni-x-circle-o class="w-5 h-5 text-white cursor-pointer"/>
                </span>
                </div>
                <div class="mt-2 text-sm text-base-100">
                    <p x-text="currentAlert.message"></p>
                </div>
            </div>
        </div>
    </div>

    <div class="rounded-md bg-red-light p-4" x-show="currentAlert.type == 'danger'">
        <div class="flex">
            <div class="flex-shrink-0">
                <x-heroicon-o-exclamation-circle class="h5 w-5 text-white"/>
            </div>
            <div class="ml-3 flex-1">
                <div class="flex justify-between">
                    <h3 class="text-sm font-medium text-white flex-1" x-text="currentAlert.title"></h3>
                    <span x-on:click="currentAlert = {}">
                    <x-tni-x-circle-o class="w-5 h-5 text-white cursor-pointer"/>
                </span>
                </div>
                <div class="mt-2 text-sm text-base-100">
                    <p x-text="currentAlert.message"></p>
                </div>
            </div>
        </div>
    </div>

    <div class="rounded-md bg-blue-light p-4" x-show="currentAlert.type == 'info'">
        <div class="flex">
            <div class="flex-shrink-0">
                <x-tni-info-circle-o class="h5 w-5 text-white"/>
            </div>
            <div class="ml-3 flex-1">
                <div class="flex justify-between">
                    <h3 class="text-sm font-medium text-white flex-1" x-text="currentAlert.title"></h3>
                    <span x-on:click="currentAlert = {}">
                    <x-tni-x-circle-o class="w-5 h-5 text-white cursor-pointer"/>
                </span>
                </div>
                <div class="mt-2 text-sm text-base-100">
                    <p x-text="currentAlert.message"></p>
                </div>
            </div>
        </div>
    </div>

    <div class="rounded-md bg-yellow-light p-4" x-show="currentAlert.type == 'warning'">
        <div class="flex">
            <div class="flex-shrink-0">
                <x-heroicon-o-exclamation-triangle class="h5 w-5 text-white"/>
            </div>
            <div class="ml-3 flex-1">
                <div class="flex justify-between">
                    <h3 class="text-sm font-medium text-white flex-1" x-text="currentAlert.title"></h3>
                    <span x-on:click="currentAlert = {}">
                    <x-tni-x-circle-o class="w-5 h-5 text-white cursor-pointer"/>
                </span>
                </div>
                <div class="mt-2 text-sm text-base-100">
                    <p x-text="currentAlert.message"></p>
                </div>
            </div>
        </div>
    </div>


    @if(session('alert'))
        @php($type = session('alert-type'))

        <x-dynamic-component :component="$type->component()"
                             :title="session('alert-title')"
                             :message="session('alert-message')"
        />
    @endif

</div>

