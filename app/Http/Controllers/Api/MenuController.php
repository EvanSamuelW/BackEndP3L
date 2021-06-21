<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\menu;
use App\bahan;
use Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class MenuController extends Controller
{
    public function create(Request $request)
    {
        $data = $request->all();

        $validate = Validator::make($data, [
            'nama_menu' => 'required',
            'deskripsi' => 'required',
            'tipe' => 'required',
            'id_bahan' => 'required',
            'gambar' => 'required',
            'satuan' => 'required',
            'serving' => 'required',
            'harga' => 'required',
        ]);

        if ($validate->fails()) {
            return response([
                'message' => $validate->errors()
            ], 402);
        
        }

        $data['deleted'] = false;
        
        $exploded = explode(',',$request->gambar);
        $decoded = base64_decode($exploded[1]);

        $fileName = Str::random(40).'.'.'jpg';
        $path = public_path().'/menu/'.$fileName;

        if(!file_put_contents($path,$decoded)){
            return response([
                'message' => 'Insert Data Failed'
            ],400);
        }

        $data['gambar'] = $fileName;
        $user = Menu::create($data);

        return response([
            'message' => 'Menu Terdaftar',
            'data' => $user,
        ], 200);
    }

    public function deleteMenu($id)
    {
        $user = Menu::find($id);

        if (is_null($user)) {
            return response([
                'message' => 'Menu tidak ditemukan'
            ], 400);
        }

        $user->deleted = true;
        if ($user->save()) {
            File::delete(public_path().'/menu/'.$user->gambar);
            return response([
                'message' => 'Delete Menu Success',
                'data' => $user
            ],200);
        }

        return response([
            'message' => 'Delete Menu Gagal'
        ],401);
    }

    public function editMenu(Request $request, $id)
    {
        
        $user = Menu::find($id);

        $data = $request->all();

        $validate = Validator::make($data, [
            'nama_menu' => 'required',
            'deskripsi' => 'required',
            'tipe' => 'required',
            'id_bahan' => 'required',
            'satuan' => 'required',
            'serving' => 'required',
            'harga' => 'required',
        ]);

        if ($validate->fails())
        return response([
            'message' => $validate->errors()
        ], 402);

        $user->nama_menu = $data['nama_menu'];
        $user->deskripsi = $data['deskripsi'];
        $user->tipe = $data['tipe'];
        $user->id_bahan = $data['id_bahan'];
        $user->satuan = $data['satuan'];
        $user->harga = $data['harga'];
        $user->serving = $data['serving'];
        
        if(isset($data['gambar']) && !is_null($data['gambar'])){
            $exploded = explode(',',$data['gambar']);
            $decoded = base64_decode($exploded[1]);
            $fileName = Str::random(40).'.jpg';
            $path = public_path().'/menu/'.$fileName;
            file_put_contents($path,$decoded);

            $user->gambar = $fileName;
        }

        if($user->save()){
            return response([
                'message' => 'Data menu berhasil diubah',
                'data' => $user
            ]);
        }
    }

    public function getAllMenu()
    {
        $data = Menu::where('deleted', false)->get();

        if (count($data) < 1) {
            return response([
                'message' => 'Menu is Empty'
            ], 400);
        }
        
        
        $data->transform(function ($item) {
            $bahan = Bahan::where('id', $item['id_bahan'])->get()->first();

            $item->stok = floor($bahan->stok/$item['serving']);
            return $item;
        });
        return response([
            'message' => 'Retrieve all menu success',
            'data' => $data
        ]);
    }
}
