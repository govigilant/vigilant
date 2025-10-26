<?php

namespace Vigilant\Frontend\Integrations\Table;

use Illuminate\Database\Eloquent\Model;
use RamonRietdijk\LivewireTables\Columns\Column;

class GeoIpColumn extends Column
{
    public function render(Model $model): mixed
    {
        $geoip = $model['geoip'] ?? [];

        return view('frontend::integrations.table.geoip-column', $geoip);
    }
}
