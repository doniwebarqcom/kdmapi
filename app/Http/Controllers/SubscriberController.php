<?php

namespace App\Http\Controllers;

use Kodami\Models\Mysql\Subscriber;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\JWTAuth;
use Validator;

class SubscriberController extends ApiController
{
	public function index()
	{
		$rules = [
                'email' 	=> 'required|email'
        ];

    	$validator = Validator::make(
    		$this->request->all(),
    		$rules
		);
		
		if ($validator->fails())
			return $this->response()->error($validator->errors()->all());	

		$Subscriber = Subscriber::where('email', $this->request->get('email'))->first();

		if(!$Subscriber)
		{
			$Subscriber = new Subscriber;
			$Subscriber->email	= $this->request->get('email');	
		} else {
			
			if($Subscriber->is_active == 1)
				return $this->response()->error("your email already subcribe");	
			else
				$Subscriber->is_active = 1;
		}


		if(! $Subscriber->save())
			return $this->response()->error("failed save data");
    	
    	return $this->response()->success($Subscriber);
	}

	public function delete()
	{
		$rules = [
                'email' 	=> 'required|email'
        ];

    	$validator = Validator::make(
    		$this->request->all(),
    		$rules
		);

		$Subscriber = Subscriber::where('email', $this->request->get('email'))->first();

		if($Subscriber)
		{
			$Subscriber->is_active = 0;
			if(! $Subscriber->save())
				return $this->response()->error("failed save data");
		}
    	
    	return $this->response()->success(['succes UnSubscribe']);
	}
}