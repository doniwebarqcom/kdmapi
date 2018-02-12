<?php

namespace App\Http\Controllers;

use App\Transformers\MemberTransformer;
use Illuminate\Support\Facades\Hash;
use Kodami\Models\Mysql\Member;
use Kodami\Models\Mysql\RegistrationMemberByPhone;
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
        $user =  $JWTAuth->parseToken()->authenticate();
        $token = $JWTAuth->getToken();
        return $this->response()->success($user, ['meta.token' => (string) $token] , 200, new MemberTransformer(), 'item');
    }

    public function phone()
    {
        $rules = [
            'phone'      => 'required'
        ];

        $validator = Validator::make(
            $this->request->all(),
            $rules
        );

        if ($validator->fails())
            return $this->response()->error($validator->errors()->all());

        $register = RegistrationMemberByPhone::where('phone_number', $this->request->get('phone'))->first();
        if(! $register)
            $register = new RegistrationMemberByPhone;

        $newtimestamp = strtotime(date("Y-m-d h:i:s").' + 5 minute');
        $finalDate =  date('Y-m-d H:i:s', $newtimestamp);
        $register->phone_number = $this->request->get('phone');
        $register->unique_code = quickRandom(6);
        $register->expired_code = $finalDate;

        if(! $register->save())
            return $this->response()->error("error at saving data");

        return $this->response()->success(['phone_number' => $register->phone_number, 'unique_code' => $register->unique_code]);
    }    
}
