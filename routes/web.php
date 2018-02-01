<?php


$router->get('/', [
  'uses' => 'ExampleController@index'
]);


$router->post('member/register', ['uses' => 'MemberController@register', 'middleware' => ['cors']]);
$router->post('member/login', ['uses' => 'MemberController@login', 'middleware' => ['cors']]);
$router->get('user/info', ['uses' => 'MemberController@getUser', 'middleware' => ['cors', 'jwtauth']]);

$router->post('subscribe', ['uses' => 'SubscriberController@index', 'middleware' => ['cors']]);
$router->post('unsubscribe', ['uses' => 'SubscriberController@delete', 'middleware' => ['cors']]);

$router->get('category', ['uses' => 'CategoryController@index', 'middleware' => ['cors']]);
$router->get('category-insearch', ['uses' => 'CategoryController@search', 'middleware' => ['cors']]);

$router->post('shop/register', ['uses' => 'ShopController@register', 'middleware' => ['cors', 'jwtauth']]);

$router->post('product/input', ['uses' => 'ProductController@input', 'middleware' => ['cors', 'jwtauth']]);

$router->get('place/postal', ['uses' => 'PlaceController@getPostal', 'middleware' => ['cors']]);
$router->get('place/regency', ['uses' => 'PlaceController@getRegency', 'middleware' => ['cors']]);

$router->post('image/upload', ['uses' => 'ImageController@upload', 'middleware' => ['cors']]);