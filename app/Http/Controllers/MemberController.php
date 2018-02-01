<?php

namespace App\Http\Controllers;

use App\Transformers\MemberTransformer;
use Illuminate\Support\Facades\Hash;
use Kodami\Models\Mysql\Member;
use Tymon\JWTAuth\JWTAuth;
use Validator;

class MemberController extends ApiController
{

    public function register(JWTAuth $JWTAuth)
    {    
    	$rules = [
            'name' 		=> 'required',
            'email' 	=> 'required|email',
            'username' 	=> 'required',
            'password' 	=> 'required|alpha_num|between:6,12',
            'address' 	=> '',
            'phone' 	=> 'required'
        ];

    	$validator = Validator::make(
    		$this->request->all(),
    		$rules
		);

        if ($validator->fails())
            return $this->response()->error($validator->errors()->all());
		
        $cekMember = Member::where('email', $this->request->get('email'))->first();
        if ($cekMember)
            return $this->response()->error('User already exists', 409);		

		$member = new Member;
		$member->name	= $this->request->get('name');
		$member->email	= $this->request->get('email');
		$member->username	= $this->request->get('username');
		$member->password	= Hash::make($this->request->get('password'));
		$member->address	= $this->request->get('address');
		$member->phone	= $this->request->get('phone');

		if(! $member->save())
			return $this->response()->error("failed save data");
    	
        $token = $JWTAuth->fromUser($member);
        return $this->response()->success($member, ['meta.token' => $token] , 200, new MemberTransformer(), 'item');
    }

    public function login(JWTAuth $JWTAuth)
    {
    	$email = $this->request->get('email');
    	$username = $this->request->get('username');

    	if( $username == "" AND $email == "")
    		return $this->response()->error("email or username cant be null");

    	$rules = [
                'password' 	=> 'required',
        ];

    	$validator = Validator::make(
    		$this->request->all(),
    		$rules
		);

		if ($validator->fails())
			return $this->response()->error($validator->errors()->all());

		$password = $this->request->get('password');
		$member = "";		
		
        if($username == "" )
			$member = Member::where('email', $email)->first();
        elseif($email == "" )
		  $member = Member::Where('username', $username)->first();

		if( ! $member OR ! (Hash::check($password, $member->password)))
			return $this->response()->error("Wrong username or email or password");

		$token = $JWTAuth->fromUser($member);
        return $this->response()->success($member, ['meta.token' => $token] , 200, new MemberTransformer(), 'item');
    }

    public function getUser(JWTAuth $JWTAuth)
    {        
        // $token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vYXBpLmtvZGFtaS5jdWsvdXNlci9pbmZvIiwiaWF0IjoxNTE2NzI3MjM1LCJleHAiOjE1MTkzMTkyMzUsIm5iZiI6MTUxNjcyNzIzNSwianRpIjoiRmxIcDdKTDFYUVNZaDdCNCIsInN1YiI6MjIsInBydiI6IjQwM2VjZWY3NDk1N2YzNmZkMmU3OGU4MjliZjRlYTg1NTRkYWIyMDYifQ.0ngk87W-zBrrPXXndjX5HSBqsXrWDinmY6G63NczXYY";
        // $JWTAuth->setToken($token);
        // $user = $JWTAuth->user();
        // return $user;

        $user =  $JWTAuth->parseToken()->authenticate();
        $token = $JWTAuth->getToken();
        return $this->response()->success($user, ['meta.token' => (string) $token] , 200, new MemberTransformer(), 'item');
    }

}
