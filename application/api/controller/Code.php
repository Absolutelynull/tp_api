<?php


namespace app\api\controller;

use phpmailer\Phpmailer;



class Code extends Common
{
    public function get_code(){
        $username = $this->params['username'];
        $exist = $this->params['is_exist'];
        $username_type = $this->check_username($username);

        switch ($username_type){
            case 'phone':
                $this->get_code_by_username($username,'phone',$exist);
                break;
            case 'email':
                $this->get_code_by_username($username,'email',$exist);
                break;
//            default:
//                break;
        }
    }


    /**
     * 获取手机/邮箱获取验证码
     * @param $username    手机号/邮箱
     * @param $exist    手机号/邮箱 是否应该在数据库  1:是  0：否
     *
     * 返回json数据
     */
    public function get_code_by_username($username,$type,$exist)
    {
        $type_name = $type == 'phone' ? '手机' : '邮箱';

        /*检测手机号是否存在*/
        $this->check_exist($username, $type, $exist);
        /*检查验证码请求频繁 30秒一次*/
        if (session('?' . $username . '_last_send_time')) {
            if (time() - session($username . '_last_send_time') < 30) {
                $this->return_msg(400, $type_name . '验证码，每30秒只能发送一次！');
            }
        }
        /*生成验证码*/
        $code = $this->make_code(6);
        /*使用session存储验证码 方便对比 md5 加密*/
        $md5_code = md5($username . '_' . md5($code));
        session($username . '_code', $md5_code);
        /*使用session存储验证码的发送时间*/
        session($username . '_last_send_time', time());
        /*发送验证码*/
        if ($type == 'phone') {
            dump($code);
//            $this->send_code_to_phone($username, $code);
        }else{

//            $this->send_code_to_email($username, $code);
            dump($code);

        }
    }

    /**
     * 生成验证码
     * @param int $num  验证码位数
     * @return int  验证码
     */
    public function make_code($num = 4){
        $max = pow(10,$num)-1;
        $min = pow(10,$num-1);
        return rand($min,$max);

    }

    public function send_code_to_phone($username,$code){
//        echo 'send_code_to_phone';



    }

    /**
     * 发送邮箱验证码
     * @param $email
     * @param $code
     */
    public function send_code_to_email($email,$code){
//        echo 'send_code_to_phone';
        $toemail = $email;
//        $mail = new PHPMailer();
        /*
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->CharSet = 'utf8';
        $mail->Host = 'smtp.163.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'a13265014032@qq.com';
        $mail->Password = 'aa112233';
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 994;
        $mail->setFrom('a13265014032@163.com','接口测试');
        $mail->addAddress($toemail,'test');
        $mail->addReplyTo('a13265014032@163.com','Reply');
        $mail->Subject = "您有新的验证码!";
        $mail->Body = "这是一个测试邮件，您的验证码为：$code,验证码的有效期为1分钟，本邮件请勿回复!";
*/
        $mail = new Phpmailer();

        $mail->isSMTP();// 使用SMTP服务
        $mail->CharSet = "utf8";// 编码格式为utf8，不设置编码的话，中文会出现乱码
        $mail->Host = "smtp.163.com";// 发送方的SMTP服务器地址
        $mail->SMTPAuth = true;// 是否使用身份验证
        $mail->Username = "a13265014032@163.com";// 发送方的163邮箱用户名，就是你申请163的SMTP服务使用的163邮箱</span><span style="color:#333333;">
        $mail->Password = "aa112233";// 发送方的邮箱密码，注意用163邮箱这里填写的是“客户端授权密码”而不是邮箱的登录密码！</span><span style="color:#333333;">
        $mail->SMTPSecure = "ssl";// 使用ssl协议方式</span><span style="color:#333333;">
        $mail->Port = 994;// 163邮箱的ssl协议方式端口号是465/994
        $mail->setFrom("a13265014032@163.com","Mailer");// 设置发件人信息，如邮件格式说明中的发件人，这里会显示为Mailer(xxxx@163.com），Mailer是当做名字显示
        $mail->addAddress($toemail,'Wang');// 设置收件人信息，如邮件格式说明中的收件人，这里会显示为Liang(yyyy@163.com)
        $mail->addReplyTo("a13265014032@163.com","Reply");// 设置回复人信息，指的是收件人收到邮件后，如果要回复，回复邮件将发送到的邮箱地址
        //$mail->addCC("xxx@163.com");// 设置邮件抄送人，可以只写地址，上述的设置也可以只写地址(这个人也能收到邮件)
        //$mail->addBCC("xxx@163.com");// 设置秘密抄送人(这个人也能收到邮件)
        //$mail->addAttachment("bug0.jpg");// 添加附件
        $mail->Subject = "这是一个测试邮件";// 邮件标题
        $mail->Body = "邮件内容是 <b>您的验证码是：123456</b>，哈哈哈！";// 邮件正文
        dump($mail->send());
//        if(!$mail->send()){
//            $this->return_msg(400,$mail->ErrorInfo);
//        }else{
//            $this->return_msg(200,'验证码已经发送成功,请注意查收!');
//        }

    }

}