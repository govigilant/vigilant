@props(['difference'])

@if ($difference !== null)
    @php($differencePercent = $difference->averageDifference())
    <span @class([
        'text-green-light' => $differencePercent > 0,
        'text-base-600' => $differencePercent == 0,
        'text-red-light' => $differencePercent < 0,
    ])>{{ round($differencePercent, 1) . '%' }}</span>
@else
    <span>-</span>
@endif
