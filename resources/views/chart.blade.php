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
          $xico2 = new CoinController();
          $datas_values = $xico2->getDatasByCurrencyAll();
          $arr_values = $datas_values['values'];

          $charts_values = [

              'title' => ['text' => 'Dash'],

              'yAxis' => [
                  'max' => 80,
                  'min' => 60,
                  // 'max' => 0.1,
                  // 'min' => 0.01,
                  'tickInterval' => 2,
                  'title' => [
                      'text' => 'Fruit Eaten'
                  ]
              ],
              'series' => [
                  [
                      'name' => 'Values',
                      'data' => $arr_values
                  ]
              ]
          ];


          $xico = new CoinController();
          //$datas = $xico->getDatasByCurrency60minutes();
          $datas = $xico->getDatasByCurrencyBTC();
          $data_max_donchian = $datas['maximasDonchian'];
          $data_min_donchian = $datas['minimasDonchian'];
          $data_max = $datas['maximas'];
          $data_min = $datas['minimas'];
          //$data_media = $datas['medias'];

          //$data_maxima = $data_maxmin[0]['maximas'];
          //print_r($data_maxmin);exit;

          $charts = [

              'title' => ['text' => 'BTC'],

              'yAxis' => [
                  'max' => 4000,
                  'min' => 850,
                  // 'max' => 0.1,
                  // 'min' => 0.01,
                  'tickInterval' => 2,
                  'title' => [
                      'text' => 'Fruit Eaten'
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
                  // [
                  //     'name' => 'Media',
                  //     'data' => $data_media
                  // ],
              ]
          ];

        ?>

        <div id="id-highchartsnya" style="height: 400px">
        </div>
        <div id="id-highchartsnya_2" style="height: 400px">
        </div>

          <!-- {!! Chart::display("id-highchartsnya", $charts_values) !!} -->
          {!! Chart::display("id-highchartsnya", $charts) !!}



    </body>
</html>
