<?php

namespace App\Transformers;

use Kodami\Models\Mysql\Product;
use League\Fractal\TransformerAbstract;

class ProductTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'criteria'
    ];

    public function transform(Product $product)
    {    
        $data =  [
            'id'                    => (int) $product->id,
            'category'              => isset($product->category->name) ? $product->category->name : null,
            'name'                  => $product->name,
            'alias'                 => $product->name_alias,
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
            'discont'               => $product->discont,
            'discont_anggota'       => $product->discont_anggota,
            'image'                 => $product->image,
            'koprasi'               => $product->koprasi,
        ];

        return $data;
    }

    public function includeCriteria(Product $product)
    {
        if(isset($product->criteria))
            return $this->collection($product->criteria, new CriteriaProductCriteriaTransformer);
        else
            return [];
    }
}

