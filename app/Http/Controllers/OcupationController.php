<?php

namespace App\Http\Controllers;

use App\Repositories\CostumePagination;
use Kodami\Models\Mysql\Ocupation;
use Illuminate\Http\Request;

class OcupationController extends ApiController
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
    }

    public function index()
    {
        $limit = $this->request->get('limit') ? $this->request->get('limit') : 100;
        $ocupation = Ocupation::select('id', 'name')->paginate($limit);
        $pagination = new CostumePagination($ocupation);     
        $result = $pagination->render();

        return $this->response()->success($result['data'], ['paging' => $result['paging']]);
    }
}
