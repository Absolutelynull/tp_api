<?php

namespace app\api\controller;

class User extends Common
{
    /**
     * 用户登陆
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function login()
    {
//        echo 'login';
        /*接受参数*/
        $data = $this->params;
        /*检测用户名*/
        $user_name_type = $this->check_username($data['user_name']);
        /*是否存在*/
        $db_res = [];
        switch ($user_name_type) {
            case 'phone':
                $this->check_exist($data['user_name'], 'phone', 1);
                $db_res = db('user')
                    ->where('user_phone', $data['user_name'])
                    ->find();
                break;
            case 'email':
                $this->check_exist($data['user_name'], 'email', 1);
                $db_res = db('user')
                    ->where('user_email', $data['user_name'])
                    ->find();
                break;
        }

        if ($db_res['user_pwd'] !== $data['user_pwd']) {
            $this->return_msg(400, '用户名或密码不正确!');
        } else {
            unset($db_res['user_pwd']);/*密码不返回*/
            $this->return_msg(200, '登陆成功!', $db_res);
        }
    }

    /**
     * 用户注册
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function register()
    {
        /*接收参数*/
        $data = $this->params;

        /*检查验证码*/
        $this->check_code($data['user_name'], $data['code']);
        /*检测用户名*/
        $user_name_type = $this->check_username($data['user_name']);

        switch ($user_name_type) {
            case 'phone':
                $this->check_exist($data['user_name'], 'phone', 0);
                $data['user_phone'] = $data['user_name'];
                break;
            case 'email':
                $this->check_exist($data['user_name'], 'email', 0);
                $data['user_email'] = $data['user_name'];
                break;
        }
        /*将用户信息写入数据库*/
        unset($data['user_name']);
        $data['user_retime'] = time();
        $res = db('user')->insert($data);
        if ($res) {
            $this->return_msg(200, '用户注册成功!');
        } else {
            $this->return_msg(400, '用户注册失败');
        }


    }

    /**
     * 上传头像
     */
    public function upload_head_img()
    {
        /*接受参数*/
        $data = $this->params;
        /*上传图片  获取路径*/
        $head_img_path = $this->upload_file($data['user_icon'], 'head_img');

        /*存入数据库*/
        $res = db('user')->where('user_id', $data['user_id'])->setField('user_icon', $head_img_path);
        dump($res);
        if ($res) {
            $this->return_msg(200, '文件上传成功!', $head_img_path);
        } else {
            $this->return_msg(400, '文件上传失败!');
        }
    }

    /**
     * 用户修改密码
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function chang_pwd()
    {
        /*接受参数*/
        $data = $this->params;
        /*检查用户名并取出数据库中的密码*/
        $user_name_type = $this->check_username($data['user_name']);
        $where = [];
        switch ($user_name_type) {
            case 'phone':
                $this->check_exist($data['user_name'], 'phone', 1);
                $where['user_phone'] = $data['user_name'];
                break;
            case 'email':
                $this->check_exist($data['user_name'], 'email', 1);
                $where['user_email'] = $data['user_name'];
                break;
        }
        /*判断原密码是否正确*/
        $db_ini_pwd = db('user')->where($where)->value('user_pwd');
        if ($db_ini_pwd !== $data['user_ini_pwd']) {
            $this->return_msg(400, '原密码错误!');
        }

        /*更新新密码*/
        $res = db('user')
            ->where($where)
            ->setField('user_pwd', $data['user_pwd']);

        if ($res === 1) {
            $this->return_msg(200, '密码修改成功!');
        } else {
            $this->return_msg(400, '密码修改失败!');
        }


    }

    /**
     * 用户找回密码
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function find_pwd()
    {
        /*获取参数*/
        $data = $this->params;
        /*检测验证码*/
        $this->check_code($data['user_name'], $data['code']);
        /*检测用户名*/
        $user_name_type = $this->check_username($data['user_name']);
        $where = [];
        switch ($user_name_type) {
            case 'phone':
                $this->check_exist($data['user_name'], 'phone', 1);
                $where['user_phone'] = $data['user_name'];
                break;
            case 'email':
                $this->check_exist($data['user_name'], 'email', 1);
                $where['user_email'] = $data['user_name'];
                break;
        }
        /*修改密码*/
        $res = db('user')->where($where)->setField('user_pwd', $data['user_pwd']);
        if ($res === 1) {
            $this->return_msg(200, '找回密码成功!');
        } else {
            $this->return_msg(400, '找回密码失败');
        }
    }


    /**
     * 绑定email
     */
    public function bind_email()
    {
        /*接收参数*/
        $data = $this->params;
        /*验证 验证码*/
        $this->check_code($data['email'],$data['code']);
        /*绑定email*/
        $res = db('user')
            ->where('user_id',$data['user_id'])
            ->setField('user_email',$data['email']);
        if($res){
            $this->return_msg(400,'绑定邮箱成功!');
        }else{
            $this->return_msg(400,'绑定邮箱失败!');
        }
    }

    /**
     * 绑定手机号码
     */
    public function bind_phone()
    {
        /*接受参数*/
        $data = $this->params;
        /*检查验证码*/
        $this->check_code($data['phone'], $data['code']);
        /*更新手机号码*/
        $res = db('user')
            ->where('user_id', $data['user_id'])
            ->setField('user_phone',$data['phone']);
        if($res === 1){
            $this->return_msg(200,'绑定手机号成功!');
        }else{
            $this->return_msg(400,'绑定手机号失败!');
        }
    }


    /**
     * 修改昵称
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function nickname(){
        /*接受参数*/
        $data = $this->params;

        /*判断nickname是否存在*/
        $nick = db('user')->where(['user_nickname'=>$data['user_nickname']])->find();

        if($nick){
            $this->return_msg(400,'昵称已存在!');
        }

        /*修改nickname*/
        $res = db('user')
            ->where(['user_id'=>$data['user_id']])
            ->setField(['user_nickname'=>$data['user_nickname']]);
        if($res === 1){
            $this->return_msg(200,'修改昵称成功!');
        }else{

            $this->return_msg(400,'修改昵称失败!');
        }
    }

}