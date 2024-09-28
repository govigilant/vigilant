<?php

namespace Vigilant\Core\Policies;

use Illuminate\Database\Eloquent\Model;
use Vigilant\Users\Models\User;

class AllowAllPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Model $model): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Model $model): bool
    {
        return true;
    }

    public function delete(User $user, Model $model): bool
    {
        return true;
    }

    public function restore(User $user, Model $model): bool
    {
        return true;
    }

    public function forceDelete(User $user, Model $model): bool
    {
        return true;
    }
}
