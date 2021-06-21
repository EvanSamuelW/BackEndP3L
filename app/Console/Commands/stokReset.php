<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class stokReset extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stok:reset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        
        $bahan = DB::table('bahans')->where('deleted', false)->get();
        
        foreach($bahan as $item)
        { 
            if($item->stok > 0)
            {
                     DB::table('stok_keluars')->insert([
                'id_bahan' => $item->id,
                'addedDate' => Carbon::now(),
                'jumlah' => $item->stok,
                'Keterangan' => 'Waste Stok',
                ]);
            }
       
        }
        DB::table('bahans')->update(['stok' => 0]);
        
    }
}
