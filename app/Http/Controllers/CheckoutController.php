<?php

namespace App\Http\Controllers;

use DB;
use Kodami\Models\Mysql\CartItem;
use Kodami\Models\Mysql\KodamiProduct;
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
    		$transaction->admin_fee = quickRandomNumber();

    		if(! $transaction->save())
    			return $this->response()->error('error save transaction');
    	}

    	if(count($transaction) > 0 AND count($cart) > 0){
			foreach ($cart as $key => $value) {
				$TransactionItem = new TransactionItem;

				$product = KodamiProduct::find($value->product_id);

				if($product){
					$TransactionItem->transaction_id = $transaction->id;
					$TransactionItem->kodami_product_id = $value->product_id;
					$TransactionItem->product_price = $product->price;
					$TransactionItem->product_name = $product->name;
					$TransactionItem->product_weight = $product->weight;
					$TransactionItem->shipping = $value->shipping_cost;
					$TransactionItem->district_id = $value->district_id;
					$TransactionItem->recipient_name = $value->recipient_name;
					$TransactionItem->phone_number_recipient = $value->phone_number_recipient;
					$TransactionItem->postal_code = $value->postal_code;
					$TransactionItem->quantity = $value->quantity;
					$TransactionItem->addres = $value->addres;
					$TransactionItem->save();
				}
			}			
    	}

    	DB::table('cart_items')->where('member_id', $member->id)->delete();

    	$transaction_items = DB::table('transaction_items')
                     ->select(DB::raw(' SUM(`product_price` * `quantity`) AS total,  sum(`shipping`) total_shiping'))
                     ->where('transaction_id', $transaction->id)
                     ->first();

        $total = isset($transaction_items->total) ? $transaction_items->total : 0;
        $total_shiping = isset($transaction_items->total_shiping) ? $transaction_items->total_shiping : 0;
        $transaction->price_product = $total;
        $transaction->shipping = $total_shiping;
        $transaction->save();

    	return $this->response()->success($transaction);
    }    
}