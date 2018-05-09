<?php


$router->get('/', [
  'uses' => 'ExampleController@index'
]);


$router->get('rajaongkir', 'ExampleController@rajaongkir');

$router->get('special-offer', 'BannerController@special_offer');

$router->get('wishlist', ['uses' => 'WishlistController@list' , 'middleware' => ['cors', 'jwtauth']]);
$router->delete('wishlist', ['uses' => 'WishlistController@destroy' , 'middleware' => ['cors', 'jwtauth']]);
$router->post('wishlist', ['uses' => 'WishlistController@add' , 'middleware' => ['cors', 'jwtauth']]);

$router->get('search/product/{category}', 'ProductController@category');

$router->post('shipping', 'ShippingController@getData');

$router->get('ads-home', ['uses' => 'BannerController@ads', 'middleware' => ['cors']]);

$router->get('checkout', ['uses' => 'CheckoutController@store', 'middleware' => ['cors', 'jwtauth']]);

$router->get('product/most-viewed', ['uses' => 'ProductController@most_viewed', 'middleware' => ['cors']]);

$router->get('banner_slideshow', ['uses' => 'BannerController@slideshow', 'middleware' => ['cors']]);
$router->get('our_product', ['uses' => 'BannerController@our_product', 'middleware' => ['cors']]);
$router->get('category_home', ['uses' => 'BannerController@category_home', 'middleware' => ['cors']]);

$router->get('user/info', ['uses' => 'MemberController@getUser', 'middleware' => ['cors', 'jwtauth']]);
$router->get('member/place/list', ['uses' => 'MemberController@place_list' , 'middleware' => ['cors', 'jwtauth']]);
$router->post('member/place/get', ['uses' => 'Member\PlaceController@getPlace' , 'middleware' => ['cors', 'jwtauth']]);
$router->post('member/register', ['uses' => 'MemberController@register', 'middleware' => ['cors']]);
$router->post('member/login/phone', ['uses' => 'MemberController@phone', 'middleware' => ['cors']]);
$router->post('cek/code/register', ['uses' => 'MemberController@cekCode', 'middleware' => ['cors']]);
$router->post('member/login', ['uses' => 'MemberController@login', 'middleware' => ['cors']]);
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
$router->get('product/list', ['uses' => 'ProductController@list', 'middleware' => ['cors']]);

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
$router->get('payment/bill', ['uses' => 'PaymentController@bill' , 'middleware' => ['cors', 'jwtauth']]);

$router->get('product/{alias}', 'ProductController@getData');
$router->get('{koprasi}/{product}', 'ProductController@single');