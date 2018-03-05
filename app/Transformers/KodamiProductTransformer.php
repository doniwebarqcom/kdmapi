<?php

namespace App\Transformers;

use Kodami\Models\Mysql\KodamiProduct;
use League\Fractal\TransformerAbstract;

class KodamiProductTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'criteria', 'spesification'
    ];

    public function transform(KodamiProduct $product)
    {    
        $data =  [
            'id'                    => (int) $product->id,
            'category'              => isset($product->category->name) ? $product->category->name : null,
            'name'                  => $product->name,
            'alias'                 => $product->name_alias,
            'description'           => $product->description,
            'long_description'      => $product->long_description,
            'price'                 => (double) $product->price,
            'comisi'                => (int) ($product->price * 0.015),
            'primary_image'         => $product->primary_image,
            'avaible'               => $product->is_avaible,
            'success_transaction'   => (int) $product->success_transaction,
            'total_comment'         => (int) $product->total_comment,
            'weight'                => (int) $product->weight,
            'viewer'                => (int) $product->viewer,
            'stock'                 => (int) $product->stock(),
            'new'                   => $product->new,
            'discont'               => $product->discont,
            'discont_anggota'       => $product->discont_anggota,
            'image'                 => $product->image
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

    public function includeSpesification(Product $product)
    {
        if(isset($product->spesification))
            return $this->collection($product->spesification, new SpesificationProductCriteriaTransformer);
        else
            return [];
    }
}

