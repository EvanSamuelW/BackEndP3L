<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penjualan Item Tahunan</title>

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
        <p style="text-align:center; font-weight:bold; width:90%;">LAPORAN PENJUALAN ITEM MENU</p>
        <p style="margin-left: -20px;">TAHUN:{{ $year }} </p>
        <p style="margin-left: -20px;">BULAN:ALL </p>

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
                <th>Penjualan Item Tertinggi</th>
                <th>Total Penjualan</th>
            </tr>
            @foreach ($menuMakanan as $item)\
            <tr>
                <td>{{ $value+1 }}</td>
                <td>{{ $item->nama_menu }}</td>
                <td>{{ $item->satuan }}</td>

                <?php

                for ($i = 1; $i <= count($laporan); $i++) {
                    if ($laporan[strval($i)]["Item_Menu"] == $item->nama_menu) {
                        $penjualan1 = $laporan[strval($i)]["Penjualan_Harian_Tertinggi"];
                        $penjualan2 = $laporan[strval($i)]["Total_Penjualan"];
                    }
                }

                ?>
                <td>{{ $penjualan1 }}</td>
                <td>{{ $penjualan2 }}</td>
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
                <th>Penjualan Item Tertinggi</th>
                <th>Total Penjualan</th>
            </tr>
            @foreach ($menuMinuman as $item)\
            <tr>
                <td>{{ $value+1 }}</td>
                <td>{{ $item->nama_menu }}</td>
                <td>{{ $item->satuan }}</td>

                <?php

                for ($i = 1; $i <= count($laporan); $i++) {
                    if ($laporan[strval($i)]["Item_Menu"] == $item->nama_menu) {
                        $penjualan1 = $laporan[strval($i)]["Penjualan_Harian_Tertinggi"];
                        $penjualan2 = $laporan[strval($i)]["Total_Penjualan"];
                    }
                }

                ?>
                <td>{{ $penjualan1 }}</td>
                <td>{{ $penjualan2 }}</td>
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
                <th>Penjualan Item Tertinggi</th>
                <th>Total Penjualan</th>
            </tr>
            @foreach ($menuSide as $item)\
            <tr>
                <td>{{ $value+1 }}</td>
                <td>{{ $item->nama_menu }}</td>
                <td>{{ $item->satuan }}</td>

                <?php

                for ($i = 1; $i <= count($laporan); $i++) {
                    if ($laporan[strval($i)]["Item_Menu"] == $item->nama_menu) {
                        $penjualan1 = $laporan[strval($i)]["Penjualan_Harian_Tertinggi"];
                        $penjualan2 = $laporan[strval($i)]["Total_Penjualan"];
                    }
                }

                ?>
                <td>{{ $penjualan1 }}</td>
                <td>{{ $penjualan2 }}</td>
            </tr>
            @php

            $value+=1;
            @endphp
            @endforeach


        </table>

    </div>

    <p style="text-align:center;  margin-top: 40px;  font-size:15px; font-weight:bold;">Printed
        {{ Carbon\Carbon::now() }}
    </p>
    <p style="text-align:center;  margin-top: 15px;  font-size:15px;">Printed by {{ $user }} </p>
</body>

</html>