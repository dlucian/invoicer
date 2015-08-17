<?php

use Illuminate\Database\Seeder;

class InvoicesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $seller_name = ucwords(strtolower(str_random(10) . ' ' . str_random(10)));
        $seller_info = $this->getRandomInfo();
        $issuer_info = ucwords(strtolower(str_random(10) . ' ' . str_random(10))) . "\n" . str_random(16);
        for($i = 0; $i < 100; $i++) {
            DB::table('invoices')->insert([
                'invoice' => strtoupper(str_random(3)) . ' X' . sprintf('%05d', rand(1,1000)),
                'issued_on' => date('Y-m-d', rand(time() - 5600000, time()) ),
                'seller_name' => $seller_name,
                'seller_info' => $seller_info,
                'buyer_name' => ucwords(strtolower(str_random(10) . ' ' . str_random(10))),
                'buyer_info' => $this->getRandomInfo(),
                'vat_percent' => rand(5,29),
                'products' => json_encode(['test' => 'info']),
                'issuer_info' => $issuer_info,
                'receiver_info' => ucwords(strtolower(str_random(10) . ' ' . str_random(10))) . "\n" . str_random(16),
                'branding' => 'Invoicer' . strtoupper(str_random(3)),
                'extra' => str_random()
            ]);
        }
    }

    private function getRandomInfo()
    {
        return sprintf("J%d/%d/%d\nRO%08d\n%s\n%s\nWadiya\n%s", rand(10,99), rand(1000,9000), rand(1990,2015),
            rand(10000000,99999999),str_random(),str_random(),str_random()
        );
    }
}
