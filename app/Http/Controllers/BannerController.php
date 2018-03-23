<?php

namespace App\Http\Controllers;

use App\Transformers\BannerSlideShowTransformer;
use Kodami\Models\Mysql\BannerSlideshow;
use Validator;

class BannerController extends ApiController
{
	public function slideshow()
	{
		$banner  = BannerSlideshow::orderBy('urut', 'ASC')->where('status', 1)->get();
		return $this->response()->success($banner, [] , 200, new BannerSlideShowTransformer(), 'collection');
	}
}