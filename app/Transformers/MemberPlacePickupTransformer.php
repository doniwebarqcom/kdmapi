<?php

namespace App\Transformers;

use Kodami\Models\Mysql\MemberPlacePickup;
use League\Fractal\TransformerAbstract;

class MemberPlacePickupTransformer extends TransformerAbstract
{
    public function transform(MemberPlacePickup $pickup)
    {    
    	
    	$district = $pickup->district ? $pickup->district->name : "";
    	$regency = $pickup->district->regency ? $pickup->district->regency->name : "";
    	$province = $pickup->district->regency->province ? $pickup->district->regency->province->name : "";

    	$long_address = $province.", ".$regency.", ".$district;
        $data =  [
            'id'						=> $pickup->id,
            'district_id'				=> $pickup->district_id,
            'long_address'				=> $long_address,
            'place_name'				=> $pickup->place_name,
            'recipient_name'			=> $pickup->recipient_name,
            'phone_number_recipient'	=> $pickup->phone_number_recipient,
            'postal_code'				=> $pickup->postal_code,
            'addres' 					=> $pickup->addres,
        ];    

        return $data;
    }  
}

