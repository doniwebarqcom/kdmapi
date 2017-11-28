<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ApiController extends Controller
{

    protected $request;

    protected $response;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    protected function response($content = '', $status = 200, array $headers = [])
    {

        $factory = new \App\Repositories\Response($this->request);

        if (func_num_args() === 0) {
            return $factory;
        }

        return $factory->make($content, $status, $headers);
    }
}
