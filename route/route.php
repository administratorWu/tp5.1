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


Route::group(['name'=>'/admin/user','prefix'=>'admin/UserController/'],function(){
    Route::rule('index','index');
    Route::rule('adduser','adduser');
    Route::rule('amenduser','amenduser');
});

Route::group(['name'=>'/admin/redis','prefix'=>'admin/RedisController/'],function(){
    Route::rule('test','test');
});


Route::get('think', function () {
    return 'hello,ThinkPHP5!';
});

Route::get('hello/:name', 'index/hello');

return [

];
