<?php

namespace App\Repositories;

use App\Transformers\Serializers\CiayoSerializer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Laravel\Lumen\Http\ResponseFactory;
use League\Fractal\Manager;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class Response extends ResponseFactory
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * Response constructor.
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @param       $data
     * @param array $attribute
     * @param int   $status
     * @param bool  $transformer
     * @param null  $serializer
     * @param array $headers
     * @param int   $options
     *
     * @return SymfonyResponse
     * @author Fathur Rohman <fathur@dragoncapital.center>
     */
    public function success($data, array $attribute = [], $status = 200, $transformer = true, $serializer = null, array $headers = [], $options = 0)
    {

        if (!array_key_exists('Cache-Control', $headers)) {
            $headers['Cache-Control'] = 'max-age=604800'; 
        }

        $response = $this->generateStructureNonTransformer($data, $attribute);

        return $this->json($response, $status, $headers, $options);
    }

    /**
     *
     *
     * @param string $message
     * @param int    $response_status
     * @param array  $headers
     * @param int    $options
     *
     * @param int    $header_status
     *
     * @return SymfonyResponse
     * @author   Fathur Rohman <fathur@dragoncapital.center>
     */
    public function error($message, $response_status = 400, $header_status = 400, array $headers = [], $options = 0)
    {
        $attribute = [
            'status.error'   => true,
            'status.code'    => $response_status,
            'status.message' => $message
        ];

        $response = $this->generateStructureNonTransformer([], $attribute);

        return $this->json($response, $header_status, $headers, $options);
    }

    /**
     * @param       $data
     * @param array $attribute
     *
     * @return array
     */
    private function generateStructureNonTransformer($data, array $attribute)
    {
        $status = [
            'error'   => array_key_exists('status.error', $attribute) ? $attribute['status.error'] : false,
            'message' => array_key_exists('status.message', $attribute) ? $attribute['status.message'] : null,
            'code'    => array_key_exists('status.code', $attribute) ? $attribute['status.code'] : SymfonyResponse::HTTP_OK,

        ];

        $meta = [
            'token_jwt'     => array_key_exists('meta.token', $attribute) ? $attribute['meta.token'] : null,
            'language'  => array_key_exists('meta.language', $attribute) ? $attribute['meta.language'] : app('translator')->getLocale(),
            'timestamp' => array_key_exists('meta.timestamp', $attribute) ? $attribute['meta.timestamp'] : Carbon::now()->timestamp
        ];

        // if (env('APP_DEBUG') and env('APP_ENV') != 'testing') {
        //     $meta['debug'] = \DB::getQueryLog();
        // }

        $response = [
            'status' => $status,
            'data'   => $data,
            'meta'   => $meta
        ];

        return $response;
    }
}
