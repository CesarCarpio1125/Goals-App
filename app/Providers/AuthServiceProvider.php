<?php

namespace App\Providers;

use App\Models\Goal;
use App\Policies\GoalPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Goal::class => GoalPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
