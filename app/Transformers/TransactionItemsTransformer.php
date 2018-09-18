<?php

namespace App\Transformers;

use Kodami\Models\Mysql\TransactionItem;
use League\Fractal\TransformerAbstract;

class TransactionItemsTransformer extends TransformerAbstract
{
    public function transform(TransactionItem $transaction)
    {
        $data =  [
            'id'				=> $transaction->id, 
            'product_name'		=> $transaction->product_name,
            'product_price'		=> $transaction->product_price,
            'product_weight'	=> $transaction->product_weight,
            'quantity'			=> $transaction->quantity,
            'addres'			=> $transaction->addres,
            'postal_code'		=> $transaction->postal_code,
            'image'				=> $transaction->product->primary_image ? $transaction->product->primary_image : "",
            'district'			=> $transaction->district->name ? $transaction->district->name : "",
        ];    

        return $data;
    }   
}