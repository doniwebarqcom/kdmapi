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
        
        $notifications = json_decode( file_get_contents("php://input") );
       
        return $notifications;

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

                $invoice = PInvoice::where('status',2)->where('jenis_pembayaran', 1)->whereNotNull('unique')->get();
                foreach($invoice as $item)
                {
                  if($item->nominal == $mutasi->amount)
                  {
                    $data                 = PInvoice::where('id',$item->id)->first();
                    $data->mutation_id    = $temp->id;
                    $data->status = 3;
                    $data->save();

                    PPulsaTransaksi::where('invoice_id', $item->id)->update(['status_pembayaran' => 2]);

                    // UPDATE KUOTA
                    $kuota                = UserDropshiper::where('user_id', $item->user_id)->first();
                    $kuota->saldo         = $kuota->saldo + ($mutasi->amount - $item->unique);
                    $kuota->saldo_terpakai= $kuota->saldo_terpakai - ($mutasi->amount - $item->unique);
                    $kuota->save();

                    // HISTORY KUOTA
                    $history                    = new UserDropshiperHistoryKuota();
                    $history->user_id           = $item->user_id;
                    $history->user_proses_id    = 0;
                    $history->nominal           = $mutasi->amount;
                    $history->type              = 2; // topup by transfer invoice
                    $history->save();
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
