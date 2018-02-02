<?php

namespace App\Http\Controllers;

use App\Transformers\ProductTransformer;
use Carbon\Carbon;
use Kodami\Models\Mysql\Product;
use Tymon\JWTAuth\JWTAuth;
use Validator;

class ProductController extends ApiController
{
    public function input(JWTAuth $JWTAuth)
    {       	
    	$user =  $JWTAuth->parseToken()->authenticate();	
    	if(! $user->have_shop OR ! $user->shop)
			return $this->response()->error('user dont have shop', 409);

    	$rules = [
            'category' 		=> 'required',
            'name' 			=> 'required',
            'description' 	=> 'required',
            'price' 		=> 'required',
            'primary_image'	=> 'required',
            'avaible'		=> 'required',
            'weight'		=> 'required',
            'stock'			=> 'required',
            'new'			=> 'required',
        ];

    	$validator = Validator::make(
    		$this->request->all(),
    		$rules
		);

		if ($validator->fails())
			return $this->response()->error($validator->errors()->all());

		$name = $this->request->get('name');
		$category_id = (int) $this->request->get('category');
		$description = $this->request->get('description');
		$price = $this->request->get('price');
		$primary_image = $this->request->get('primary_image');
		$avaible = $this->request->get('avaible');
		$weight = $this->request->get('weight');
		$stock = $this->request->get('stock');
		$new = $this->request->get('new');
		$images = $this->request->get('images');

		if($new == true)
			$new = 1;
		else
			$new = 0;

		if($avaible == true)
			$avaible = 1;
		else
			$avaible = 0;
		

		$product = new Product();
		$product->koprasi_id = $user->shop->id;
		$product->category_id = $category_id;
		$product->name = $name;
		$product->description = $description;
		$product->price = $price;
		$product->primary_image = $primary_image;
		$product->avaible = $avaible;
		$product->success_transaction = 0;
		$product->total_comment =0;
		$product->weight =$weight;
		$product->viewer =0;
		$product->stock = $stock;
		$product->new = $new;

		if (! $product->save())
            return $this->response()->error('failed save data');        

        $dataImage = [];
        if(count($images) > 0)
        {
        	$a = 0;
	        foreach ($images as $key => $value) {
	        	if(isset($value))
	        	{
	        		$dataImage[$a] = array(
	        			'product_id'	=> $product->id,
	        			'image' 		=> $value,
					    'created_at'	=> Carbon::now(),
					    'updated_at'	=> Carbon::now(),
	        		);

	        		$a++;
	        	}
	        }

	        if(count($dataImage) > 0)
	        	\DB::table('product_images')->insert($dataImage);	
        }
        
        $token = $JWTAuth->fromUser($user);
        return $this->response()->success($product, ['meta.token' => $token] , 200, new ProductTransformer(), 'item');
    }
}
