<?php

date_default_timezone_set("Asia/Bangkok");

$router->post('curl-test', function (Illuminate\Http\Request $request){

	$url = @$request->url;
	if(!$url)
	{
		return ['status' => 404, 'message' => 'Error', 'data' => 'URL Empty'];
	}
	
	// create curl resource 
    $ch = curl_init(); 

    // set url 
    curl_setopt($ch, CURLOPT_URL, $url); 

    //return the transfer as a string 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 

    // $output contains the output string 
    $output = curl_exec($ch); 

    // close curl resource to free up system resources 
    curl_close($ch);

    return ['status' => 200, 'message' => 'success', 'data' => $output];
});

// APIWHA
$router->post('apiwha', function(Illuminate\Http\Request $request){

    // This is your webhook. You must configure it in the number settings section. 
    $result = '';
    $data = json_decode($request->data); 
    
    // When you receive a message an INBOX event is created 
    if ($data->event=="INBOX") 
    { 
        $result = ApiWhaCurl($data->from, '"'. $data->text .'" sedang diproses...');

        if(!(strpos(strtoupper($data->text), "TRANSAKSI")===false))
        {
            $msg = "TRANSAKSI\n";
            $msg .= "===============================\n";
            $msg .= "1 = Pulsa\n";
            $msg .= "2 = Paket Data\n";
            $msg .= "3 = Token PLN Reguler \n";
            $msg .= "4 = Token PLN Berlangganan \n";
            $msg .= "5 = Tagihan PLN \n";
            $msg .= "6 = BPJS Kesehatan \n";
            $msg .= "7 = Tagihan Air \n";
            $msg .= "===============================\n";

            $result = ApiWhaCurl($data->from, $msg);
        }
        elseif(!(strpos(strtoupper($data->text), "LIST")===false))
        {
            $msg = "LIST FUNGSI\n";
            $msg .= "===============================\n";
            $msg .= "\"INVOICE\" untuk mengetahui invoice yang belum lunas.\n";
            $msg .= "\"KATALOG\" untuk mengetahui katalog produk kodami.\n";
            $msg .= "\"PROFIL.<noanggota>\" untuk mengetahui profil anggota.\n";
            $msg .= "\"KODAMI\" untuk mengetahui profil Koperasi Kodami.\n";
            $msg .= "===============================\n";

            $result = ApiWhaCurl($data->from, $msg);
        }
        elseif(!(strpos(strtoupper($data->text), "PROFIL")===false))
        {
            $param = explode('.', $data->text);
            
            if(!isset($param[1]))
            {
                $result = ApiWhaCurl($data->from, 'Maaf, No Anggota harus diisi. Format "PROFIL.<noanggota>"');
            }
            else
            {
                $user = \Kodami\Models\Mysql\Users::where('no_anggota', $param[1])->first();
                if($user)
                {
                    $msg = "===============================\n";
                    $msg .= 'No Anggota : '. $user->no_anggota ."\n";
                    $msg .= 'Nama : '. $user->name ."\n";
                    $msg .= 'Jenis Kelamin : '. $user->jenis_kelamin ."\n";
                    $msg .= 'Email : '. $user->email ."\n";
                    $msg .= 'Telepon : '. $user->telepon ."\n";
                    $msg .= 'Kuota : '. (isset($user->user_dropshiper->saldo) ? number_format($user->user_dropshiper->saldo) : 0) ."\n";
                    $msg .= 'Kuota Terpakai : '. (isset($user->user_dropshiper->saldo_terpakai) ? number_format($user->user_dropshiper->saldo_terpakai) : 0) ."\n";
                    $msg .= "Simpanan Wajib : 0 \n";
                    $msg .= "Simpanan Sukarela : 0 \n";
                    $msg .= "Simpanan Pokok : 0 \n";
                    $msg .= "===============================\n";
                    
                    $result = ApiWhaCurl($data->from ,$msg);
                }
                else
                {
                    $result = ApiWhaCurl($data->from, 'Maaf, No Anggota tidak ditemukan.');
                }
            }
        } 
        elseif(!(strpos(strtoupper($data->text), "INVOICE")===false))
        {
            $invoice = \Kodami\Models\Mysql\PInvoice::where('status', 1)->get();
            if(count($invoice) > 0)
            {
                $msg = "#################################\n";
                $msg .= 'BERIKUT INVOICE YANG BELUM LUNAS'."\n";
                $msg .= "#################################\n";
                foreach($invoice as $item)
                {
                    if(!isset($item->user->no_anggota)) continue;

                    $msg .= "===============================\n";
                    $msg .= 'No Anggota : '. $item->user->no_anggota ."\n";
                    $msg .= 'Nama : '. $item->user->name ."\n";
                    $msg .= "No Invoice : ". $item->no_invoice ." \n";
                    $msg .= "Nominal : ". number_format($item->nominal) ." \n";
                    $msg .= "Tanggal : ". date('d F Y', strtotime($item->created_at)) ." \n";
                }
                $msg .= "===============================\n";
                $result = ApiWhaCurl($data->from ,$msg);
            }
            else
            {
                $result = ApiWhaCurl($data->from, 'Maaf, Tidak ada invoice..');
            }
        }
        elseif(!(strpos(strtoupper($data->text), "KATALOG")===false))
        {
            $result = ApiWhaCurl($data->from, 'https://www.pulsa.kodami.id/katalog/katalog-2018-12-07.pdf');
        }
        elseif(!(strpos(strtoupper($data->text), "KODAMI")===false))
        {
            $msg = 'Koperasi Daya Masyarakat Indonesia, merupakan salah satu koperasi produsen yang modern, bekerja dengan memberdayakan masyarakat Indonesia dalam rangka menjadi pelaku ekonomi yang tangguh dan profesional, dengan mengembangkan sistem ekonomi kerakyatan yang bertumpu pada mekanisme pasar yang berkeadilan, dengan suatu tujuan untuk Indonesia yang lebih baik. Layanan Kodami berupa penjualan offline dan online didukung armada kuper (kurir koperasi) dan eskop (ekspedisi koperasi) yang akan membantu masyarakat untuk kemudahan bertransaksi dengan harga yang lebih kompetitif.';
            $result = ApiWhaCurl($data->from, 'http://kodami.co.id/logo.png');
            $result = ApiWhaCurl($data->from, $msg);
        }
        else
        {
            $result = ApiWhaCurl($data->from, 'Maaf, Format "'. $data->text .'" tidak ditemukan.'); 
        }
    }

    return $result;
});

