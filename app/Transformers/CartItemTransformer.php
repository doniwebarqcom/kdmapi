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
            'id'                        => (int) $cart->id,
            'quantity'                  => (int) $cart->quantity,
            'shipping_cost'             => (int) $cart->shipping_cost,
            'recipient_name'            => $cart->recipient_name,
            'phone_number_recipient'    => $cart->phone_number_recipient,
            'postal_code'               => $cart->postal_code,
            'addres'                    => $cart->addres,
            'province'                  => $cart->district->regency->province->name,
            'regency'                   => $cart->district->regency->name,
            'district'                  => $cart->district->name,
        ];

        return $data;
    }

    public function includeProduct(CartItem $cart)
    {
        if(isset($cart->product))
            return $this->item($cart->product, new KodamiProductTransformer);       
    }
}

