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
            Route::get('admin/user', 'AdminController@userInfo')->name('admin.user');
            Route::patch('admin/user/update', 'AdminController@userInfoUpdate')->name('admin.user.update');
            Route::resource('admin', 'AdminController');
            Route::resource('permission', 'PermissionController');
            Route::resource('role', 'RoleController');
        });

        Route::group(['namespace'=>'Content','prefix'=>'content' , 'as' => 'content::'] , function (){
            Route::get('music/index' , 'MusicController@index')->name('music.index');
            Route::patch('music/{music}' , 'MusicController@update')->name('music.update');
            Route::delete('music/{music}' , 'MusicController@destroy')->name('music.destroy');
            Route::post('music' , 'MusicController@store')->name('music.store');
            Route::resource('post' , 'PostController');
            Route::get('audit/jian' , 'PostAuditController@jianHuangShi')->name('audit.jian');
            Route::get('audit/claim' , 'PostAuditController@claim')->name('audit.claim');
            Route::resource('audit' , 'PostAuditController');
            Route::get('post/{postId}/comment' , 'PostController@comment')->name('post.comment');
            Route::delete('post/comment/{postId}' , 'PostController@destroyComment')->name('post.destroyComment');
        });

        Route::group(['namespace'=>'Operator','prefix'=>'operator' , 'as' => 'operator::'] , function (){
            Route::get('version' , 'VersionController@index')->name('version.index');
            Route::patch('version/{version}' , 'VersionController@update')->name('version.update');
            Route::get('version/upgrade' , 'VersionController@upgrade')->name('version.upgrade');
            Route::get('chat' , 'ChatController@index')->name('chat.index');
            Route::get('operator/network' , 'OperatorController@network')->name('operator.network');
            Route::get('operator/feedback' , 'OperatorController@feedback')->name('operator.feedback');
            Route::get('operator/media' , 'OperatorController@media')->name('operator.media');
            Route::post('operator/media/destroy' , 'OperatorController@destroyMedia')->name('operator.media.destroy');
            Route::get('operator/score' , 'OperatorController@score')->name('operator.score');
            Route::get('operator/score/detail/{user}' , 'OperatorController@scoreDetail')->name('operator.score.detail');
            Route::get('operator/blacklist' , 'OperatorController@blackList')->name('operator.blacklist');
            Route::put('operator/black' , 'OperatorController@block')->name('operator.block');
            Route::get('operator/lastthree' , 'OperatorController@lastThree')->name('operator.lastthree');
            Route::resource('virtual' , 'VirtualUserController');
        });

        Route::group(['namespace'=>'Passport','prefix'=>'passport' , 'as' => 'passport::'] , function (){
            Route::get('user/kol', 'UserController@kol')->name('user.kol');
            Route::get('user/kol/create', 'UserController@createKol')->name('user.kol.create');
            Route::post('user/kol', 'UserController@storeKol')->name('user.kol.store');
            Route::post('user', 'UserController@update')->name('user.update');
            Route::resource('user', 'UserController')->only('update');
            Route::get('user/keep' , 'UserController@keep')->name('user.keep');
            Route::get('user/dau' , 'UserController@dau')->name('user.dau');
            Route::get('user/dnu' , 'UserController@dnu')->name('user.dnu');
            Route::get('user/export' , 'UserController@export')->name('user.export');
            Route::get('user/friend/{userId}' , 'UserController@friend')->name('user.friend');
            Route::get('user/history/{userId}' , 'UserController@history')->name('user.history');
            Route::get('user/message' , 'UserController@message')->name('user.message');
            Route::get('user/chat' , 'UserController@chat')->name('user.chat');
            Route::get('user/msgExport' , 'UserController@msgExport')->name('user.msgExport');
            Route::get('user/online', 'UserController@online')->name('user.online');
            Route::get('user/{user}/friend/status', 'UserController@friendStatus')->name('user.friend.status');
            Route::get('user/{user}/friend/yesterday/status', 'UserController@friendYesterdayStatus')->name('user.friend.yesterday.status');
            Route::get('user', 'UserController@index')->name('user.index');
            Route::post('user/block', 'UserController@block')->name('user.block');
            Route::get('user/device/{id}', 'UserController@device')->name('user.device');
            Route::post('user/device/block', 'UserController@deviceBlock')->name('user.device.block');

            Route::get('friend/index', 'FriendController@index')->name('friend.index');
            Route::get('friend/request', 'FriendController@request')->name('friend.request');
            Route::get('group', 'GroupController@index')->name('group.index'); // 群组列表管理
            Route::get('group/{groupId}', 'GroupController@member')->name('group.member'); // 群组列表管理
            Route::get('group/members', 'GroupController@members')->name('group.members'); // 群组列表管理

        });

        Route::group(['namespace'=>'Service','prefix'=>'service' , 'as' => 'service::'] , function (){
            Route::get('message/operation' , 'MessageController@operation')->name('message.operation');
            Route::post('message/operation' , 'MessageController@submit')->name('message.submit');
            Route::get('message/play' , 'MessageController@play')->name('message.play');
            Route::get('message/video' , 'MessageController@video')->name('message.video');
            Route::post('message/comment' , 'MessageController@comment')->name('message.comment');
            Route::get('message/export' , 'MessageController@export')->name('message.export');
            Route::get('question/upload/{id}' , 'QuestionController@upload')->name('question.upload');
            Route::resource('question' , 'QuestionController');
        });

        Route::group(['namespace'=>'Props','prefix'=>'props' , 'as' => 'props::'] , function (){
            Route::get('props/index' , 'PropsController@index')->name('props.index');
            Route::resource('props' , 'PropsController');
            Route::get('category/index' , 'CategoryController@index')->name('category.index');
            Route::resource('category' , 'CategoryController');
            Route::resource('medal' , 'MedalController'); //勋章
        });

        Route::group(['namespace'=>'Business','prefix'=>'business' , 'as' => 'business::'] , function (){
            Route::resource('shop' , 'ShopController');
            Route::resource('goods' , 'GoodsController');
        });

        Route::resource('menu' , 'MenuController');
        Route::resource('translation' , 'TranslationController');
        Route::resource('config' , 'ConfigController');
    });
    Route::group(['middleware' => ['auth']] , function (){
        Route::group(['prefix'=>'qn' , 'as' => 'qn.'] , function (){
            Route::get('bundle/token' , 'QiNiuController@bundleToken')->name('bundle.token'); // bundle token
        });
    });
    Route::get('passport/user/yesterday/view' , 'Passport\UserController@yesterdayView')->name('passport.user.yesterday.view');
    Route::get('operator/operator/goal' , 'Operator\OperatorController@goal')->name('operator.operator.goal');
    Route::get('operator/operator/goal/optimization' , 'Operator\OperatorController@goalOptimization')->name('operator.operator.goal.optimization');
});
Route::any('/' , function(){
    return redirect('/backstage');
});


