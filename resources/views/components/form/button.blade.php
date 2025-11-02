@php
$tag = isset($href) ? 'a' : 'button';

// Define color variants based on classes
$hasBlue = str_contains($attributes->get('class', ''), 'bg-blue');
$hasRed = str_contains($attributes->get('class', ''), 'bg-red');
$hasGradient = str_contains($attributes->get('class', ''), 'gradient');

// Default classes for neutral buttons (no color specified)
$defaultClasses = 'bg-base-900/50 border-base-800/50 hover:bg-base-800/50 hover:border-base-700';

// Color-specific classes
$blueClasses = 'bg-base-900/50 border-blue/50 text-blue hover:bg-blue/10 hover:border-blue hover:text-blue-light';
$redClasses = 'bg-red/10 border-red/50 text-red hover:bg-red/20 hover:border-red hover:text-red-light';
$gradientClasses = 'border-transparent';

// Determine which classes to use
$colorClasses = $defaultClasses;
if ($hasBlue) {
    $colorClasses = $blueClasses;
} elseif ($hasRed) {
    $colorClasses = $redClasses;
} elseif ($hasGradient) {
    $colorClasses = $gradientClasses;
}
@endphp

<{{ $tag }}
    {{ $attributes->merge(['class' => "inline-flex items-center justify-center gap-2 rounded-lg px-4 py-2.5 text-sm font-semibold text-base-100 border transition-all duration-300 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red cursor-pointer {$colorClasses}"]) }}>
    {{ $slot }}
    </{{ $tag }}>
