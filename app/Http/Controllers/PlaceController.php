<?php

namespace App\Http\Controllers;

use App\Transformers\PostalTransformer;
use App\Transformers\RegencyTransformer;
use App\Repositories\CostumePagination;
use Kodami\Models\Mysql\District;
use Kodami\Models\Mysql\KodePos;
use Kodami\Models\Mysql\Province;
use Kodami\Models\Mysql\Regency;
use Kodami\Models\Mysql\Village;
use Illuminate\Http\Request;

class PlaceController extends ApiController
{
	public function __construct(Request $request)
    {
        parent::__construct($request);
    }

    public function getProvince()
    {        
        $q = $this->request->get('query') ? $this->request->get('query') : null;
        $limit = $this->request->get('limit') ? $this->request->get('limit') : 50;
        $post = Province::select('id', 'name')->where( function ($query) use($q) {
                    if( $q != null)
                        $query->where('name', 'like', '%'.$q.'%');
                })->paginate($limit);
        $pagination = new CostumePagination($post);     
        $result = $pagination->render();

        return $this->response()->success($result['data'], ['paging' => $result['paging']]);
    }

    public function getRegency()
    {        
        $q = $this->request->get('query') ? $this->request->get('query') : null;
        $province = $this->request->get('province') ? $this->request->get('province') : null;
        $limit = $this->request->get('limit') ? $this->request->get('limit') : 30;
        $post = Regency::where( function ($query) use($q, $province) {
                    if( $q != null)
                        $query->where('name', 'like', '%'.$q.'%');

                    if( $province != null)
                        $query->where('province_id', $province);

                })->paginate($limit);
        $pagination = new CostumePagination($post);     
        $result = $pagination->render();

        return $this->response()->success($result['data'], ['paging' => $result['paging']], 200, new RegencyTransformer(), 'collection');
    }

    public function getDistrict()
    {        
        $q = $this->request->get('query') ? $this->request->get('query') : null;
        $regency = $this->request->get('regency') ? $this->request->get('regency') : null;
        $limit = $this->request->get('limit') ? $this->request->get('limit') : 30;
        $post = District::select('id', 'name')->where( function ($query) use($q, $regency) {
                    if( $q != null)
                        $query->where('name', 'like', '%'.$q.'%');

                    if( $regency != null)
                        $query->where('regency_id', $regency);

                })->paginate($limit);
        $pagination = new CostumePagination($post);     
        $result = $pagination->render();

        return $this->response()->success($result['data'], ['paging' => $result['paging']]);
    }

    public function getVillage()
    {        
        $q = $this->request->get('query') ? $this->request->get('query') : null;
        $district = $this->request->get('district') ? $this->request->get('district') : null;
        $limit = $this->request->get('limit') ? $this->request->get('limit') : 30;
        $post = Village::select('id', 'name')->where( function ($query) use($q, $district) {
                    if( $q != null)
                        $query->where('name', 'like', '%'.$q.'%');

                    if( $district != null)
                        $query->where('district_id', $district);

                })->paginate($limit);
        $pagination = new CostumePagination($post);     
        $result = $pagination->render();

        return $this->response()->success($result['data'], ['paging' => $result['paging']]);
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
}
