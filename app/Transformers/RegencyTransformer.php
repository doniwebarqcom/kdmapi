<?php

namespace App\Transformers;

use Kodami\Models\Mysql\Regency;
use League\Fractal\TransformerAbstract;

class RegencyTransformer extends TransformerAbstract
{
    public function transform(Regency $regency)
    {    
        return [
	        'id'	=> (int) $regency->id,
	        'name'	=> $regency->name,
	    ];
    }  
}

