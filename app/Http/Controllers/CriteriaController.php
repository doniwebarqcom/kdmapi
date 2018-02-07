<?php

namespace App\Http\Controllers;

use App\Transformers\CriteriaTransformer;
use Kodami\Models\Mysql\CategoryCriteria;

class CriteriaController extends ApiController
{
	public function category()
	{
		$category = $this->request->get("category") ? $this->request->category : 1 ;
		$criteria = CategoryCriteria::where('category_id', $category)->get();
		return $this->response()->success($criteria, [] , 200, new CriteriaTransformer(), 'collection', null, ['selection']);		
	}
}
