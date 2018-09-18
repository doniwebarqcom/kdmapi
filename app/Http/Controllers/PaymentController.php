<?php

namespace App\Http\Controllers;

use Kodami\Models\Mysql\Transaction;
use Tymon\JWTAuth\JWTAuth;
use Validator;

class PaymentController extends ApiController
{
	public function choose(JWTAuth $JWTAuth)
	{
		$member =  $JWTAuth->parseToken()->authenticate();
		$transaction = Transaction::where('member_id', $member->id)->where('status', 0)->first();
		if(! $transaction)
			return $this->response()->error('no have transaction');

		$random_number = (int) quickRandomNumber();
		$transaction->fee_random = $random_number;
		$transaction->type_payment = $this->request->type;
		$transaction->status = 1;
		$transaction->save();
		return $this->response()->success($transaction);
	}

	public function bill($invoice, JWTAuth $JWTAuth)
	{
		$member =  $JWTAuth->parseToken()->authenticate();
		$transaction = Transaction::where('member_id', $member->id)->where('transaction_code', $invoice)->first();
		return $this->response()->success($transaction);
	}
}