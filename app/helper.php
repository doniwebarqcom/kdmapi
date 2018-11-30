<?php 

/**
 * Approve Invoice Fungsi
 * @param  invoice id
 * @return status array
 */
function approve_invoice($id, $mutation_id="")
{
    $data                 = \Kodami\Models\Mysql\PInvoice::where('id',$id)->first();
    
    if(!empty($mutation_id))
    {
      $data->mutation_id    = $mutation_id;      
    }

    $data->status = 3;
    $data->save();

    \Kodami\Models\Mysql\PPulsaTransaksi::where('invoice_id', $id)->update(['status_pembayaran' => 2]);

    # cek invoice kuota sementara
    $transaksi_kuota_sementara = \Kodami\Models\Mysql\PPulsaTransaksi::whereNull('invoice_id')->whereNull('status_pembayaran')->whereNotNull('user_kuota_sementara_id')->where('user_id', $data->user_id)->where('status', 2)->sum('harga_beli');        
    if($transaksi_kuota_sementara > 0)
    {
      $saldo_invoice = $data->nominal - $transaksi_kuota_sementara;

      $no_invoice = (\Kodami\Models\Mysql\PInvoice::count()+1).$data->user_id.'/INVSTR/'. date('d').date('m').date('y');
      $invoice = new \Kodami\Models\Mysql\PInvoice();
      $invoice->no_invoice    = $no_invoice;
      $invoice->user_id       = $data->user_id;
      $invoice->nominal       = $transaksi_kuota_sementara;
      $invoice->status        = 1;
      $invoice->type_pembuatan= 3; // Request Dropshiper
      $invoice->save();            

      $transaksi = \Kodami\Models\Mysql\PPulsaTransaksi::whereNull('invoice_id')
                                    ->whereNull('status_pembayaran')
                                    ->whereNotNull('user_kuota_sementara_id')
                                    ->where('user_id', $data->user_id)
                                    ->where('status', 2)
                                    ->update(['invoice_id'=> $invoice->id, 'status_pembayaran'=>1]);
      if($saldo_invoice > 0)
      {
          // UPDATE KUOTA
          $kuota                = \Kodami\Models\Mysql\UserDropshiper::where('user_id', $data->user_id)->first();
          $kuota->saldo         = $kuota->saldo + $saldo_invoice;
          $kuota->saldo_terpakai= $kuota->saldo_terpakai - $saldo_invoice;
          $kuota->kuota_sementara_status    = 0;
          $kuota->kuota_sementara_is_avaliable = 0;
          $kuota->save();

          // HISTORY KUOTA
          $history                    = new \Kodami\Models\Mysql\UserDropshiperHistoryKuota();
          $history->user_id           = $data->user_id;
          $history->user_proses_id    = 0;
          $history->nominal           = $saldo_invoice;
          $history->type              = 2; // topup by transfer invoice
          $history->save();
      }
    }
    else
    {
      // UPDATE KUOTA
      $kuota                = \Kodami\Models\Mysql\UserDropshiper::where('user_id', $data->user_id)->first();
      $kuota->saldo         = $kuota->saldo + ($data->nominal - $data->unique);
      $kuota->saldo_terpakai= $kuota->saldo_terpakai - ($data->nominal - $data->unique);
      $kuota->save();

      // HISTORY KUOTA
      $history                    = new \Kodami\Models\Mysql\UserDropshiperHistoryKuota();
      $history->user_id           = $data->user_id;
      $history->user_proses_id    = 0;
      $history->nominal           = $data->nominal - $data->unique;
      $history->type              = 2; // topup by transfer invoice
      $history->save();
    }
}

/**
 * [parsingMessagePln description]
 * @return [type] [description]
 */
function parsingMessagePln($str)
{
  $str = explode("SUKSES.", $str);
  $str = explode('Saldo', @$str[1]);
  $str = explode('SN/Ref:', $str[0]);
  $str = explode('/', @$str[1]); 

  $nama   = @$str[1];
  $volt   = @$str[2].'/'.@$str[3];
  $token  = @$str[0];

  return ['nama'=>$nama,'token'=>$token,'volt'=>$volt, 'jumlah_kwh'=>@$str[4]];
}

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
  $invoice->type_pembuatan= 4; // Otomatis Ter-Create dari system
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
  $message = $code;
  
  if (strpos($code, '#0#') !== false)
  {
      $message = "Trx akan di proses";
  }
  if (strpos($code, '#1#') !== false)
  {
      $message = "Status Sukses";
  }
  if (strpos($code, '#2#') !== false)
  {
      $message = "Status timeout/ biasanya karena parsing blm aktif/ provider offline";
  }
  if (strpos($code, '#3#') !== false)
  {
      $message = "Transaksi di batalkan";
  }
  if (strpos($code, '#4#') !== false)
  {
      $message = "Request batal dari user gagal";
  }
  if (strpos($code, '#5#') !== false)
  {
      $message = "Terblokir salah Pin";
  }
  if (strpos($code, '#6#') !== false)
  {
      $message = "Transaksi sudah sukses di batalkan oleh CS";
  }
  if (strpos($code, '#7#') !== false)
  {
      $message = "Format pesan salah";
  }
  if (strpos($code, '#8#') !== false)
  {
      $message = "Gagal tanpa definisi ( umum )";
  }
  if (strpos($code, '#9#') !== false)
  {
      $message = "Gagal karena produk di set gangguan";
  }
  if (strpos($code, '#10#') !== false)
  {
      $message = "Gagal karena produk tdk terdaftar atau tdk aktif";
  }
  if (strpos($code, '#11#') !== false)
  {
      $message = "Gagal karena saldo tdk cukup";
  }
  if (strpos($code, '#12#') !== false)
  {
      $message = "Gagal karena pesan masuk expired / terlambat";
  }
  if (strpos($code, '#13#') !== false)
  {
      $message = "Gagal karena produk di set kosong";
  }
  if (strpos($code, '#14#') !== false)
  {
      $message = "Trx dobel dalam waktu sama ( di tolak )";
  }
  if (strpos($code, '#15#') !== false)
  {
      $message = "Trx dalam proses ( pending )";
  }
  if (strpos($code, '#16#') !== false)
  {
      $message = "Tujuan diluar CLuster/ wilayah";
  }
  if (strpos($code, '#17#') !== false)
  {
      $message = "Gagal nomer tujuan salah";
  }

  return $message;
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