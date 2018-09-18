<?php

namespace App\Transformers;

use Kodami\Models\Mysql\CategoryCriteria;
use League\Fractal\TransformerAbstract;

class CriteriaTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'selection'
    ];

    public function transform(CategoryCriteria $Criteria)
    {    
        
        $data =  [
            'id'    => $Criteria->id,
            'label' => $Criteria->label
        ];

        return $data;
    }

    public function includeSelection(CategoryCriteria $Criteria)
    {
        if(isset($Criteria->selection))
            return $this->collection($Criteria->selection, new ValueCategoryCriteriaTransformer);
    }
}

