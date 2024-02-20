<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Laravel\Jetstream\Jetstream;
use Vigilant\Users\Actions\Jetstream\Jetstream\AddTeamMember;
use Vigilant\Users\Actions\Jetstream\Jetstream\CreateTeam;
use Vigilant\Users\Actions\Jetstream\Jetstream\DeleteTeam;
use Vigilant\Users\Actions\Jetstream\Jetstream\DeleteUser;
use Vigilant\Users\Actions\Jetstream\Jetstream\InviteTeamMember;
use Vigilant\Users\Actions\Jetstream\Jetstream\RemoveTeamMember;
use Vigilant\Users\Actions\Jetstream\Jetstream\UpdateTeamName;
use Vigilant\Users\Models\Membership;
use Vigilant\Users\Models\Team;
use Vigilant\Users\Models\TeamInvitation;
use Vigilant\Users\Models\User;

class JetstreamServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configurePermissions();

        Jetstream::createTeamsUsing(CreateTeam::class);
        Jetstream::updateTeamNamesUsing(UpdateTeamName::class);
        Jetstream::addTeamMembersUsing(AddTeamMember::class);
        Jetstream::inviteTeamMembersUsing(InviteTeamMember::class);
        Jetstream::removeTeamMembersUsing(RemoveTeamMember::class);
        Jetstream::deleteTeamsUsing(DeleteTeam::class);
        Jetstream::deleteUsersUsing(DeleteUser::class);

        Jetstream::useUserModel(User::class);
        Jetstream::useTeamModel(Team::class);
        Jetstream::useTeamInvitationModel(TeamInvitation::class);
        Jetstream::useMembershipModel(Membership::class);
    }

    /**
     * Configure the roles and permissions that are available within the application.
     */
    protected function configurePermissions(): void
    {
        Jetstream::defaultApiTokenPermissions(['read']);

        Jetstream::role('admin', 'Administrator', [
            'create',
            'read',
            'update',
            'delete',
        ])->description('Administrator users can perform any action.');

        Jetstream::role('editor', 'Editor', [
            'read',
            'create',
            'update',
        ])->description('Editor users have the ability to read, create, and update.');
    }
}
