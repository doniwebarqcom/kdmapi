<?php

namespace App\Transformers;

use Kodami\Models\Mysql\ValueCategoryCriteria;
use League\Fractal\TransformerAbstract;

class ValueCategoryCriteriaTransformer extends TransformerAbstract
{
    public function transform(ValueCategoryCriteria $value)
    {    
        
        $data =  [
            'id'    => $value->id,
            'value' => $value->value
        ];

        return $data;
    }
}

