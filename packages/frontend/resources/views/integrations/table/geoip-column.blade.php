@if(isset($country_code))
    <div class="flex items-center gap-x-3 font-semibold text-white leading-6 text-sm">
    <span class="min-w-0 has-tooltip">
         <span class='tooltip rounded shadow-lg p-2 bg-base-950 text-neutral-200 -mt-8 prose'>
             {{ collect([$country_name, $region_name, $city, $isp, $org, $as])->whereNotNull()->implode(PHP_EOL) }}
         </span>
        <span class="truncate w-6">
            <x-icon name="flag-country-{{ $country_code }}" class="h-4"/>
        </span>
    </span>
        <span>{{ $country_name }}</span>
    </div
@else
    <span class="text-base-100 text-xs">@lang('N/A')</span>
@endif
