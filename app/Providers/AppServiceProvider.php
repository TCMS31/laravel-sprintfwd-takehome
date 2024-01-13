<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Outside production, turn the two mistakes that are easiest to ship
        // silently into loud exceptions: a lazily loaded relation (the N+1 a
        // reviewer only sees in the query log) and a mass-assigned attribute
        // that is quietly discarded because it is not in $fillable.
        Model::preventLazyLoading(! $this->app->isProduction());
        Model::preventSilentlyDiscardingAttributes(! $this->app->isProduction());
    }
}
