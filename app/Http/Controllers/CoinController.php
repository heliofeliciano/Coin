<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use DatePeriod;
use DateInterval;
use DateTime;

class CoinController extends Controller
{
    // Este método esta sendo utilizado pelo Schedule
    public function getDatasByCurrency(){

      $currency = 'dash';

      $url = "https://api.coinmarketcap.com/v1/ticker/{$currency}";
      $contents = file_get_contents($url);

      $arrayOfValues = json_decode($contents, true);
      //return $arrayOfValues;

      $coin_id = $this->getCoinIdByCurrency($currency)[0];

      DB::table('coin_market')->insert(
        array(
          "idcoin"              => $coin_id,
          "rank"                => $arrayOfValues[0]["rank"],
          "price_usd"           => $arrayOfValues[0]["price_usd"],
          "price_btc"           => $arrayOfValues[0]["price_btc"],
          "volume_24h_usd"      => $arrayOfValues[0]["24h_volume_usd"],
          "market_cap_usd"      => $arrayOfValues[0]["market_cap_usd"],
          "available_supply"    => $arrayOfValues[0]["available_supply"],
          "total_supply"        => $arrayOfValues[0]["total_supply"],
          "percent_change_1h"   => $arrayOfValues[0]["percent_change_1h"],
          "percent_change_24h"  => $arrayOfValues[0]["percent_change_24h"],
          "percent_change_7d"   => $arrayOfValues[0]["percent_change_7d"],
          "last_updated"        => date("Y-m-d H:i:s", $arrayOfValues[0]["last_updated"])
        )
      );

      return $contents;

    }
    public function getDatasByCurrencyBTC_ins(){

      $currency = 'bitcoin';

      $url = "https://api.coinmarketcap.com/v1/ticker/{$currency}";
      $contents = file_get_contents($url);

      $arrayOfValues = json_decode($contents, true);
      //return $arrayOfValues;

      $coin_id = $this->getCoinIdByCurrency($currency)[0];

      DB::table('coin_market')->insert(
        array(
          "idcoin"              => $coin_id,
          "rank"                => $arrayOfValues[0]["rank"],
          "price_usd"           => $arrayOfValues[0]["price_usd"],
          "price_btc"           => $arrayOfValues[0]["price_btc"],
          "volume_24h_usd"      => $arrayOfValues[0]["24h_volume_usd"],
          "market_cap_usd"      => $arrayOfValues[0]["market_cap_usd"],
          "available_supply"    => $arrayOfValues[0]["available_supply"],
          "total_supply"        => $arrayOfValues[0]["total_supply"],
          "percent_change_1h"   => $arrayOfValues[0]["percent_change_1h"],
          "percent_change_24h"  => $arrayOfValues[0]["percent_change_24h"],
          "percent_change_7d"   => $arrayOfValues[0]["percent_change_7d"],
          "last_updated"        => date("Y-m-d H:i:s", $arrayOfValues[0]["last_updated"])
        )
      );

      return $contents;

    }

