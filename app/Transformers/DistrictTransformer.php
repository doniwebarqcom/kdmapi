<?php

namespace App\Transformers;

use Kodami\Models\Mysql\District;
use League\Fractal\TransformerAbstract;

class DistrictTransformer extends TransformerAbstract
{
    public function transform(District $d)
    {    
    	$district = $d ? $d->name : "";
    	$regency = isset($d->regency) ? $d->regency->name : "";
    	$province = isset($d->regency->province) ? $d->regency->province->name : "";

    	$long_address = $province.", ".$regency.", ".$district;
    	
        return [
	        'id'	=> isset($d->id) ? (int) $d->id : 0,
	        'name'	=> isset($d->name) ? $d->name : "",
	        'full_name'	=> $long_address,
	    ];
    }  
}

