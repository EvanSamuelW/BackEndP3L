<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('jenisKelamin');
            $table->string('noTelp');
            $table->bigInteger('jabatan_id')->unsigned();
            $table->Date('tglGabung');
            $table->boolean('is_active');
            $table->rememberToken();
            $table->timestamps(); 

         
        });
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('jabatan_id')->references('id')->on('jabatans')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
