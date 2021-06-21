<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\reservasi;
use App\meja;
use App\User;
use App\transaksi;
use App\Pelanggan;
use Validator;
use Carbon\Carbon;
use Illuminate\Support\Str;


class ReservasiController extends Controller
{
    public function create(Request $request)
    {
        $data = $request->all();

        $validate = Validator::make($data, [
            'id_meja' => 'required',
            'id_pelanggan' => 'required',
            'id_karyawan' => 'required',
            'tanggal' => 'required',
            'sesi' => 'required',
        ]);

        if ($validate->fails()) {
            return response([
                'message' => $validate->errors()
            ], 402);
        }

        $data['deleted'] = false;
        $data['qrcode'] = Str::random(30);

        $pelanggan = Pelanggan::findOrFail($data['id_pelanggan']);

        $transaction = Transaksi::create([
            'nama_pelanggan' => $pelanggan->name,
            'total' => 0,
            'status' => 'Not Paid'
        ]);

        $data['id_transaksi'] = $transaction->id;
        $reservasi = Reservasi::create($data);
        $reservasi->status = 'Reserved';
        $reservasi->save();

        return response([
            'message' => 'Reservasi dibuat',
            'data' => $reservasi,
        ], 200);
    }

    public function deleteReservasi($id)
    {
        $user = Reservasi::find($id);

        if (is_null($user)) {
            return response([
                'message' => 'Reservasi tidak ditemukan'
            ], 400);
        }

        $user->deleted = true;
        if ($user->save()) {
            return response([
                'message' => 'Reservasi dibatalkan',
                'data' => $user
            ]);
        }
    }

    public function editReservasi(Request $request, $id)
    {
        $data = $request->all();

        $validate = Validator::make($data, [
            'id_meja' => 'required',
            'tanggal' => 'required',
            'sesi' => 'required',
        ]);

        if ($validate->fails())
            return response([
                'message' => $validate->errors()
            ], 402);

        $user = Reservasi::find($id);
        $user->id_meja = $data['id_meja'];
        $user->tanggal = $data['tanggal'];
        $user->sesi = $data['sesi'];
        if ($user->save()) {
            return response([
                'message' => 'Edit Reservasi Success',
                'user' => $user
            ]);
        }

        return response([
            'message' => 'Edit Reservasi Gagal',
        ], 403);
    }

    public function getAllReservasi()
    {

        $data = Reservasi::where('deleted', false)->orderBy('id', 'DESC')->get();

        if (count($data) < 1) {
            return response([
                'message' => 'Reservasi is Empty'
            ], 400);
        }

        $data->transform(function ($item) {
            $pelanggan = pelanggan::find($item['id_pelanggan']);
            $user = user::find($item['id_karyawan']);
            $meja = meja::find($item['id_meja']);
            $item->pelanggan = $pelanggan->name;
            $item->meja = $meja->kode;
            $item->karyawan = $user->name;

            return $item;
        });

        return response([
            'message' => 'Retrieve all Reservasi success',
            'data' => $data
        ]);
    }
    public function printQR($id, $user)
    {
        $data = Reservasi::find($id);
        $customPaper = array(0, 0, 510.00, 340.80);
        $pdf = \App::make('dompdf.wrapper')->setPaper($customPaper, 'landscape');
        $mytime = Carbon::now();
        $pdf->loadView('welcome', [
            'data' => $data,
            'mytime' => $mytime,
            'user' => $user,
        ]);

        return $pdf->stream();
    }

    public function scanQR(Request $request)
    {
        $user = Reservasi::where('qrcode', $request->qrcode)->get();



        if (count($user) < 1) {
            return response([
                'message' => 'Reservasi is Empty'
            ], 400);
        }
        $date = strtotime($user->first()->tanggal);
        $now = strtotime(date("Y-m-d"));

        $datediff = $date - $now;
        $difference = floor($datediff / (60 * 60 * 24));



        if ($difference == 0) {
            $now =  Carbon::now();

            $startLunch = Carbon::createFromTimeString('11:00');
            $endLunch = Carbon::createFromTimeString('16:00');
            $startDinner = Carbon::createFromTimeString('17:00');
            $endDinner = Carbon::createFromTimeString('21:00');

            if ($now->between($startLunch, $endLunch) && $user->first()->sesi == "Lunch" ||  $now->between($startDinner, $endDinner) && $user->first()->sesi == "Dinner") {

                if ($user->first()->status == 'Reserved' || $user->first()->status == 'Not Paid') {
                    $meja = meja::find($user->first()->id_meja);
                    $meja->available = false;
                    $user->first()->status = 'Not Paid';
                    $user->first()->save();
                    if ($meja->save()) {
                        return response([
                            'message' => 'Reservasi ditemukan',
                            'data' => $user
                        ]);
                    }
                }
            } else {
                return response([
                    'message' => 'Jam Sesi tidak sesuai'
                ], 400);
            }
        } else {
            return response([
                'message' => 'Tanggal Reservasi tidak sesuai'
            ], 400);
        }
    }
}
