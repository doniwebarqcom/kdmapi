<?php

namespace App\Transformers;

use Kodami\Models\Mysql\ProductSpesification;
use League\Fractal\TransformerAbstract;

class SpesificationProductCriteriaTransformer extends TransformerAbstract
{
    public function transform(ProductSpesification $spec)
    {    
        $data =  [
            'id'		=> (int) $spec->id,
            'label'		=> $spec->category->label,
            'value'		=> $spec->value
        ];

        return $data;
    }
}

