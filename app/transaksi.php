<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class transaksi extends Model
{
    protected $fillable = [
        'total','metode','kode_verif','nama_pelanggan','id_karyawan','id_kartu','tanggal','status'
    ];

    public function getCreatedAtAttribute(){
        if(!is_null($this->attributes['created_at'])){
            return Carbon::parse($this->attributes['created_at'])->format('Y-m-d H:i:s');
        }
    }

    public function getUpdatedAtAttribute(){
        if(!is_null($this->attributes['updated_at'])){
            return Carbon::parse($this->attributes['updated_at'])->format('Y-m-d H:i:s');
        }
    }
}
