@php
if (! isset($scrollTo)) {
    $scrollTo = 'body';
}

$scrollIntoViewJsSnippet = ($scrollTo !== false)
    ? <<<JS
       (\$el.closest('{$scrollTo}') || document.querySelector('{$scrollTo}')).scrollIntoView()
    JS
    : '';
@endphp

<div>
    @if($paginator->hasPages())
        <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-between">
            <div class="flex justify-between flex-1 sm:hidden">
                <span>
                    @if($paginator->onFirstPage())
                        <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-base-500 bg-base-850 border border-base-700 cursor-default leading-5 rounded-lg transition-all duration-200">
                            {!! __('pagination.previous') !!}
                        </span>
                    @else
                        <button type="button" wire:click="previousPage('{{ $paginator->getPageName() }}')" x-on:click="{{ $scrollIntoViewJsSnippet }}" wire:loading.attr="disabled" dusk="previousPage{{ $paginator->getPageName() == 'page' ? '' : '.' . $paginator->getPageName() }}.before" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-base-100 bg-base-850 border border-base-700 leading-5 rounded-lg hover:bg-base-800 hover:text-base-50 hover:border-base-600 focus:outline-none focus:ring-2 focus:ring-red/50 focus:border-red active:bg-base-800 transition-all duration-200">
                            {!! __('pagination.previous') !!}
                        </button>
                    @endif
                </span>

                <span>
                    @if($paginator->hasMorePages())
                        <button type="button" wire:click="nextPage('{{ $paginator->getPageName() }}')" x-on:click="{{ $scrollIntoViewJsSnippet }}" wire:loading.attr="disabled" dusk="nextPage{{ $paginator->getPageName() == 'page' ? '' : '.' . $paginator->getPageName() }}.before" class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-base-100 bg-base-850 border border-base-700 leading-5 rounded-lg hover:bg-base-800 hover:text-base-50 hover:border-base-600 focus:outline-none focus:ring-2 focus:ring-red/50 focus:border-red active:bg-base-800 transition-all duration-200">
                            {!! __('pagination.next') !!}
                        </button>
                    @else
                        <span class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-base-500 bg-base-850 border border-base-700 cursor-default leading-5 rounded-lg transition-all duration-200">
                            {!! __('pagination.next') !!}
                        </span>
                    @endif
                </span>
            </div>

            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                <div>
                    <p class="transition-all duration-200 text-sm text-base-300 leading-5">
                        <span>{!! __('Showing') !!}</span>
                        <span class="font-medium text-base-100">{{ $paginator->firstItem() }}</span>
                        <span>{!! __('to') !!}</span>
                        <span class="font-medium text-base-100">{{ $paginator->lastItem() }}</span>
                        <span>{!! __('of') !!}</span>
                        <span class="font-medium text-base-100">{{ $paginator->total() }}</span>
                        <span>{!! __('results') !!}</span>
                    </p>
                </div>

                <div>
                    <span class="relative z-0 inline-flex rtl:flex-row-reverse rounded-lg shadow-sm">
                        <span>
                            {{-- Previous Page Link --}}
                            @if($paginator->onFirstPage())
                                <span aria-disabled="true" aria-label="{{ __('pagination.previous') }}">
                                    <span class="transition-all duration-200 relative inline-flex items-center px-2 py-2 text-sm font-medium text-base-500 bg-base-850 border border-base-700 cursor-default rounded-l-lg leading-5" aria-hidden="true">
                                        <x-livewire-table::icon icon="chevron-left" class="size-5" />
                                    </span>
                                </span>
                            @else
                                <button type="button" wire:click="previousPage('{{ $paginator->getPageName() }}')" x-on:click="{{ $scrollIntoViewJsSnippet }}" dusk="previousPage{{ $paginator->getPageName() == 'page' ? '' : '.' . $paginator->getPageName() }}.after" class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-base-200 bg-base-850 border border-base-700 rounded-l-lg leading-5 hover:text-base-50 hover:bg-base-800 hover:border-base-600 focus:z-10 focus:outline-none focus:border-red focus:ring-2 focus:ring-red/50 active:bg-base-800 transition-all duration-200" aria-label="{{ __('pagination.previous') }}">
                                    <x-livewire-table::icon icon="chevron-left" class="size-5" />
                                </button>
                            @endif
                        </span>

                        {{-- Pagination Elements --}}
                        @foreach($elements as $element)
                            {{-- "Three Dots" Separator --}}
                            @if(is_string($element))
                                <span aria-disabled="true">
                                    <span class="transition-all duration-200 relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-base-300 bg-base-850 border border-base-700 cursor-default leading-5">{{ $element }}</span>
                                </span>
                            @endif

                            {{-- Array Of Links --}}
                            @if(is_array($element))
                                @foreach($element as $page => $url)
                                    <span wire:key="paginator-{{ $paginator->getPageName() }}-page{{ $page }}">
                                        @if($page == $paginator->currentPage())
                                            <span aria-current="page">
                                                <span class="transition-all duration-200 relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-base-50 bg-red border border-red cursor-default leading-5 font-semibold">{{ $page }}</span>
                                            </span>
                                        @else
                                            <button type="button" wire:click="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')" x-on:click="{{ $scrollIntoViewJsSnippet }}" class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-base-200 bg-base-850 border border-base-700 leading-5 hover:text-base-50 hover:bg-base-800 hover:border-base-600 focus:z-10 focus:outline-none focus:border-red focus:ring-2 focus:ring-red/50 active:bg-base-800 transition-all duration-200" aria-label="{{ __('Go to page :page', ['page' => $page]) }}">
                                                {{ $page }}
                                            </button>
                                        @endif
                                    </span>
                                @endforeach
                            @endif
                        @endforeach

                        <span>
                            {{-- Next Page Link --}}
                            @if($paginator->hasMorePages())
                                <button type="button" wire:click="nextPage('{{ $paginator->getPageName() }}')" x-on:click="{{ $scrollIntoViewJsSnippet }}" dusk="nextPage{{ $paginator->getPageName() == 'page' ? '' : '.' . $paginator->getPageName() }}.after" class="relative inline-flex items-center px-2 py-2 -ml-px text-sm font-medium text-base-200 bg-base-850 border border-base-700 rounded-r-lg leading-5 hover:text-base-50 hover:bg-base-800 hover:border-base-600 focus:z-10 focus:outline-none focus:border-red focus:ring-2 focus:ring-red/50 active:bg-base-800 transition-all duration-200" aria-label="{{ __('pagination.next') }}">
                                    <x-livewire-table::icon icon="chevron-right" class="size-5" />
                                </button>
                            @else
                                <span aria-disabled="true" aria-label="{{ __('pagination.next') }}">
                                    <span class="transition-all duration-200 relative inline-flex items-center px-2 py-2 -ml-px text-sm font-medium text-base-500 bg-base-850 border border-base-700 cursor-default rounded-r-lg leading-5" aria-hidden="true">
                                        <x-livewire-table::icon icon="chevron-right" class="size-5" />
                                    </span>
                                </span>
                            @endif
                        </span>
                    </span>
                </div>
            </div>
        </nav>
    @endif
</div>
