<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */

    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Gate::define('delete', function (User $user, Model $model) {
            return $user->id === $model->user_id || $user->isAdmin();
        });
        Gate::define('update', function (User $user, Model $model) {
            return $user->id === $model->user_id || $user->isAdmin();
        });
        Gate::define('crud-access', function (User $user) {
            return in_array($user->role, ['admin', 'moderator'], true);
        });
    }
}
