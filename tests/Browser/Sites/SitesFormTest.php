<?php

namespace Tests\Browser\Sites;

use Laravel\Dusk\Browser;
use PHPUnit\Framework\Attributes\Test;
use Tests\DuskTestCase;
use Vigilant\Sites\Models\Site;

class SitesFormTest extends DuskTestCase
{
    #[Test]
    public function it_can_add_site(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->login()
                ->visit(route('sites'))
                ->click('@site-add-button')
                ->assertSee('The URL of the site that you want to add.') // Help text of URL field
                ->click('@submit-button')
                ->waitForText('field is required')
                ->assertSee('field is required')
                ->type('#form\.url', 'invalid value')
                ->click('@submit-button')
                ->waitForText('must be a valid URL', 5)
                ->assertSee('must be a valid URL')
                ->type('#form\.url', 'https://govigilant.io')
                ->click('@submit-button')
                ->pause(250);

            /** @var ?Site $createdSite */
            $createdSite = Site::query()->firstWhere('url', '=', 'https://govigilant.io');
            $this->assertNotNull($createdSite);
        });
    }

    #[Test]
    public function it_can_add_uptime_monitor_via_tab(): void
    {
        $this->browse(function (Browser $browser) {
            $this->user();

            /** @var Site $site */
            $site = Site::query()->create([
                'url' => 'https://govigilant.io',
            ]);

            $browser->login()
                ->visit(route('site.edit', ['site' => $site]))
                ->waitFor('@uptime-tab-enabled')
                ->check('@uptime-tab-enabled')
                ->waitForText('Friendly name for this monitor')
                ->click('@submit-button')
                ->pause(250)
                ->visit(route('uptime'))
                ->pause(250)
                ->assertSee('https://govigilant.io');
        });
    }
}
