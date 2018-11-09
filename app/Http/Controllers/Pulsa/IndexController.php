<?php

namespace App\Http\Controllers\Pulsa;

use App\Http\Controllers\ApiController;
use Tymon\JWTAuth\JWTAuth;
use Kodami\Models\Mysql\PPulsaTransaksi;
use Kodami\Models\Mysql\UserDropshiper;
use Kodami\Models\Mysql\UserKuotaSementara;
use Kodami\Models\Mysql\TransaksiPlnToken;

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
    /*
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
                $pulsa->simko_message       = respon_simko_pulsa(@$str[0]);

                $kuota_sementara   = UserKuotaSementara::where('id', $pulsa->user_kuota_sementara_id)->first();

                #find status
                if (@$str[0] == 1)
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
                        }
                    }
                }
                else
                {
                    $pulsa->status              = 3;

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
                
                if($kuota_sementara)
                {
                    $kuota_sementara->save();
                }
                $pulsa->save();
            }
        }

        return $this->response()->success($response);
    }
    */

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
                    $pulsa->simko_message       = respon_simko_pulsa(@$str[0]);
                }
                else
                {
                    $pulsa->simko_message       = $_GET['message'];
                }

                $kuota_sementara   = UserKuotaSementara::where('id', $pulsa->user_kuota_sementara_id)->first();

                #find status
                if (@$str[0] == 1)
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
                        }
                    }
                }
                else
                {
                    $pulsa->status              = 3;

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

                if($kuota_sementara)
                {
                    $kuota_sementara->save();
                }
                $pulsa->save();
            }
        }

        return $this->response()->success($response);
    }
}