    public function getDatasByCurrency60minutes(){

      $currency = 'dash';
      $dateMin = '2017-04-05';
      //$dateMax = '2017-04-10';
      $dateMax = date('Y-m-d');

      $interval = new DateInterval('P1D');
      $dateMax_aux = new DateTime($dateMax);
      $dateMax_aux->modify( '+1 day' );
      $daterange = new DatePeriod(new DateTime($dateMin), $interval, $dateMax_aux);

      $arrValuesPerHour = array();
      $arrValuesPerDay = array();

      $arrMaximas = array();
      $arrMinimas = array();
      $arrMedias = array();

      // Variaveis para calculo da tendencia
      // $a = 0
      // $b = 0
      // $c = 0
      // $d = 0
      // $m_inclinacao = 0; // m = (a - b) / (c - d)
      // $b_intersecao = 0; //

      foreach($daterange as $date){

        // for ($i=0; $i < 24; $i++) {

          // $hourMin = str_pad($i,2,'0', STR_PAD_LEFT) . ':00:00';
          // $hourMax = str_pad($i,2,'0', STR_PAD_LEFT) . ':59:59';
          $hourMin = '00:00:00';
          $hourMax = '23:59:59';

          $dateInitial = $date->format("Y-m-d") . " " . $hourMin;
          $dateFinal = $date->format("Y-m-d") . " " . $hourMax;

          $coin_market_detail = DB::table('coin_market')
                          ->select([DB::raw('MAX(price_usd) AS max'), DB::raw('MIN(price_usd) AS min')])
                          ->where("idcoin", $this->getCoinIdByCurrency($currency))
                          ->where("last_updated", '>=', $dateInitial)
                          ->where("last_updated", '<=', $dateFinal)
                          ->get();

          $arrayMaxMin = json_decode($coin_market_detail, true);

          //array_push($arrValuesPerHour, array(str_pad($i,2,'0', STR_PAD_LEFT) => $arrayMaxMin));

          if ((double)$arrayMaxMin[0]['max'] <> 0 || (double)$arrayMaxMin[0]['min'] <> 0) {
            $arrMaximas[] = (double)$arrayMaxMin[0]['max'];
            $arrMinimas[] = (double)$arrayMaxMin[0]['min'];
            $arrMedias[] = ((double)$arrayMaxMin[0]['min'] + (double)$arrayMaxMin[0]['max'])/2;
          }

        //   $i = $i + 6;
        // }

        //array_push($arrValuesPerDay, array($date->format("Y-m-d") => $arrValuesPerHour));
      }

      /*
      // Get the last XX prices of array
      $arrMinimas_xx = array_slice($arrMinimas, -30, 30, false);
      $arrMaximas_xx = array_slice($arrMaximas, -80, 80, false);
      */

      // Percorrer todo o array para calcular a media de cada dia (Maximas)
      $values_maximas_return = null;
      $values_maximas_formedia = null;
      for ($i=0; $i < sizeof($arrMaximas); $i++) {

        $values_maximas_formedia[] = $arrMaximas[$i];

        $sumvalues_formedia = 0;
        for ($j=0; $j < sizeof($values_maximas_formedia); $j++) {
          $sumvalues_formedia = $sumvalues_formedia + $values_maximas_formedia[$j];
        }

        $values_maximas_return[] = ($sumvalues_formedia/sizeof($values_maximas_formedia));
      }

      // Percorrer todo o array para calcular a media de cada dia (Minimas)
      $values_minimas_return = null;
      $values_minimas_formedia = null;
      for ($i=0; $i < sizeof($arrMinimas); $i++) {

        $values_minimas_formedia[] = $arrMinimas[$i];

        $sumvalues_formedia = 0;
        for ($j=0; $j < sizeof($values_minimas_formedia); $j++) {
          $sumvalues_formedia = $sumvalues_formedia + $values_minimas_formedia[$j];
        }

        $values_minimas_return[] = ($sumvalues_formedia/sizeof($values_minimas_formedia));
      }

      // Calcular a media
      $values_medias_return = null;
      $values_medias_formedia = null;
      for ($i=0; $i < sizeof($arrMedias) ; $i++) {

        $values_medias_formedia[] = $arrMedias[$i];
        $sumvalues_media_formedia = 0;
        for ($j=0; $j < sizeof($values_medias_formedia); $j++) {
          $sumvalues_media_formedia = $sumvalues_media_formedia + $values_medias_formedia[$j];
        }

        $values_medias_return[] = ($sumvalues_media_formedia/sizeof($values_medias_formedia));
      }

      return array(
                  "maximas" => $values_maximas_return,
                  "minimas" => $values_minimas_return,
                  "medias" => $values_medias_return
                )
            ;
      // return array(
      //             "maximas" => $arrMinimas,
      //             "minimas" => $arrMaximas
      //           )
      //       ;

      //return $arrValuesPerDay;
    }

