<?php

namespace App\Http\Controllers;

use App\Transformers\MemberTransformer;
use App\Transformers\MemberPlacePickupTransformer;
use Illuminate\Support\Facades\Hash;
use Kodami\Models\Mysql\Member;
use Kodami\Models\Mysql\MemberPlacePickup;
use Kodami\Models\Mysql\RegistrationMemberByPhone;
use Tymon\JWTAuth\JWTAuth;
use Nexmo\Laravel\Facade\Nexmo;
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

        $phone = $this->request->get('phone');
        $newtimestamp = strtotime(date("Y-m-d h:i:s").' + 5 minute');
        $finalDate =  date('Y-m-d H:i:s', $newtimestamp);
        $register->phone_number = $phone;
        $register->unique_code = quickRandom(6);
        $register->expired_code = $finalDate;

        if(! $register->save())
            return $this->response()->error("error at saving data");

        $send_sms = Nexmo::message()->send([
            'to'   => $this->request->get('phone'),
            'from' => '6282134916615',
            'text' => "This is your code : ".$register->unique_code."   powered by"
        ]);

        if(! $send_sms)
            return $this->response()->error("error at sending sms");

        return $this->response()->success('succes');
    }

    public function cekCode(JWTAuth $JWTAuth)
    {
        $rules = [
            'phone' => 'required',
            'code'  => 'required',
        ];

        $validator = Validator::make(
            $this->request->all(),
            $rules
        );

        if ($validator->fails())
            return $this->response()->error($validator->errors()->all());

        $newtimestamp = strtotime(date("Y-m-d h:i:s").' + 5 minute');
        $finalDate =  date('Y-m-d H:i:s', $newtimestamp);

        $register = RegistrationMemberByPhone::where('phone_number', $this->request->get('phone'))
                    ->where('unique_code', $this->request->get('code'))
                    ->where('expired_code', '<=', $finalDate)
                    ->first();

        if(! isset($register))
            return $this->response()->error("data not found");

        $member = Member::where('phone', $this->request->get('phone'))->first();
        $isMember = 0;
        $header = [];
        if(isset($member)){
            $isMember = 1;
            $token = $JWTAuth->fromUser($member);
            $header['meta.token'] = $token;
        }

        return $this->response()->success(["member" => $isMember], ['meta.token' => $token]);
    }

    public function registerByPhone(JWTAuth $JWTAuth)
    {
        $register = RegistrationMemberByPhone::where('phone_number', $this->request->get('phone'))
                    ->where('unique_code', $this->request->get('code'))
                    ->first();

        $member = Member::where('phone', $this->request->get('phone'))->first();
        if(! isset($member) AND isset($register) )
        {
            $member = new Member;
            $member->name   = "kodami_".$this->request->get('phone');
            $member->email  = $this->request->get('phone')."@mail.com";
            $member->username   = "kodami_".$this->request->get('phone');
            $member->password   = Hash::make($this->request->get('password'));
            $member->address    = "";
            $member->phone  = $this->request->get('phone');

            $member->save();            
        } else if(! isset($member) AND ! isset($register) )
            return $this->response()->error("data not found");
        
        $token = $JWTAuth->fromUser($member);
        return $this->response()->success($member, ['meta.token' => $token] , 200, new MemberTransformer(), 'item');
    }

    public function upload_image(JWTAuth $JWTAuth)
    {
        $rules = [
            'photo' => 'required',
        ];

        $validator = Validator::make(
            $this->request->all(),
            $rules
        );

        if ($validator->fails())
            return $this->response()->error($validator->errors()->all());

        $member =  $JWTAuth->parseToken()->authenticate();
        $member->image  = $this->request->get('photo');

        if(! $member->save())
            return $this->response()->error("failed save data");
        
        $token = $JWTAuth->fromUser($member);
        return $this->response()->success($member, ['meta.token' => $token] , 200, new MemberTransformer(), 'item');
    }

    public function profile_store(JWTAuth $JWTAuth)
    {
        $rules = [
            'name' => 'required',
            'birth' => 'required',
            'gender' => 'required',
        ];

        $validator = Validator::make(
            $this->request->all(),
            $rules
        );

        if ($validator->fails())
            return $this->response()->error($validator->errors()->all());

        $member =  $JWTAuth->parseToken()->authenticate();
        $member->name  = $this->request->get('name');
        $member->gender  = $this->request->get('gender');
        $member->birth  = date("Y-m-d" , $this->request->get('birth'));

        if(! $member->save())
            return $this->response()->error("failed save data");
        
        $token = $JWTAuth->fromUser($member);
        return $this->response()->success($member, ['meta.token' => $token] , 200, new MemberTransformer(), 'item');
    }

    public function place_list(JWTAuth $JWTAuth)
    {
        $member =  $JWTAuth->parseToken()->authenticate();
        $pickup = MemberPlacePickup::where('member_id', $member->id)->get();
        $token = $JWTAuth->fromUser($member);
        return $this->response()->success($pickup, ['meta.token' => $token] , 200, new MemberPlacePickupTransformer(), 'collection');
    }
}