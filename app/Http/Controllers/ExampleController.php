<?php

namespace App\Http\Controllers;

use Kodami\Models\Mysql\Member;
use Kodami\Models\Test;

class ExampleController extends ApiController
{

    public function index()
    {
    	$data = Member::get();
   		return $data;
    }

}
