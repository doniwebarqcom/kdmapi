<?php

namespace App\Transformers;

use Kodami\Models\Mysql\CartItem;
use League\Fractal\TransformerAbstract;

class CartItemTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'product'
    ];

    public function transform(CartItem $cart)
    {    
        $data =  [
            'id'        => (int) $cart->id,
            'quantity'  => (int) $cart->quantity,
        ];

        return $data;
    }

    public function includeProduct(CartItem $cart)
    {
        if(isset($cart->product))
            return $this->item($cart->product, new KodamiProductTransformer);       
    }
}

