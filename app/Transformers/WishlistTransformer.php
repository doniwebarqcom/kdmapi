<?php

namespace App\Transformers;

use Kodami\Models\Mysql\Wishlist;
use League\Fractal\TransformerAbstract;

class WishlistTransformer extends TransformerAbstract
{  
    protected $availableIncludes = [
        'product'
    ];

    public function transform(Wishlist $wishlist)
    {    
        $data =  [
            'id'                    => (int) $wishlist->id
        ];

        return $data;
    }

    public function includeProduct(Wishlist $wishlist)
    {
        if(isset($wishlist->product))
            return $this->item($wishlist->product, new KodamiProductTransformer);
        else
            return [];
    }
}

