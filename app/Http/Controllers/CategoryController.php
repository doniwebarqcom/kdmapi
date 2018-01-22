<?php

namespace App\Http\Controllers;

use App\Transformers\CategoryTransformer;
use App\Transformers\CategoryInSearchTransformer;
use App\Repositories\CostumeDataArraySerializer;
use Kodami\Models\Mysql\Category;
use League\Fractal;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;

class CategoryController extends ApiController
{
	public function index()
	{
		$Category = Category::where('active', 1)->where('parent_id', 0)->orderBy('order_num', 'ASC')->get();
		$data = [];
		return $this->response()->success($Category, [] , 200, new CategoryTransformer(), 'collection');;
	}

	public function search()
	{
		$Category = Category::where('active', 1)->where('parent_id', 0)->where('has_children', 1)->orderBy('order_num', 'ASC')->limit(5)->get();
		$data = [];
		return $this->response()->success($Category, [] , 200, new CategoryInSearchTransformer(), 'collection', null, ['sub_category']);
	}
}