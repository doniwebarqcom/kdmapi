<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\JWTAuth;
use Validator;

class MemberController extends ApiController
{

    public function register()
    {
    	$data =  Member::get();
    	$rules = [
                'name' 		=> 'required|min:3',
                'email' 	=> 'required|email|unique:members,email',
                'username' 	=> 'required|unique:members,username',
                'password' 	=> 'required|alpha_num|between:6,12',
                'address' 	=> 'required',
                'phone' 	=> 'required'
        ];

    	$validator = Validator::make(
    		$this->request->all(),
    		$rules
		);
		

		if ($validator->fails())
			return $this->response()->error($validator->errors()->all());

		$member = new Member;
		$member->name	= $this->request->get('name');
		$member->email	= $this->request->get('email');
		$member->username	= $this->request->get('username');
		$member->password	= Hash::make($this->request->get('password'));
		$member->address	= $this->request->get('address');
		$member->phone	= $this->request->get('phone');

		if(! $member->save())
			return $this->response()->error("failed save data");
    	
    	return $this->response()->success($member);
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
		$Member = "";
		
		if($username == "" )
		{

			$Member = Member::where('email', $email)->first();

		} elseif($password == "" ) {

			$Member = Member::Where('username', $username)->first();

		}

		if( ! (Hash::check($password, $Member->password)))
			return $this->response()->error("Wrong username or email or password");

		$token = $JWTAuth->fromUser($Member);

		return $this->response()->success($Member, ['meta.token' => $token]);
    }

}
