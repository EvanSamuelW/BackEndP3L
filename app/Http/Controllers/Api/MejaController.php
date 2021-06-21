<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Meja;
use Validator;

class MejaController extends Controller
{
    public function create(Request $request)
    {
        $data = $request->all();

        $validate = Validator::make($data, [
            'kode' => 'required',
        ]);

        if ($validate->fails()) {
            return response([
                'message' => $validate->errors()
            ], 402);
        }

        $data['available'] = true;
        $data['deleted'] = false;
    
        $user = Meja::create($data);

        return response([
            'message' => 'Meja Terdaftar',
            'data' => $user,
        ], 200);
    }

    public function deleteMeja($id)
    {
        $user = Meja::find($id);

        if (is_null($user)) {
            return response([
                'message' => 'Meja tidak ditemukan'
            ], 400);
        }

        $user->deleted = true;
        if ($user->save()) {
            return response([
                'message' => 'Meja dihapus',
                'data' => $user
            ]);
        }
    }

    public function editMeja(Request $request, $id)
    {
        $data = $request->all();

        $validate = Validator::make($data, [
            'kode' => 'required',
        ]);

        if ($validate->fails())
            return response([
                'message' => $validate->errors()
            ], 402);

        $user = Meja::find($id);
        $user->kode = $data['kode'];
        if ($user->save()) {
            return response([
                'message' => 'Edit Meja Success',
                'user' => $user
            ]);
        }

        return response([
            'message' => 'Edit Customer Failed',
        ], 403);
    }

    public function getAllMeja()
    {
        $data = Meja::where('deleted', false)->get();

        if (count($data) < 1) {
            return response([
                'message' => 'Meja is Empty'
            ], 400);
        }

        return response([
            'message' => 'Retrieve all meja success',
            'data' => $data
        ]);
    }
}
