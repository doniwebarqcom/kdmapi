<?php

namespace App\Transformers;

use Kodami\Models\Mysql\Member;
use League\Fractal\TransformerAbstract;

class MemberTransformer extends TransformerAbstract
{
    public function transform(Member $member)
    {    
        $shop = null;
        if($member->have_shop == 1)
            $shop = $member->shop;

        $data =  [
            'id'                => (int) $member->id,
            'email'             => $member->email,
            'username'          => $member->username,
            'phone'             => $member->phone,
            'phone'             => $member->phone,
            'address'           => $member->address,
            'have_shop'         => (int) $member->have_shop,
            'shop'              => $shop
        ];


        return $data;
    }  
}

