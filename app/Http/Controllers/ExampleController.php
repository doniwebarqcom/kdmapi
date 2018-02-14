<?php

namespace App\Http\Controllers;

use App\Transformers\ProductTransformer;
use Kodami\Models\Mysql\Member;
use Kodami\Models\Mysql\Product;
use Kodami\Models\Test;

class ExampleController extends ApiController
{
    public function index()
    {
    	$data = Product::orderBy('id', 'desc')->first();
    	return $this->response()->success($data, ['meta.token' => ''] , 200, new ProductTransformer(), 'item', null, ['criteria']);
    }

    public function sms()
    {
    	\Nexmo\Laravel\Facade\Nexmo::message()->send([
		    'to'   => '6287775365856',
		    'from' => '6282134916615',
		    'text' => 'Using the facade to send a message.'
		]);
    }

}
