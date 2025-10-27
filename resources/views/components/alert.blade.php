<div x-data="{ currentAlert: {} }"
    @alert.window="currentAlert = $event.detail[0] ?? {}" x-cloak class="mb-4">

{{--    Same alerts as the components just rendered via a JS event --}}
    <div class="rounded-xl bg-gradient-to-r from-green to-green-light border border-green-light/30 p-5 shadow-lg shadow-green/20" x-show="currentAlert.type == 'success'">
        <div class="flex">
            <div class="shrink-0">
                <x-heroicon-o-check-circle class="h-6 w-6 text-white"/>
            </div>
            <div class="ml-3 flex-1">
                <div class="flex justify-between">
                    <h3 class="text-sm font-semibold text-white flex-1" x-text="currentAlert.title"></h3>
                    <span x-on:click="currentAlert = {}" class="hover:bg-white/20 rounded-lg p-1 transition-colors duration-200">
                        <x-tni-x-circle-o class="w-5 h-5 text-white cursor-pointer"/>
                    </span>
                </div>
                <div class="mt-2 text-sm text-base-50">
                    <p x-text="currentAlert.message"></p>
                </div>
            </div>
        </div>
    </div>

    <div class="rounded-xl bg-gradient-to-r from-red to-red-light border border-red-light/30 p-5 shadow-lg shadow-red/20" x-show="currentAlert.type == 'danger'">
        <div class="flex">
            <div class="shrink-0">
                <x-heroicon-o-exclamation-circle class="h-6 w-6 text-white"/>
            </div>
            <div class="ml-3 flex-1">
                <div class="flex justify-between">
                    <h3 class="text-sm font-semibold text-white flex-1" x-text="currentAlert.title"></h3>
                    <span x-on:click="currentAlert = {}" class="hover:bg-white/20 rounded-lg p-1 transition-colors duration-200">
                        <x-tni-x-circle-o class="w-5 h-5 text-white cursor-pointer"/>
                    </span>
                </div>
                <div class="mt-2 text-sm text-base-50">
                    <p x-text="currentAlert.message"></p>
                </div>
            </div>
        </div>
    </div>

    <div class="rounded-xl bg-gradient-to-r from-blue to-blue-light border border-blue-light/30 p-5 shadow-lg shadow-blue/20" x-show="currentAlert.type == 'info'">
        <div class="flex">
            <div class="shrink-0">
                <x-tni-info-circle-o class="h-6 w-6 text-white"/>
            </div>
            <div class="ml-3 flex-1">
                <div class="flex justify-between">
                    <h3 class="text-sm font-semibold text-white flex-1" x-text="currentAlert.title"></h3>
                    <span x-on:click="currentAlert = {}" class="hover:bg-white/20 rounded-lg p-1 transition-colors duration-200">
                        <x-tni-x-circle-o class="w-5 h-5 text-white cursor-pointer"/>
                    </span>
                </div>
                <div class="mt-2 text-sm text-base-50">
                    <p x-text="currentAlert.message"></p>
                </div>
            </div>
        </div>
    </div>

    <div class="rounded-xl bg-gradient-to-r from-yellow to-yellow-light border border-yellow-light/30 p-5 shadow-lg shadow-yellow/20" x-show="currentAlert.type == 'warning'">
        <div class="flex">
            <div class="shrink-0">
                <x-heroicon-o-exclamation-triangle class="h-6 w-6 text-white"/>
            </div>
            <div class="ml-3 flex-1">
                <div class="flex justify-between">
                    <h3 class="text-sm font-semibold text-white flex-1" x-text="currentAlert.title"></h3>
                    <span x-on:click="currentAlert = {}" class="hover:bg-white/20 rounded-lg p-1 transition-colors duration-200">
                        <x-tni-x-circle-o class="w-5 h-5 text-white cursor-pointer"/>
                    </span>
                </div>
                <div class="mt-2 text-sm text-base-50">
                    <p x-text="currentAlert.message"></p>
                </div>
            </div>
        </div>
    </div>


    @if (session('alert'))
        @php($type = session('alert-type'))

        <x-dynamic-component :component="$type->component()"
                             :title="session('alert-title')"
                             :message="session('alert-message')"
        />
    @endif
    </div>
