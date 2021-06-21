<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStokMasuksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        Schema::create('stok_masuks', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_bahan')->unsigned();
            $table->date('addedDate');
            $table->integer('jumlah');
            $table->double('subtotal');
            $table->boolean('deleted');
            $table->timestamps();
        });

        Schema::table('stok_masuks', function (Blueprint $table) {
            $table->foreign('id_bahan')->references('id')->on('bahans')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stok_masuks');
    }
}
