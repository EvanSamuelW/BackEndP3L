<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\transaksi;
use App\kartu;
use App\reservasi;
use App\meja;
use App\User;
use App\bahan;
use App\menu;
use App\order;
use Illuminate\Support\Facades\DB;
use App\stok_keluar;
use Carbon\Carbon;
use Validator;

class TransaksiController extends Controller
{
    public function tambahTransaksi(Request $request, $id)
    {
        $data = $request->all();

        $validate = Validator::make($data, [
            'metode' => 'required',
            'card'=>'required'
        ]);

        if ($validate->fails())
            return response([
                'message' => $validate->errors()
            ], 402);
        
        $user = transaksi::find($id);
        $count = DB::table('transaksis')
                ->whereDate('tanggal', Carbon::today()->toDateString())->where('status','Paid')->count();
        $count ++;
        $angka=strval($count);
        $date2 = date_format(Carbon::today(), "dmy");
        $string = "AKB-". $date2 . "-" .$count;
        
        $order = order::where('id_transaksi',$id)->get();
        
         foreach($order as $item)
        { 
                $menu = menu::find($item->id_menu);
                if($item->status == 'not paid')
                {
                     $bahan = bahan::find($menu->id_bahan);
                     $bahan->stok += $menu->serving*$item->jumlah;
                     $bahan->save();
                    $item->delete();
                }
                else
                {
                    DB::table('stok_keluars')->insert([
                'id_bahan' => $menu->id_bahan,
                'addedDate' => Carbon::now(),
                'jumlah' => $item->jumlah * $menu->serving,
                'Keterangan' => $menu->nama_menu . " Transaksi " . strval($id),
                ]);
                
                }
                     
                
        }
        $user->status = 'Paid';
        $user->metode = $data['metode'];
        $user->id_karyawan = $request->id_karyawan;
        $user->tanggal = Carbon::now();
        $user->nomor_nota = $string;
        if($data['card'] == true)
        {
            $user->kode_verif = $data['kode_verif'];
            $kartu = kartu::create([
                'nama_pelanggan' =>$user->nama_pelanggan,
                'tipe_kartu' => $request->tipe_kartu,
                'nomor' => $request->nomor,
                'exp_date'=>$request->exp_date
            ]);
            $user->id_kartu = $kartu->id;
        }

            $Reservasi = Reservasi::where('id_transaksi', $id)->get();
             $Reservasi->first()->status = 'Paid';
             $Reservasi->first()->save();
             $meja = meja::find($Reservasi->first()->id_meja);
                    $meja->available = true;
                    $meja->save();
        $data =  DB::table('orders')->where([
            ['id_transaksi', $id],
            ['status', 'Dihidangkan'],
        ])->update(array('status' => 'Paid'));

          

        if ($user->save()) {
            return response([
                'message' => 'Transaksi berhasil ditambahkan',
                'user' => $user
            ]);
        }

        return response([
            'message' => 'Transaksi gagal ditambahkan',
        ], 403);
    }

    public function getAllTransaksi()
    {
        $data = transaksi::where('status', 'Paid')->get();
       

        if (count($data) < 1) {
            return response([
                'message' => 'Transaksi is Empty'
            ], 400);
        }
        
        $data->transform(function ($item) {
            $karyawan = User::where('id', $item['id_karyawan'])->get()->first();
             $reservasi = reservasi::find($item['id']);
             $meja = meja::find($reservasi->id_meja);
            $item->nama_karyawan = $karyawan->name;
            $item->meja = $meja->kode;
            return $item;
        });
        
        return response([
            'message' => 'Retrieve all Transaksi success',
            'data' => $data
        ]);
    }

    public function getAllTransaksiNotPaid()
    {
        $data = transaksi::where([
            ['total', '<>', 0],
            ['status', '=', 'Not Paid']
        ])->get();
        

        if (count($data) < 1) {
            return response([
                'message' => 'Transaksi is Empty'
            ], 400);
        }

        return response([
            'message' => 'Retrieve all Transaksi success',
            'data' => $data
        ]);
    }
    
    public function printStruk($id,$user)
    {
         $transaksi = transaksi::find($id);
         $reservasi = reservasi::where('id_transaksi', $id)->get();
         $Pesanan=DB::table('menus')
            ->rightjoin('orders','menus.id','=','orders.id_menu')
            ->select('id_menu',DB::raw('SUM(orders.jumlah) as jml_pesanan'),DB::raw('SUM(orders.subtotal) as total_pesanan'))
            ->where('orders.id_transaksi','=', $id)
            ->where('orders.status','=', 'Paid')
            ->groupBy('id_menu')
            ->get();
             $customPaper = array(0,0,600.00,400.80);
            
            $pdf = \App::make('dompdf.wrapper')->setPaper($customPaper, 'landscape');
         
         $temp=[];
         if(isset($Pesanan)){
             foreach($Pesanan as $p ){
                $item = Menu::find($p->id_menu);
                
                 if(!is_null($item)){
                       $p->nama_menu = $item->nama_menu;
                       $p->harga = $item->harga;
                       $temp[]=$p;
                    }
                
             }
             foreach($reservasi as $meja)
             {
                 $kode = meja::find($meja->id_meja);
                 $kodeMeja = $kode->kode;
             }
    }
    
   $mytime = Carbon::now();
    
     $pdf->loadView('struk',[
            'temp' => $temp,
            'mytime' => $mytime,
            'user' => $user,
            'transaksi' => $transaksi,
            'kode' => $kodeMeja,
        ]);
    
          return $pdf->stream();
    }
    
    
    public function getOrder($id)
    {
         $Pesanan=DB::table('menus')
            ->rightjoin('orders','menus.id','=','orders.id_menu')
            ->select('id_menu',DB::raw('SUM(orders.jumlah) as jml_pesanan'),DB::raw('SUM(orders.subtotal) as total_pesanan'))
            ->where('orders.id_transaksi','=', $id)
            ->where('orders.status','=', 'Dihidangkan')
            ->groupBy('id_menu')
            ->get();
            
             $temp=[];
         if(isset($Pesanan)){
             foreach($Pesanan as $p ){
                $item = Menu::find($p->id_menu);
                
                 if(!is_null($item)){
                       $p->nama_menu = $item->nama_menu;
                       $p->harga = $item->harga;
                       $temp[]=$p;
                    }
                
             }
         }  
              return response([
            'message' => 'Retrieve all Order success',
            'data' => $temp
        ]);
    }
    
    public function getKartu($id)
    {
        $kartu=kartu::find($id);
         return response([
            'message' => 'Retrieve Kartu Success',
            'data' => $kartu
        ]);
        
    }
}
