<?php

namespace Vigilant\Frontend\Integrations\Table;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use RamonRietdijk\LivewireTables\Columns\DateColumn as BaseDateColumn;

/* Extended to handle the team's timezone */
class DateColumn extends BaseDateColumn
{
    public function resolveValue(Model $model): mixed
    {
        /** @var string|Carbon|null $value */
        $value = $this->getValue($model);

        if ($this->displayUsing !== null) {
            return call_user_func($this->displayUsing, $value, $model);
        }

        if ($value === null) {
            return null;
        }

        /** @var Carbon $date */
        $date = teamTimezone(Carbon::parse($value));

        return $this->format === null
            ? $date->toDateTimeString()
            : $date->format($this->format);
    }
}
