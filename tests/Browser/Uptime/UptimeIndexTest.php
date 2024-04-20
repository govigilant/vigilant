<?php

namespace Tests\Browser\Uptime;

use Laravel\Dusk\Browser;
use PHPUnit\Framework\Attributes\Test;
use Tests\DuskTestCase;
use Vigilant\Uptime\Enums\Type;
use Vigilant\Uptime\Models\Monitor;

class UptimeIndexTest extends DuskTestCase
{
    #[Test]
    public function it_shows_table(): void
    {
        $this->browse(function (Browser $browser) {

            $this->user();

            Monitor::query()->create([
                'name' => 'Test Monitor',
                'type' => Type::Http,
                'settings' => [
                    'host' => 'https://govigilant.io',
                ],
                'interval' => '* * * * *',
                'timeout' => 60,
                'retries' => 3,
            ]);

            $browser->login()
                ->visit(route('uptime'))
                ->assertSee('Test Monitor');
        });
    }
}
