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
            'name'              => $member->name,
            'gender'            => (int) $member->gender,
            'username'          => $member->username,
            'phone'             => $member->phone,            
            'address'           => $member->address,
            'image'             => $member->image,
            'birth'             => $member->birth,
            'shop'              => $member->shop,
            'dropshiper'        => $member->dropshiper,
        ];
        
        return $data;
    }  
}

