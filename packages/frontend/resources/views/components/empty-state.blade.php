@props([
    'title',
    'description',
    'icon' => 'phosphor-warning-circle',
    'iconClass' => 'h-12 w-12 text-base-100',
    'iconWrapperClass' => 'rounded-full bg-base-700/50 p-4 mb-6',
    'buttonHref' => null,
    'buttonText' => null,
    'buttonClass' => 'bg-red text-base-50 px-5 py-2.5 rounded-lg transition-all duration-300',
    'wrapperClass' => 'mx-auto max-w-3xl text-center py-12',
    'cardClass' => 'bg-base-850/50 border-base-700/50',
    'contentClass' => 'flex flex-col items-center',
    'titleClass' => 'text-2xl font-bold text-base-50 mb-2',
    'descriptionClass' => 'text-base text-base-300 mb-8 max-w-md',
])

<div class="{{ $wrapperClass }}">
    <x-card class="{{ $cardClass }}">
        <div class="{{ $contentClass }}">
            @if ($icon)
                <div class="{{ $iconWrapperClass }}">
                    @svg($icon, $iconClass)
                </div>
            @endif

            <h3 class="{{ $titleClass }}">{{ $title }}</h3>

            @if ($description)
                <p class="{{ $descriptionClass }}">
                    {{ $description }}
                </p>
            @endif

            @if ($buttonHref && $buttonText)
                <x-form.button class="{{ $buttonClass }}" :href="$buttonHref">
                    {{ $buttonText }}
                </x-form.button>
            @endif
        </div>
    </x-card>
</div>
