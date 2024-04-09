<?php

namespace Tests\Browser\Uptime;

use Laravel\Dusk\Browser;
use PHPUnit\Framework\Attributes\Test;
use Tests\DuskTestCase;
use Vigilant\Uptime\Models\Monitor;

class UptimeFormTest extends DuskTestCase
{
    #[Test]
    public function it_can_add_monitor(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->login()
                ->visit(route('uptime'))
                ->click('@monitor-add-button')
                ->assertSee('Friendly name for this monitor') // Help text of name field
                ->type('#form\.name', 'Test Monitor')
                ->select('#form\.type', 'ping')
                ->pause(500)
                ->type('#form\.settings\.host', 'govigilant.io')
                ->type('#form\.settings\.port', 22)
                ->select('#form\.interval', '* * * * */2')
                ->type('#form\.retries', 5)
                ->type('#form\.timeout', 10)
                ->click('@submit-button')
                ->pause(250);


            /** @var ?Monitor $monitor */
            $monitor = Monitor::query()->first();
            $this->assertNotNull($monitor);
            $this->assertEquals('Test Monitor', $monitor->name);
            $this->assertEquals(['host' => 'govigilant.io', 'port' => 22], $monitor->settings);
        });
    }

}
