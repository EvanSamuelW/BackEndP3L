<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\bahan;
use Validator;


class BahanController extends Controller
{
    public function create(Request $request)
    {
        $data = $request->all();

        $validate = Validator::make($data, [
            'nama_bahan' => 'required',
            'harga' => 'required',
            'unit' => 'required',
        ]);

        if ($validate->fails()) {
            return response([
                'message' => $validate->errors()
            ], 402);
        }

        $data['stok'] = 0;
        $data['deleted'] = false;


        $bahan = bahan::create($data);

        return response([
            'message' => 'Bahan dibuat',
            'data' => $bahan,
        ], 200);
    }

    public function deleteBahan($id)
    {
        $user = bahan::find($id);

        if (is_null($user)) {
            return response([
                'message' => 'Bahan tidak ditemukan'
            ], 400);
        }

        $user->deleted = true;
        if ($user->save()) {
            return response([
                'message' => 'Bahan Dihapus',
                'data' => $user
            ]);
        }
    }

    public function editBahan(Request $request, $id)
    {
        $data = $request->all();

        $validate = Validator::make($data, [
            'nama_bahan' => 'required',
            'harga' => 'required',
            'unit' => 'required',
        ]);

        if ($validate->fails())
            return response([
                'message' => $validate->errors()
            ], 402);

        $user = bahan::find($id);
        $user->nama_bahan = $data['nama_bahan'];
        $user->harga = $data['harga'];
        $user->unit = $data['unit'];
        if ($user->save()) {
            return response([
                'message' => 'Edit Bahan Success',
                'user' => $user
            ]);
        }

        return response([
            'message' => 'Edit Bahan Gagal',
        ], 403);
    }

    public function getAllBahan()
    {
        $data = bahan::where('deleted', false)->get();

        if (count($data) < 1) {
            return response([
                'message' => 'Bahan is Empty'
            ], 400);
        }

        return response([
            'message' => 'Retrieve all Bahan success',
            'data' => $data
        ]);
    }
}
