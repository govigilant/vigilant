<?php

namespace Vigilant\Frontend\Integrations\Table;

use Illuminate\Database\Eloquent\Model;
use RamonRietdijk\LivewireTables\Columns\Column;

class GeoIpColumn extends Column
{
    protected string $view = 'frontend::integrations.table.geoip-column';

    public function render(Model $model): mixed
    {
        $geoip = $model['geoip'] ?? [];


        return view($this->view, $geoip);
    }
}
