<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Stok Custom Period</title>

    <style>
        .container {
            width: 100%;
            margin: auto;
            margin-left: 50px;
        }

        div img {
            height: 170px;
        }



        table {
            width: 90%;
            margin: auto;

            text-align: center;

            border-collapse: collapse;
        }

        table td {
            border-left: 0;
            border-right: 0;
            border-top: 0;
            border-bottom: 1px dotted black;
        }

        table th {
            border-top: 1px solid black;
            border-bottom-style: double;
        }

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
                    <li>
                        <h2 style="color:#44546A; ">ATMA KOREAN BBQ</h2>
                    </li>
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
           <p style="text-align:center; font-weight:bold; width:90%;">LAPORAN STOK BAHAN</p>
   <p style="margin-left: -20px;">ITEM MENU:ALL </p>
     <p style="margin-left: -20px;">PERIODE:CUSTOM ( {{  strtoupper($date1) }} s/d {{ strtoupper($date2) }} ) </p>

    </div>
    <div>
        <?php $value = 0 ?>
        <table style="margin-top: 240px;">
            <tr>
            <th colspan="2">MAKANAN</th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
            </tr>
            <tr>
                <th>No</th>
                <th>Item Menu</th>
                <th>Unit</th>
                <th>Incoming Stock</th>
                <th>Remaining Stock</th>
                <th>Waste Stock</th>
            </tr>
            @foreach ($menuMakanan as $item)\
            <tr>
                <td>{{ $value+1 }}</td>
                <td>{{ $item->nama_menu }}</td>
                <td>{{ $item->satuan }}</td>

                <?php
                if ($myArray[0]->contains('nama_menu', $item->nama_menu)) {
                    for ($i = 0; $i < count($myArray[0]); $i++) {
                        if ($myArray[0][$i]->nama_menu == $item->nama_menu) {
                            $stok1 = $myArray[0][$i]->Incoming_stok;
                        }
                    }
                } else {
                    $stok1 = 0;
                }


                if ($myArray[1]->contains('nama_menu', $item->nama_menu)) {

                    for ($i = 0; $i < count($myArray[1]); $i++) {
                        if ($myArray[1][$i]->nama_menu == $item->nama_menu) {
                            $stok2 = $myArray[1][$i]->Waste_Stok;
                        }
                    }
                } else {
                    $stok2 = 0;
                }



                if ($myArray[2]->contains('nama_menu', $item->nama_menu)) {
                    for ($i = 0; $i < count($myArray[2]); $i++) {
                        if ($myArray[2][$i]->nama_menu == $item->nama_menu) {
                            $stok3 = $myArray[2][$i]->Remaining_Stok;
                        }
                    }
                } else {
                    $stok3 = 0;
                }
                ?>
                <td>{{ $stok1 }}</td>
                <td>{{ $stok3 }}</td>
                <td>{{ $stok2 }}</td>
            </tr>
            @php

            $value+=1;
            @endphp
            @endforeach


        </table>

    </div>
    <div>
        <?php $value = 0 ?>
        <table style="margin-top: 20px;">
            <tr>
            <th colspan="2">MINUMAN</th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
            </tr>
            <tr>
                <th>No</th>
                <th>Item Menu</th>
                <th>Unit</th>
                <th>Incoming Stock</th>
                <th>Remaining Stock</th>
                <th>Waste Stock</th>
            </tr>
            @foreach ($menuMinuman as $item)\
            <tr>
                <td>{{ $value+1 }}</td>
                <td>{{ $item->nama_menu }}</td>
                <td>{{ $item->satuan }}</td>

                <?php
                if ($myArray[3]->contains('nama_menu', $item->nama_menu)) {
                    for ($i = 0; $i < count($myArray[3]); $i++) {
                        if ($myArray[3][$i]->nama_menu == $item->nama_menu) {
                            $stok1 = $myArray[3][$i]->Incoming_stok;
                        }
                    }
                } else {
                    $stok1 = 0;
                }


                if ($myArray[4]->contains('nama_menu', $item->nama_menu)) {

                    for ($i = 0; $i < count($myArray[4]); $i++) {
                        if ($myArray[4][$i]->nama_menu == $item->nama_menu) {
                            $stok2 = $myArray[4][$i]->Waste_Stok;
                        }
                    }
                } else {
                    $stok2 = 0;
                }



                if ($myArray[5]->contains('nama_menu', $item->nama_menu)) {
                    for ($i = 0; $i < count($myArray[5]); $i++) {
                        if ($myArray[5][$i]->nama_menu == $item->nama_menu) {
                            $stok3 = $myArray[5][$i]->Remaining_Stok;
                        }
                    }
                } else {
                    $stok3 = 0;
                }
                ?>
                <td>{{ $stok1 }}</td>
                <td>{{ $stok1-$stok3 }}</td>
                <td>{{ $stok2 }}</td>
            </tr>
            @php

            $value+=1;
            @endphp
            @endforeach


        </table>

    </div>
    <div>
        <?php $value = 0 ?>
        <table style="margin-top: 20px;">
            <tr>
            <th colspan="2">SIDE DISH</th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
            </tr>
            <tr>
                <th>No</th>
                <th>Item Menu</th>
                <th>Unit</th>
                <th>Incoming Stock</th>
                <th>Remaining Stock</th>
                <th>Waste Stock</th>
            </tr>
            @foreach ($menuSide as $item)\
            <tr>
                <td>{{ $value+1 }}</td>
                <td>{{ $item->nama_menu }}</td>
                <td>{{ $item->satuan }}</td>

                <?php
                if ($myArray[6]->contains('nama_menu', $item->nama_menu)) {
                    for ($i = 0; $i < count($myArray[6]); $i++) {
                        if ($myArray[6][$i]->nama_menu == $item->nama_menu) {
                            $stok1 = $myArray[6][$i]->Incoming_stok;
                        }
                    }
                } else {
                    $stok1 = 0;
                }


                if ($myArray[7]->contains('nama_menu', $item->nama_menu)) {

                    for ($i = 0; $i < count($myArray[7]); $i++) {
                        if ($myArray[7][$i]->nama_menu == $item->nama_menu) {
                            $stok2 = $myArray[7][$i]->Waste_Stok;
                        }
                    }
                } else {
                    $stok2 = 0;
                }



                if ($myArray[8]->contains('nama_menu', $item->nama_menu)) {
                    for ($i = 0; $i < count($myArray[8]); $i++) {
                        if ($myArray[8][$i]->nama_menu == $item->nama_menu) {
                            $stok3 = $myArray[8][$i]->Remaining_Stok;
                        }
                    }
                } else {
                    $stok3 = 0;
                }
                ?>
                <td>{{ $stok1 }}</td>
                <td>{{ $stok1-$stok3 }}</td>
                <td>{{ $stok2 }}</td>
            </tr>
            @php

            $value+=1;
            @endphp
            @endforeach


        </table>

    </div>

    <p style="text-align:center;  margin-top: 40px;  font-size:15px; font-weight:bold;">Printed {{ Carbon\Carbon::now() }} </p>
    <p style="text-align:center;  margin-top: 15px;  font-size:15px;">Printed by {{ $user }} </p>
</body>

</html>