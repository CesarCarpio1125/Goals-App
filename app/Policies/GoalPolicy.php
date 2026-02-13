<?php

namespace App\Policies;

use App\Models\Goal;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class GoalPolicy
{
    public function view(User $user, Goal $goal): Response
    {
        return $user->id === $goal->user_id
            ? Response::allow()
            : Response::deny('You can only view your own goals.');
    }

    public function update(User $user, Goal $goal): Response
    {
        return $user->id === $goal->user_id
            ? Response::allow()
            : Response::deny('You can only update your own goals.');
    }

    public function delete(User $user, Goal $goal): Response
    {
        return $user->id === $goal->user_id
            ? Response::allow()
            : Response::deny('You can only delete your own goals.');
    }
}
