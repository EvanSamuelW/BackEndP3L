<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\order;
use Illuminate\Http\Request;
use App\menu;
use App\bahan;
use App\transaksi;
use App\reservasi;
use App\pelanggan;
use App\stok_keluar;
use Carbon\Carbon;
use Validator;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function create(Request $request)
    {
        $data = $request->all();

        $validate = Validator::make($data, [
            'id_menu' => 'required',
            'id_transaksi' => 'required',
            'jumlah' => 'required',
        ]);

        if ($validate->fails()) {
            return response([
                'message' => $validate->errors()
            ], 402);
        }

        $data['status'] = 'not paid';
        $transaksi = transaksi::find($request->id_transaksi);
        if ($transaksi->status == 'Paid') {
            return response([
                'message' => 'Transaksi sudah dibayar, tidak bisa menambah pesanan'
            ], 400);
        }
        $order = order::where([
            ['id_transaksi', $request->id_transaksi],
            ['id_menu', $request->id_menu],
            ['status', 'not paid'],
        ])->get()->first();

        if ($order != null) {
            return response([
                'message' => 'Menu sudah ditambahkan'
            ], 401);
        }
        $menu = menu::find($request->id_menu);
        $bahan = bahan::find($menu->id_bahan);
        if ($bahan->stok < $menu->serving * $data['jumlah']) {
            return response([
                'message' => 'Stok Menu tidak mencukupi'
            ], 401);
        }
        $bahan->stok -= $menu->serving * $request->jumlah;
        $bahan->save();
        $data['subtotal'] = $menu->harga * $request->jumlah;
        $user = order::create($data);





        return response([
            'message' => 'Order berhasil ditambahkan',
            'data' => $user,
        ], 200);
    }


    public function editOrder(Request $request, $id)
    {
        $cart = order::find($id);

        if (is_null($cart)) {
            return response([
                'message' => 'Cart is not found'
            ], 400);
        }

        $cartData = $request->all();

        $validate = Validator::make($cartData, [
            'id_menu' => 'required',
            'id_transaksi' => 'required',
            'jumlah' => 'required',
        ]);

        if ($validate->fails()) {
            return response([
                'message' => $validate->errors()
            ], 402);
        }

        $transaksi = transaksi::find($cartData['id_transaksi']);
        if ($transaksi->status == 'Paid') {
            return response([
                'message' => 'Transaksi sudah dibayar, tidak bisa menambah pesanan'
            ], 400);
        }
 

        $menu = menu::find($cartData['id_menu']);
        $bahan = bahan::find($menu->id_bahan);

        $bahan->stok += $cart->jumlah * $menu->serving;
        if ($bahan->stok < $menu->serving * $cartData['jumlah']) {
            return response([
                'message' => 'Stok Menu tidak mencukupi'
            ], 401);
        }

        $bahan->save();
        $bahan->stok -= $menu->serving * $cartData['jumlah'];
        $bahan->save();




        $cart->jumlah = $cartData['jumlah'];
        $cart->subtotal = $menu->harga * $cartData['jumlah'];
        $cart->status = $request->status;
        $cart->save();
        return response([
            'message' => 'Order berhasil diubah',
            'data' => $cartData,
        ], 200);
    }
    public function deleteOrder($id)
    {
        $cart = order::find($id);

        $menu = menu::find($cart->id_menu);
        $bahan = bahan::find($menu->id_bahan);


        $bahan->stok += $menu->serving * $cart->jumlah;


        if ($cart->delete() && $bahan->save()) {
            return response([
                'message' => 'Delete Order Success',
                'data' => $cart
            ], 200);
        }

        return response([
            'message' => 'Delete Order Failed',
            'data' => $cart
        ], 401);
    }



    public function getOrdersbyUser($id)
    {
        $data = order::where([
            ['id_transaksi', $id],
            ['status', 'not paid'],
        ])->get();


        if ($data->count() == 0) {
            return response([
                'message' => 'Order is Empty'
            ]);
        }


        $data->transform(function ($item) {
            $photo = Menu::where('id', $item['id_menu'])->get()->first();

            $item->photo = $photo->gambar;
            $item->harga = $photo->harga;
            $item->nama_menu = $photo->nama_menu;

            return $item;
        });

        $total = order::where([
            ['id_transaksi', $id],
            ['status', 'not paid'],
        ])->sum('subtotal');

        return response([
            'message' => 'Cart Retrieved',
            'data' => $data,
            'total' => $total
        ]);
    }

    public function proceedOrder($id)
    {
        $data = order::where([
            ['id_transaksi', $id],
            ['status', 'not paid'],
        ])->get();

        foreach ($data as $item) {
            $transaksi = transaksi::find($id);
            $transaksi->total += $item->subtotal;
        }



        if ($data->count() == 0) {
            return response([
                'message' => 'Order is Empty'
            ]);
        }

        $data =  DB::table('orders')->where([
            ['id_transaksi', $id],
            ['status', 'not paid'],
        ])->update(array('status' => 'Sedang Dibuat'));


        if ($transaksi->save()) {
            return response([
                'message' => 'Order Is Being Prepared',
                'data' => $data,
            ]);
        }
    }




    public function getOrders()
    {
        $data = order::where('status', 'not paid')->get();


        if ($data->count() == 0) {
            return response([
                'message' => 'Order is Empty',
            ]);
        }


        $data->transform(function ($item) {
            $photo = Menu::where('id', $item['id_menu'])->get()->first();

            $item->photo = $photo->gambar;
            $item->price = $photo->price;

            return $item;
        });



        return response([
            'message' => 'Cart Retrieved',
            'data' => $data,
        ]);
    }

    public function getOrdersOnProcess($id)
    {

        $data = order::where([
            ['id_transaksi', '=', $id],
            ['status', '<>', 'not paid'],
            ['status', '<>', 'Paid']
        ])->get();

        if ($data->count() == 0) {
            return response([
                'message' => 'Order is Empty'
            ]);
        }


        $data->transform(function ($item) {
            $photo = Menu::where('id', $item['id_menu'])->get()->first();

            $item->photo = $photo->gambar;
            $item->nama_menu = $photo->nama_menu;

            return $item;
        });



        return response([
            'message' => 'Cart Retrieved',
            'data' => $data,
        ]);
    }



    public function getOrdersWeb()
    {

        $data = order::where([
            ['status', '<>', 'not paid'],
            ['status', '<>', 'paid']
        ])->get();


        if ($data->count() == 0) {
            return response([
                'message' => 'Order is Empty',
                'data' => $data
            ]);
        }


        $data->transform(function ($item) {
            $photo = Menu::where('id', $item['id_menu'])->get()->first();
            $reservasi =  Reservasi::where('id_transaksi', $item['id_transaksi'])->get()->first();
            $user = pelanggan::where('id', $reservasi->id_pelanggan)->get()->first();
            $item->nama_menu = $photo->nama_menu;
            $item->photo = $photo->photo;
            $item->price = $photo->price;
            $item->nama_pelanggan = $user->name;
            return $item;
        });



        return response([
            'message' => 'Cart Retrieved',
            'data' => $data,
        ]);
    }


    public function laporanPenghasilan1($year, $user)
    {
        $users1 = DB::table('orders')
            ->join('menus', 'orders.id_menu', '=', 'menus.id')
            ->select(DB::raw('SUM(orders.subtotal) as Makanan_Utama'), DB::raw('orders.created_at'))
            ->where('menus.tipe', '=', 'Makanan Utama')
            ->where('orders.status', '=', 'Paid')
            ->whereYear('orders.created_at', $year)
            ->groupBy(DB::raw('orders.created_at'))
            ->get()->groupBy(function ($d) {
                return Carbon::parse($d->created_at)->format('m');
            });

        $users2 = DB::table('orders')
            ->join('menus', 'orders.id_menu', '=', 'menus.id')
            ->select(DB::raw('SUM(orders.subtotal) as Minuman'), DB::raw('orders.created_at'))
            ->where('menus.tipe', '=', 'Minuman')
            ->where('orders.status', '=', 'Paid')
            ->whereYear('orders.created_at', $year)
            ->groupBy(DB::raw('orders.created_at'))
            ->get()->groupBy(function ($d) {
                return Carbon::parse($d->created_at)->format('m');
            });


        $users3 = DB::table('orders')
            ->join('menus', 'orders.id_menu', '=', 'menus.id')
            ->select(DB::raw('SUM(orders.subtotal) as Side_Dish'), DB::raw('orders.created_at'))
            ->where('menus.tipe', '=', 'Side Dish')
            ->where('orders.status', '=', 'Paid')
            ->whereYear('orders.created_at', $year)
            ->groupBy(DB::raw('orders.created_at'))
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
                $totalMakanan += $item->Side_Dish;
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

        $customPaper = array(0, 0, 550.00, 500.80);

        $pdf = \App::make('dompdf.wrapper')->setPaper($customPaper, 'landscape');


        $myArray = array(
            $userArr1,
            $userArr2,
            $userArr3
        );

        $pdf->loadView('laporanpenghasilan1', [
            'myArray' => $myArray,
            'user' => $user,
            'year' => $year
        ]);

        return $pdf->stream();
    }

    public function laporanPenghasilan2($year1, $year2, $user)
    {
        $users1 = DB::table('orders')
            ->join('menus', 'orders.id_menu', '=', 'menus.id')
            ->select(DB::raw('SUM(orders.subtotal) as Makanan_Utama'), DB::raw('orders.created_at'))
            ->where('menus.tipe', '=', 'Makanan Utama')
            ->where('orders.status', '=', 'Paid')
            ->whereBetween(DB::raw('YEAR(orders.created_at)'), [$year1, $year2])
            ->groupBy(DB::raw('orders.created_at'))
            ->get()->groupBy(function ($d) {
                return Carbon::parse($d->created_at)->format('Y');
            });


        $users2 = DB::table('orders')
            ->join('menus', 'orders.id_menu', '=', 'menus.id')
            ->select(DB::raw('SUM(orders.subtotal) as Minuman'), DB::raw('orders.created_at'))
            ->where('menus.tipe', '=', 'Minuman')
            ->where('orders.status', '=', 'Paid')
            ->whereBetween(DB::raw('YEAR(orders.created_at)'), [$year1, $year2])
            ->groupBy(DB::raw('orders.created_at'))
            ->get()->groupBy(function ($d) {
                return Carbon::parse($d->created_at)->format('Y');
            });

        $users3 = DB::table('orders')
            ->join('menus', 'orders.id_menu', '=', 'menus.id')
            ->select(DB::raw('SUM(orders.subtotal) as Side_Dish'), DB::raw('orders.created_at'))
            ->where('menus.tipe', '=', 'Side Dish')
            ->where('orders.status', '=', 'Paid')
            ->whereBetween(DB::raw('YEAR(orders.created_at)'), [$year1, $year2])
            ->groupBy(DB::raw('orders.created_at'))
            ->get()->groupBy(function ($d) {
                return Carbon::parse($d->created_at)->format('Y');
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

        $pdf->loadView('laporanpenghasilan2', [
            'myArray' => $myArray,
            'user' => $user,
            'year1' => $year1,
            'year2' => $year2
        ]);

        return $pdf->stream();
    }

    public function laporanPenjualan1($month, $year, $user)
    {

        $menus = Menu::where('menus.deleted', '=', 0)->get();
        $size = $menus->count();
        $number = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        for ($i = 1; $i <= $size; $i++) {

            $total[$i] = 0;
            $item = [];

            for ($j = 1; $j <= $number; $j++) {
                if ($j >= 10) {
                    $date = $year . '-' . $month . '-' . $j;
                } else {
                    $date = $year . '-' . $month . '-0' . $j;
                }

                $item[$j] = DB::table('orders')->join('menus', 'orders.id_menu', '=', 'menus.id')
                    ->selectRaw('ifnull(sum(orders.jumlah), 0) as penjualan')
                    ->where('menus.nama_menu', '=', $menus[$i - 1]->nama_menu)
                    ->where('orders.status', '=', 'Paid')
                    ->where('menus.deleted', '=', 0)
                    ->whereDate('orders.created_at', '=', $date)
                    ->first();

                $total[$i] = $total[$i] + $item[$j]->penjualan;
            }

            $max = max($item);

            $laporan[$i] = array(
                "Item_Menu" => $menus[$i - 1]->nama_menu,
                "Unit" => $menus[$i - 1]->satuan,
                "Penjualan_Harian_Tertinggi" => $max->penjualan,
                "Total_Penjualan" => $total[$i]
            );
        }
        
        $customPaper = array(0, 0, 600.00, 570.80);

        $pdf = \App::make('dompdf.wrapper')->setPaper($customPaper, 'landscape');

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

        $pdf->loadView('laporanpenjualan1', [
            'laporan' => $laporan,
            'user' => $user,
            'year' => $year,
            'month' => $month,
            'menuMakanan' => $menuMakanan,
             'menuMinuman' => $menuMinuman,
              'menuSide' => $menuSide,
        ]);

        return $pdf->stream();
        // return response([
        //     'message' => 'Cart Retrieved',
        //     'data' => $laporan["3"]["Item_Menu"],
        // ]);

        
    }



    public function laporanPenjualan2($year, $user)
    {

        $menus = Menu::where('menus.deleted', '=', 0)->get();
        $size = $menus->count();

        for ($i = 1; $i <= $size; $i++) {
            $total[$i] = 0;
                $item = [];
                $maximum = [];

            for ($k = 1; $k <= 12; $k++) {
                
                $number = cal_days_in_month(CAL_GREGORIAN, $k, $year);
                for ($j = 1; $j <= $number; $j++) {
                    if($k<10)
                    {
                        $date = '0'.$k;
                    }
                    else
                    {
                        $date = $k;
                    }
                    if ($j >= 10) {
                        $date = $year . '-' . $date . '-' . $j;
                    } else {
                        $date = $year . '-' . $date . '-0' . $j;
                    }

                    $item[$j] = DB::table('orders')->join('menus', 'orders.id_menu', '=', 'menus.id')
                        ->selectRaw('ifnull(sum(orders.jumlah), 0) as penjualan')
                        ->where('menus.nama_menu', '=', $menus[$i - 1]->nama_menu)
                        ->where('orders.status', '=', 'Paid')
                        ->where('menus.deleted', '=', 0)
                        ->whereDate('orders.created_at', '=', $date)
                        ->first();

                    $total[$i] = $total[$i] + $item[$j]->penjualan;
                     $max = max($item);
                }

                 $maximum[$k] = $max;
            }
                $maxSales = max($maximum);
                
                $laporan[$i] = array(
                    "Item_Menu" => $menus[$i - 1]->nama_menu,
                    "Unit" => $menus[$i - 1]->satuan,
                    "Penjualan_Harian_Tertinggi" => $maxSales->penjualan,
                    "Total_Penjualan" => $total[$i]
                );
        }

             $customPaper = array(0, 0, 600.00, 570.80);

        $pdf = \App::make('dompdf.wrapper')->setPaper($customPaper, 'landscape');

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

        $pdf->loadView('laporanpenjualan2', [
            'laporan' => $laporan,
            'user' => $user,
            'year' => $year,
            'menuMakanan' => $menuMakanan,
             'menuMinuman' => $menuMinuman,
              'menuSide' => $menuSide,
        ]);

        return $pdf->stream();
    }
}
