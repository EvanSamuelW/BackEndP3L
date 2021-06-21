<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <div>
        <div style="text-align: center;">
            <img src="https://p3lakb9681.xyz/public/menu/logo.JPG" alt="">
        </div>

        <div style="text-align: center;">

            <img src="data:image/png;base64, {!! base64_encode(QrCode::format('svg')->size(200)->generate($data->qrcode)) !!} " >


        </div>
        <div style="text-align:center;">
            <h2>Printed {{ $mytime }}</h2>
            <h3>Printed by {{ $user }}</h3>

            <h2 style="margin-top: 50px;">FUN PLACE TO GRILL</h2>
        </div>
    </div>


</body>

</html>