<?php

namespace App\Transformers;

use Kodami\Models\Mysql\ProductSpecialOffer;
use League\Fractal\TransformerAbstract;

class ProductSpecialOfferTransformer extends TransformerAbstract
{  
    protected $availableIncludes = [
        'product'
    ];

    public function transform(ProductSpecialOffer $special)
    {    
        $data =  [
            'id'                    => (int) $special->id,            
            'long_description'      => $special->long_description,
            'short_description'     => $special->short_description,
            'image'                 => $special->image,
            'saving'                => (int) $special->save_money,
            'expired_time'          => \Carbon\Carbon::createFromFormat('Y-m-d h:i:s', $special->expired_time)->timestamp
        ];

        return $data;
    }

    public function includeProduct(ProductSpecialOffer $special)
    {
        if(isset($special->product))
            return $this->item($special->product, new KodamiProductTransformer);
        else
            return [];
    }
}

