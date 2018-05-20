<?php

namespace App\Http\Controllers;

use App\Transformers\ProductTransformer;
use Kodami\Models\Mysql\Member;
use Kodami\Models\Mysql\CategorySpesification;
use Kodami\Models\Mysql\Category;
use Kodami\Models\Mysql\CategoryCriteria;
use Kodami\Models\Mysql\JunkCategorySpesification;
use Kodami\Models\Mysql\JunkCategoryCriteria;
use Kodami\Models\Mysql\Product;
use Kodami\Models\Mysql\KodePosTemp;
use Kodami\Models\Mysql\Province;
use Kodami\Models\Mysql\Regency;
use Kodami\Models\Mysql\District;
use Kodami\Models\Mysql\Village;
use Kodami\Models\Mysql\PostalCode;
use Kodami\Models\Test;
use DB;

class ExampleController extends ApiController
{
    public function index()
    {
        ini_set('max_execution_time', 36000);
        $kode = DB::table('kodepos')
        ->orderBy('propinsi', 'ASC')
        ->orderBy('kabupaten', 'ASC')
        ->orderBy('kecamatan', 'ASC')
        ->orderBy('kelurahan', 'ASC')
        ->orderBy('kodepos', 'ASC')
        ->get();

        $p = "";
        $p_id = 0;
        $ka = "";
        $ka_id = 0;
        $kec = "";
        $kec_id = 0;
        $kel = "";
        $kel_id = 0;

        // foreach ($kode as $key => $value) {
        //     if($p !== $value->propinsi)
        //     {
        //         $province = new Province;
        //         $province->name = strtoupper($value->propinsi);
        //         $province->save();                
        //         $p_id = $province->id;
        //     }

        //     $p = $value->propinsi;

        //     if($ka !== $value->kabupaten)
        //     {
        //         $regency = new Regency;
        //         $regency->province_id = $p_id;
        //         $regency->name = strtoupper($value->jenis)." ".strtoupper($value->kabupaten);
        //         $regency->save();
        //         $ka_id = $regency->id;
        //     }

        //     $ka = $value->kabupaten;

        //     if($kec !== $value->kecamatan)
        //     {
        //        $district = new District;
        //        $district->regency_id = $ka_id;
        //        $district->name = strtoupper($value->kecamatan);
        //        $district->save();
        //        $kec_id = $district->id;
        //     }
            
        //     $kec = $value->kecamatan;

        //     if($kel !== $value->kelurahan)
        //     {
        //        $village = new Village;
        //        $village->district_id = $ka_id;
        //        $village->name = strtoupper($value->kelurahan);
        //        $village->save();
        //        $kel_id = $village->id;
        //     }
            
        //     $kel = $value->kecamatan;

        //     $PostalCode = new PostalCode;
        //     $PostalCode->village_id = $kel_id;
        //     $PostalCode->code = $value->kodepos;
        //     $PostalCode->save();

        // }

        return 1;
    }

    public function rajaongkir()
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => env('RAJAONGKIR_ENDPOINT')."/cost",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS => 'origin=501&destination=114&weight=1&courier=jne',
          CURLOPT_HTTPHEADER => array(
            "key:".env('RAJAONGKIR_KEY')
          ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
           "cURL Error #:" . $err;
        } else {
            $data = json_decode($response, true);
        }

        return $data['rajaongkir']['results'][0]['costs'][0]['cost'][0]['value'];
    }

    public function sms()
    {
    	\Nexmo\Laravel\Facade\Nexmo::message()->send([
		    'to'   => '6287775365856',
		    'from' => '6282134916615',
		    'text' => 'Using the facade to send a message.'
		]);
    }

    public function rajaongkirProvince()
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => env('RAJAONGKIR_ENDPOINT')."/province",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "GET",
          CURLOPT_POSTFIELDS => '',
          CURLOPT_HTTPHEADER => array(
            "key:".env('RAJAONGKIR_KEY')
          ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err){
           //"cURL Error #:" . $err;
            $data = "";           
        }
        else
            $data = json_decode($response, true);

        foreach ($data['rajaongkir']['results'] as $key => $value) {
        }

        
    }

    public function rajaongkirCity()
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => env('RAJAONGKIR_ENDPOINT')."/city",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "GET",
          CURLOPT_POSTFIELDS => '',
          CURLOPT_HTTPHEADER => array(
            "key:".env('RAJAONGKIR_KEY')
          ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err){
           //"cURL Error #:" . $err;
            $data = "";           
        }
        else
            $data = json_decode($response, true);

        foreach ($data['rajaongkir']['results'] as $key => $value) {
        }
    }

}
