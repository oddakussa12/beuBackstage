<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/



Route::group(['prefix'=>'backstage'] , function(){
    Route::get('passport/user/yesterday' , 'Passport\UserController@yesterday')->name('passport.user.yesterday');
    Route::get('login', 'Auth\LoginController@showLoginForm')->name('admin.login_form');
    Route::post('login', 'Auth\LoginController@login')->name('admin.login');
//    Route::get('register', 'Auth\RegisterController@showRegistrationForm');
//    Route::post('register', 'Auth\RegisterController@register')->name('admin.register');
    Route::get('logout', 'Auth\LoginController@logout')->name('admin.logout');
    Route::group(['middleware' => ['auth','auth.check']] , function (){

        Route::get('/', 'BackstageController@index');
        Route::group(['namespace'=>'Auth'] , function (){
            Route::resource('admin', 'AdminController');
            Route::resource('permission', 'PermissionController');
            Route::resource('role', 'RoleController');
        });
        Route::group(['namespace'=>'Service','prefix'=>'service' , 'as' => 'service::'] , function (){
            Route::resource('message' , 'MessageController');
            Route::get('message/{message}/image' , 'MessageController@image')->name('message.image');
        });

        Route::group(['namespace'=>'Content','prefix'=>'content' , 'as' => 'content::'] , function (){
            Route::get('music/index' , 'MusicController@index')->name('music.index');
            Route::patch('music/{music}' , 'MusicController@update')->name('music.update');
        });

        Route::group(['namespace'=>'Passport','prefix'=>'passport' , 'as' => 'passport::'] , function (){

            Route::get('user/keep' , 'UserController@keep')->name('user.keep');
            Route::get('user/dau' , 'UserController@dau')->name('user.dau');
        });

        Route::group(['namespace'=>'Lovbee','prefix'=>'lovbee' , 'as' => 'lovbee::'] , function (){
            Route::get('message/operation' , 'MessageController@operation')->name('message.operation'); // 消息管理
            Route::post('message/operation' , 'MessageController@submit')->name('message.submit'); // 消息管理
            Route::get('message/play' , 'MessageController@play')->name('message.play'); // 消息管理
            Route::get('message/video' , 'MessageController@video')->name('message.video'); // 消息管理
            Route::post('message/comment' , 'MessageController@comment')->name('message.comment'); // 消息管理
            Route::get('message/export' , 'MessageController@export')->name('message.export'); // 消息管理
        });

        Route::group(['namespace'=>'Props','prefix'=>'props' , 'as' => 'props::'] , function (){
            Route::get('props/index' , 'PropsController@index')->name('props.index'); // 道具管理
            Route::resource('props' , 'PropsController'); // 道具管理
            Route::get('category/index' , 'CategoryController@index')->name('category.index'); // 分类管理
            Route::resource('category' , 'CategoryController'); // 分类管理
        });

        Route::resource('menu' , 'MenuController');
        Route::resource('translation' , 'TranslationController');
        Route::resource('config' , 'ConfigController');
    });
    Route::get('passport/user/yesterday/view' , 'Passport\UserController@yesterdayView')->name('passport.user.yesterday.view');

});


