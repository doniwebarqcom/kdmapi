<?php

namespace App\Transformers;

use Kodami\Models\Mysql\KodamiProduct;
use League\Fractal\TransformerAbstract;

class KodamiProductMiniTransformer extends TransformerAbstract
{

    public function transform(KodamiProduct $product)
    {    
        $data =  [
            'id'                    => (int) $product->id,
            'category'              => isset($product->category->name) ? $product->category->name : null,
            'name'                  => $product->name,
            'alias'                 => $product->name_alias
        ];

        return $data;
    }
}

