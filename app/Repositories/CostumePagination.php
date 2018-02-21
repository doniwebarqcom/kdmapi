<?php

namespace App\Repositories;

class CostumePagination
{
	protected $result;

	public function __construct($result)
    {
        $this->result = $result;
    }

    public function render()
    {
    	$result['data'] = $this->result->getCollection();
    	$data = $this->result->toArray();    
    	$result['paging']['current_page'] = $data['current_page'];
        if($data['current_page'] > 1)
            $result['paging']['previous_page'] = $data['current_page'] - 1;
        else
            $result['paging']['previous_page'] = $data['current_page'];

        if($data['current_page'] < $data['last_page'])
            $result['paging']['next_page'] = $data['current_page'] + 1;
        else 
            $result['paging']['next_page'] = $data['last_page'];
    	$result['paging']['from_page'] = $data['from'];
    	$result['paging']['last_page'] = $data['last_page'];
    	$result['paging']['total_data'] = $data['total'];
    	$result['paging']['per_page'] = $data['per_page'];

    	return $result;
    }
}