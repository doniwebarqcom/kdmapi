<?php

namespace App\Transformers;

use Kodami\Models\Mysql\Product;
use League\Fractal\TransformerAbstract;

class ProductTransformer extends TransformerAbstract
{
    public function transform(Product $product)
    {    
        $data =  [
            'id'                    => (int) $product->id,
            'category'              => isset($product->category->name) ? $product->category->name : null,
            'description'           => $product->description,
            'price'                 => (double) $product->price,
            'primary_image'         => $product->primary_image,
            'avaible'               => $product->is_avaible,
            'success_transaction'   => (int) $product->success_transaction,
            'total_comment'         => (int) $product->total_comment,
            'weight'                => (int) $product->weight,
            'viewer'                => (int) $product->viewer,
            'stock'                 => (int) $product->stock,
            'new'                   => $product->new,
            'image'                 => $product->image,
        ];

        return $data;
    }  
}

