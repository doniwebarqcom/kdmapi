<?php

namespace App\Http\Controllers;

use App\Transformers\CartItemTransformer;
use Kodami\Models\Mysql\CartItem;
use Tymon\JWTAuth\JWTAuth;
use Validator;

class CartController extends ApiController
{
	public function store()
	{

		$cart = CartItem::where('member_place_pickup_id', $this->request->pickup_id)->where('product_id', $this->request->product)->first();
		$quantity = isset($_POST['quantity']) ? $_POST['quantity'] : false;
		$member =  $JWTAuth->parseToken()->authenticate();

		if(! $cart){
			$cart = new CartItem;
			$cart->member_id = $member->id;
			$cart->product_id = $product->id;
			$cart->member_place_pickup_id = $this->request->pickup_id;
			
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

		return $this->response()->success($_POST);
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