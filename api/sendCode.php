<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require './PHPMailer/Exception.php';
require './PHPMailer/PHPMailer.php';
require './PHPMailer/SMTP.php';


function sendCode($config, $address, $title, $content)
{
    $mail = new PHPMailer(true); // Passing `true` enables exceptions
    try {
        //服务器配置
        $mail->CharSet = "UTF-8"; //设定邮件编码
        $mail->SMTPDebug = 0; // 调试模式输出
        $mail->isSMTP(); // 使用SMTP
        $mail->Host = $config['smtp']['smtp_host']; // SMTP服务器
        $mail->SMTPAuth = true; // 允许 SMTP 认证
        $mail->Username = $config['smtp']['smtp_username']; // SMTP 用户名 即邮箱的用户名
        $mail->Password = $config['smtp']['smtp_password']; // SMTP 密码 部分邮箱是授权码(例如163邮箱)
        $mail->SMTPSecure = $config['smtp']['smtp_secure']; // 允许 TLS 或者ssl协议
        $mail->Port = $config['smtp']['smtp_port']; // 服务器端口 25 或者465 具体要看邮箱服务器支持
        $mail->setFrom($config['smtp']['sendFrom'], $config['smtp']['sendFromName']); //发件人
        $mail->addAddress($address, '用户'); // 收件人
        //$mail->addAddress('ellen@example.com'); // 可添加多个收件人
        $mail->addReplyTo($config['smtp']['sendFrom'], $config['smtp']['sendFromName']); //回复的时候回复给哪个邮箱 建议和发件人一致
        //$mail->addCC('cc@example.com'); //抄送
        //$mail->addBCC('bcc@example.com'); //密送

        //发送附件
        // $mail->addAttachment('../xy.zip'); // 添加附件
        // $mail->addAttachment('../thumb-1.jpg', 'new.jpg'); // 发送附件并且重命名

        //Content
        $mail->isHTML(true); // 是否以HTML文档格式发送  发送后客户端可直接显示对应HTML内容
        $mail->Subject = $title; // 主体
        $mail->Body = $content; // 内容(HTML)
        $mail->AltBody = $content; // 如果邮件客户端不支持HTML则显示此内容
        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}
