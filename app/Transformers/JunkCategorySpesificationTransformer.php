<?php

namespace App\Transformers;

use Kodami\Models\Mysql\JunkCategorySpesification;
use League\Fractal\TransformerAbstract;

class JunkCategorySpesificationTransformer extends TransformerAbstract
{
    public function transform(JunkCategorySpesification $junk)
    {    
        
        $data =  [
            'id'    => $junk->category_spesification_id,
            'label' => $junk->spesification->label ? $junk->spesification->label : null
        ];

        return $data;
    }
}

