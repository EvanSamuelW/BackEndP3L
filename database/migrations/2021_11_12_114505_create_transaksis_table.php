<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransaksisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaksis', function (Blueprint $table) {
            $table->id();
            $table->double('total')->nullable();
            $table->string('metode')->nullable();
            $table->string('kode_verif')->nullable();
            $table->string('nama_pelanggan');
            $table->bigInteger('id_karyawan')->unsigned();
            $table->bigInteger('id_kartu')->unsigned()->nullable();
            $table->dateTime('tanggal')->nullable();
            $table->string('nomor_nota')->nullable();
            $table->string('status');
            $table->timestamps();
        });

        Schema::table('transaksis', function (Blueprint $table) {
            $table->foreign('id_karyawan')->references('id')->on('users')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('id_kartu')->references('id')->on('kartus')->onUpdate('restrict')->onDelete('restrict');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transaksis');
    }
}
