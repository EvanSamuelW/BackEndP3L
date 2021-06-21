<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Transaksi</title>

    <style>
       .container {
           display:flex;
           width: 500px;
           flex-wrap: wrap;
           flex-direction: row;
           
       }
        div
        {
            border: 1px solid white;
        }
       div img {
           height: 170px;
       }
       
       table  {
           margin-left: 27px;
           width: 400px;
           text-align: center;
    border-collapse: collapse;
     
   
        .bold {
              font-weight: bold;
        }  
}


     
    </style>
</head>

<body>
    <div class="container">
        <div style="margin-right: 60px; margin-top: 30px;">
             <img style="height: 150px;" src="https://p3lakb9681.xyz/public/menu/logo.JPG" alt="">
        </div>
            
        <div style="margin-top: -50px; border-bottom-style:dashed;">
            <ul style="list-style-type:none; text-align: center;">
            <li><h2 style="color:#44546A; ">ATMA KOREAN BBQ</h2></li>
            <li style="color:#8B0000;">FUN PLACE TO GRILL!</li>
            <li>Jl. Babarsari No. 43 Yogyakarta</li>
            <li>552181</li>
            <li>Telp. (0274) 487711</li>
        </div>
        
   
    </div>
          <hr style=" border:none;
        border-top:1px dashed ; margin-top: -130px; margin-left: 27px; width: 400px;
        height:1px;"> 
        @php
          $tanggal=strtotime($transaksi->tanggal);
          $date = date_create_from_format('d/m/Y:H:i:s', $tanggal);
          $time =date_create_from_format('H:i:s', $tanggal);
          @endphp
        
           <div style="display: inline-block; margin-top: 30px; margin-left:27px;">
            <div style="display: inline-block;">
                <p style="font-weight:bold;">Receipt #</p>
                <p style="font-weight:bold;">Cashier</p>
            </div>
            <div style="display: inline-block;">
                <p>{{ $transaksi->nomor_nota }} </p>
                <p>{{$user}}</p>
            </div>
            <div style="display: inline-block;">
                <p style="font-weight:bold;">Date </p>
                <p style="font-weight:bold;">Time</p>
            </div>
            <div style="display: inline-block;">
               
                <p>{{ Carbon\Carbon::parse($transaksi->tanggal)->format('d/m/Y') }}</p>
                 <p>{{ Carbon\Carbon::parse($transaksi->tanggal)->format('H:i') }} </p>
            </div>
        </div>
        <div style="margin-top: -50px;">
              <hr style=" border:none;
        border-top:1px dashed ; margin-left: 27px; width: 400px;
        height:1px;"> 
        <div style="display: inline-block; margin-left: 27px;">
            <div style="display: inline-block;">
                <p style="font-weight:bold;">Table # </p>
            </div>
             <div style="display: inline-block; margin-right: 85px;">
                <p>{{ $kode }} </p>
            </div>
            <div style="display: inline-block;">
                <p style="font-weight:bold;">Customer </p>
            </div>
             <div style="display: inline-block;">
                <p>{{ $transaksi->nama_pelanggan }} </p>
            </div>
        </div>
        </div>
      
         <?php $value = 0; $value2 = 0; $value3 = 0 ?>
         
<table style="border-collapse:collapse; border-bottom-style: dotted;">
  <tr >
    <th  style="border-bottom: 1px solid black; border-top-style: double;" >Qty</th>
    <th  style="border-bottom: 1px solid black; border-top-style: double;">Item Menu</th>
    <th  style="border-bottom: 1px solid black; border-top-style: double;">Harga</th>
    <th  style="border-bottom: 1px solid black; border-top-style: double;">Subtotal</th>
  </tr>
  
      @foreach($temp as $item)
          <tr>
               <td> {{$item->jml_pesanan}}</td>
                <td>{{$item->nama_menu}}</td>
                <td>Rp {{$item->harga}}</td>
                <td>Rp {{$item->total_pesanan}}</td>
          </tr>
          @php
          $value+=$item->total_pesanan;
           $value2+=$item->jml_pesanan;
            $value3+=1;
          @endphp
          
      @endforeach

</table>


<table style="margin-top: -20px;">
      <tr style="visibility:hidden;">
    <th>Qty</th>
    <th>Item Menu</th>
    <th>Harga</th>
    <th>Subtotal</th>
  </tr>
  
    <tr>
    <td></td>
    <td></td>
    <td>Sub Total</td>
    <td>Rp {{ $value }}</td>
</tr>  
<tr>
    <td></td>
    <td></td>
    <td>Service 5%</td>
    <td>Rp {{ $value*5/100 }}</td>
</tr>   
    <tr>
    <td></td>
    <td></td>
    <td>Tax 10%</td>
    <td>Rp {{ $value*10/100 }}</td>
</tr>  
</table>


<table style="margin-top: -20px;">
      <tr style="visibility:hidden;">
    <th>Qty</th>
    <th>Item Menu</th>
    <th>Harga</th>
    <th>Subtotal</th>
  </tr>
  
    <tr>
    <td style="border-bottom-style: double; border-top-style: dotted;"></td>
    <td style="border-bottom-style: double; border-top-style: dotted;"></td>
    <td style="border-bottom-style: double; border-top-style: dotted; font-weight:bold;">Total</td>
    <td style="border-bottom-style: double; border-top-style: dotted; font-weight:bold;">Rp {{ $value+$value*5/100+$value*10/100  }}</td>
</tr>  

</tr>  
</table>
<div><p style="text-align:right; margin-right: 60px;  font-size:10px;">Total Qty {{ $value2 }} </p>
</div>
<div><p style="text-align:right;  margin-top: -5px; margin-right: 60px; font-size:10px; ">Total Item {{ $value3 }} </p>
</div>

<div><p style="text-align:right; margin-right: 60px; margin-top: 15px;  font-size:10px; font-weight:bold;">Printed {{ $mytime }} </p>
</div>

<div><p style="text-align:right; margin-right: 60px; margin-top: -5px;  font-size:10px;">Printed by {{ $user }} </p>
</div>

   <h3 style="text-align:center; border-bottom-style:dotted; border-top-style:dotted;">THANK YOU FOR YOUR VISIT</h3>
</body>

</html>