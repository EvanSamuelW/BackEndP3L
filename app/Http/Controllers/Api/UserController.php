<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\jabatan;
use Validator;

class UserController extends Controller
{
    public function register(Request $request){
        $data = $request->all();

        $validate = Validator::make($data,[
            'name' => 'required',
            'email' => 'required',
            'password' => 'required',
            'jenisKelamin' => 'required',
            'noTelp' => 'required',
            'jabatan_id' => 'required',
            'tglGabung' => 'required',
            
        ]);

        if($validate->fails()){
            return response([
                'message' => $validate->errors()
            ],402);
        }

        $data['password'] = bcrypt($request->password);
        $data['is_active'] = true;
        $user = User::create($data);
       

        return response([
            'message' => 'Karyawan Terdaftar',
            'data' => $user,
        ],200);
    }

    public function getUser(){
        if(Auth::check()){
            return response([
                'data' => Auth::user()
            ],200);
        }
    }

    public function login(Request $request){
        $data = $request->all();

        $validate = Validator::make($data,[
            'email' => 'required',
            'password' => 'required'
        ]);

        if($validate->fails() ){
            return response([
                'message' => $validate->errors()
            ],400);
        }


        Auth::attempt($data);

        if(!Auth::check()){
            return response([
                'message' => 'Login Gagal'
            ],401);
        }

        $user = Auth::user();

        if(!$user->is_active)
        {
            return response([
                'message' => 'user sudah tidak aktif'
            ],401);
        }

     

        $token = $user->createToken('Authentication Token')->accessToken;

        return response([
            'message' => 'Authenticated',
            'data' => $user,
            'token' => $token
        ],200);
    }

    public function logout(Request $request){

        Auth::user()->token()->revoke();
            return response([
                'message' => 'Log Out Succeeded'
            ],200);
        
    }

    public function deleteUser($id){
        $user = User::find($id);

        if(is_null($user)){
            return response([
                'message' => 'User is not Found'
            ],400);
        }

        $user->is_active=false;


        if($user->save()){
            return response([
                'message' => 'Karyawan Dinonaktifkan',
                'data' => $user
            ]);
        }
       
    }

    public function enableUser($id){
        $user = User::find($id);

        if(is_null($user)){
            return response([
                'message' => 'User is not Found'
            ],400);
        }

        $user->is_active=true;


        if($user->save()){
            return response([
                'message' => 'Karyawan Dinonaktifkan',
                'data' => $user
            ]);
        }
       
    }

    public function editUser(Request $request , $id){
        $data = $request->all();

        $validate = Validator::make($data,[
            'name' => 'required',
            'email' => 'required',
            'jenisKelamin' => 'required',
            'noTelp' => 'required',
            'jabatan_id' => 'required',
            'tglGabung' => 'required',
        ]);

        if($validate->fails())
            return response([
                'message' => $validate->errors()
            ],402);

        $user = User::find($id);
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->jenisKelamin = $data['jenisKelamin'];
        $user->noTelp = $data['noTelp'];
        $user->jabatan_id = $data['jabatan_id'];
        $user->tglGabung = $data['tglGabung'];

        if($user->save()){
            return response([
                'message' => 'Edit User Success',
                'user' => $user
            ]);
        }

        return response([
            'message' => 'Edit User Failed',
        ],403);
    }


    public function getAllUser(){
        $data = User::all();

        if(count($data) < 1 ){
            return response([
                'message' => 'User is Empty'
            ],400);
        }
        
         $data->transform(function ($item) {
            $jabatan = jabatan::find($item['jabatan_id']);
            $item->jabatan = $jabatan->nama_jabatan;

            return $item;
        });
        
        return response([
            'message' => 'Retrieve all user success',
            'data' => $data
        ]);
    }

    public function getJabatan(){
        $data = jabatan::all();

        if(count($data) < 0 ){
            return response([
                'message' => 'Jabatan is Empty'
            ],400);
        }

        return response([
            'message' => 'Retrieve all jabatan success',
            'data' => $data
        ]);
    }


}
