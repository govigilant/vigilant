<?php

namespace Vigilant\Notifications\Contracts;

use Vigilant\Sites\Models\Site;

interface HasSite
{
    public function site(): ?Site;
}
