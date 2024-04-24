<?php

namespace Tests;

use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Laravel\Dusk\TestCase as BaseTestCase;
use PHPUnit\Framework\Attributes\BeforeClass;
use Vigilant\Core\Services\TeamService;
use Vigilant\Users\Actions\Jetstream\CreateTeam;
use Vigilant\Users\Models\User;

abstract class DuskTestCase extends BaseTestCase
{
    use CreatesApplication;
    use DatabaseMigrations;

    /**
     * Prepare for Dusk test execution.
     */
    #[BeforeClass]
    public static function prepare(): void
    {
        if (! static::runningInSail()) {
            static::startChromeDriver();
        }
    }

    /**
     * Create the RemoteWebDriver instance.
     */
    protected function driver(): RemoteWebDriver
    {
        $options = (new ChromeOptions)->addArguments(collect([
            $this->shouldStartMaximized() ? '--start-maximized' : '--window-size=1920,1080',
        ])->unless($this->hasHeadlessDisabled(), function (Collection $items) {
            return $items->merge([
                '--disable-gpu',
                '--headless=new',
            ]);
        })->all());

        return RemoteWebDriver::create(
            $_ENV['DUSK_DRIVER_URL'] ?? 'http://localhost:9515',
            DesiredCapabilities::chrome()->setCapability(
                ChromeOptions::CAPABILITY, $options
            )
        );
    }

    /**
     * Determine whether the Dusk command has disabled headless mode.
     */
    protected function hasHeadlessDisabled(): bool
    {
        return isset($_SERVER['DUSK_HEADLESS_DISABLED']) ||
            isset($_ENV['DUSK_HEADLESS_DISABLED']);
    }

    /**
     * Determine if the browser window should start maximized.
     */
    protected function shouldStartMaximized(): bool
    {
        return isset($_SERVER['DUSK_START_MAXIMIZED']) ||
            isset($_ENV['DUSK_START_MAXIMIZED']);
    }

    protected function user(): User
    {
        /** @var User $user */
        $user = User::query()->firstOrCreate([
            'email' => 'tester@govigilant.io',
        ], [
            'name' => 'Tester',
            'password' => bcrypt('password'),
            'current_team_id' => 1,
        ]);

        if ($user->currentTeam === null) {
            /** @var CreateTeam $createTeam */
            $createTeam = app(CreateTeam::class);

            $team = $createTeam->create($user, [
                'name' => 'Tester\'s Team',
            ]);

            $team->users()->attach($user);
        }

        Auth::login($user);

        return $user;
    }
}
