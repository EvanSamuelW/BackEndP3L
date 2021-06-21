<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\stok_masuk;
use App\stok_keluar;
use App\bahan;
use App\menu;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Validator;




class StokMasukController extends Controller
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
        $data['subtotal'] = $user->harga * $request->jumlah;
        $data['deleted'] = false;
        $data['unit'] = $user->unit;
        $bahan = stok_masuk::create($data);
        $user->stok += $bahan->jumlah;
        if ($user->save()) {
            return response([
                'message' => 'Stok Masuk Ditambahkan',
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
        $stokMasuk = stok_masuk::find($request->id);
        $created = new Carbon($stokMasuk->created_at);
        if ($created->diff(Carbon::now())->days < 1) {
            $user->stok -= $stokMasuk->jumlah;
            $stokMasuk->subtotal = 0;
            $stokMasuk->subtotal = $user->harga * $request->jumlah;
            $user->stok +=  $data['jumlah'];
        }

        $stokMasuk->jumlah = $data['jumlah'];
        if ($stokMasuk->save() && $user->save()) {
            return response([
                'message' => 'Stok Masuk berhasil diubah',
                'user' => $stokMasuk
            ]);
        }
    }

    public function getAllStokMasuk()
    {
        $data = stok_masuk::where('deleted', false)->get();

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
            'message' => 'Retrieve all stok masuk success',
            'data' => $data
        ]);
    }

    public function deleteStokMasuk($id)
    {
        $user = stok_masuk::find($id);


        if (is_null($user)) {
            return response([
                'message' => 'Stok Masuk tidak ditemukan'
            ], 400);
        }

        $user2 = bahan::find($user->id_bahan);
        $user2->stok -= $user->jumlah;

        if ($user->delete() && $user2->save()) {
            return response([
                'message' => 'Reservasi dibatalkan',
                'data' => $user
            ]);
        }
    }

    public function laporanPengeluaran1($year, $user)
    {
        $users1 = DB::table('stok_masuks')
            ->join('bahans', 'stok_masuks.id_bahan', '=', 'bahans.id')
            ->join('menus', 'menus.id_bahan', '=', 'bahans.id')
            ->select(DB::raw('SUM(stok_masuks.subtotal) as Makanan_Utama'), DB::raw('stok_masuks.created_at'))
            ->where('menus.tipe', '=', 'Makanan Utama')
            ->where('stok_masuks.deleted', '=', 0)
            ->whereYear('stok_masuks.created_at', $year)
            ->groupBy(DB::raw('stok_masuks.created_at'))
            ->get()->groupBy(function ($d) {
                return Carbon::parse($d->created_at)->format('m');
            });

        $users2 = DB::table('stok_masuks')
            ->join('bahans', 'stok_masuks.id_bahan', '=', 'bahans.id')
            ->join('menus', 'menus.id_bahan', '=', 'bahans.id')
            ->select(DB::raw('SUM(stok_masuks.subtotal) as Minuman'), DB::raw('stok_masuks.created_at'))
            ->where('menus.tipe', '=', 'Minuman')
            ->where('stok_masuks.deleted', '=', 0)
            ->whereYear('stok_masuks.created_at', $year)
            ->groupBy(DB::raw('stok_masuks.created_at'))
            ->get()->groupBy(function ($d) {
                return Carbon::parse($d->created_at)->format('m');
            });


        $users3 = DB::table('stok_masuks')
            ->join('bahans', 'stok_masuks.id_bahan', '=', 'bahans.id')
            ->join('menus', 'menus.id_bahan', '=', 'bahans.id')
            ->select(DB::raw('SUM(stok_masuks.subtotal) as Side_dish'), DB::raw('stok_masuks.created_at'))
            ->where('menus.tipe', '=', 'Side Dish')
            ->where('stok_masuks.deleted', '=', 0)
            ->whereYear('stok_masuks.created_at', $year)
            ->groupBy(DB::raw('stok_masuks.created_at'))
            ->get()->groupBy(function ($d) {
                return Carbon::parse($d->created_at)->format('m');
            });


        $usermcount1 = [];
        $usermcount2 = [];
        $usermcount3 = [];
        $userArr1 = [];
        $userArr2 = [];
        $userArr3 = [];


        foreach ($users1 as $key => $value) {
            $totalMakanan = 0;
            foreach ($value as $item) {
                $totalMakanan += $item->Makanan_Utama;
            }
            $usermcount1[(int)$key] = $totalMakanan;
        }

        for ($i = 1; $i <= 12; $i++) {
            if (!empty($usermcount1[$i])) {
                $userArr1[$i] = $usermcount1[$i];
            } else {
                $userArr1[$i] = 0;
            }
        }

        foreach ($users2 as $key => $value) {
            $totalMakanan = 0;
            foreach ($value as $item) {
                $totalMakanan += $item->Minuman;
            }
            $usermcount2[(int)$key] = $totalMakanan;
        }

        for ($i = 1; $i <= 12; $i++) {
            if (!empty($usermcount2[$i])) {
                $userArr2[$i] = $usermcount2[$i];
            } else {
                $userArr2[$i] = 0;
            }
        }


        foreach ($users3 as $key => $value) {
            $totalMakanan = 0;
            foreach ($value as $item) {
                $totalMakanan += $item->Side_dish;
            }
            $usermcount3[(int)$key] = $totalMakanan;
        }

        for ($i = 1; $i <= 12; $i++) {
            if (!empty($usermcount3[$i])) {
                $userArr3[$i] = $usermcount3[$i];
            } else {
                $userArr3[$i] = 0;
            }
        }

        $customPaper = array(0, 0, 530.00, 500.80);

        $pdf = \App::make('dompdf.wrapper')->setPaper($customPaper, 'landscape');


        $myArray = array(
            $userArr1,
            $userArr2,
            $userArr3
        );

        $pdf->loadView('laporanpengeluaran1', [
            'myArray' => $myArray,
            'user' => $user,
            'year' => $year
        ]);

        return $pdf->stream();
    }

    public function laporanPengeluaran2($year1, $year2, $user)
    {
        $users1 = DB::table('stok_masuks')
            ->join('bahans', 'stok_masuks.id_bahan', '=', 'bahans.id')
            ->join('menus', 'menus.id_bahan', '=', 'bahans.id')
            ->select(DB::raw('SUM(stok_masuks.subtotal) as Makanan_Utama'), DB::raw('stok_masuks.created_at'))
            ->where('menus.tipe', '=', 'Makanan Utama')
            ->where('stok_masuks.deleted', '=', 0)
            ->whereBetween(DB::raw('YEAR(stok_masuks.created_at)'), [$year1, $year2])
            ->groupBy(DB::raw('stok_masuks.created_at'))
            ->get()->groupBy(function ($d) {
                return Carbon::parse($d->created_at)->format('Y'); // 
            });

        $users2 = DB::table('stok_masuks')
            ->join('bahans', 'stok_masuks.id_bahan', '=', 'bahans.id')
            ->join('menus', 'menus.id_bahan', '=', 'bahans.id')
            ->select(DB::raw('SUM(stok_masuks.subtotal) as Minuman'), DB::raw('stok_masuks.created_at'))
            ->where('menus.tipe', '=', 'Minuman')
            ->where('stok_masuks.deleted', '=', 0)
            ->whereBetween(DB::raw('YEAR(stok_masuks.created_at)'), [$year1, $year2])
            ->groupBy(DB::raw('stok_masuks.created_at'))
            ->get()->groupBy(function ($d) {
                return Carbon::parse($d->created_at)->format('Y'); // 
            });


        $users3 = DB::table('stok_masuks')
            ->join('bahans', 'stok_masuks.id_bahan', '=', 'bahans.id')
            ->join('menus', 'menus.id_bahan', '=', 'bahans.id')
            ->select(DB::raw('SUM(stok_masuks.subtotal) as Side_Dish'), DB::raw('stok_masuks.created_at'))
            ->where('menus.tipe', '=', 'Side Dish')
            ->where('stok_masuks.deleted', '=', 0)
            ->whereBetween(DB::raw('YEAR(stok_masuks.created_at)'), [$year1, $year2])
            ->groupBy(DB::raw('stok_masuks.created_at'))
            ->get()->groupBy(function ($d) {
                return Carbon::parse($d->created_at)->format('Y'); // 
            });
        $usermcount1 = [];
        $usermcount2 = [];
        $usermcount3 = [];
        $userArr1 = [];
        $userArr2 = [];
        $userArr3 = [];


        foreach ($users1 as $key => $value) {
            $totalMakanan = 0;
            foreach ($value as $item) {
                $totalMakanan += $item->Makanan_Utama;
            }
            $usermcount1[(int)$key] = $totalMakanan;
        }

        for ($i = $year1; $i <= $year2; $i++) {
            if (!empty($usermcount1[$i])) {
                $userArr1[$i] = $usermcount1[$i];
            } else {
                $userArr1[$i] = 0;
            }
        }

        foreach ($users2 as $key => $value) {
            $totalMakanan = 0;
            foreach ($value as $item) {
                $totalMakanan += $item->Minuman;
            }
            $usermcount2[(int)$key] = $totalMakanan;
        }

        for ($i = $year1; $i <= $year2; $i++) {
            if (!empty($usermcount2[$i])) {
                $userArr2[$i] = $usermcount2[$i];
            } else {
                $userArr2[$i] = 0;
            }
        }


        foreach ($users3 as $key => $value) {
            $totalMakanan = 0;
            foreach ($value as $item) {
                $totalMakanan += $item->Side_Dish;
            }
            $usermcount3[(int)$key] = $totalMakanan;
        }

        for ($i = $year1; $i <= $year2; $i++) {
            if (!empty($usermcount3[$i])) {
                $userArr3[$i] = $usermcount3[$i];
            } else {
                $userArr3[$i] = 0;
            }
        }

        $customPaper = array(0, 0, 530.00, 500.80);

        $pdf = \App::make('dompdf.wrapper')->setPaper($customPaper, 'landscape');


        $myArray = array(
            $userArr1,
            $userArr2,
            $userArr3
        );

        $pdf->loadView('laporanpengeluaran2', [
            'myArray' => $myArray,
            'user' => $user,
            'year1' => $year1,
            'year2' => $year2
        ]);

        return $pdf->stream();
    }


    public function laporanstok1($date1, $date2, $user)
    {


        $date1 = Carbon::createFromFormat('d-m-Y', $date1)->format('Y-m-d');
        $date2 = Carbon::createFromFormat('d-m-Y', $date2)->format('Y-m-d');


        $makananRemaining = DB::table('menus')
            ->leftjoin('stok_keluars', 'menus.id_bahan', '=', 'stok_keluars.id_bahan')
            ->select(DB::raw('menus.nama_menu'),  DB::raw('SUM(stok_keluars.jumlah) as Remaining_Stok'))
            ->where('stok_keluars.keterangan', '<>', 'Waste Stok')
            ->where('menus.tipe', '=', 'Makanan Utama')
            ->where('menus.deleted', '=', 0)
            ->WhereBetween(DB::raw('DATE(stok_keluars.addedDate)'), [$date1, $date2])
            ->groupBy(DB::raw('menus.nama_menu'))
            ->get();


        $makananIncoming = DB::table('menus')
            ->leftjoin('stok_masuks', 'stok_masuks.id_bahan', '=', 'menus.id_bahan')
            ->select(DB::raw('SUM(stok_masuks.jumlah) as Incoming_stok'), DB::raw('menus.nama_menu'))
            ->where('menus.tipe', '=', 'Makanan Utama')
            ->where('menus.deleted', '=', 0)
            ->where('stok_masuks.deleted', '=', 0)
            ->whereBetween(DB::raw('DATE(stok_masuks.addedDate)'), [$date1, $date2])
            ->groupBy(DB::raw('menus.nama_menu'))
            ->get();



        $makananWaste = DB::table('menus')
            ->leftjoin('stok_keluars', 'menus.id_bahan', '=', 'stok_keluars.id_bahan')
            ->select(DB::raw('SUM(stok_keluars.jumlah) as Waste_Stok'), DB::raw('menus.nama_menu'))
            ->where('stok_keluars.keterangan', '=', 'Waste Stok')
            ->where('menus.tipe', '=', 'Makanan Utama')
            ->where('menus.deleted', '=', 0)
            ->whereBetween(DB::raw('DATE(stok_keluars.addedDate)'), [$date1, $date2])
            ->groupBy(DB::raw('menus.nama_menu'))
            ->get();


        $minumanRemaining = DB::table('menus')
            ->leftjoin('stok_keluars', 'menus.id_bahan', '=', 'stok_keluars.id_bahan')
            ->select(DB::raw('menus.nama_menu'),  DB::raw('SUM(stok_keluars.jumlah) as Remaining_Stok'))
            ->where('stok_keluars.keterangan', '<>', 'Waste Stok')
            ->where('menus.tipe', '=', 'Minuman')
            ->where('menus.deleted', '=', 0)
            ->WhereBetween(DB::raw('DATE(stok_keluars.addedDate)'), [$date1, $date2])
            ->groupBy(DB::raw('menus.nama_menu'))
            ->get();


        $minumanIncoming = DB::table('menus')
            ->leftjoin('stok_masuks', 'stok_masuks.id_bahan', '=', 'menus.id_bahan')
            ->select(DB::raw('SUM(stok_masuks.jumlah) as Incoming_stok'), DB::raw('menus.nama_menu'))
            ->where('menus.tipe', '=', 'Minuman')
            ->where('menus.deleted', '=', 0)
            ->where('stok_masuks.deleted', '=', 0)
            ->whereBetween(DB::raw('DATE(stok_masuks.addedDate)'), [$date1, $date2])
            ->groupBy(DB::raw('menus.nama_menu'))
            ->get();



        $minumanWaste = DB::table('menus')
            ->leftjoin('stok_keluars', 'menus.id_bahan', '=', 'stok_keluars.id_bahan')
            ->select(DB::raw('SUM(stok_keluars.jumlah) as Waste_Stok'), DB::raw('menus.nama_menu'))
            ->where('stok_keluars.keterangan', '=', 'Waste Stok')
            ->where('menus.tipe', '=', 'Minuman')
            ->where('menus.deleted', '=', 0)
            ->whereBetween(DB::raw('DATE(stok_keluars.addedDate)'), [$date1, $date2])
            ->groupBy(DB::raw('menus.nama_menu'))
            ->get();



        $sideRemaining = DB::table('menus')
            ->leftjoin('stok_keluars', 'menus.id_bahan', '=', 'stok_keluars.id_bahan')
            ->select(DB::raw('menus.nama_menu'),  DB::raw('SUM(stok_keluars.jumlah) as Remaining_Stok'))
            ->where('stok_keluars.keterangan', '<>', 'Waste Stok')
            ->where('menus.tipe', '=', 'Side Dish')
            ->where('menus.deleted', '=', 0)
            ->WhereBetween(DB::raw('DATE(stok_keluars.addedDate)'), [$date1, $date2])
            ->groupBy(DB::raw('menus.nama_menu'))
            ->get();


        $sideIncoming = DB::table('menus')
            ->leftjoin('stok_masuks', 'stok_masuks.id_bahan', '=', 'menus.id_bahan')
            ->select(DB::raw('SUM(stok_masuks.jumlah) as Incoming_stok'), DB::raw('menus.nama_menu'))
            ->where('menus.tipe', '=', 'Side Dish')
            ->where('menus.deleted', '=', 0)
            ->where('stok_masuks.deleted', '=', 0)
            ->whereBetween(DB::raw('DATE(stok_masuks.addedDate)'), [$date1, $date2])
            ->groupBy(DB::raw('menus.nama_menu'))
            ->get();



        $sideWaste = DB::table('menus')
            ->leftjoin('stok_keluars', 'menus.id_bahan', '=', 'stok_keluars.id_bahan')
            ->select(DB::raw('SUM(stok_keluars.jumlah) as Waste_Stok'), DB::raw('menus.nama_menu'))
            ->where('stok_keluars.keterangan', '=', 'Waste Stok')
            ->where('menus.tipe', '=', 'Side Dish')
            ->where('menus.deleted', '=', 0)
            ->whereBetween(DB::raw('DATE(stok_keluars.addedDate)'), [$date1, $date2])
            ->groupBy(DB::raw('menus.nama_menu'))
            ->get();



        $menuMakanan =  menu::where([
            ['deleted', 0],
            ['tipe', 'Makanan Utama'],
        ])->get();

        $menuMinuman =  menu::where([
            ['deleted', 0],
            ['tipe', 'Minuman'],
        ])->get();

        $menuSide =  menu::where([
            ['deleted', 0],
            ['tipe', 'Side Dish'],
        ])->get();

        $myArray = array(
            $makananIncoming,
            $makananWaste,
            $makananRemaining,
            $minumanIncoming,
            $minumanWaste,
            $minumanRemaining,
            $sideIncoming,
            $sideWaste,
            $sideRemaining
        );

        $date1 = Carbon::createFromFormat('Y-m-d', $date1)->format('d M Y');
        $date2 = Carbon::createFromFormat('Y-m-d', $date2)->format('d M Y');

        $customPaper = array(0, 0, 620.00, 550.80);


        $pdf = \App::make('dompdf.wrapper')->setPaper($customPaper, 'landscape');

        $pdf->loadView('laporanstok1', [
            'myArray' => $myArray,
            'menuMakanan' => $menuMakanan,
            'menuMinuman' => $menuMinuman,
            'menuSide' => $menuSide,
            'user' => $user,
            'date1' => $date1,
            'date2' => $date2
        ]);

        return $pdf->stream();

        // return response([
        //     'message' => 'Retrieve all stok masuk success',
        //     'data1' => $myArray,
        // ]);
    }


    public function laporanstok2($year, $month, $id, $user)
    {
        $menu = menu::find($id);
        $bahan = bahan::find($menu->id_bahan);

        $menuRemaining = DB::table('menus')
            ->leftjoin('stok_keluars', 'menus.id_bahan', '=', 'stok_keluars.id_bahan')
            ->select(DB::raw('stok_keluars.addedDate'),  DB::raw('SUM(stok_keluars.jumlah) as Remaining_Stok'))
            ->where('stok_keluars.keterangan', '<>', 'Waste Stok')
            ->where('menus.nama_menu', '=', $menu->nama_menu)
            ->where('menus.deleted', '=', 0)
            ->whereYear('stok_keluars.addedDate', $year)
            ->whereMonth('stok_keluars.addedDate', $month)
            ->groupBy(DB::raw('stok_keluars.addedDate'))
            ->get()->groupBy(function ($d) {
                return Carbon::parse($d->addedDate)->format('d'); // 
            });

        $menuIncoming = DB::table('menus')
            ->leftjoin('stok_masuks', 'stok_masuks.id_bahan', '=', 'menus.id_bahan')
            ->select(DB::raw('SUM(stok_masuks.jumlah) as Incoming_stok'), DB::raw('stok_masuks.created_at'))
            ->where('menus.nama_menu', '=', $menu->nama_menu)
            ->where('menus.deleted', '=', 0)
            ->whereYear('stok_masuks.created_at', $year)
            ->whereMonth('stok_masuks.created_at', $month)
            ->where('stok_masuks.deleted', '=', 0)
            ->groupBy(DB::raw('stok_masuks.created_at'))
            ->get()->groupBy(function ($d) {
                return Carbon::parse($d->created_at)->format('d'); // 
            });



        $menuWaste = DB::table('menus')
            ->leftjoin('stok_keluars', 'menus.id_bahan', '=', 'stok_keluars.id_bahan')
            ->select(DB::raw('SUM(stok_keluars.jumlah) as Waste_Stok'), DB::raw('stok_keluars.addedDate'))
            ->where('stok_keluars.keterangan', '=', 'Waste Stok')
            ->where('menus.nama_menu', '=', $menu->nama_menu)
            ->where('menus.deleted', '=', 0)
            ->whereYear('stok_keluars.addedDate', $year)
            ->whereMonth('stok_keluars.addedDate', $month)
            ->groupBy(DB::raw('stok_keluars.addedDate'))
            ->get()->groupBy(function ($d) {
                return Carbon::parse($d->addedDate)->format('d'); // 
            });

        $usermcount1 = [];
        $usermcount2 = [];
        $usermcount3 = [];
        $userArr1 = [];
        $userArr2 = [];
        $userArr2 = [];

        foreach ($menuRemaining as $key => $value) {
            $totalMakanan = 0;
            foreach ($value as $item) {
                $totalMakanan += $item->Remaining_Stok;
            }
            $usermcount1[(int)$key] = $totalMakanan;
        }

        $number = cal_days_in_month(CAL_GREGORIAN, $month, $year);

        for ($i = 1; $i <= $number; $i++) {
            if (!empty($usermcount1[$i])) {
                $userArr1[$i] = $usermcount1[$i];
            } else {
                $userArr1[$i] = 0;
            }
        }

        foreach ($menuIncoming as $key => $value) {
            $totalMakanan = 0;
            foreach ($value as $item) {
                $totalMakanan += $item->Incoming_stok;
            }
            $usermcount2[(int)$key] = $totalMakanan;
        }


        for ($i = 1; $i <= $number; $i++) {
            if (!empty($usermcount2[$i])) {
                $userArr2[$i] = $usermcount2[$i];
            } else {
                $userArr2[$i] = 0;
            }
        }



        foreach ($menuWaste as $key => $value) {
            $totalMakanan = 0;
            foreach ($value as $item) {
                $totalMakanan += $item->Waste_Stok;
            }
            $usermcount3[(int)$key] = $totalMakanan;
        }


        for ($i = 1; $i <= $number; $i++) {
            if (!empty($usermcount3[$i])) {
                $userArr3[$i] = $usermcount3[$i];
            } else {
                $userArr3[$i] = 0;
            }
        }

        $myArray = array(
            $userArr2,
            $userArr1,
            $userArr3,
        );

        $month_name = date("F", mktime(0, 0, 0, $month, 10));
        $customPaper = array(0, 0, 920.00, 600.80);

        $pdf = \App::make('dompdf.wrapper')->setPaper($customPaper, 'landscape');

        $pdf->loadView('laporanstok2', [
            'myArray' => $myArray,
            'nama_menu' => $menu->nama_menu,
            'satuan' => $bahan->unit,
            'number' => $number,
            'month' => $month_name,
            'year' => $year,
            'user' => $user
        ]);

        return $pdf->stream();
    }

    public function getYear()
    {
        $menuRemaining = DB::table('stok_masuks')
            ->select(DB::raw('YEAR(created_at) as tahun'))
            ->groupBy(DB::raw('YEAR(created_at)'))
            ->get();
        return response([
            'message' => 'Retrieve all stok masuk success',
            'data' => $menuRemaining,
        ]);
    }
}
