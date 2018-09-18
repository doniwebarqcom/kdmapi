<?php

namespace App\Http\Controllers;

use League\Fractal;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;

class ShippingController extends ApiController
{
	public function getData()
	{	

		$shipping = rajaOngkirApi('cost', 'POST', 'origin=501&destination=114&weight=1&courier=jne');
		$result_shipping = isset($shipping['rajaongkir']['results'][0]['costs'][0]['cost'][0]) ? $shipping['rajaongkir']['results'][0]['costs'][0]['cost'][0] : null;
		return $this->response()->success($result_shipping);
	}
}