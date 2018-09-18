<?php

namespace App\Transformers;

use Kodami\Models\Mysql\BannerSlideshow;
use League\Fractal\TransformerAbstract;

class BannerSlideShowTransformer extends TransformerAbstract
{  
    public function transform(BannerSlideshow $banner)
    {    
        $data =  [
            'id'                => (int) $banner->id,            
            'product_id'        => (int) $banner->kodami_product_id,
            'name'              => isset($banner->product->name) ? $banner->product->name : "",
            'type'              => $banner->type,
            'alias'             => isset($banner->product->name_alias) ? $banner->product->name_alias : "",
            'category'          => isset($banner->product->category->full_name) ? $banner->product->category->full_name : "",
            'price'             => isset($banner->product->price) ? $banner->product->price : 0,
            'price_discont'     => 0,
            'discont'           => isset($banner->product->discont) ? (int) $banner->product->discont : 0,
            'discont_anggota'   => isset($banner->product->discont_anggota) ? $banner->product->discont_anggota : 0,
            'description'       => $banner->descripsi,
            'image'             => $banner->image,
        ];

        return $data;
    }
}

