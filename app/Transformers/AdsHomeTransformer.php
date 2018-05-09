<?php

namespace App\Transformers;

use Kodami\Models\Mysql\AdsHome;
use League\Fractal\TransformerAbstract;

class AdsHomeTransformer extends TransformerAbstract
{  
    protected $availableIncludes = [
        'product'
    ];

    public function transform(AdsHome $ads)
    {    
        $data =  [
            'id'        => (int) $ads->id,            
            'image'     => $ads->image,
            'width'     => $ads->width,
            'height'    => $ads->height,
        ];

        return $data;
    }

    public function includeProduct(AdsHome $ads)
    {
        if(isset($ads->product))
            return $this->item($ads->product, new KodamiProductTransformer);
        else
            return [];
    }
}

