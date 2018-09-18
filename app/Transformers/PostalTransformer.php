<?php

namespace App\Transformers;

use Kodami\Models\Mysql\KodePos;
use League\Fractal\TransformerAbstract;

class PostalTransformer extends TransformerAbstract
{
    public function transform(KodePos $pos)
    {    
        return [
	        'id'      		=> (int) $pos->id,
	        'kelurahan'		=> $pos->kelurahan,
	        'kecamatan'		=> $pos->kecamatan,
	        'kabupaten'		=> $pos->kabupaten,
	        'provinsi'		=> $pos->provinsi,
	        'kodepos'   	=> $pos->kodepos
	    ];
    }  
}

