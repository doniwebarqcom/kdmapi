<?php

namespace App\Http\Controllers;

use App\Http\Controllers\ApiController;
use Tymon\JWTAuth\JWTAuth;
use Kodami\Models\Mysql\PPulsaTransaksi;
use Kodami\Models\Mysql\UserDropshiper;
use Kodami\Models\Mysql\Mutation;
use Kodami\Models\Mysql\PInvoice;
use Kodami\Models\Mysql\RekeningBank;
use Kodami\Models\Mysql\UserDropshiperHistoryKuota;

class MootaController extends Controller
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
        return "";
        $response['status'] = 'success';
        $response['code'] = '200';
        
        $temp = json_decode( file_get_contents("php://input") );
        
        if(!is_array($temp)) {
            $temp = json_decode($temp);
        }

        foreach($temp as $mutasi)
        {
            if(isset($mutasi->id))
            {
                $temp = Mutation::where('mutation_id', $mutasi->id)->first();
                $bank = RekeningBank::where('moota_bank_id', $mutasi->bank_id)->first();

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

                if(!$temp and $bank)
                {
                    $temp                   = new Mutation();
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
                        $history->nominal           = $mutasi->amount - $item->unique;
                        $history->type              = 2; // topup by transfer invoice
                        $history->save();
                      }
                    }         
                }
            }
        }

        return $response;        
    }
}