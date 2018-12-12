<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\Route;
Route::get('getToken', 'token/Token/getToken');
Route::rule('token', 'token/Token/responseMsg');
Route::get('weather', 'miniweather/Weather/showWeather');
Route::get('showWeather', 'token/Token/showWeather');
Route::get('showLocation', 'token/Token/showLocation');
Route::get('token', 'token/Token/responseToken');

Route::get('create_menu', 'token/Menu/createMenu');
Route::get('usr', 'token/Menu/getUser');
Route::get('code', 'token/Oauth/echoCode');
Route::get('api', 'api/News/read');

Route::rule('news/:id','api/news/read');
Route::rule('city/:city_name','api/city/read');
return [
    '__pattern__' => [
        'name' => '\w+',
    ],
    '[hello]'     => [
        ':id'   => ['index/hello', ['method' => 'get'], ['id' => '\d+']],
        ':name' => ['index/hello', ['method' => 'pos']]]];