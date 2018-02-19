<?php

namespace App\Http\Controllers;

use App\Transformers\MemberTransformer;
use Kodami\Models\Mysql\Koprasi;
use Tymon\JWTAuth\JWTAuth;
use Validator;

class ShopController extends ApiController
{
    public function register(JWTAuth $JWTAuth)
    {    	
    	$rules = [
            'name' 				=> 'required',
            'url' 				=> 'required',
            'pickup_address' 	=> 'required',
            'regency'           => 'required',
            'description' 		=> 'required',
            'postal_code' 		=> 'required|min:5'
        ];

    	$validator = Validator::make(
    		$this->request->all(),
    		$rules
		);

    	if ($validator->fails())
			return $this->response()->error($validator->errors()->all());

    	$user =  $JWTAuth->parseToken()->authenticate();
		
		if($user->have_shop)
			return $this->response()->error('user already have shop', 409);

		$name = trim($this->request->get('name'));
		$url = trim($this->request->get('url'));
		$regency_id = $this->request->get('regency');
		$slogan = $this->request->get('slogan');
		$description = $this->request->get('description');
		$image = $this->request->get('image');
		$pickup_address = $this->request->get('pickup_address');
		$postal_code = $this->request->get('postal_code');

		$cek_shop = Koprasi::where(function($q) use($name, $url){
          $q->where('url', 'like', '%'.$url."%")
            ->orWhere('name', 'like', '%'.$name.'%');
      	})->first();

        if ($cek_shop)
            return $this->response()->error('shop already exists', 408);

        $koprasi = new Koprasi;
        $koprasi->member_id = $user->id;
        $koprasi->name = $name;
        $koprasi->url = $url;
        $koprasi->regency_id = $regency_id;
        $koprasi->slogan = $slogan;
        $koprasi->description = $description;
        $koprasi->image = $image;
        $koprasi->pickup_address = $pickup_address;
        $koprasi->postal_code = $postal_code;

        if (! $koprasi->save())
            return $this->response()->error('failed save data');

        $user->have_shop = 1;
        $user->koprasi_id = $koprasi->id;
        $user->save();

        $token = $JWTAuth->fromUser($user);
        return $this->response()->success($user, ['meta.token' => $token] , 200, new MemberTransformer(), 'item');
    }
}