// PULSA
$router->group(['namespace' => 'Pulsa', 'prefix' => 'pulsa'], function() use($router){
	$router->post('response', 'IndexController@response_post');
	$router->get('response', 'IndexController@response_get');
});

$router->post('/', 'MootaController@response_post');


// $router->post('moota/response', 'MootaController@response_post');

$router->get('/', ['uses' => 'ExampleController@index']);
$router->get('rajaongkir', 'ExampleController@rajaongkir');
$router->get('rajaongkir/province', 'ExampleController@rajaongkirProvince');
$router->get('rajaongkir/city', 'ExampleController@rajaongkirCity');
$router->get('transaction/{transaction_code}/detail', ['uses' => 'MemberController@detail_transaction' , 'middleware' => ['cors', 'jwtauth']]);
$router->get('transaction/list', ['uses' => 'MemberController@list_transaction' , 'middleware' => ['cors', 'jwtauth']]);
$router->get('pending/top_up', ['uses' => 'MemberController@pending_top_up' , 'middleware' => ['cors', 'jwtauth']]);
$router->get('special-offer', 'BannerController@special_offer');
$router->get('wishlist', ['uses' => 'WishlistController@list' , 'middleware' => ['cors', 'jwtauth']]);
$router->delete('wishlist', ['uses' => 'WishlistController@destroy' , 'middleware' => ['cors', 'jwtauth']]);
$router->post('wishlist', ['uses' => 'WishlistController@add' , 'middleware' => ['cors', 'jwtauth']]);
$router->post('isi/saldo', ['uses' => 'MemberController@isi_saldo' , 'middleware' => ['cors', 'jwtauth']]);
$router->get('search/product/{category}', 'ProductController@category');
$router->get('suggest/product', 'ProductController@suggest');
$router->post('shipping', 'ShippingController@getData');
$router->get('ads-home', ['uses' => 'BannerController@ads', 'middleware' => ['cors']]);
$router->get('checkout', ['uses' => 'CheckoutController@store', 'middleware' => ['cors', 'jwtauth']]);
$router->get('product/most-viewed', ['uses' => 'ProductController@most_viewed', 'middleware' => ['cors']]);
$router->get('banner_slideshow', ['uses' => 'BannerController@slideshow', 'middleware' => ['cors']]);
$router->get('our_product', ['uses' => 'BannerController@our_product', 'middleware' => ['cors']]);
$router->get('category_home', ['uses' => 'BannerController@category_home', 'middleware' => ['cors']]);
$router->get('user/info', ['uses' => 'MemberController@getUser', 'middleware' => ['cors', 'jwtauth']]);
$router->get('member/dana/simpanan_anggota', ['uses' => 'MemberController@dana_simpanan_anggota' , 'middleware' => ['cors', 'jwtauth']]);
$router->get('member/koprasi/product/validated', ['uses' => 'MemberController@product_validated' , 'middleware' => ['cors', 'jwtauth']]);
$router->get('member/place/list', ['uses' => 'MemberController@place_list' , 'middleware' => ['cors', 'jwtauth']]);
$router->post('member/place/get', ['uses' => 'Member\PlaceController@getPlace' , 'middleware' => ['cors', 'jwtauth']]);
$router->post('member/register', ['uses' => 'MemberController@register', 'middleware' => ['cors']]);
$router->post('member/login/phone', ['uses' => 'MemberController@phone', 'middleware' => ['cors']]);
$router->post('cek/code/register', ['uses' => 'MemberController@cekCode', 'middleware' => ['cors']]);
$router->post('member/login', ['uses' => 'MemberController@login', 'middleware' => ['cors']]);
$router->post('member/login/anggota', ['uses' => 'MemberController@login_by_anggota', 'middleware' => ['cors']]);
$router->post('register/user/byphone', ['uses' => 'MemberController@registerByPhone', 'middleware' => ['cors']]);
$router->post('member/edit/image', ['uses' => 'MemberController@upload_image' , 'middleware' => ['cors', 'jwtauth']]);
$router->post('member/edit/profile', ['uses' => 'MemberController@profile_store' , 'middleware' => ['cors', 'jwtauth']]);
$router->post('member/place', ['uses' => 'Member\PlaceController@store' , 'middleware' => ['cors', 'jwtauth']]);
$router->put('member/place', ['uses' => 'Member\PlaceController@put' , 'middleware' => ['cors', 'jwtauth']]);
$router->delete('member/place', ['uses' => 'Member\PlaceController@destroy' , 'middleware' => ['cors', 'jwtauth']]);
$router->post('subscribe', ['uses' => 'SubscriberController@index', 'middleware' => ['cors']]);
$router->post('unsubscribe', ['uses' => 'SubscriberController@delete', 'middleware' => ['cors']]);
$router->get('category', ['uses' => 'CategoryController@index', 'middleware' => ['cors']]);
$router->get('category-insearch', ['uses' => 'CategoryController@search', 'middleware' => ['cors']]);
$router->post('shop/register', ['uses' => 'ShopController@register', 'middleware' => ['cors', 'jwtauth']]);
$router->post('product/input', ['uses' => 'ProductController@input', 'middleware' => ['cors', 'jwtauth']]);
$router->get('product/list', ['uses' => 'ProductController@list', 'middleware' => ['cors', 'jwtauth']]);
$router->get('place/province', ['uses' => 'PlaceController@getProvince', 'middleware' => ['cors']]);
$router->get('place/regency', ['uses' => 'PlaceController@getRegency', 'middleware' => ['cors']]);
$router->get('place/district', ['uses' => 'PlaceController@getDistrict', 'middleware' => ['cors']]);
$router->get('place/village', ['uses' => 'PlaceController@getVillage', 'middleware' => ['cors']]);
$router->get('place/postal', ['uses' => 'PlaceController@getPostal', 'middleware' => ['cors']]);
$router->get('place/postal-code/district', ['uses' => 'PlaceController@postalcodeByDistrict', 'middleware' => ['cors']]);
$router->post('image/upload', ['uses' => 'ImageController@upload', 'middleware' => ['cors']]);
$router->get('criteria/category', ['uses' => 'CriteriaController@category', 'middleware' => ['cors']]);
$router->get('spesification/category', ['uses' => 'CriteriaController@spesification', 'middleware' => ['cors']]);
$router->get('sms', ['uses' => 'ExampleController@sms', 'middleware' => ['cors']]);
$router->get('ocupation', ['uses' => 'OcupationController@index', 'middleware' => ['cors']]);
$router->get('selling/enviroment', ['uses' => 'OcupationController@sellEnv', 'middleware' => ['cors']]);
$router->post('register/dropshiper', ['uses' => 'DropshiperController@store', 'middleware' => ['cors', 'jwtauth']]);
$router->get('cart', ['uses' => 'CartController@list' , 'middleware' => ['cors', 'jwtauth']]);
$router->post('cart/update', ['uses' => 'CartController@update' , 'middleware' => ['cors', 'jwtauth']]);
$router->post('cart/store', ['uses' => 'CartController@store' , 'middleware' => ['cors', 'jwtauth']]);
$router->post('cart/store/withNewPlace', ['uses' => 'CartController@withNewPlace' , 'middleware' => ['cors', 'jwtauth']]);
$router->delete('cart/{id}', ['uses' => 'CartController@destroy_cart' , 'middleware' => ['cors', 'jwtauth']]);
$router->post('{product}/add-cart', ['uses' => 'CartController@addCart' , 'middleware' => ['cors', 'jwtauth']]);
$router->post('payment/choose', ['uses' => 'PaymentController@choose' , 'middleware' => ['cors', 'jwtauth']]);
$router->get('payment/bill/{invoice}', ['uses' => 'PaymentController@bill' , 'middleware' => ['cors', 'jwtauth']]);
$router->get('product/{alias}', 'ProductController@getData');
$router->get('{koprasi}/{product}', 'ProductController@single');
