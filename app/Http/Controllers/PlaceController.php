<?php

namespace App\Http\Controllers;

use App\Transformers\PostalTransformer;
use App\Transformers\RegencyTransformer;
use App\Repositories\CostumePagination;
use Kodami\Models\Mysql\KodePos;
use Kodami\Models\Mysql\Regency;
use Illuminate\Http\Request;

class PlaceController extends ApiController
{
	public function __construct(Request $request)
    {
        parent::__construct($request);
    }

    public function getPostal()
    {        
        $q = $this->request->get('query');
    	$limit = $this->request->get('limit') ? $this->request->get('limit') : 10;
    	$post = KodePos::where( function ($query) use($q) {
                    $query->where('kodepos', 'like', '%'.$q.'%');
                    $query->orWhere('kelurahan', 'like', '%'.$q.'%');
                    $query->orWhere('kecamatan', 'like', '%'.$q.'%');
                    $query->orWhere('kabupaten', 'like', '%'.$q.'%');
                    $query->orWhere('provinsi', 'like', '%'.$q.'%');
                })->paginate($limit);
    	$pagination = new CostumePagination($post);    	
    	$result = $pagination->render();

    	return $this->response()->success($result['data'], ['paging' => $result['paging']], 200, new PostalTransformer(), 'collection');
    }

    public function getRegency()
    {        
        $q = $this->request->get('query');
        $limit = $this->request->get('limit') ? $this->request->get('limit') : 10;
        $post = Regency::where( function ($query) use($q) {
                    $query->where('name', 'like', '%'.$q.'%');
                })->paginate($limit);
        $pagination = new CostumePagination($post);     
        $result = $pagination->render();

        return $this->response()->success($result['data'], ['paging' => $result['paging']], 200, new RegencyTransformer(), 'collection');
    }
}
