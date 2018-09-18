<?php

namespace App\Http\Controllers\Pulsa;

use App\Http\Controllers\ApiController;

class IndexController extends ApiController
{
    public function index()
    {
        $response['status'] = 'success';
        $response['code'] = '200';
        $response['data'] = [];

        return $response;
    }
}
