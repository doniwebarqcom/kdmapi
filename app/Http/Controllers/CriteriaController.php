<?php

namespace App\Http\Controllers;

use Kodami\Models\Mysql\CategoryCriteria;
use Kodami\Models\Mysql\JunkCategoryCriteria;
use Kodami\Models\Mysql\JunkCategorySpesification;
use App\Transformers\JunkCategoryCriteriaTransformer;
use App\Transformers\JunkCategorySpesificationTransformer;

class CriteriaController extends ApiController
{
	public function category()
	{
		$category = $this->request->get("category") ? $this->request->category : 1 ;
		$JunkCategoryCriteria = JunkCategoryCriteria::where('category_id', $category)->get();
		return $this->response()->success($JunkCategoryCriteria, [] , 200, new JunkCategoryCriteriaTransformer(), 'collection', null, ['selection']);
	}

	public function spesification()
	{
		$category = $this->request->get("category") ? $this->request->category : 1 ;
		$JunkCategorySpesification = JunkCategorySpesification::where('category_id', $category)->get();
		return $this->response()->success($JunkCategorySpesification, [] , 200, new JunkCategorySpesificationTransformer(), 'collection');
	}
}
