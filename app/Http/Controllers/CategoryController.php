<?php

namespace App\Http\Controllers;

use App\Transformers\CategoryTransformer;
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
		if($Category) {
            $manager = new Manager();
            $manager->setSerializer(new CostumeDataArraySerializer());
            $resource = new Collection($Category, new CategoryTransformer());
            $data =  $manager->createData($resource)->toArray();
        }

        return $this->response()->success($data);
	}
}