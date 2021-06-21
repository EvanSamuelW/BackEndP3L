<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\pelanggan;
use Validator;


class PelangganController extends Controller
{
    public function create(Request $request)
    {
        $data = $request->all();

        $validate = Validator::make($data, [
            'name' => 'required',
        ]);

        if ($validate->fails()) {
            return response([
                'message' => $validate->errors()
            ], 402);
        }

        $data['status'] = true;
        $user = pelanggan::create($data);

        return response([
            'message' => 'Pelanggan Terdaftar',
            'data' => $user,
        ], 200);
    }


    public function deletePelanggan($id)
    {
        $user = pelanggan::find($id);

        if (is_null($user)) {
            return response([
                'message' => 'Pelanggan tidak ditemukan'
            ], 400);
        }

        $user->status = false;


        if ($user->save()) {
            return response([
                'message' => 'Pelanggan Dinonaktifkan',
                'data' => $user
            ]);
        }
    }

    public function editPelanggan(Request $request, $id)
    {
        $data = $request->all();

        $validate = Validator::make($data, [
            'name' => 'required',

        ]);

        if ($validate->fails())
            return response([
                'message' => $validate->errors()
            ], 402);

        $user = pelanggan::find($id);
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->noTelp = $data['noTelp'];


        if ($user->save()) {
            return response([
                'message' => 'Edit Customer Success',
                'user' => $user
            ]);
        }

        return response([
            'message' => 'Edit Customer Failed',
        ], 403);
    }

    public function getAllPelanggan()
    {
        $data = pelanggan::where('status', true)->get();

        if (count($data) < 1) {
            return response([
                'message' => 'Pelanggan is Empty'
            ], 400);
        }

        return response([
            'message' => 'Retrieve all user success',
            'data' => $data
        ]);
    }
}
