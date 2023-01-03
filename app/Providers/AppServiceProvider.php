<?php

namespace App\Providers;

use App\Models\Choice;
use App\Observers\ChoiceObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Choice::observe(ChoiceObserver::class);
    }
}
