<?php

namespace App\Transformers;

use Kodami\Models\Mysql\KodamiProductSpesification;
use League\Fractal\TransformerAbstract;

class KodamiProductSpecificationTransformer extends TransformerAbstract
{
    public function transform(KodamiProductSpesification $spec)
    {    
        $data =  [
            'id'		=> (int) $spec->id,
            'label'		=> $spec->category->label,
            'value'		=> $spec->value
        ];

        return $data;
    }
}

