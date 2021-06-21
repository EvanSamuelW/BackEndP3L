<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Stok Sebulan</title>

    <style>
       .container {
           width: 100%;
           margin:auto;
           margin-left: 50px;
       }
      
       div img {
           height: 170px;
       }
       
    
       
       table {
             width: 90%;
           margin:auto;
           margin-top: 40px;
           text-align: center;
 
    border-collapse: collapse;
}
table td,
 {
    border-left: 0;
    border-right: 0;
    border-top: 0;
    border-bottom: 1px dotted black;
}
       
       table th{
           border-top: 1px solid black; border-bottom-style: double; }
       
 tr:last-child td {
     border-left: 0;
    border-right: 0;
    border-top: 0;
     border-bottom-style: double; 
}



     
    </style>
</head>

<body>
    <div class="container">
        <div style="margin-right: 60px;">
             <img style="height: 150px;" src="https://p3lakb9681.xyz/public/menu/logo.JPG" alt="">
                 <div style="margin-top: -207px; margin-left: 70px;">
            <ul style="list-style-type:none; text-align: center;">
            <li><h2 style="color:#44546A; ">ATMA KOREAN BBQ</h2></li>
            <li style="color:#8B0000;">FUN PLACE TO GRILL!</li>
            <li>Jl. Babarsari No. 43 Yogyakarta 552181</li>
            <li>Telp. (0274) 487711</li>
             <li>http://www.atmakoreanbbq.com</li>
        </div>
        
        </div>
            <hr style=" border:none;
        border-top:1px dashed ;
        margin-top: -35px;
        margin-right: 80px;
        width: 80%;
        height:1px;"> 

    </div>
 <div style: "margin:auto;">
      <h3 style="text-align:center; font-weight:bold; margin-top: 150px;">LAPORAN STOK BAHAN</h3>
   <p style="margin-left: 40px; margin-top: 30px;">ITEM MENU: {{ strtoupper($nama_menu) }} </p>
   <p style="margin-left: 40px;">PERIODE: {{ strtoupper(Carbon\Carbon::createFromFormat('F Y', $month . " " . strval($year))->format('F Y'))}} </p>
 </div>
 
 
              
             <table>
  <tr>
    <th>No</th>
    <th>Tanggal</th>
    <th>Unit</th>
    <th>Incoming Stock</th>
    <th>Remaining Stock</th>
    <th>Waste Stock</th>
  </tr>
     @for ($i = 1; $i<=$number; $i++)
       <tr>
      <td>{{ $i }}</td>
    <td>{{ Carbon\Carbon::createFromFormat('d F Y',  strval($i) . " " . $month . " " . strval($year))->format('d F Y')}} </td>
    <td>{{ $satuan }}</td>
    <td>{{ $myArray[0][strval($i)] }}</td>
     <td>{{ $myArray[0][strval($i)]-$myArray[1][strval($i)] }}</td>
    <td>{{ $myArray[2][strval($i)] }}</td>
 </tr>
@endfor
  
</table>

         </div>

         
           <p style="text-align:center;  margin-top: 40px;  font-size:15px; font-weight:bold;">Printed {{ Carbon\Carbon::now() }} </p>
           <p style="text-align:center;  margin-top: 15px;  font-size:15px;">Printed by {{ $user }} </p>
</body>

</html>