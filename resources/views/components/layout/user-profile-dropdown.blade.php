<!-- User profile card -->
<div class="mb-3 p-3 rounded-xl bg-gradient-to-br from-base-900 to-base-950 border border-base-800/50 shadow-lg" x-data="{ open: false }">
    <button type="button"
        class="group w-full flex items-center gap-3 transition-all duration-200"
        x-on:click="open = !open">
        <!-- Avatar -->
        <div class="relative flex-shrink-0">
            <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-red/20 to-orange/20 border border-red/30 flex items-center justify-center overflow-hidden group-hover:border-red/50 transition-colors duration-200">
                <svg class="h-6 w-6 text-red" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                </svg>
            </div>
            <!-- Online indicator -->
            <div class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-green rounded-full border-2 border-base-900"></div>
        </div>
        
        <!-- User info -->
        <div class="flex-1 text-left min-w-0">
            <p class="text-sm font-semibold text-base-50 truncate">{{ auth()->user()->name ?? __('User') }}</p>
            <p class="text-xs text-base-400 truncate">{{ auth()->user()->email ?? '' }}</p>
        </div>
        
        <!-- Chevron -->
        <div class="transition-transform duration-200 flex-shrink-0" x-bind:class="open ? 'rotate-180' : ''">
            <svg class="h-4 w-4 text-base-400 group-hover:text-base-200 transition-colors duration-200" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
            </svg>
        </div>
    </button>
    
    <!-- Dropdown menu -->
    <div x-show="open" 
         x-cloak 
         x-collapse
         class="mt-3 pt-3 border-t border-base-800/50 space-y-1 overflow-hidden">
        <!-- Settings link -->
        <a href="{{ route('settings') }}" wire:navigate.hover
            @class([
                'group flex items-center gap-2 rounded-lg p-2 text-sm font-medium transition-all duration-200',
                'bg-red/10 text-base-50' => Route::currentRouteName() === 'settings',
                'text-base-300 hover:text-base-50 hover:bg-base-800/50' => Route::currentRouteName() !== 'settings',
            ])>
            <div class="flex items-center justify-center w-7 h-7 rounded-lg transition-all duration-200 {{ Route::currentRouteName() === 'settings' ? 'bg-red/20' : 'group-hover:bg-base-800' }}">
                <svg class="h-4 w-4 transition-colors duration-200 {{ Route::currentRouteName() === 'settings' ? 'text-red' : 'text-base-400 group-hover:text-base-200' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </div>
            @lang('Settings')
        </a>
        
        <!-- Logout -->
        <form action="{{ route('logout') }}" method="POST" class="w-full">
            @csrf
            <button type="submit"
                class="group w-full flex items-center gap-2 rounded-lg p-2 text-sm font-medium text-base-300 hover:text-base-50 hover:bg-base-800/50 transition-all duration-200">
                <div class="flex items-center justify-center w-7 h-7 rounded-lg transition-all duration-200 group-hover:bg-base-800">
                    <svg class="h-4 w-4 text-base-400 group-hover:text-base-200 transition-colors duration-200" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                    </svg>
                </div>
                @lang('Sign out')
            </button>
        </form>
    </div>
</div>
