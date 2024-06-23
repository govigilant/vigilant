@props(['difference'])

@if ($difference !== null)
    @php($differencePercent = $difference->averageDifference())
    @php($indicator = match(true) {
        $differencePercent > 0 => '+',
        $differencePercent < 0 => '-',
        default => '',
    })

    <span @class([
        'text-green-light' => $differencePercent > 0,
        'text-base-600' => $differencePercent == 0,
        'text-red-light' => $differencePercent < 0,
    ])>{{ $indicator . round($differencePercent) . '%' }}</span>
@else
    <span>-</span>
@endif
