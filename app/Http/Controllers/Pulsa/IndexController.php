<?php

namespace App\Http\Controllers\Pulsa;

use App\Http\Controllers\ApiController;
use Tymon\JWTAuth\JWTAuth;
use Kodami\Models\Mysql\PPulsaTransaksi;

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
            $pulsa->simko_messsage      = $this->request->message;
            #find status
            if (strpos($this->request->message, '#1') !== false)
            {
                $pulsa->status              = 2;
            }
            else
            {
                $pulsa->status              = 3;
                $pulsa->simko_messsage      = $this->request->message;
            }
            $pulsa->save();
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

            $pulsa                      = PPulsaTransaksi::where('simko_reff_id', $this->request->refid)->first();
            #find status
            if (strpos($this->request->message, '#1') !== false)
            {
                $pulsa->status              = 2;
            }
            else
            {
                $pulsa->status              = 3;
                $pulsa->simko_messsage      = $_GET['message'];
            }
            $pulsa->simko_messsage      = $_GET['message'];
            $pulsa->save();
        }

        return $this->response()->success($response);
    }
}
