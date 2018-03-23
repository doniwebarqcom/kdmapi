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
            'name'              => $banner->product->name,
            'alias'             => $banner->product->name_alias,
            'category'          => $banner->product->category->full_name,
            'price'             => $banner->product->price,
            'price_discont'     => 0,
            'discont'           => $banner->product->discont,
            'discont_anggota'   => $banner->product->discont_anggota,
            'description'       => $banner->descripsi,
            'image'             => $banner->image,
        ];

        return $data;
    }
}

