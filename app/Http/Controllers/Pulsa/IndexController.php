<?php

namespace App\Http\Controllers\Pulsa;

use App\Http\Controllers\ApiController;
use Tymon\JWTAuth\JWTAuth;
use Kodami\Models\Mysql\PPulsaTransaksi;
use Kodami\Models\Mysql\UserDropshiper;
use Kodami\Models\Mysql\UserKuotaSementara;
use Kodami\Models\Mysql\TransaksiPlnToken;
use Kodami\Models\Mysql\Deposit;
use Kodami\Models\Mysql\UserAnggota;
use Kodami\Models\Mysql\Withdrawal;

class IndexController extends ApiController
{
	/**
	 * Index Response
	 * @param  JWTAuth $JWTAuth [description]
	 * @return [type]           [description]
	 */
    public function index(JWTAuth $JWTAuth)
    {
        $response['status'] = 'success';
        $response['code'] = '200';
        $response['data'] = [];

        return $response;
    }

    /**
     * [response description]
     * @param  JWTAuth $JWTAuth [description]
     * @return [type]           [description]
     */
    public function response_post()
    {
        $response['status'] = 'success';
        $response['code'] = '200';

        if(isset($this->request->refid))
        {
            // record post
         	$data 				    = new \Kodami\Models\Mysql\PPulsaResponse();
         	$data->reffid 	        = $this->request->refid;
         	$data->pesan 		    = $this->request->message;
         	$data->result_post 	    = json_encode($this->request->all());
         	$data->save(); 
                    
            $pulsa                      = PPulsaTransaksi::where('simko_reff_id', $this->request->refid)->first();
            if($pulsa)
            {
                $str                        = explode('#', $this->request->message);
                $pulsa->simko_message       = respon_simko_pulsa($this->request->message);

                $kuota_sementara   = UserKuotaSementara::where('id', $pulsa->user_kuota_sementara_id)->first();

                #find status
                if (strpos($this->request->message, '#1#') !== false)
                {
                    $pulsa->status              = 2;

                    # jika token listrik
                    if(isset($pulsa->pulsa->simko_provider_id) and $pulsa->pulsa->simko_provider_id == 6)
                    {
                        $msg_token = parsingMessagePln($this->request->message);

                        $token              = new TransaksiPlnToken();
                        $token->nama        = $msg_token['nama'];
                        $token->token       = $msg_token['token'];
                        $token->volt        = $msg_token['volt'];
                        $token->jumlah_kwh        = $msg_token['jumlah_kwh'];
                        $token->original_return=$this->request->message;
                        $token->save();
                        # set relasi token pln
                        $pulsa->transaksi_pln_token_id  = $token->id;
                    }

                    if(!empty($pulsa->user_kuota_sementara_id))
                    {
                        if($kuota_sementara)
                        {
                            $kuota_sementara->transaksi_sukses = (Int)$kuota_sementara->transaksi_sukses + 1;
                        }
                    }

                    # create invoice jika sukses
                    $cekkuotasementara = \Kodami\Models\Mysql\UserDropshiper::where('user_id', $pulsa->user_id)->first();
                    if($cekkuotasementara)
                    {
                        if($cekkuotasementara->kuota_sementara_is_avaliable == 1)
                        {
                            create_invoice($pulsa->user_id);
                            # update status kuota sementara
                            $cekkuotasementara->kuota_sementara_is_avaliable = 0;
                            $cekkuotasementara->save();
                        }
                    }
                }
                else
                {
                    $pulsa->status              = 3;

                     # jika akses sebagai dropshiper
                    if($pulsa->user->access_id == 7)
                    {
                        if(!empty($pulsa->user_kuota_sementara_id))
                        {
                            if($kuota_sementara)
                            {
                                $kuota_sementara->transaksi_gagal      = (Int)$kuota_sementara->transaksi_gagal + 1;
                                $kuota_sementara->saldo                = (Int)$kuota_sementara->saldo + $pulsa->harga_beli;
                            }
                        }
                        else
                        {
                            $ceksaldo = UserDropshiper::where('user_id', $pulsa->user_id)->first();
                            $ceksaldo->saldo_terpakai           = $ceksaldo->saldo_terpakai - $pulsa->harga_beli;
                            $ceksaldo->saldo                    = $ceksaldo->saldo + $pulsa->harga_beli;
                            $ceksaldo->total_saldo_terpakai     = $ceksaldo->total_saldo_terpakai - $pulsa->harga_beli;
                            $ceksaldo->save();
                        }
                    }

                    # jika sebagai anggota
                    if($pulsa->user->access_id == 2)
                    {
                        # jika metode pembayaran transfer
                        if($pulsa->payment_method == 1)
                        {
                            $simpanan_sukarela              = new Deposit();
                            $simpanan_sukarela->nominal     = $pulsa->harga_beli_add_random_code;
                            $simpanan_sukarela->status      = 3;
                            $simpanan_sukarela->type        = 4;
                            $simpanan_sukarela->user_id      = $pulsa->user_id;
                            $simpanan_sukarela->save();

                            $anggota = UserAnggota::where('user_id', $pulsa->user_id)->first();
                            if($anggota)
                            {
                                $anggota->simpanan_sukarela = $anggota->simpanan_sukarela + $pulsa->harga_beli_add_random_code; 
                                $anggota->save();
                            }
                        }

                        # jika methode pembayaran simpanan
                        if($pulsa->payment_method == 2)
                        {
                            # calcalulate simpanan
                            $user                       = UserAnggota::where('user_id', $pulsa->user_id)->first();
                            $user->simpanan_sukarela    = $user->simpanan_sukarela + $pulsa->harga_beli; 
                            $user->save();

                            /** rubah status jadi batal  */
                            $withdrawal = Withdrawal::where('user_id', $pulsa->user_id)->where('transaksi_id', $pulsa->id)->where('nominal', $pulsa->harga_beli)->where('status', 1)->first();
                            if($withdrawal)
                            {
                                $withdrawal->status = 2;
                                $withdrawal->save();
                            }
                        }

                        # jika methode pembayaran kuota
                        if($pulsa->payment_method == 4)
                        {
                            # calcalulate kuota
                            $user                       = UserAnggota::where('user_id', $pulsa->user_id)->first();
                            $user->kuota                = $user->kuota + $pulsa->harga_beli; 
                            $user->kuota_terpakai       = $user->kuota_terpakai - $pulsa->harga_beli; 
                            $user->save();
                        }
                    }
                }
                
                if($kuota_sementara)
                {
                    $kuota_sementara->save();
                }
                $pulsa->save();

                # send notifikasi
                if(isset($pulsa->user->telepon))
                {
                    $msg = "";
                    if(isset($pulsa->pulsa->jenis_paket))
                    {
                        $msg .= "*TRANSAKSI ". $pulsa->pulsa->jenis_paket ."*\n\n";
                    }
                    elseif($pulsa->pulsa->jenis_product)
                    {
                        $msg .= "*TRANSAKSI ". $pulsa->pulsa->jenis_product ."*\n\n";
                    }
                    
                    $msg .= "No Transaksi :". $pulsa->no_invoice ."\n";
                    if($pulsa->pulsa->simko_provider_id==6 || $pulsa->pulsa->jenis_product == "PLN PASCABAYAR")
                    {
                        $msg .= 'No Meter/ID Pel ';
                    }
                    else
                    {
                        $msg .= 'Phone ';
                    }
                    $msg .= $pulsa->no_telepon ."\n";

                    if($pulsa->pulsa->jenis_product != "PLN PASCABAYAR")
                    {
                        $msg .= 'Harga Rp. '. number_format($pulsa->harga_beli) ."\n";
                    }
                   
                    $msg .= 'Status ';

                    switch($pulsa->status)
                    {
                        case(0):
                           $msg .= "Menunggu Pembayaran (Pending) \n";
                            break;
                        case(1):
                            $msg .= "Sedang Di proses\n";
                           break;
                       case(4):
                           $msg .= "Sedang Di proses\n";
                           break;
                        case(2):
                           $msg .= "Sukses\n";
                           break;
                       case(3):
                            $msg .="Gagal ". $pulsa->simko_message ."\n";
                         break;
                       default: 
                           $msg .= "Blank \n";
                        break;
                    }

                    if($pulsa->pulsa->jenis_product == "PLN PASCABAYAR")
                    {
                        $msg .= "Nama ". (isset($pulsa->plnPascabayar->nama) ? $pulsa->plnPascabayar->nama : '') ."\n";
                        $msg .= "Tarif / Daya ". (isset($pulsa->plnPascabayar->tarif_daya) ? $pulsa->plnPascabayar->tarif_daya : '') ."\n";
                        $msg .= "Periode ". (isset($pulsa->plnPascabayar->periode) ? parsing_pln_periode($pulsa->plnPascabayar->periode) : ''). "\n";
                        $msg .= "Denda ". (isset($pulsa->plnPascabayar->denda) ? $pulsa->plnPascabayar->denda : '') ."\n";
                        $msg .= "Tagihan PLN ". (isset($pulsa->plnPascabayar->tagihan) ? number_format($pulsa->plnPascabayar->tagihan) : '') ."\n";
                        $msg .= "Biaya Admin ". (isset($pulsa->plnPascabayar->biaya_admin) ? number_format($pulsa->plnPascabayar->biaya_admin) : ''). "\n";
                        $msg .= "Cashback ". (isset($pulsa->plnPascabayar->cashback) ? number_format($pulsa->plnPascabayar->cashback) : '') ."\n";
                        $msg .= "Total Dibayarkan ". number_format($pulsa->harga_beli) ."\n";
                    }

                    if($pulsa->status == 2 and $pulsa->pulsa->simko_provider_id==6 and $pulsa->pulsa->jenis_product != "PLN PASCABAYAR")
                    {
                        $msg .= "Nama ". (isset($pulsa->plnToken->nama) ? $pulsa->plnToken->nama : '') ."\n";
                        $msg .= "Tarif / Daya ". (isset($pulsa->plnToken->volt) ? $pulsa->plnToken->volt : ''). "\n";
                        $msg .= "Jumlah KWH ". (isset($pulsa->plnToken->jumlah_kwh) ? $pulsa->plnToken->jumlah_kwh : ''). "\n";
                        $msg .= "Stroom / Token *" . ltrim(isset($pulsa->plnToken->token) ? $pulsa->plnToken->token : ''). "* \n";
                    }
                    
                    $msg .= "Tanggal ". date('d F Y H:i:s', strtotime($pulsa->created_at)). "\n";
                    
                    ApiWhaCurl($pulsa->user->telepon, $msg);    
                }
            }
        }

        return $this->response()->success($response);
    }
    

