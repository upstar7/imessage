<?php

namespace imessage\controllers\api;

use Yii;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
class SmtpController extends BaseController
{
    public function actionCreate(){
        $mail = new PHPMailer(true);
        $request = Yii::$app->request;
        $host = $request->post('host');
        $port = $request->post('port');
        $username = $request->post('username');
        $password = $request->post('password');
        $from_name = $request->post('from_name','');
        $address =  $request->post('address');
        $address_name = $request->post('address_name','');
        $body = $request->post('body');
        $subject =$request->post('subject');
        $charset = $request->post('charset','UTF-8');
        $secure = $request->post('secure','tls');
        $encoding = $request->post('encoding','quoted-printable');
        $options = $request->post('options',[
            'ssl' => [
                'verify_peer' => true,
                'verify_peer_name' => true,
                'allow_self_signed' => true, // 设置为true来允许信任自签名证书
            ]
        ]);


        $mail->isSMTP();
        $mail->Host = $host; // 设置 SMTP 服务器地址
        $mail->SMTPAuth = true;
        $mail->Username = $username; // 发件人邮箱地址
        $mail->Password = $password; // 发件人邮箱密码或授权码
        $mail->SMTPSecure =$secure; // 启用 TLS 加密，可以是 'tls' 或 'ssl'
        $mail->Port = $port; // SMTP 端口号，一般为 587
        $mail->SMTPOptions = $options;
        // 设置发件人和收件人
        $mail->setFrom($username, $from_name); // 设置发件人姓名（可选）
        $mail->addAddress($address,$address_name); // 设置收件人邮箱地址

        // 设置邮件主题和内容
        $mail->Subject = $subject;
        $mail->Body = $body;
        $mail->CharSet =  $charset;

        // 添加邮件头信息，明确指定邮件的字符编码为 UTF-8
        $mail->Encoding = $encoding;
        $mail->addCustomHeader('Content-Type: text/plain; charset='.$charset);
        $mail->addCustomHeader('Content-Transfer-Encoding: '.$encoding);

        // 发送邮件
        $mail->send();
    }
}