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
    Route::group(['middleware' => ['auth']] , function (){
        Route::get('/', 'BackstageController@index');
        Route::get('admin/{admin}/edit', 'Auth\AdminController@edit')->name('admin.edit');
        Route::patch('admin/self', 'Auth\AdminController@updateSelf')->name('admin.update.self');
        Route::patch('admin/{admin}/reset', 'Auth\AdminController@resetPwd')->name('admin.reset.pwd');
        Route::group(['middleware' => ['auth.check']] , function (){
            Route::group(['namespace'=>'Auth'] , function (){
                Route::resource('admin', 'AdminController');
                Route::resource('permission', 'PermissionController');
                Route::resource('role', 'RoleController');
            });

            Route::group(['namespace'=>'Content','prefix'=>'content' , 'as' => 'content::'] , function (){
                Route::get('music/index' , 'MusicController@index')->name('music.index');
                Route::patch('music/{music}' , 'MusicController@update')->name('music.update');
                Route::delete('music/{music}' , 'MusicController@destroy')->name('music.destroy');
                Route::post('music' , 'MusicController@store')->name('music.store');
            });

            Route::group(['namespace'=>'Operation','prefix'=>'operation' , 'as' => 'operation::'] , function (){
                Route::get('version' , 'VersionController@index')->name('version.index');

                Route::patch('version/{version}' , 'VersionController@update')->name('version.update');

                Route::get('version/upgrade' , 'VersionController@upgrade')->name('version.upgrade');

                Route::get('message_layer' , 'MessageLayerController@index')->name('message_layer.index');

                Route::get('network_log' , 'NetworkLogController@index')->name('network_log.index');

                Route::get('feedback' , 'FeedbackController@index')->name('feedback.index');

                Route::get('active_user' , 'ActiveUserController@index')->name('active_user.index');
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
//                Route::get('user/online', 'UserController@online')->name('user.online');
                Route::get('user/{user}/friend/status', 'UserController@friendStatus')->name('user.friend.status');
                Route::get('user/{user}/friend/yesterday/status', 'UserController@friendYesterdayStatus')->name('user.friend.yesterday.status');
                Route::get('user', 'UserController@index')->name('user.index');
                Route::post('user/block', 'UserController@block')->name('user.block');
                Route::get('user/device/{id}', 'UserController@device')->name('user.device');
                Route::post('user/device/block', 'UserController@deviceBlock')->name('user.device.block');

                Route::get('follow' , 'FollowController@index')->name('follow.index');

                Route::get('friend/index', 'FriendController@index')->name('friend.index');
                Route::get('friend/request', 'FriendController@request')->name('friend.request');
                Route::get('group', 'GroupController@index')->name('group.index'); // 群组列表管理
                Route::get('group/{groupId}', 'GroupController@member')->name('group.member'); // 群组列表管理
                Route::get('group/members', 'GroupController@members')->name('group.members'); // 群组列表管理


            });

            Route::group(['namespace'=>'Service','prefix'=>'service' , 'as' => 'service::'] , function (){
                Route::get('message/chat' , 'MessageController@chatMessage')->name('message.chat');
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
                Route::patch('shop/owner' , 'ShopController@owner')->name('shop.owner');
                Route::get('shop/{shop}/view' , 'ShopController@view')->name('shop.view');
                Route::get('offline_shop' , 'ShopController@offline')->name('shop.offline');
                Route::get('shop/search' , 'ShopController@search')->name('shop.search');
                Route::get('shop/review' , 'ShopController@review')->name('shop.review');
                Route::get('shop/search/{search}' , 'ShopController@searchShow')->name('shop.search.show');

                Route::resource('shop' , 'ShopController');
                Route::get('goods/{goods}/view' , 'GoodsController@view')->name('goods.view');
                Route::resource('goods' , 'GoodsController');
                Route::get('goods_comment' , 'GoodsCommentController@index')->name('goods_comment.index');
                Route::put('goods_comment/{goods_comment}' , 'GoodsCommentController@update')->name('goods_comment.update');
                Route::get('goods_comment/statistics' , 'GoodsCommentController@statistics')->name('goods_comment.statistics');
                Route::get('goods_comment/acquisition' , 'GoodsCommentController@acquisition')->name('goods_comment.acquisition');
                Route::get('delivery_order' , 'DeliveryOrderController@index')->name('delivery_order.index');
                Route::patch('delivery_order/{delivery_order}' , 'DeliveryOrderController@update')->name('delivery_order.update');
                Route::get('delivery_order/browse' , 'DeliveryOrderController@browse')->name('delivery_order.browse');
                Route::get('shop_order' , 'ShopOrderController@index')->name('shop_order.index');
                Route::get('shop_order/browse' , 'ShopOrderController@browse')->name('shop_order.browse');
                Route::get('shopping_cart' , 'ShoppingCartController@index')->name('shopping_cart.index');
                Route::get('shop_tag' , 'ShopTagController@index')->name('shop_tag.index');
                Route::post('shop_tag' , 'ShopTagController@store')->name('shop_tag.store');
                Route::patch('shop_tag/{shop_tag}' , 'ShopTagController@update')->name('shop_tag.update');
                Route::get('shop_order/{id}' , 'ShopOrderController@show')->name('shop_order.show');
                Route::patch('shop_order/{shop_order}' , 'ShopOrderController@update')->name('shop_order.update');
                Route::get('graph' , 'GraphController@index')->name('graph.index');
                Route::resource('promo_code' , 'PromoCodeController');
                Route::get('goods_category' , 'GoodsCategoryController@index')->name('goods_category.index');
                Route::get('comment_manager' , 'CommentManagerController@index')->name('comment_manager.index');
                Route::get('comment_manager/{comment_manager}' , 'CommentManagerController@show')->name('comment_manager.show');
                Route::get('complex' , 'ComplexController@index')->name('complex.index');

            });

            Route::resource('menu' , 'MenuController');
            Route::resource('translation' , 'TranslationController');
            Route::resource('config' , 'ConfigController');
        });

    });
    Route::group(['middleware' => ['auth']] , function (){
        Route::group(['prefix'=>'qn' , 'as' => 'qn.'] , function (){
            Route::get('bundle/token' , 'QiNiuController@bundleToken')->name('bundle.token'); // bundle token
        });
    });
    Route::get('business/shop_order/count' , 'Business\OrderController@count')->name('business.order.count');
    Route::get('passport/user/yesterday/view' , 'Passport\UserController@yesterdayView')->name('passport.user.yesterday.view');
//    Route::get('operator/operator/goal' , 'Operator\OperatorController@goal')->name('operator.operator.goal');
//    Route::post('operator/operator/goal/data' , 'Operator\OperatorController@goalData')->name('operator.operator.goal.data');
//    Route::get('operator/operator/goal202104' , 'Operator\OperatorController@goal202104')->name('operator.operator.goal202104');
//    Route::get('operator/operator/goal202105' , 'Operator\OperatorController@goal202105')->name('operator.operator.goal202105');
});
Route::any('/' , 'BackstageController@redirect')->name('backstage.redirect');


