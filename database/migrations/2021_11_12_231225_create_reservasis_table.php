<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReservasisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reservasis', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_meja')->unsigned();
            $table->bigInteger('id_pelanggan')->unsigned();
            $table->bigInteger('id_karyawan')->unsigned();
            $table->bigInteger('id_transaksi')->unsigned();
            $table->date('tanggal');
            $table->String('sesi');
            $table->String('status');
            $table->boolean('deleted');
            $table->string('qrcode');
            $table->timestamps();
        });

        Schema::table('reservasis', function (Blueprint $table) {
            $table->foreign('id_meja')->references('id')->on('mejas')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('id_pelanggan')->references('id')->on('pelanggans')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('id_karyawan')->references('id')->on('users')->onUpdate('restrict')->onDelete('restrict');
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
        Schema::dropIfExists('reservasis');
    }
}
