<?php 
if (!function_exists('config_path')) {
    function config_path($path = '')
    {
        return app()->basePath() . '/config' . ($path ? '/' . $path : $path);
    }
}

if (!function_exists('quickRandom')) {
	function quickRandom($length = 16)
    {
        $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

        return substr(str_shuffle(str_repeat($pool, $length)), 0, $length);
    }
}

if (!function_exists('quickRandomNumber')) {
    function quickRandomNumber($length = 3)
    {
        $pool = '0123456789';

        return substr(str_shuffle(str_repeat($pool, $length)), 0, $length);
    }
}

if (!function_exists('sellingEnv')) {
	function sellingEnv()
    {
        return ['Lingkungan Rumah', 'Kantor'];
    }
}

if (!function_exists('random_trasaction_code')) {
    function random_trasaction_code($total_data = 0)
    {
        $charackter = strlen($total_data);
        for ($i=$charackter; $i < 6; $i++) { 
            $total_data="0".$total_data;
        }

        $result = "Kodami".quickRandom(5).$total_data;

        return $result;
    }
}


if (!function_exists('rajaOngkirApi')) {
    function rajaOngkirApi($url = "", $type = 'GET', $params = null)
    {
        $curl = curl_init();
        $TempCurl = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1
        ];

        if($type === 'GET'){
            curl_setopt_array($curl, array(
                CURLOPT_URL => env('RAJAONGKIR_ENDPOINT')."/".$url."?".$params,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => array(
                    "key:".env('RAJAONGKIR_KEY')
                ),
            ));
        }
        else{
           curl_setopt_array($curl, array(
                CURLOPT_URL => env('RAJAONGKIR_ENDPOINT')."/".$url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => $params,
                CURLOPT_HTTPHEADER => array(
                    "content-type: application/x-www-form-urlencoded",
                    "key:".env('RAJAONGKIR_KEY')
                ),
            ));
        }

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
           $response = [];
        }

        $data = json_decode($response, true);

        return $data;
    }
}