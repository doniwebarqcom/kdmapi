<?php

namespace App\Http\Controllers;

use App\Http\Controllers\ApiController;
use Tymon\JWTAuth\JWTAuth;
use Kodami\Models\Mysql\PPulsaTransaksi;
use Kodami\Models\Mysql\UserDropshiper;
use Kodami\Models\Mysql\Mutation;
use Kodami\Models\Mysql\PInvoice;
use Kodami\Models\Mysql\UserDropshiperHistoryKuota;

class MootaController extends ApiController
{
	/**
	 * Index Response
	 * @param  JWTAuth $JWTAuth [description]
	 * @return [type]           [description]
	 */
    public function index()
    {
        return;
    }

    /**
     * [response_post description]
     * @return [type] [description]
     */
    public function response_post()
    {
        $response['status'] = 'success';
        $response['code'] = '200';
        $mutasi      = $this->request;

        if(isset($mutasi->id))
        {
            $temp = \Kodami\Models\Mysql\Mutation::where('mutation_id', $mutasi->id)->first();

            if(!$temp)
            {
                $temp                   = new \Kodami\Models\Mysql\Mutation();
                //$temp->rekening_bank_id = $bank->id;
                $temp->date_transfer    = $mutasi->date;
                $temp->description      = $mutasi->description;
                $temp->amount           = $mutasi->amount;
                $temp->type             = $mutasi->type == 'DB' ? 2 : 1;
                $temp->account_number   = $mutasi->account_number;
                $temp->mutation_id      = $mutasi->id;
                $temp->save();

                $invoice = PInvoice::where('status',2)->where(function($table){ 
                    $table->where('jenis_pembayaran', 1)
                          ->orWhere('jenis_pembayaran', 3);
                })->whereNotNull('unique')->get();

                foreach($invoice as $item)
                {
                  if($item->nominal == $mutasi->amount)
                  {
                    # FUNCTION APPROVE INVOICE HERE
                    approve_invoice($item->id, $temp->id);
                  }
                }         
            }
        }
        else
        {
            $response['status'] = 'error';
            $response['code'] = '300';
        }

        $response['data_from_moota'] = $mutasi;

        return $this->response()->success($response);        
    }
}