    /*
      Método que abastece o gráfico Canais de Donchian Bitcoin
    */
    public function getDatasByCurrencyBTC(){

      $currency = 'bitcoin';
      $dateMin = '2017-01-01';
      //$dateMax = '2017-04-10';
      $dateMax = date('Y-m-d');

      $interval = new DateInterval('P1D');
      $dateMax_aux = new DateTime($dateMax);
      $dateMax_aux->modify( '+1 day' );
      $daterange = new DatePeriod(new DateTime($dateMin), $interval, $dateMax_aux);

      $arrValuesPerHour = array();
      $arrValuesPerDay = array();

      $arrMaximas = array();
      $arrMinimas = array();
      $arrMedias = array();

      $coin_market_detail = DB::table('coin_market')
                      ->select([DB::raw('MAX(price_usd) AS max, MIN(price_usd) AS min, last_updated::timestamp::date')])
                      ->where("idcoin", $this->getCoinIdByCurrency($currency))
                      ->where("last_updated", '>=', $dateMin)
                      ->groupby([DB::raw("DATE(last_updated)")])
                      ->orderby("last_updated")
                      ->get();
      $arrayMaxMin = json_decode($coin_market_detail, true);

      for ($i=0; $i < sizeof($arrayMaxMin); $i++) {

        if ((double)$arrayMaxMin[$i]['max'] <> 0 || (double)$arrayMaxMin[$i]['min'] <> 0) {
          $arrMaximas[] = (double)$arrayMaxMin[$i]['max'];
          $arrMinimas[] = (double)$arrayMaxMin[$i]['min'];
        }

      }

      $arrMaximasDonchian = array();
      for ($i=1; $i <= sizeof($arrMaximas); $i++) {

        $flagSlice = $i*(-1);
        $sizeDays = 2;
        $valueDonchianSup = 0;
        while ($sizeDays <> 0) {

          $lastValueMax = array_slice($arrMaximas, $flagSlice, 1, false)[0];

          if ($lastValueMax > $valueDonchianSup) {
            $valueDonchianSup = $lastValueMax;
          }

          $flagSlice--;
          $sizeDays--;

        }
        $arrMaximasDonchian[] = $valueDonchianSup;
        // exit;
      }
      $arrMaximasDonchian = array_reverse($arrMaximasDonchian);


      $arrMinimasDonchian = array();
      for ($i=1; $i <= sizeof($arrMinimas); $i++) {

        $flagSlice = $i*(-1);
        $sizeDays = 1;
        $valueDonchianInf = 99999999;
        while ($sizeDays <> 0) {

          $lastValueMin = array_slice($arrMinimas, $flagSlice, 1, false)[0];

          if ($lastValueMin < $valueDonchianInf) {
            $valueDonchianInf = $lastValueMin;
          }

          $flagSlice--;
          $sizeDays--;

        }
        $arrMinimasDonchian[] = $valueDonchianInf;
        // exit;
      }
      $arrMinimasDonchian = array_reverse($arrMinimasDonchian);

      $arrMinimas = array_slice($arrMinimas, -80, 80, false);
      $arrMinimasDonchian = array_slice($arrMinimasDonchian, -80, 80, false);
      $arrMaximasDonchian = array_slice($arrMaximasDonchian, -80, 80, false);
      $arrMaximas = array_slice($arrMaximas, -80, 80, false);

      // $arrMinimasAux = array();
      // for ($i=0; $i < 50; $i++) {
      //   $arrMinimasAux[] = null;
      // }
      //
      // for ($i=0; $i < sizeof($arrMinimas); $i++) {
      //   $arrMinimasAux[] = $arrMinimas[$i];
      // }

      return array(
                  "maximas" => $arrMaximas,
                  "minimas" => $arrMinimas,
                  "maximasDonchian" => $arrMaximasDonchian,
                  "minimasDonchian" => $arrMinimasDonchian,
                )
            ;
    }

