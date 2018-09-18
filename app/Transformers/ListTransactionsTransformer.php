<?php

namespace App\Transformers;

use Kodami\Models\Mysql\Transaction;
use League\Fractal\TransformerAbstract;

class ListTransactionsTransformer extends TransformerAbstract
{
	protected $availableIncludes = [
        'items'
    ];

    public function transform(Transaction $transaction)
    {        	
        $data =  [
            'id'				=> $transaction->id,
            'transaction_code'	=> $transaction->transaction_code,
            'price_total'		=> (int) $transaction->price_product,
            'shipping'			=> (int) $transaction->shipping,
            'admin_fee'			=> (int) $transaction->admin_fee,
            'unique_number'		=> (int) $transaction->fee_random,
            'type_payment'		=> (int) $transaction->type_payment,
            'status'			=> (int) $transaction->status,
            'created_at'		=> strtotime($transaction->created_at),
        ];    

        return $data;
    }

    public function includeItems(Transaction $transaction)
    {
        if(isset($transaction->items))
            return $this->collection($transaction->items, new TransactionItemsTransformer);
        else
            return [];
    }
}