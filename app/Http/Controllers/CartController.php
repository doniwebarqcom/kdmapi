<?php

namespace App\Http\Controllers;

use App\Transformers\CartItemTransformer;
use Kodami\Models\Mysql\CartItem;
use Kodami\Models\Mysql\MemberPlacePickup;
use Kodami\Models\Mysql\KodamiProduct;
use Tymon\JWTAuth\JWTAuth;
use Validator;

class CartController extends ApiController
{
	public function store(JWTAuth $JWTAuth)
	{
		$product = KodamiProduct::find($_POST['product']);
    	if(! $product)
    		return $this->response()->success();

		$MemberPlacePickup = MemberPlacePickup::find($_POST['pickup_id']);

		if(! $MemberPlacePickup)
			return $this->response()->success('succes');

		$member =  $JWTAuth->parseToken()->authenticate();	
		$cart = CartItem::where('member_id', $member->id)->where('product_id', $this->request->product)->where('addres', $MemberPlacePickup->addres)->where('postal_code', $MemberPlacePickup->postal_code)->first();
		$quantity = isset($_POST['quantity']) ? $_POST['quantity'] : false;
		
		if(! $cart){
			$cart = new CartItem;			
			if($quantity)
				$cart->quantity = $quantity;
			else
				$cart->quantity = 1;

			$cart->shipping_cost = $_POST['shipping'];
		}else{
			$cart->quantity += $quantity;
			$cart->shipping_cost += $_POST['shipping'];
		}

		$cart->member_id = $member->id;
		$cart->product_id = $product->id;
		$cart->recipient_name = $MemberPlacePickup->recipient_name;
		$cart->phone_number_recipient = $MemberPlacePickup->phone_number_recipient;
		$cart->postal_code = $MemberPlacePickup->postal_code;
		$cart->district_id = $MemberPlacePickup->district_id;
		$cart->addres = $MemberPlacePickup->addres;
		
		if(! $cart->save())
			return $this->response()->error('failed save cart');
	
		return $this->response()->success($cart);
	}

	public function update()
	{		
		$cart_id = $this->request->cart_id;
		$cart = CartItem::find($cart_id);
		if(! $cart)
			return $this->response()->error('cart not found');

		$product = KodamiProduct::find($cart->product_id);
    	if(! $product)
    		return $this->response()->error('product not found');

		return $this->response()->success($cart->product_id);
	}

	public function withNewPlace(JWTAuth $JWTAuth)
	{

		$product = KodamiProduct::find($_POST['product']);
    	if(! $product)
    		return $this->response()->success();

		$rules = [
            'addres' 					=> 'required',
            'district' 					=> 'required',
            'recipient_number' 			=> 'required',
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
        $MemberPlacePickup = new MemberPlacePickup;
        $MemberPlacePickup->member_id = $member->id;
        $MemberPlacePickup->addres = $this->request->addres;
        $MemberPlacePickup->district_id = $this->request->district;
        $MemberPlacePickup->phone_number_recipient = $this->request->recipient_number;
        $MemberPlacePickup->place_name	 = $this->request->place_name;
        $MemberPlacePickup->postal_code = $this->request->postal_code;
        $MemberPlacePickup->recipient_name = $this->request->recipient_name;

        if(! $MemberPlacePickup->save())
            return $this->response()->error("error at saving data");

        $cart = CartItem::where('member_id', $member->id)->where('product_id', $this->request->product)->where('addres', $MemberPlacePickup->addres)->where('postal_code', $MemberPlacePickup->postal_code)->first();
		
		$quantity = isset($_POST['quantity']) ? $_POST['quantity'] : false;		
		$cart = new CartItem;			
		if($quantity)
			$cart->quantity = $quantity;
		else
			$cart->quantity = 1;

		$cart->shipping_cost = $_POST['shipping'];
		$cart->member_id = $member->id;
		$cart->product_id = $product->id;
		$cart->recipient_name = $MemberPlacePickup->recipient_name;
		$cart->phone_number_recipient = $MemberPlacePickup->phone_number_recipient;
		$cart->postal_code = $MemberPlacePickup->postal_code;
		$cart->district_id = $MemberPlacePickup->district_id;
		$cart->addres = $MemberPlacePickup->addres;
		
		if(! $cart->save())
			return $this->response()->error('failed save cart');
	
		return $this->response()->success($cart);	

	}

	public function list(JWTAuth $JWTAuth)
    {
		$member =  $JWTAuth->parseToken()->authenticate();				
		$cart = CartItem::where('member_id', $member->id)->get();
		return $this->response()->success($cart, [] , 200, new CartItemTransformer(), 'collection', null, ['product']);    	
    }

	public function destroy_cart($id)
    {
    	$cart = CartItem::find($id);
    	$cart->delete();
    	return $this->response()->success('succes');	
    }

    public function addCart($product, JWTAuth $JWTAuth)
    {
    	$product = KodamiProduct::where('name_alias', strtolower(trim($product)))->first();
    	$quantity = isset($_POST['quantity']) ? $_POST['quantity'] : false;

    	if(! $product)
    		return $this->response()->success();
		
		$member =  $JWTAuth->parseToken()->authenticate();
		$cart = CartItem::where('member_id', $member->id)->where('product_id', $product->id)->first();

		if(! $cart){
			$cart = new CartItem;
			$cart->member_id = $member->id;
			$cart->product_id = $product->id;
			
			if($quantity)
				$cart->quantity = $quantity;
			else
				$cart->quantity = 1;

		}else {

			if($quantity)
				$cart->quantity = $quantity;
			else
				$cart->quantity = $cart->quantity + 1;

		}

		if(! $cart->save())
			return $this->response()->error('failed save cart');
	
		return $this->response()->success($cart);
    }
}