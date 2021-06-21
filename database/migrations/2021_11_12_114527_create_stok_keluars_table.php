<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStokKeluarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stok_keluars', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_bahan')->unsigned();
            $table->date('addedDate');
            $table->string('keterangan');
            $table->integer('jumlah');
            $table->timestamps();
        });

        Schema::table('stok_keluars', function (Blueprint $table) {
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
        Schema::dropIfExists('stok_keluars');
    }
}
