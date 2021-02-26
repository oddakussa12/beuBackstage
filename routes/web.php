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
    Route::get('login', 'Auth\LoginController@showLoginForm')->name('admin.login_form');
    Route::post('login', 'Auth\LoginController@login')->name('admin.login');
//    Route::get('register', 'Auth\RegisterController@showRegistrationForm');
//    Route::post('register', 'Auth\RegisterController@register')->name('admin.register');
    Route::get('logout', 'Auth\LoginController@logout')->name('admin.logout');
    Route::group(['middleware' => ['auth','auth.check']] , function (){
        //Route::get('/', 'BackstageController@index')->name('admin.home');
        //Route::get('/admin', 'AdminController@index')->name('admin.home');
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
        //
        Route::group(['namespace'=>'Content','prefix'=>'content' , 'as' => 'content::'] , function (){
            //Route::resource('video' , 'VideoController');
            Route::resource('post' , 'PostController');
            Route::get('audit/flow' , 'PostAuditFlowController@index')->name('audit.flow');
            Route::post('audit/flow' , 'PostAuditFlowController@store')->name('audit.store');
            Route::get('audit/{uid?}/{status?}' , 'PostAuditController@index')->name('audit.index');
            Route::post('audit' , 'PostAuditController@store')->name('audit.store');
            Route::resource('banner' , 'BannerController');
            Route::resource('event' , 'EventController');
            Route::get('banner/{banner}/image' , 'BannerController@image')->name('banner.image');
            Route::get('event/{event}/image' , 'EventController@image')->name('event.image');
            Route::resource('chat' , 'RyChatController');
            Route::resource('postComment' , 'PostCommentController');
            Route::get('post/{post}/comment' , 'PostController@comment')->name('post.comment');
            Route::get('post/{post}/translation' , 'PostController@translation')->name('post.translation');
            Route::post('post/{post}/translation/{translation}' , 'PostController@translationUpdate')->name('post.translation.update');
            Route::post('post/batch' , 'PostController@batch')->name('post.batch');
            Route::get('post/{post}/image' , 'PostController@image')->name('post.image');
            Route::resource('topic', 'TopicController');

            //Route::resource('category' , 'CategoryController');
            //Route::resource('tag' , 'TagController');
            Route::get('clear/cache' , 'PostController@clearCache')->name('clear.cache');
        });

        Route::group(['namespace'=>'Passport','prefix'=>'passport' , 'as' => 'passport::'] , function (){
            Route::get('user/{userId}/fan' , 'UserController@fan')->name('user.fan');

            Route::get('user/suspend' , 'UserController@suspend')->name('user.suspend');
            Route::get('user/keep' , 'UserController@keep')->name('user.keep');

            Route::get('user/yesterday' , 'UserController@yesterday')->name('user.yesterday');


            Route::get('user/dau' , 'UserController@dau')->name('user.dau');
            Route::get('user/export' , 'UserController@export')->name('user.export');
            Route::get('user/difference/keep' , 'UserController@differenceKeep')->name('user.difference.keep');
            Route::get('user/action/statistics' , 'UserController@actionStatistics')->name('user.action.statistics');
            Route::get('user/action/dailynumber' , 'UserController@dailyNumber')->name('user.action.daily.number');
            Route::get('user/action/duration' , 'UserController@duration')->name('user.action.duration');
            Route::resource('user' , 'UserController');
//            Route::group(['prefix'=>'user' , 'as' => 'user.'] , function (){
//                Route::get('video_view/{user}' , 'UserController@videoView')->name('video_view');
//            });
            Route::post('user/{userId}/fan' , 'UserController@follow')->name('user.follow');
            Route::put('user/{userId}/cancelled' , 'UserController@cancelled')->name('user.cancelled');
            Route::put('user/{userId}/block' , 'UserController@block')->name('user.block');
            Route::put('user/{userId}/unblock' , 'UserController@unblock')->name('user.unblock');

            // 群组管理
            Route::get('group/index', 'GroupController@index')->name('group.index'); // 群组列表管理
            Route::put('group/{groupId}/{status}', 'GroupController@update')->name('group.update'); // 群组列表管理

        });

        // 邀请码
        Route::group(['namespace'=>'Invitation','prefix'=>'invitation' , 'as' => 'invitation::'] , function (){
            /*Route::get('activity' , 'ActivityController@index')->name('activity.index'); // 活动列表
            Route::get('activity/edit' , 'ActivityController@edit')->name('activity.edit'); // 活动列表
            Route::get('activity/update' , 'ActivityController@update')->name('activity.update'); // 活动列表
            Route::get('activity/create' , 'ActivityController@create')->name('activity.create'); // 活动列表
            Route::get('activity/store' , 'ActivityController@store')->name('activity.store'); // 活动列表*/

            Route::resource('activity' , 'ActivityController'); // 活动列表
            Route::resource('order' , 'OrderController'); // 订单列表
            Route::resource('good' , 'GoodController'); // 商品列表
            Route::get('score/invite/{userId}' , 'ScoreController@invite')->name('score.invite'); // 邀请明细
            Route::get('score/score/{userId}' , 'ScoreController@score')->name('score.score'); // 积分明细
            Route::get('score' , 'ScoreController@index')->name('score.index'); // 积分列表

        });
        Route::group(['namespace'=>'Lovbee','prefix'=>'lovbee' , 'as' => 'lovbee::'] , function (){
            Route::get('message/operation' , 'MessageController@operation')->name('message.operation'); // 消息管理
            Route::post('message/operation' , 'MessageController@submit')->name('message.submit'); // 消息管理
            Route::get('message/play' , 'MessageController@play')->name('message.play'); // 消息管理
            Route::get('message/video' , 'MessageController@video')->name('message.video'); // 消息管理
            Route::post('message/comment' , 'MessageController@comment')->name('message.comment'); // 消息管理
            Route::get('message/export' , 'MessageController@export')->name('message.export'); // 消息管理
        });

        Route::group(['namespace'=>'Report','prefix'=>'report' , 'as' => 'report::'] , function (){
            Route::get('report' , 'ReportController@index')->name('report.index');
            Route::get('report/user' , 'ReportController@user')->name('report.user');
            Route::get('report/history' , 'ReportController@history')->name('report.history');
            Route::get('report/reportUser' , 'ReportController@reportUser')->name('report.reportUser');
            Route::get('report/reportPost' , 'ReportController@reportPost')->name('report.reportPost');
            Route::get('report/post/flow' , 'ReportController@reportPostFlow')->name('report.post.flow');
            Route::post('report' , 'ReportController@store')->name('report.store');
            Route::resource('feedback' , 'FeedbackController' , ['only' => ['index' , 'update']]);
        });
        Route::resource('menu' , 'MenuController');
        Route::resource('translation' , 'TranslationController');
        Route::resource('config' , 'ConfigController');
        Route::get('logs', 'LogViewerController@index');
        Route::resource('app', 'AppController');


    });
    Route::get('passport/user/yesterday/view' , 'Passport\UserController@yesterdayView')->name('user.yesterday.view');
});