    /**
     * [response_get description]
     * @return [type] [description]
     */
    public function response_get()
    {
        $response['status'] = 'success';
        $response['code'] = '200';
        
        if(isset($_GET['refid']))
        {
            // record post
            $data                   = new \Kodami\Models\Mysql\PPulsaResponse();
            $data->reffid           = $_GET['refid'];
            $data->pesan            = $_GET['message'];
            $data->result_post      = json_encode($_GET);
            $data->save(); 

            $pulsa                      = PPulsaTransaksi::where('simko_reff_id', $_GET['refid'])->first();

            if($pulsa)
            {
                $str = explode('#', $_GET['message']);
                
                if(isset($str[0]))
                {
                    $pulsa->simko_message       = respon_simko_pulsa($_GET['message']);
                }
                else
                {
                    $pulsa->simko_message       = $_GET['message'];
                }

                $kuota_sementara   = UserKuotaSementara::where('id', $pulsa->user_kuota_sementara_id)->first();

                #find status
                if (strpos($_GET['message'], '#1#') !== false) 
                {
                    $pulsa->status              = 2;

                    # jika token listrik
                    if(isset($pulsa->pulsa->simko_provider_id) and $pulsa->pulsa->simko_provider_id == 6)
                    {
                        $msg_token = parsingMessagePln($_GET['message']);

                        $token              = new TransaksiPlnToken();
                        $token->nama        = $msg_token['nama'];
                        $token->token       = $msg_token['token'];
                        $token->volt        = $msg_token['volt'];
                        $token->jumlah_kwh          = $msg_token['jumlah_kwh'];
                        $token->original_return     = $_GET['message'];
                        $token->save();
                        # set relasi token pln
                        $pulsa->transaksi_pln_token_id  = $token->id;
                    }

                    if(!empty($pulsa->user_kuota_sementara_id))
                    {
                        if($kuota_sementara)
                        {
                            $kuota_sementara->transaksi_sukses = (Int)$kuota_sementara->transaksi_sukses + 1;
                        }
                    }

                    # create invoice jika sukses
                    $cekkuotasementara = \Kodami\Models\Mysql\UserDropshiper::where('user_id', $pulsa->user_id)->first();
                    if($cekkuotasementara)
                    {
                        if($cekkuotasementara->kuota_sementara_is_avaliable == 1)
                        {
                            create_invoice($pulsa->user_id);
                            # update status kuota sementara
                            $cekkuotasementara->kuota_sementara_is_avaliable = 0;
                            $cekkuotasementara->save();
                        }
                    }
                }
                else
                {
                    $pulsa->status              = 3;

                    if(isset($pulsa->user->telepon))
                    {
                        $msg = "";
                        
                        ApiWhaCurl($pulsa->user->telepon, $msg);    
                    }


                    # jika akses sebagai dropshiper
                    if($pulsa->user->access_id == 7)
                    {
                        if(!empty($pulsa->user_kuota_sementara_id))
                        {
                            if($kuota_sementara)
                            {
                                $kuota_sementara->transaksi_gagal      = (Int)$kuota_sementara->transaksi_gagal + 1;
                                $kuota_sementara->saldo                = (Int)$kuota_sementara->saldo + $pulsa->harga_beli;
                                $kuota_sementara->total_nominal        = (Int)$kuota_sementara->total_nominal + $pulsa->harga_beli;
                            }
                        }
                        else
                        {
                            $ceksaldo = UserDropshiper::where('user_id', $pulsa->user_id)->first();
                            $ceksaldo->saldo_terpakai           = $ceksaldo->saldo_terpakai - $pulsa->harga_beli;
                            $ceksaldo->saldo                    = $ceksaldo->saldo + $pulsa->harga_beli;
                            $ceksaldo->total_saldo_terpakai     = $ceksaldo->total_saldo_terpakai - $pulsa->harga_beli;
                            $ceksaldo->save();
                        }
                    }

                    # jika sebagai anggota
                    if($pulsa->user->access_id == 2)
                    {
                        # jika metode pembayaran transfer
                        if($pulsa->payment_method == 1)
                        {
                            $simpanan_sukarela              = new Deposit();
                            $simpanan_sukarela->nominal     = $pulsa->harga_beli_add_random_code;
                            $simpanan_sukarela->status      = 3;
                            $simpanan_sukarela->type        = 4;
                            $simpanan_sukarela->user_id      = $pulsa->user_id;
                            $simpanan_sukarela->save();

                            $anggota = UserAnggota::where('user_id', $pulsa->user_id)->first();
                            if($anggota)
                            {
                                $anggota->simpanan_sukarela = $anggota->simpanan_sukarela + $pulsa->harga_beli_add_random_code; 
                                $anggota->save();
                            }
                        }

                        # jika methode pembayaran simpanan
                        if($pulsa->payment_method == 2)
                        {
                            # calcalulate simpanan
                            $user                       = UserAnggota::where('user_id', $pulsa->user_id)->first();
                            $user->simpanan_sukarela    = $user->simpanan_sukarela + $pulsa->harga_beli; 
                            $user->save();

                            /** rubah status jadi batal  */
                            $withdrawal = Withdrawal::where('user_id', $pulsa->user_id)->where('transaksi_id', $pulsa->id)->where('nominal', $pulsa->harga_beli)->where('status', 1)->first();
                            if($withdrawal)
                            {
                                $withdrawal->status = 2;
                                $withdrawal->save();
                            }
                        }

                        # jika methode pembayaran kuota
                        if($pulsa->payment_method == 4)
                        {
                            # calcalulate kuota
                            $user                       = UserAnggota::where('user_id', $pulsa->user_id)->first();
                            $user->kuota                = $user->kuota + $pulsa->harga_beli; 
                            $user->save();
                        }
                    }   
                }

                if($kuota_sementara)
                {
                    $kuota_sementara->save();
                }
                $pulsa->save();

                # send notifikasi
                if(isset($pulsa->user->telepon))
                {
                    $msg = "";
                    if(isset($pulsa->pulsa->jenis_paket))
                    {
                        $msg .= "*TRANSAKSI ". $pulsa->pulsa->jenis_paket ."*\n\n";
                    }
                    elseif($pulsa->pulsa->jenis_product)
                    {
                        $msg .= "*TRANSAKSI ". $pulsa->pulsa->jenis_product ."*\n\n";
                    }
                    
                    $msg .= "No Transaksi :". $pulsa->no_invoice ."\n";

                    if($pulsa->pulsa->simko_provider_id==6 || $pulsa->pulsa->jenis_product == "PLN PASCABAYAR")
                    {
                        $msg .= 'No Meter/ID Pel ';
                    }
                    else
                    {
                        $msg .= 'Phone ';
                    }
                    $msg .= $pulsa->no_telepon ."\n";

                    if($pulsa->pulsa->jenis_product != "PLN PASCABAYAR")
                    {
                        $msg .= 'Harga Rp. '. number_format($pulsa->harga_beli) ."\n";
                    }
                   
                    $msg .= 'Status ';

                    switch($pulsa->status)
                    {
                        case(0):
                           $msg .= "Menunggu Pembayaran (Pending) \n";
                            break;
                        case(1):
                            $msg .= "Sedang Di proses\n";
                           break;
                       case(4):
                           $msg .= "Sedang Di proses\n";
                           break;
                        case(2):
                           $msg .= "Sukses\n";
                           break;
                       case(3):
                            $msg .="Gagal ". $pulsa->simko_message ."\n";
                         break;
                       default: 
                           $msg .= "Blank \n";
                        break;
                    }

                    if($pulsa->pulsa->jenis_product == "PLN PASCABAYAR")
                    {
                        $msg .= "Nama ". (isset($pulsa->plnPascabayar->nama) ? $pulsa->plnPascabayar->nama : '') ."\n";
                        $msg .= "Tarif / Daya ". (isset($pulsa->plnPascabayar->tarif_daya) ? $pulsa->plnPascabayar->tarif_daya : '') ."\n";
                        $msg .= "Periode ". (isset($pulsa->plnPascabayar->periode) ? parsing_pln_periode($pulsa->plnPascabayar->periode) : ''). "\n";
                        $msg .= "Denda ". (isset($pulsa->plnPascabayar->denda) ? $pulsa->plnPascabayar->denda : '') ."\n";
                        $msg .= "Tagihan PLN ". (isset($pulsa->plnPascabayar->tagihan) ? number_format($pulsa->plnPascabayar->tagihan) : '') ."\n";
                        $msg .= "Biaya Admin ". (isset($pulsa->plnPascabayar->biaya_admin) ? number_format($pulsa->plnPascabayar->biaya_admin) : ''). "\n";
                        $msg .= "Cashback ". (isset($pulsa->plnPascabayar->cashback) ? number_format($pulsa->plnPascabayar->cashback) : '') ."\n";
                        $msg .= "Total Dibayarkan ". number_format($pulsa->harga_beli) ."\n";
                    }

                    if($pulsa->status == 2 and $pulsa->pulsa->simko_provider_id==6 and $pulsa->pulsa->jenis_product != "PLN PASCABAYAR")
                    {
                        $msg .= "Nama ". (isset($pulsa->plnToken->nama) ? $pulsa->plnToken->nama : '') ."\n";
                        $msg .= "Tarif / Daya ". (isset($pulsa->plnToken->volt) ? $pulsa->plnToken->volt : ''). "\n";
                        $msg .= "Jumlah KWH ". (isset($pulsa->plnToken->jumlah_kwh) ? $pulsa->plnToken->jumlah_kwh : ''). "\n";
                        $msg .= "Stroom / Token  *" . ltrim(isset($pulsa->plnToken->token) ? $pulsa->plnToken->token : ''). "* \n";
                    }
                    
                    $msg .= "Tanggal ". date('d F Y H:i:s', strtotime($pulsa->created_at)). "\n";

                    ApiWhaCurl($pulsa->user->telepon, $msg);    
                }
            }
        }

        return $this->response()->success($response);
    }
}
