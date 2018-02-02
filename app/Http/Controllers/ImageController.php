<?php

namespace App\Http\Controllers;

use JD\Cloudder\Facades\Cloudder;
use Validator;

class ImageController extends ApiController
{
	public function upload()
	{
		$rules = [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ];

    	$validator = Validator::make(
    		$this->request->all(),
    		$rules
		);

        if ($validator->fails())
            return $this->response()->error($validator->errors()->all());

		$result_upload = Cloudder::upload($this->request->file('image')->getPathName());
		return $this->response()->success($result_upload->getResult());
	}
}
