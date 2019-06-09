<?php


namespace app\api\controller;


class Article extends Common
{
    public function add_article()
    {
        /*接受参数*/
        $data = $this->params;
        $data['article_ctime'] = time();
        /*发表文章*/
        $res = db('article')->insertGetId($data);
        if ($res) {
            $this->return_msg(200, '文章发表成功!', $res);
        } else {
            $this->return_msg(400, '文章发表失败!');
        }

    }

    public function article_list()
    {
        /*接收参数*/
        $data = $this->params;
        if (!isset($data['num'])) {
            $data['num'] = 10;
        }
        if (!isset($data['page'])) {
            $data['page'] = 1;
        }


        /*查询数据*/

        $where['article_uid'] = $data['user_id'];
        $where['article_isdel'] = 0;
        $count = db('article')->where($where)->count();
        $page_num = ceil($count / $data['num']);  /*文章总页数*/
        $field = 'article_id,article_ctime,article_title,article_content,user_nickname';
        $join = [['api_user u', 'u.user_id = a.article_uid']];
        $articles = db('article')
            ->alias('a')
            ->field($field)
            ->join($join)
            ->where($where)
            ->page($data['page'], $data['num'])
            ->select();
        if ($articles) {
            $return_data['page_num'] = $page_num;
            $return_data['article'] = $articles;
            $this->return_msg(200, '查询文章成功!', $return_data);
        } elseif (empty($articles)) {
            $this->return_msg(400, '暂无数据!');
        } else {
            $this->return_msg(400, '查询文章失败!');
        }

    }

    public function article_detail()
    {
        /*接收参数*/
        $data = $this->params;
        /*查询数据库*/
        $where = ['article_id' => $data['article_id']];
        $field = 'article_id,article_title,article_content,article_ctime,user_nickname';
        $join = [['api_user u', 'u.user_id=a.article_uid']];
        $res = db('article')
            ->alias('a')
            ->join($join)
            ->field($field)
            ->where($where)
            ->find();
        $res['article_content'] = htmlspecialchars_decode($res['article_content']);
        /*进行判断*/
        if ($res) {
            $this->return_msg(200, '查询成功！', $res);
        } else {
            $this->return_msg('400', '文章查询失败!');
        }
    }


    public function update_article()
    {
        $data = $this->params;
        $res = db('article')->where(['article_id' => $data['article_id']])->update($data);
        if ($res) {
            $this->return_msg(200, '修改文章成功!');
        }else{
            $this->return_msg(400,'文章修改失败!');
        }
    }


    public function delete_article(){
        $data = $this->params;
        $res = db('article')
            ->where(['article_id'=>$data['article_id']])
            ->setField(['article_isdel'=>1]);
        if($res){
            $this->return_msg(200,'删除文章成功!');
        }else{
            $this->return_msg(400,'删除文章失败!');
        }
    }

}