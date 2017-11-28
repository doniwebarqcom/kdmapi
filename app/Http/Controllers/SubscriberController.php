<?php

namespace App\Http\Controllers;

use App\Models\Subscriber;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\JWTAuth;
use Validator;

class SubscriberController extends ApiController
{
	public function index()
	{
		$rules = [
                'email' 	=> 'required|email|unique:subscribers,email'
        ];

    	$validator = Validator::make(
    		$this->request->all(),
    		$rules
		);
		
		
		if ($validator->fails())
			return $this->response()->error($validator->errors()->all());	

		$Subscriber = new Subscriber;
		$Subscriber->email	= $this->request->get('email');

		if(! $Subscriber->save())
			return $this->response()->error("failed save data");
    	
    	return $this->response()->success($Subscriber);
	}
}