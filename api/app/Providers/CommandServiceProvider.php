<?php
/**
 * Artisan Commands Service Provider class.
 */

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Console\Commands\CurrencyRetriever;

class CommandServiceProvider extends ServiceProvider
{

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('command.my.currency', function()
        {
            return new CurrencyRetriever;
        });

        $this->commands(
            'command.my.currency'
        );
    }
}