    public function getDatasByCurrencyDASH(){

      $currency = 'dash';
      $dateMin = '2017-01-01';
      //$dateMax = '2017-04-10';
      $dateMax = date('Y-m-d');

      $interval = new DateInterval('P1D');
      $dateMax_aux = new DateTime($dateMax);
      $dateMax_aux->modify( '+1 day' );
      $daterange = new DatePeriod(new DateTime($dateMin), $interval, $dateMax_aux);

      $arrValuesPerHour = array();
      $arrValuesPerDay = array();

      $arrMaximas = array();
      $arrMinimas = array();
      $arrMedias = array();

      $coin_market_detail = DB::table('coin_market')
                      ->select([DB::raw('MAX(price_btc) AS max, MIN(price_btc) AS min, last_updated::timestamp::date')])
                      ->where("idcoin", $this->getCoinIdByCurrency($currency))
                      ->where("last_updated", '>=', $dateMin)
                      ->groupby([DB::raw("DATE(last_updated)")])
                      ->orderby("last_updated")
                      ->get();
      $arrayMaxMin = json_decode($coin_market_detail, true);

      for ($i=0; $i < sizeof($arrayMaxMin); $i++) {

        if ((double)$arrayMaxMin[$i]['max'] <> 0 || (double)$arrayMaxMin[$i]['min'] <> 0) {
          $arrMaximas[] = (double)$arrayMaxMin[$i]['max'];
          $arrMinimas[] = (double)$arrayMaxMin[$i]['min'];
        }

      }

      $arrMaximasDonchian = array();
      for ($i=1; $i <= sizeof($arrMaximas); $i++) {

        $flagSlice = $i*(-1);
        $sizeDays = 2;
        $valueDonchianSup = 0;
        while ($sizeDays <> 0) {

          $lastValueMax = array_slice($arrMaximas, $flagSlice, 1, false)[0];

          if ($lastValueMax > $valueDonchianSup) {
            $valueDonchianSup = $lastValueMax;
          }

          $flagSlice--;
          $sizeDays--;

        }
        $arrMaximasDonchian[] = $valueDonchianSup;
        // exit;
      }
      $arrMaximasDonchian = array_reverse($arrMaximasDonchian);


      $arrMinimasDonchian = array();
      for ($i=1; $i <= sizeof($arrMinimas); $i++) {

        $flagSlice = $i*(-1);
        $sizeDays = 1;
        $valueDonchianInf = 99999999;
        while ($sizeDays <> 0) {

          $lastValueMin = array_slice($arrMinimas, $flagSlice, 1, false)[0];

          if ($lastValueMin < $valueDonchianInf) {
            $valueDonchianInf = $lastValueMin;
          }

          $flagSlice--;
          $sizeDays--;

        }
        $arrMinimasDonchian[] = $valueDonchianInf;
        // exit;
      }
      $arrMinimasDonchian = array_reverse($arrMinimasDonchian);

      $arrMinimas = array_slice($arrMinimas, -80, 80, false);
      $arrMinimasDonchian = array_slice($arrMinimasDonchian, -80, 80, false);
      $arrMaximasDonchian = array_slice($arrMaximasDonchian, -80, 80, false);
      $arrMaximas = array_slice($arrMaximas, -80, 80, false);

      return array(
                  "maximas" => $arrMaximas,
                  "minimas" => $arrMinimas,
                  "maximasDonchian" => $arrMaximasDonchian,
                  "minimasDonchian" => $arrMinimasDonchian,
                )
            ;
    }

    public function getDatasByCurrencyAll(){

      $currency = 'dash';
      $dateMin = '2017-04-05';
      $dateMax = '2017-04-09';

      $interval = new DateInterval('P1D');
      $dateMax_aux = new DateTime($dateMax);
      $dateMax_aux->modify( '+1 day' );
      $daterange = new DatePeriod(new DateTime($dateMin), $interval, $dateMax_aux);

      $arrValuesPerHour = array();
      $arrValuesPerDay = array();

      $arrMaximas = array();
      $arrMinimas = array();
      $arrMedias = array();

      // Variaveis para calculo da tendencia
      // $a = 0
      // $b = 0
      // $c = 0
      // $d = 0
      // $m_inclinacao = 0; // m = (a - b) / (c - d)
      // $b_intersecao = 0; //
      $coin_market_detail = DB::table('coin_market')
                      ->select(['price_usd AS price'])
                      ->where("idcoin", $this->getCoinIdByCurrency($currency))
                      ->orderby('id')
                      ->get();

      $arrayDetail = json_decode($coin_market_detail, true);

      for ($i=0; $i < sizeof($arrayDetail); $i++) {

        if ((double)$arrayDetail[$i]['price'] <> 0) {
          $arr[] = (double)$arrayDetail[$i]['price'];

        }

        //$i = $i + 4;
      }

      return array(
                  "values" => $arr
                )
            ;
      // return array(
      //             "maximas" => $arrMinimas,
      //             "minimas" => $arrMaximas
      //           )
      //       ;

      //return $arrValuesPerDay;
    }

    public function getCoinIdByCurrency($currency){

      $coin = DB::table('coin')
                    ->where("idcurrency", $currency)
                    ->pluck("id");
      return $coin;

    }
}
