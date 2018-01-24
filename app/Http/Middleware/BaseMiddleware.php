<?php

namespace App\Http\Middleware;

use Illuminate\Events\Dispatcher;
use Laravel\Lumen\Http\ResponseFactory;
use Tymon\JWTAuth\JWTAuth;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware as Base;

class BaseMiddleware extends Base
{

    protected $response;
    protected $events;
    protected $auth;

    public function __construct(ResponseFactory $response, Dispatcher $events, JWTAuth $auth)
    {        

        $this->response = $response;
        $this->events = $events;
        $this->auth = $auth;
    }

    /**
     * Fire event and return the response
     *
     * @param  string  $event
     * @param  string  $error
     * @param  integer $status
     * @param  array   $payload
     *
     * @return mixed
     */
    protected function respond($event, $error, $status, $payload = [])
    {
        $response = $this->events->fire($event, $payload, true);

        $result['status']['error'] = true;
        $result['status']['message'] = $error;
        $result['status']['code'] = $status;
        $result['data'] = (object)[];
        $result['token'] = null;
        $result['timestamp'] = time();

        return $response ?: $this->response->json($result, $status);
    }
}
