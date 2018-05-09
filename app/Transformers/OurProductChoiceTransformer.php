<?php

namespace App\Transformers;

use Kodami\Models\Mysql\ChoiceOfOurProductFront;
use League\Fractal\TransformerAbstract;

class OurProductChoiceTransformer extends TransformerAbstract
{  
    public function transform(ChoiceOfOurProductFront $choice)
    {    
        $data =  [
            'id'                => (int) $choice->id,            
            'product_id'        => (int) $choice->kodami_product_id,
            'name'              => $choice->product->name,
            'alias'             => $choice->product->name_alias,
            'category'          => $choice->product->category->full_name,
            'price'             => $choice->product->price,
            'price_discont'     => 0,
            'discont'           => $choice->product->discont,
            'discont_anggota'   => $choice->product->discont_anggota,
            'image'             => $choice->image,
        ];

        return $data;
    }
}

