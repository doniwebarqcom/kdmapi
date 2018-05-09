<?php

namespace App\Http\Controllers;

use App\Repositories\CostumePagination;
use App\Transformers\CartItemTransformer;
use App\Transformers\ProductTransformer;
use App\Transformers\KodamiProductTransformer;
use Carbon\Carbon;
use DB;
use Kodami\Models\Mysql\CartItem;
use Kodami\Models\Mysql\Category;
use Kodami\Models\Mysql\Koprasi;
use Kodami\Models\Mysql\Product;
use Kodami\Models\Mysql\KodamiProduct;
use Kodami\Models\Mysql\ProductSpesification;
use Tymon\JWTAuth\JWTAuth;
use Validator;

class ProductController extends ApiController
{
	public function single($koprasi, $product)
    {
    	$k = Koprasi::where('url', strtolower($koprasi))->first();
    	if($k === null)
			return $this->response()->error('shop not avaible', 409);

		$p = Product::where('koprasi_id', $k->id)->where('name_alias', strtolower(trim($product)))->first();

		if($p === null)
			return $this->response()->error('product no found', 409);

		return $this->response()->success($p, [] , 200, new ProductTransformer(), 'item', null, ['criteria', 'spesification']);
    }

    public function getData($alias)
    {
    	$product = KodamiProduct::where('name_alias', strtolower(trim($alias)))->where('status', 1)->first();
		return $this->response()->success($product, [] , 200, new KodamiProductTransformer(), 'item', null, ['spesification']);
    }

    public function category($category)
    {
    	$data_category = Category::where(DB::raw('lower(permalink)'), strtolower($category))->first();
    	if(! $data_category)
    		return $this->response()->success([]);

    	$child_data = $data_category->collection_child;
    	$child = explode(',', $child_data);
    	if($child[0] == "")
    		$child[0] = $data_category->id;
    	else
    		$child[] = $data_category->id;

    	foreach ($child as $key => $value) {
    		$child[$key] = (int) $value;
    	}
    	
        $limit = $this->request->get('limit') ? $this->request->get('limit') : 15;
        $product = KodamiProduct::whereIn('category_id', $child)->where('status', 1)->paginate($limit);
        $pagination = new CostumePagination($product);     
        $result = $pagination->render();           
    	
    	return $this->response()->success($result['data'], ['paging' => $result['paging']] , 200, new KodamiProductTransformer(), 'collection');
    }

    public function most_viewed()
    {
    	$product = KodamiProduct::where('status', 1)->orderBy('viewer', 'DESC')->limit(20)->get();
    	return $this->response()->success($product, [] , 200, new KodamiProductTransformer(), 'collection');
    }
    
    public function input(JWTAuth $JWTAuth)
    {         	
    	$user =  $JWTAuth->parseToken()->authenticate();
    	if($user->shop === null)
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
		$long_description = $this->request->get('long_description');
		$price = $this->request->get('price');
		$primary_image = $this->request->get('primary_image');
		$avaible = $this->request->get('avaible');
		$weight = $this->request->get('weight');
		$stock = $this->request->get('stock');
		$new = $this->request->get('new');
		$images = $this->request->get('images');
		$discont_anggota = $this->request->get('discont_anggota');
		$discont = $this->request->get('discont');
		$criterias = $this->request->get('criterias') ? $this->request->get('criterias') : [];
		$spesification = $this->request->get('spesification') ? $this->request->get('spesification') : [];
		$grosir_start = $this->request->get('grosir_start') ? $this->request->get('grosir_start') : [];
		$grosir_until = $this->request->get('grosir_until') ? $this->request->get('grosir_until') : [];
		$grosir_price = $this->request->get('grosir_price') ? $this->request->get('grosir_price') : [];

		if($new == true)
			$new = 1;
		else
			$new = 0;

		if($avaible == true)
			$avaible = 1;
		else
			$avaible = 0;		

		$name_alias = str_replace(" ", "-", $name);
		$name_alias = str_replace("_", "-", $name_alias);

		$product = new Product();
		$product->koprasi_id = $user->shop->id;
		$product->category_id = $category_id;
		$product->name = $name;		
		$product->description = $description;
		$product->long_description = $long_description;
		$product->price = $price;
		$product->primary_image = $primary_image;
		$product->avaible = $avaible;
		$product->success_transaction = 0;
		$product->total_comment =0;
		$product->weight =$weight;
		$product->viewer =0;
		$product->stock = $stock;
		$product->new = $new;
		$product->discont = $discont;
		$product->discont_anggota = $discont_anggota;

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
		        
        if(count($grosir_start) > 0)
        {
        	$wholesaleprice = [];
	        foreach ($grosir_start as $key => $value) {
	        	if(isset($value) AND isset($grosir_until[$key]) AND $grosir_price[$key])
	        	{
	        		$wholesaleprice[] = array(
	        			'product_id'	=> $product->id,
	        			'from' 			=> (int) $value,
	        			'to' 			=> (int) $grosir_until[$key],
	        			'price' 		=> (double) $grosir_price[$key],
					    'created_at'	=> Carbon::now(),
					    'updated_at'	=> Carbon::now(),
	        		);
	        	}
	        }

	        if(count($wholesaleprice) > 0)
	        	\DB::table('wholesale_price')->insert($wholesaleprice);	
        }

        if(count($criterias) > 0)
        {
        	$dataCriteria = [];
        	foreach ($criterias as $key => $value) {
        		$dataCriteria[] = array(
        			'product_id'	=> $product->id,
        			'value_category_criteria_id' 		=> $value,
				    'created_at'	=> Carbon::now(),
				    'updated_at'	=> Carbon::now(),
        		);
        	}

        	\DB::table('product_criteria')->insert($dataCriteria);
        }

        if(count($spesification) > 0)
        {
        	$dataSpesification = [];
        	foreach ($spesification as $key => $value) {
        		$dataSpesification[] = array(
        			'product_id'					=> $product->id,
        			'category_spesification_id'		=> $key,
        			'value'							=> $value,
				    'created_at'					=> Carbon::now(),
				    'updated_at'					=> Carbon::now(),
        		);
        	}

        	\DB::table('product_spesifications')->insert($dataSpesification);
        }

        $product->name_alias = strtolower($name_alias)."-".$product->id;
        $product->save();        
        $token = $JWTAuth->fromUser($user);
        return $this->response()->success($product, ['meta.token' => $token] , 200, new ProductTransformer(), 'item');
    }

    public function list()
    {
    	$q = $this->request->get('query') ? $this->request->get('query') : null;
        $limit = $this->request->get('limit') ? $this->request->get('limit') : 10;
        $post = Product::paginate($limit);
        $pagination = new CostumePagination($post);     
        $result = $pagination->render();           
    	return $this->response()->success($result['data'], ['paging' => $result['paging']] , 200, new ProductTransformer(), 'collection');
    }    
}
