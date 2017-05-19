<!DOCTYPE html>
<?php
  use App\Http\Controllers\CoinController;
?>
<html lang="{{ config('app.locale') }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">

    </head>
    <body>
        <?php

          $xico = new CoinController();
          $datas = $xico->getDatasByCurrencyDASH();
          $data_max = $datas['maximas'];
          $data_min = $datas['minimas'];
          $data_max_donchian = $datas['maximasDonchian'];
          $data_min_donchian = $datas['minimasDonchian'];

          $charts = [

              'title' => ['text' => 'DASH'],

              'yAxis' => [
                  'max' => 0.07,
                  'min' => 0.04,
                  // 'max' => 0.1,
                  // 'min' => 0.01,
                  'tickInterval' => 0.01,
                  'title' => [
                      'text' => 'Prices'
                  ]
              ],
              // 'xAxis' => [
              //     'max' => 10,
              //     'min' => 1
              // ],
              'series' => [
                  [
                      'name' => 'Max (Donchian)',
                      'data' => $data_max_donchian
                  ],
                  [
                      'name' => 'Min (Donchian)',
                      'data' => $data_min_donchian
                  ],
                  [
                      'name' => 'Max',
                      'data' => $data_max
                  ],
                  [
                      'name' => 'Min',
                      'data' => $data_min
                  ],
              ]
          ];

        ?>

        <div id="id-highchartsnya" style="height: 400px">
        </div>

          {!! Chart::display("id-highchartsnya", $charts) !!}



    </body>
</html>
