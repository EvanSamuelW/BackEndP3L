<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\stok_keluar;
use App\bahan;
use Carbon\Carbon;
use Validator;

class StokKeluarController extends Controller
{
    public function create(Request $request)
    {
        $data = $request->all();
        $data['addedDate'] = Carbon::now()->toDate();
        $validate = Validator::make($data, [
            'id_bahan' => 'required',
            'jumlah' => 'required',
        ]);

        if ($validate->fails()) {
            return response([
                'message' => $validate->errors()
            ], 402);
        }
        $user = bahan::find($request->id_bahan);
        $bahan = stok_keluar::create($data);
        $user->stok -= $bahan->jumlah;
        if ($user->save()) {
            return response([
                'message' => 'Stok Keluar Ditambahkan',
                'user' => $bahan
            ]);
        }
    }

    public function edit(Request $request)
    {
        $data = $request->all();

        $validate = Validator::make($data, [
            'id_bahan' => 'required',
            'jumlah' => 'required'
        ]);

        if ($validate->fails()) {
            return response([
                'message' => $validate->errors()
            ], 402);
        }
        $user = bahan::find($request->id_bahan);
        $stokKeluar = stok_keluar::find($request->id);
        $created = new Carbon($stokKeluar->created_at);
        if ($created->diff(Carbon::now())->days < 1) {
            $user->stok += $stokKeluar->jumlah;
            $user->stok -=  $data['jumlah'];
        }

        $stokKeluar->jumlah = $data['jumlah'];
        if ($stokKeluar->save() && $user->save()) {
            return response([
                'message' => 'Stok Keluar berhasil diubah',
                'user' => $stokKeluar
            ]);
        }
    }

    public function getAllStokKeluar()
    {
        $data = stok_keluar::all();

        if (count($data) < 1) {
            return response([
                'message' => 'Data is Empty'
            ], 400);
        }

        $data->transform(function ($item) {
            $bahan = bahan::find($item['id_bahan']);
            $item->unit = $bahan->unit;
            $item->nama_bahan = $bahan->nama_bahan;

            return $item;
        });
        return response([
            'message' => 'Retrieve all stok keluar success',
            'data' => $data
        ]);
    }

    public function deleteStokKeluar($id)
    {
        $user = stok_keluar::find($id);


        if (is_null($user)) {
            return response([
                'message' => 'Stok Keluar tidak ditemukan'
            ], 400);
        }

        $user2 = bahan::find($user->id_bahan);
        $user2->stok += $user->jumlah;

        if ($user->delete() && $user2->save()) {
            return response([
                'message' => 'Stok Keluar Dihapus',
                'data' => $user
            ]);
        }
    }
}
