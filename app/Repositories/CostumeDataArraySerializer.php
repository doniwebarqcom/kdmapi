<?php

namespace App\Repositories;

use League\Fractal\Serializer\ArraySerializer;

class CostumeDataArraySerializer extends ArraySerializer
{
    /**
     * Serialize a collection.
     *
     * @param string $resourceKey
     * @param array  $data
     *
     * @return array
     */
    public function collection($resourceKey, array $data)
    {
    	if($resourceKey != null)
        	return [$resourceKey => $data];
        else
        	return $data;
    }

    /**
     * Serialize an item.
     *
     * @param string $resourceKey
     * @param array  $data
     *
     * @return array
     */
    public function item($resourceKey, array $data)
    {
    	if($resourceKey != null)
        	return [$resourceKey=> $data];
        else
        	return $data;
    }

    /**
     * Serialize null resource.
     *
     * @return array
     */
    public function null()
    {
        return ['data' => []];
    }
}
