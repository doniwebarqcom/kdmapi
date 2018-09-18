<?php
namespace App\Http\Controllers;

use App\Repositories\CostumePagination;
use App\Transformers\WishlistTransformer;
use Kodami\Models\Mysql\Wishlist;
use Kodami\Models\Mysql\KodamiProduct;
use Tymon\JWTAuth\JWTAuth;
use Validator;

class WishlistController extends ApiController
{
	public function list(JWTAuth $JWTAuth)
	{
		$member =  $JWTAuth->parseToken()->authenticate();
		$limit = $this->request->get('limit') ? $this->request->get('limit') : 10;
        $wishlist = Wishlist::where('status', 1)->where('member_id', $member->id)->paginate($limit);
        $pagination = new CostumePagination($wishlist);     
        $result = $pagination->render();

		return $this->response()->success($result['data'], ['paging' => $result['paging']] , 200, new WishlistTransformer(), 'collection', null, ['product']);
	}

	public function destroy(JWTAuth $JWTAuth)
	{
		$rules = [
            'id' => 'required'
        ];

    	$validator = Validator::make(
    		$this->request->all(),
    		$rules
		);

		if ($validator->fails())
            return $this->response()->error($validator->errors()->all());

        $member =  $JWTAuth->parseToken()->authenticate();
		$wishlist = Wishlist::where('id', $this->request->id)->where('member_id' , $member->id)->first();

		if(! $wishlist->delete())
            return $this->response()->error("error at delete data");

        return $this->response()->success([]);
	}

	public function add(JWTAuth $JWTAuth)
	{
		$rules = [
            'id' => 'required'
        ];

    	$validator = Validator::make(
    		$this->request->all(),
    		$rules
		);

		if ($validator->fails())
            return $this->response()->error($validator->errors()->all());

        $kodami = KodamiProduct::find($this->request->id);
        if(! $kodami)
        	return $this->response()->error("product not found");

		$member =  $JWTAuth->parseToken()->authenticate();
		$wishlist = Wishlist::where('member_id' , $member->id)->where('kodami_product_id', $this->request->id)->first();

		if(! $wishlist){
            $wishlist = new Wishlist;
            $wishlist->member_id =  $member->id;
            $wishlist->kodami_product_id =  $this->request->id;
            $wishlist->status =  1;

            if(! $wishlist->save())
            	return $this->response()->error("error at save wishlist");
		}

        return $this->response()->success([]);
	}
}