<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->increments('id');
            $table->string('invoice', 100);
            $table->date('issued_on');
            $table->string('seller_name');
            $table->text('seller_info');
            $table->string('buyer_name');
            $table->text('buyer_info');
            $table->decimal('vat_percent', 5, 2);
            $table->json('products');
            $table->text('issuer_info');
            $table->text('receiver_info');
            $table->string('branding');
            $table->text('extra');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('invoices');
    }
}
