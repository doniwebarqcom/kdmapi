<?php


$router->get('/', [
  'uses' => 'ExampleController@index'
]);


$router->post('member/register', ['uses' => 'MemberController@register', 'middleware' => ['cors']]);
$router->post('member/login/phone', ['uses' => 'MemberController@phone', 'middleware' => ['cors']]);
$router->post('cek/code/register', ['uses' => 'MemberController@cekCode', 'middleware' => ['cors']]);
$router->post('member/login', ['uses' => 'MemberController@login', 'middleware' => ['cors']]);
$router->post('register/user/byphone', ['uses' => 'MemberController@registerByPhone', 'middleware' => ['cors']]);
$router->get('user/info', ['uses' => 'MemberController@getUser', 'middleware' => ['cors', 'jwtauth']]);
$router->post('member/edit/image', ['uses' => 'MemberController@upload_image' , 'middleware' => ['cors', 'jwtauth']]);

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

$router->post('image/upload', ['uses' => 'ImageController@upload', 'middleware' => ['cors']]);

$router->get('criteria/category', ['uses' => 'CriteriaController@category', 'middleware' => ['cors']]);
$router->get('spesification/category', ['uses' => 'CriteriaController@spesification', 'middleware' => ['cors']]);

$router->get('sms', ['uses' => 'ExampleController@sms', 'middleware' => ['cors']]);

$router->get('ocupation', ['uses' => 'OcupationController@index', 'middleware' => ['cors']]);
$router->get('selling/enviroment', ['uses' => 'OcupationController@sellEnv', 'middleware' => ['cors']]);

$router->post('register/dropshiper', ['uses' => 'DropshiperController@store', 'middleware' => ['cors', 'jwtauth']]);

$router->get('{koprasi}/{product}', 'ProductController@single');