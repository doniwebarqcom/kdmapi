<?php

namespace App\Http\Controllers;

use App\Transformers\MemberTransformer;
use Illuminate\Http\Request;
use Kodami\Models\Mysql\Dropshiper;
use Tymon\JWTAuth\JWTAuth;
use Validator;

class DropshiperController extends ApiController
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
    }

    public function store(JWTAuth $JWTAuth)
    {
    	$rules = [
            'physical_store' => 'required',
            'ocupation'		 => 'required',
            'selling_env' 	 => 'required',
            'place_selling'	 => 'required',
            'province' 		 => 'required',
            'regency' 		 => 'required',
            'district' 		 => 'required',
            'village' 		 => 'required',
            'province' 		 => 'required',
            'postal_code'	 => 'required'
        ];

    	$validator = Validator::make(
    		$this->request->all(),
    		$rules
		);

    	if ($validator->fails())
			return $this->response()->error($validator->errors()->all());

    	$physical_store = $this->request->physical_store;
        $ocupation = $this->request->ocupation;
        $selling_env = $this->request->selling_env;
        $place_selling = $this->request->place_selling;
        $province = $this->request->province;
        $regency = $this->request->regency;
        $district = $this->request->district;
        $village = $this->request->village;
        $postal_code = $this->request->postal_code;
        $image = $this->request->image;

        $user =  $JWTAuth->parseToken()->authenticate();
        $dropshiper = Dropshiper::where('member_id', $user->id)->first();
        if (! $dropshiper) {
        	$dropshiper = new Dropshiper;
        }

        $dropshiper->member_id = $user->id;
        $dropshiper->province_id = $province;
        $dropshiper->regency_id = $regency;
        $dropshiper->district_id = $district;
        $dropshiper->village_id = $village;
        $dropshiper->has_physical_store = $physical_store;
        $dropshiper->occupation =  $ocupation;
        $dropshiper->place_to_sell = $selling_env;
        $dropshiper->postal_code = $postal_code;
        $dropshiper->location = $place_selling;   
        $dropshiper->image = $image;   

		if(! $dropshiper->save())
			return $this->response()->error('failed save in database');

        $token = $JWTAuth->fromUser($user);
        return $this->response()->success($dropshiper, ['meta.token' => $token]);
        
    }
}
