<?php

namespace App\Repositories;

use App\Repositories\CostumeDataArraySerializer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Laravel\Lumen\Http\ResponseFactory;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
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
     * @param null  $typeData
     * @param array $headers
     * @param int   $options
     *
     * @return SymfonyResponse
     */
    public function success($data, array $attribute = [], $status = 200, $transformer = null, $typeData = null, $costumeData = null, $transformInclude = [], array $headers = [], $options = 0)
    {

        if (!array_key_exists('Cache-Control', $headers)) {
            $headers['Cache-Control'] = 'max-age=604800'; 
        }
        
        
        if($transformer != null AND $typeData != null)
            $response = $this->generateStructureTransformer($data, $attribute, $typeData, $transformer, $costumeData, $transformInclude);
        else
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
     * @return SymfonyResponse\
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
            'token'     => array_key_exists('meta.token', $attribute) ? $attribute['meta.token'] : null,
            'language'  => array_key_exists('meta.language', $attribute) ? $attribute['meta.language'] : app('translator')->getLocale(),
            'timestamp' => array_key_exists('meta.timestamp', $attribute) ? $attribute['meta.timestamp'] : Carbon::now()->timestamp
        ];

        $paging = array_key_exists('paging', $attribute) ? $attribute['paging'] : [];
        $response = [
            'status'    => $status,
            'data'      => $data,
            'paging'    => $paging,
            'meta'      => $meta            
        ];


        return $response;
    }

    /**
     * @param       $data
     * @param array $attribute
     *
     * @return array
     */
    private function generateStructureTransformer($data, array $attribute, $typeData = null, $transformer = null, $costumeData = null, $transformInclude = [])
    {
        $status = [
            'error'   => array_key_exists('status.error', $attribute) ? $attribute['status.error'] : false,
            'message' => array_key_exists('status.message', $attribute) ? $attribute['status.message'] : null,
            'code'    => array_key_exists('status.code', $attribute) ? $attribute['status.code'] : SymfonyResponse::HTTP_OK,

        ];

        $meta = [
            'token'     => array_key_exists('meta.token', $attribute) ? $attribute['meta.token'] : null,
            'language'  => array_key_exists('meta.language', $attribute) ? $attribute['meta.language'] : app('translator')->getLocale(),
            'timestamp' => array_key_exists('meta.timestamp', $attribute) ? $attribute['meta.timestamp'] : Carbon::now()->timestamp
        ];

        if(isset($data) AND $typeData != null AND $transformer != null)
        {
            $manager = new Manager();
            $manager->parseIncludes($transformInclude);
            $manager->setSerializer(new CostumeDataArraySerializer($costumeData));
            if($typeData == 'item')
                $resource = new Item($data, $transformer);
            else
                $resource = new Collection($data, $transformer);

            $result =  $manager->createData($resource)->toArray();

        } else 
            $result = [];

        $paging = array_key_exists('paging', $attribute) ? $attribute['paging'] : [];
        $response = [
            'status'    => $status,
            'data'      => $result,
            'paging'    => $paging,
            'meta'      => $meta            
        ];

        return $response;
    }
}
