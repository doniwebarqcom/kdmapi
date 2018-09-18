<?php

namespace App\Http\Controllers\Pulsa;

use App\Http\Controllers\ApiController;
use Tymon\JWTAuth\JWTAuth;

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
    public function response_post(JWTAuth $JWTAuth)
    {
        $response['status'] = 'success';
        $response['code'] = '200';

        // record post
       	$data 				       = new \Kodami\Models\Mysql\PPulsaResponse();
       	$data->reffid 	     = $this->request->refid;
       	$data->pesan 		     = $this->request->message;
       	$data->result_post 	 = json_encode($this->request->all());
       	$data->save(); 

        return $response;
    }
}
