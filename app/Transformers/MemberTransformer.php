<?php

namespace App\Transformers;

use Kodami\Models\Mysql\Member;
use League\Fractal\TransformerAbstract;

class MemberTransformer extends TransformerAbstract
{
    public function transform(Member $member)
    {    
        $data =  [
            'id'                => (int) $member->id,
            'email'             => $member->email,
            'username'          => $member->username,
            'phone'             => $member->phone,
            'phone'             => $member->phone,
            'address'           => $member->address,
            'shop'              => $member->shop
        ];
        
        return $data;
    }  
}

