<?php

namespace App\Transformers;

use Kodami\Models\Mysql\Category;
use League\Fractal\TransformerAbstract;

class CategoryTransformer extends TransformerAbstract
{
    public function transform(Category $Category)
    {    
        
        $item =  [
            'id'            => $Category->id,
            'name'          => $Category->name,
            'has_children'  => $Category->has_children,
            'order_num'     => $Category->order_num,
            'slug'          => $Category->slug,
            'permalink'     => $Category->permalink,
            'description'   => $Category->description,
            'image'         => $Category->image,
        ];

        if ($Category->has_children == 1 AND isset($Category->children)) {
            foreach ($Category->children as $key => $value) {
                $item['subcategory'][] = $this->transform($value);
            }            
        }

        return $item;
    }  
}

