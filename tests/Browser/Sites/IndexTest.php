<?php

namespace Tests\Browser\Sites;

use Laravel\Dusk\Browser;
use PHPUnit\Framework\Attributes\Test;
use Tests\DuskTestCase;
use Vigilant\Sites\Models\Site;

class IndexTest extends DuskTestCase
{
    #[Test]
    public function it_shows_table(): void
    {
        $this->browse(function (Browser $browser) {

            $this->user();

            Site::query()->create([
                'team_id' => 1,
                'url' => 'https://govigilant.io',
            ]);

            $browser->login()
                ->visit(route('sites'))
                ->assertSee('https://govigilant.io');
        });
    }
}
