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

//return [
//    '__pattern__' => [
//        'name' => '\w+',
//    ],
//    '[hello]'     => [
//        ':id'   => ['index/hello', ['method' => 'get'], ['id' => '\d+']],
//        ':name' => ['index/hello', ['method' => 'post']],
//    ],
//
//];

use think\Route;

//Route::post('index/:time/:token','index/index');
/*获取验证码*/
Route::get('code/:time/:token/:username/:is_exist', 'code/get_code');

/*用户登陆*/
Route::post('user/login', 'user/login');
/*用户注册*/
Route::post('user/register', 'user/register');
/*用户上传邮箱*/
Route::post('user/icon', 'user/upload_head_img');
/*用户修改密码*/
Route::post('user/chang_pwd', 'user/chang_pwd');
/*用户找回密码*/
Route::post('user/find_pwd', 'user/find_pwd');
/*绑定手机号*/
Route::post('user/bind_phone', 'user/bind_phone');
/*绑定邮箱*/
Route::post('user/bind_email', 'user/bind_email');
/*修改昵称*/
Route::post('user/nickname','user/nickname');

                    /*Articles*/
/*发表文章*/
Route::post('article','article/add_article');
/*文章列表显示*/
Route::get('articles/:time/:token/:user_id/[:num]/[:page]','article/article_list');
/*文章单个显示*/
Route::get('article/:time/:token/:article_id','article/article_detail');
/*删除文章*/
Route::delete('article/:time/:token/:article_id','article/delete_article');
/*修改文章*/
Route::put('article','article/update_article');




