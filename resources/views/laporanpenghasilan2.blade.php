<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penghasilan Tahunan</title>

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
           margin-top: 207px;
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
    <p style="text-align:center; font-weight:bold; width:90%;">LAPORAN PENDAPATAN TAHUNAN</p>
   <p style="margin-left: -20px;">TAHUN: {{ $year1 }} s/d {{ $year2 }} </p>
    </div>
         <div>
             <?php $value=1 ?>
             <table>
  <tr>
    <th>No</th>
    <th>Bulan</th>
    <th>Makanan</th>
    <th>Side Dish</th>
    <th>Minuman</th>
    <th>Total Pendapatan</th>
  </tr>
   @for ($i = $year1; $i<=$year2; $i++)
       <tr>
      <td>{{ $value }}</td>
    <td>{{ $i }}</td>
    <td>{{ $myArray[0][strval($i)] }}</td>
    <td>{{ $myArray[2][strval($i)] }}</td>
    <td>{{ $myArray[1][strval($i)] }}</td>
    <td>{{ $myArray[1][strval($i)]+ $myArray[0][strval($i)]+ $myArray[2][strval($i)] }}</td>
 </tr>
   @php
       
            $value+=1;
          @endphp
    @endfor

  
</table>

         </div>
         
         <p style="text-align:center;  margin-top: 40px;  font-size:15px; font-weight:bold;">Printed {{ Carbon\Carbon::now() }} </p>
           <p style="text-align:center;  margin-top: 15px;  font-size:15px;">Printed by {{ $user }} </p>
         

</body>

</html>