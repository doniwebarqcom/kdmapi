<?php

namespace App\Transformers;

use Kodami\Models\Mysql\ProductCategoryHome;
use League\Fractal\TransformerAbstract;

class ProductCategoryHomeTransformer extends TransformerAbstract
{  
    public function transform(ProductCategoryHome $category)
    {    
        $data =  [
            'id'        =>  $category->id
        ];

        $data =  [
            'id'                    => (int) $category->product->id,
            'category'              => isset($category->product->category->name) ? $category->product->category->name : null,
            'name'                  => $category->product->name,
            'alias'                 => $category->product->name_alias,
            'description'           => $category->product->description,
            'long_description'      => $category->product->long_description,
            'price'                 => (double) $category->product->price,
            'comisi'                => (int) ($category->product->price * 0.015),
            'primary_image'         => $category->product->primary_image,
            'avaible'               => $category->product->is_avaible,
            'success_transaction'   => (int) $category->product->success_transaction,
            'total_comment'         => (int) $category->product->total_comment,
            'weight'                => (double) $category->product->weight,
            'viewer'                => (int) $category->product->viewer,
            'stock'                 => (int) $category->product->stock(),
            'new'                   => $category->product->new,
            'discont'               => $category->product->discont,
            'discont_anggota'       => $category->product->discont_anggota,
            'image'                 => $category->product->image
        ];

        return $data;
    }   
}

