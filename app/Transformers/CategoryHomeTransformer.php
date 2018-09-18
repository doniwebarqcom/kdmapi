<?php

namespace App\Transformers;

use Kodami\Models\Mysql\CategoryHome;
use League\Fractal\TransformerAbstract;

class CategoryHomeTransformer extends TransformerAbstract
{  
    protected $availableIncludes = [
        'product'
    ];

    public function transform(CategoryHome $category)
    {    
        $data =  [
            'id'        => (int) $category->id,            
            'name'      => $category->name
        ];

        return $data;
    }

    public function includeProduct(CategoryHome $category)
    {
        if(isset($category->productcategory))
            return $this->collection($category->productcategory, new ProductCategoryHomeTransformer);
        else
            return [];
    }
}

