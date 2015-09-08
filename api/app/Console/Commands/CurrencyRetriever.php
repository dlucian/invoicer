<?php
/**
 * Artisan command to retrieve today's exchange rate for the foreign currency.
 *
 * Hints from: http://codrspace.com/MattWohler/creating-a-new-artisan-command-with-lumen/
 */

namespace App\Console\Commands;

use App\Services\CurrencyConverter;
use Illuminate\Console\Command;
use App\Models\Setting;

class CurrencyRetriever extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'my:currency';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Retrieve currency rate for today.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        $this->info('Persisting today\'s currency...');
        $foreignCurrency = Setting::getByName('foreign_currency');
        if (empty($foreignCurrency))
            $this->error('No foregin_currency setting defined.');

        $this->info( sprintf('1 %s = %.4f RON', $foreignCurrency,
            CurrencyConverter::toForeign( 1, $foreignCurrency )
        ));
    }

}