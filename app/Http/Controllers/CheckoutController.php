<?php

namespace App\Http\Controllers;

use DB;
use Kodami\Models\Mysql\CartItem;
use Kodami\Models\Mysql\Transaction;
use Kodami\Models\Mysql\TransactionItem;
use Tymon\JWTAuth\JWTAuth;
use Validator;

class CheckoutController extends ApiController
{
    public function store(JWTAuth $JWTAuth)
    {   
    	$member =  $JWTAuth->parseToken()->authenticate();
		$cart = CartItem::where('member_id', $member->id)->get();
		$transaction = Transaction::where('member_id', $member->id)->where('status', 0)->first();

		if(count($transaction) === 0 AND count($cart) === 0)
    		return $this->response()->error('not found cart');
    	
    	if(count($transaction) === 0 AND count($cart) > 0){
    		$transaction = new Transaction;
    		
    		$total_transaction = (int) (DB::table('transactions')->count() + 1);
    		$transaction_code = random_trasaction_code($total_transaction);

    		$transaction->member_id = $member->id;
    		$transaction->transaction_code = $transaction_code;

    		if(! $trans->save())
    			return $this->response()->error('not found cart');    		
    	}

    	if(count($transaction) > 0 AND count($cart) > 0){
			foreach ($cart as $key => $value) {
				$TransactionItem = new TransactionItem;
				$TransactionItem->transaction_id = $transaction->id;
				$TransactionItem->kodami_product_id = $value->product_id;
				$TransactionItem->district_id = $value->district_id;
				$TransactionItem->recipient_name = $value->recipient_name;
				$TransactionItem->phone_number_recipient = $value->phone_number_recipient;
				$TransactionItem->postal_code = $value->postal_code;
				$TransactionItem->quantity = $value->quantity;
	    		$TransactionItem->addres = $value->addres;

	    		$TransactionItem->save();
			}

			
    	}

    	return $this->response()->success(1);

		// if(count($transaction) > 0 AND count($cart) > 0){
  //   		return $this->response()->success(1);
		// }else if(count($cart) > 0){
		// 	return $this->response()->success(2);
		// }
    }    
}