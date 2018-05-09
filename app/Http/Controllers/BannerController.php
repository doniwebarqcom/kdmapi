<?php

namespace App\Http\Controllers;

use App\Transformers\BannerSlideShowTransformer;
use App\Transformers\OurProductChoiceTransformer;
use App\Transformers\CategoryHomeTransformer;
use App\Transformers\AdsHomeTransformer;
use App\Transformers\ProductSpecialOfferTransformer;
use Kodami\Models\Mysql\BannerSlideshow;
use Kodami\Models\Mysql\ChoiceOfOurProductFront;
use Kodami\Models\Mysql\CategoryHome;
use Kodami\Models\Mysql\ProductCategoryHome;
use Kodami\Models\Mysql\AdsHome;
use Kodami\Models\Mysql\ProductSpecialOffer;
use Validator;

class BannerController extends ApiController
{
	public function slideshow()
	{
		$banner  = BannerSlideshow::orderBy('urut', 'ASC')->where('status', 1)->get();
		return $this->response()->success($banner, [] , 200, new BannerSlideShowTransformer(), 'collection');
	}

	public function our_product()
	{
		$choice  = ChoiceOfOurProductFront::where('status', 1)->get();
		return $this->response()->success($choice, [] , 200, new OurProductChoiceTransformer(), 'collection');
	}

	public function category_home()
	{
		$categoryhome = CategoryHome::where('status', 1)->get();
		return $this->response()->success($categoryhome, [] , 200, new CategoryHomeTransformer(), 'collection', null, ['product']);
	}

	public function ads()
	{
		$ads  = AdsHome::where('status', 1)->get();
		return $this->response()->success($ads, [] , 200, new AdsHomeTransformer(), 'collection', null, ['product']);
	}

	public function special_offer()
	{
		$special = ProductSpecialOffer::where('status', 1)->where('expired_time', '>', date('Y-m-d'))->get();
		return $this->response()->success($special, [] , 200, new ProductSpecialOfferTransformer(), 'collection', null, ['product']);
	}
}