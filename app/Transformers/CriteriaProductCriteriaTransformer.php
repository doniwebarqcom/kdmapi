<?php

namespace App\Transformers;

use Kodami\Models\Mysql\ProductCriteria;
use League\Fractal\TransformerAbstract;

class CriteriaProductCriteriaTransformer extends TransformerAbstract
{
    public function transform(ProductCriteria $criteria)
    {    
        $data =  [
            'id'		=> (int) $criteria->id,
            'label'		=> $criteria->valueCriteria->criteria->label,
            'value'		=> $criteria->valueCriteria->value
        ];

        return $data;
    }
}

