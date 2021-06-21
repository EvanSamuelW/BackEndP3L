<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMenusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->string('nama_menu');
            $table->string('deskripsi');
            $table->string('tipe');
            $table->bigInteger('id_bahan')->unsigned();
            $table->string('gambar');
            $table->string('satuan');
            $table->integer('serving');
            $table->double('harga');
            $table->boolean('deleted');
            $table->timestamps();
        });

        Schema::table('menus', function (Blueprint $table) {
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
        Schema::dropIfExists('menus');
    }
}
