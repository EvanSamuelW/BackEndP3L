<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('user/login', 'Api\UserController@login'); //Login di Web
    Route::get('user', 'Api\UserController@getUser')->middleware('auth:api');
    Route::get('jabatan', 'Api\UserController@getJabatan'); 
        Route::get('users', 'Api\UserController@getAllUser');
// Fungsi utama admin

    Route::post('user/register', 'Api\UserController@register');
    Route::put('user/edit/{id}', 'Api\UserController@editUser');
    Route::delete('user/delete/{id}', 'Api\UserController@deleteUser');
    Route::post('user/enable/{id}', 'Api\UserController@enableUser');
    Route::get('customer', 'Api\PelangganController@getAllPelanggan');
    Route::post('customer/create', 'Api\PelangganController@create');
    Route::put('customer/edit/{id}', 'Api\PelangganController@editPelanggan');
    Route::delete('customer/delete/{id}', 'Api\PelangganController@deletePelanggan');
    Route::get('meja', 'Api\MejaController@getAllMeja');
    Route::post('meja/create', 'Api\MejaController@create');
    Route::put('meja/edit/{id}', 'Api\MejaController@editMeja');
    Route::get('order', 'Api\OrderController@getOrdersWeb');
    Route::post('order/create', 'Api\OrderController@create');
    Route::put('transaksi/edit/{id}', 'Api\TransaksiController@tambahTransaksi');
    Route::get('transaksi/notpaid', 'Api\TransaksiController@getAllTransaksiNotPaid');
    Route::get('transaksi/paid', 'Api\TransaksiController@getAllTransaksi');
    Route::get('transaksi/{id}', 'Api\TransaksiController@getOrder');
    Route::get('order/laporan1/{year}/{user}', 'Api\OrderController@laporanPenghasilan1');
    Route::get('order/laporan2/{year1}/{year2}/{user}', 'Api\OrderController@laporanPenghasilan2');
    Route::get('order/laporanPenjualan1/{month}/{year}/{user}', 'Api\OrderController@laporanPenjualan1');
    Route::get('order/laporanPenjualan2/{year}/{user}', 'Api\OrderController@laporanPenjualan2');
    Route::delete('meja/delete/{id}', 'Api\MejaController@deleteMeja');
    Route::get('reservasi', 'Api\ReservasiController@getAllReservasi');
    Route::post('reservasi/create', 'Api\ReservasiController@create');
    Route::put('reservasi/edit/{id}', 'Api\ReservasiController@editReservasi');
    Route::get('reservasi/QR/{id}/{user}', 'Api\ReservasiController@printQR');
    Route::get('transaksi/struk/{id}/{user}', 'Api\TransaksiController@printStruk');
     Route::get('kartu/{id}', 'Api\TransaksiController@getKartu');
    Route::delete('reservasi/delete/{id}', 'Api\ReservasiController@deleteReservasi');
    Route::get('bahan', 'Api\BahanController@getAllBahan');
    Route::post('bahan/create', 'Api\BahanController@create');
    Route::put('bahan/edit/{id}', 'Api\BahanController@editBahan');
    Route::delete('bahan/delete/{id}', 'Api\BahanController@deleteBahan');
    Route::post('menu/create', 'Api\MenuController@create');
    Route::put('menu/edit/{id}', 'Api\MenuController@editMenu');
    Route::post('stok_masuk/create', 'Api\StokMasukController@create');
    Route::get('stok_masuk', 'Api\StokMasukController@getAllStokMasuk');
    Route::get('stok_masuk/year', 'Api\StokMasukController@getYear');
    Route::get('stok_masuk/laporan1/{year}/{user}', 'Api\StokMasukController@laporanPengeluaran1');
    Route::get('stok_masuk/laporanstok1/{date1}/{date2}/{user}', 'Api\StokMasukController@laporanstok1');
    Route::get('stok_masuk/laporanstok2/{year}/{month}/{id}/{user}', 'Api\StokMasukController@laporanstok2');
    Route::get('stok_masuk/laporan2/{year1}/{year2}/{user}', 'Api\StokMasukController@laporanPengeluaran2');
    Route::put('stok_masuk/edit/{id}', 'Api\StokMasukController@edit');
    Route::delete('stok_masuk/delete/{id}', 'Api\StokMasukController@deleteStokMasuk');
    Route::post('stok_keluar/create', 'Api\StokKeluarController@create');
    Route::get('stok_keluar', 'Api\StokKeluarController@getAllStokKeluar');
    Route::put('stok_keluar/edit/{id}', 'Api\StokKeluarController@edit');
    Route::delete('stok_keluar/delete/{id}', 'Api\StokKeluarController@deleteStokKeluar');
    Route::delete('menu/delete/{id}', 'Api\MenuController@deleteMenu');
    Route::post('user/logout', 'Api\UserController@logout')->middleware('auth:api');


// Api Untuk sisi customer Mobile
Route::get('order/process/{id}', 'Api\OrderController@getOrdersOnProcess');
Route::put('order/edit/{id}', 'Api\OrderController@editOrder');
Route::delete('order/delete/{id}', 'Api\OrderController@deleteOrder');
Route::put('order/onProcess/{id}', 'Api\OrderController@proceedOrder');
Route::get('order/{id}', 'Api\OrderController@getOrdersbyUser');
Route::post('reservasi/scan', 'Api\ReservasiController@scanQR');
Route::get('menu', 'Api\MenuController@getAllMenu');