<?php

namespace App\Http\Controllers;

use App\Models\Member;

class ExampleController extends ApiController
{

    public function index()
    {
    	return Member::get();
    }

}
