<?php

namespace App\Transformers;

use Kodami\Models\Mysql\JunkCategoryCriteria;
use League\Fractal\TransformerAbstract;

class JunkCategoryCriteriaTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'selection'
    ];

    public function transform(JunkCategoryCriteria $junk)
    {    
        
        $data =  [
            'id'    => $junk->category_criteria_id,
            'label' => $junk->criteria->label ? $junk->criteria->label : null
        ];

        return $data;
    }

    public function includeSelection(JunkCategoryCriteria $junk)
    {
        if(isset( $junk->criteria->selection))
            return $this->collection($junk->criteria->selection, new ValueCategoryCriteriaTransformer);
    }
}

