<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use Illuminate\Console\Command;
use App\Models\Setting;
use DB;

class Migrator extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'my:migrator';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automagic.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        $this->info('Migrating...');
//            $this->error('No foregin_currency setting defined.');

        DB::delete('delete from invoices');
        $oldInvoices = DB::select('select * from invoices5');

        foreach ($oldInvoices as $oldInvoice) {
            $invoice = Invoice::create([
                'invoice'       => $oldInvoice->number,
                'issued_on'     => $oldInvoice->date,
                'seller_name'   => $oldInvoice->seller_name,
                'seller_info'   => $oldInvoice->seller_info,
                'buyer_name'    => $oldInvoice->buyer_name,
                'buyer_info'    => $oldInvoice->buyer_info,
                'vat_percent'   => $oldInvoice->vat_percent,
                'products'      => $this->newProducts($oldInvoice->products),
                'issuer_info'   => $oldInvoice->issuer_info,
                'receiver_info' => $oldInvoice->delegate_info,
                'branding'      => $oldInvoice->branding_label,
                'extra'         => $oldInvoice->extra_info,
            ]);

            $this->info('Migrated ' . $invoice->invoice . ' / ' . $invoice->issued_on);
        }

        $this->info('Done.');
    }

    private function newProducts( $oldProductsString )
    {
        $oldProducts = json_decode($oldProductsString, true);
        if (empty($oldProducts))
            $this->error('Could not decode products from: ' . $oldProductsString);

        $newProducts = [];
        foreach ($oldProducts as $oldProduct) {
            $newProduct = [
                'description'   => $oldProduct['description'],
                'quantity'      => $oldProduct['quantity'],
            ];

            if (!empty($oldProduct['currencyb']) and $oldProduct['currencyb'] == 'USD') {
                $newProduct['currency'] = $oldProduct['currencyb'];
                $newProduct['price'] = $oldProduct['priceb'];
            } else {
                $newProduct['currency'] = $oldProduct['currency'];
                $newProduct['price'] = $oldProduct['price'];
            }

            $newProducts[] = $newProduct;
        }

        return json_encode($newProducts);
    }

}