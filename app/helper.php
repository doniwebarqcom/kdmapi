<?php 

/**
 * [create_invoice description]
 * @return [type] [description]
 */
function create_invoice($user_id, $prefix='INV')
{
  $no_invoice = (\Kodami\Models\Mysql\PInvoice::count()+1).$user_id.'/'. $prefix .'/'. date('d').date('m').date('y');

  $transaksi = \Kodami\Models\Mysql\PPulsaTransaksi::whereNull('invoice_id')->whereNull('status_pembayaran')->where('user_id', $user_id)->where('status', 2)->get();

  if(count($transaksi) == 0) return; 

  $invoice = new \Kodami\Models\Mysql\PInvoice();
  $invoice->no_invoice    = $no_invoice;
  $invoice->user_id       = $user_id;
  $invoice->nominal       = \Kodami\Models\Mysql\PPulsaTransaksi::where('status', 2)->whereNull('status_pembayaran')->where('user_id', $user_id)->sum('harga_beli');
  $invoice->status        = 1;
  $invoice->type_pembuatan= 3; // Request Dropshiper
  $invoice->save();
  
  foreach($transaksi as $item)
  {
      $data = \Kodami\Models\Mysql\PPulsaTransaksi::where('id', $item->id)->first();
      $data->invoice_id = $invoice->id;
      $data->status_pembayaran = 1;
      $data->save();
  }
}

/**
 * [respon_simko_pulsa description]
 * @param  [type] $code [description]
 * @return [type]       [description]
 */
function respon_simko_pulsa($code)
{
    $message = [
                0 => 'Trx akan di proses',
                1 => 'Status Sukses',
                2 => 'Status timeout/ biasanya karena parsing blm aktif/ provider offline',
                3 => 'Transaksi di batalkan',
                4 => 'Request batal dari user gagal',
                5 => 'Terblokir salah Pin',
                6 => 'Transaksi sudah sukses di batalkan oleh CS',
                7 => 'Format pesan salah',
                8 => 'Gagal tanpa definisi ( umum)',
                9 => 'Gagal karena produk di set gangguan',
                10 => 'Gagal karena produk tdk terdaftar atau tdk aktif',
                11 => 'Gagal karena saldo tdk cukup',
                12 => 'Gagal karena pesan masuk expired / terlambat',
                13 => 'Gagal karena produk di set kosong',
                14 => 'Trx dobel dalam waktu sama ( di tolak)',
                15 => 'Trx dalam proses ( pending)',
                16 => 'Tujuan diluar CLuster/ wilayah',
                17 => 'Gagal nomer tujuan salah'
            ];

    return @$message[$code];
}


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