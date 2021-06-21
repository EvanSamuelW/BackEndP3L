<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_menu')->unsigned();
            $table->bigInteger('id_transaksi')->unsigned();
            $table->integer('jumlah');
            $table->double('subtotal');
            $table->string('status');
            $table->timestamps();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->foreign('id_menu')->references('id')->on('menus')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('id_transaksi')->references('id')->on('transaksis')->onUpdate('restrict')->onDelete('restrict');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
