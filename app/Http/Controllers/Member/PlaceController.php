<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\ApiController;
use App\Transformers\MemberPlacePickupTransformer;
use Kodami\Models\Mysql\MemberPlacePickup;
use Tymon\JWTAuth\JWTAuth;
use Validator;

class PlaceController extends ApiController
{
	public function store(JWTAuth $JWTAuth)
	{		
		$rules = [
            'addres' 					=> 'required',
            'district_id' 				=> 'required',
            'phone_number_recipient' 	=> 'required',
            'place_name' 				=> 'required',
            'postal_code' 				=> 'required',
            'recipient_name' 			=> 'required'
        ];

    	$validator = Validator::make(
    		$this->request->all(),
    		$rules
		);

		if ($validator->fails())
            return $this->response()->error($validator->errors()->all());

        $member =  $JWTAuth->parseToken()->authenticate();
        $place = new MemberPlacePickup;
        $place->member_id = $member->id;
        $place->addres = $this->request->addres;
        $place->district_id = $this->request->district_id;
        $place->phone_number_recipient = $this->request->phone_number_recipient;
        $place->place_name	 = $this->request->place_name;
        $place->postal_code = $this->request->postal_code;
        $place->recipient_name = $this->request->recipient_name;

        if(! $place->save())
            return $this->response()->error("error at saving data");

        return $this->response()->success($place);
	}

	public function getPlace(JWTAuth $JWTAuth)
	{
		$rules = [
            'id' => 'required'
        ];

    	$validator = Validator::make(
    		$this->request->all(),
    		$rules
		);

		if ($validator->fails())
            return $this->response()->error($validator->errors()->all());

		$id = $this->request->id;
		$place = MemberPlacePickup::find($id);
		$member =  $JWTAuth->parseToken()->authenticate();
		$token = $JWTAuth->fromUser($member);
        
        return $this->response()->success($place, ['meta.token' => $token] , 200, new MemberPlacePickupTransformer(), 'item');
	}

	public function put(JWTAuth $JWTAuth)
	{
		$rules = [
            'id' 						=> 'required',
            'addres' 					=> 'required',
            'district_id' 				=> 'required',
            'phone_number_recipient' 	=> 'required',
            'place_name' 				=> 'required',
            'postal_code' 				=> 'required',
            'recipient_name' 			=> 'required'
        ];

    	$validator = Validator::make(
    		$this->request->all(),
    		$rules
		);

		if ($validator->fails())
            return $this->response()->error($validator->errors()->all());

        $place = MemberPlacePickup::find($this->request->id);
        if(! $place)
        	return $this->response()->error("data not found");

        $member =  $JWTAuth->parseToken()->authenticate();
        $place->member_id = $member->id;
        $place->addres = $this->request->addres;
        $place->district_id = $this->request->district_id;
        $place->phone_number_recipient = $this->request->phone_number_recipient;
        $place->place_name	 = $this->request->place_name;
        $place->postal_code = $this->request->postal_code;
        $place->recipient_name = $this->request->recipient_name;

        if(! $place->save())
            return $this->response()->error("error at saving data");

        return $this->response()->success($place);
	}

	public function destroy(JWTAuth $JWTAuth)
	{
		$member =  $JWTAuth->parseToken()->authenticate();
		$place = MemberPlacePickup::where('id', $this->request->id)->where('member_id' , $member->id)->first();

		if(! $place->delete())
            return $this->response()->error("error at delete data");

        return $this->response()->success([]);
	}
}