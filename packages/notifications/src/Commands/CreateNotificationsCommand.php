<?php

namespace Vigilant\Notifications\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Bus\PendingDispatch;
use Vigilant\Notifications\Jobs\CreateNotificationsJob;
use Vigilant\Users\Models\Team;

class CreateNotificationsCommand extends Command
{
    protected $signature = 'notifications:create {teamId?}';

    protected $description = 'Create notifications in DB for teams';

    public function handle(): int
    {
        /** @var ?int $teamId */
        $teamId = $this->argument('teamId');

        Team::query()
            ->when($teamId !== null, fn (Builder $builder): Builder => $builder->where('team_id', '=', $teamId))
            ->get()
            ->each(fn (Team $team): PendingDispatch => CreateNotificationsJob::dispatch($team));

        return static::SUCCESS;
    }
}
