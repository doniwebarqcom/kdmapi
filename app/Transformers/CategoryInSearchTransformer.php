<?php

namespace App\Transformers;

use Kodami\Models\Mysql\Category;
use League\Fractal\TransformerAbstract;

class CategoryInSearchTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'sub_category'
    ];

    public function transform(Category $Category)
    {    
        
        $item =  [
            'id'            => $Category->id,
            'name'          => $Category->name,
            'fullname'      => $Category->full_name,
            'permalink'     => $Category->permalink,
        ];

        return $item;
    }

    public function includeSubCategory(Category $Category)
    {
        $children = $Category->children;
        $children = $children->take(5);

        if(isset($children))
            return $this->collection($children, new CategoryInSearchTransformer);       
    }
}

