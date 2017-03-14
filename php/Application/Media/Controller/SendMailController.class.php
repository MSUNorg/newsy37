<?php
namespace Media\Controller;
use Think\Controller;

/**
* 首页
*/
class SendMailController extends BaseController {
	protected function _initialize(){
        /* 读取站点配置 */
        $config = api('WebConfig/lists');
        C($config); //添加配置
    }

    function sendMail($to,$toname,$title,$username,$link) {
		Vendor('PHPMailer.PHPMailerAutoload');     
		$mail = new \PHPMailer(); //实例化
		$mail->IsSMTP(); // 启用SMTP
		$mail->Host=C('E_SERVER'); //smtp服务器的名称（这里以126邮箱为例：smtp.126.com）
		$mail->SMTPAuth = TRUE;//C('MAIL_SMTPAUTH'); //启用smtp认证
		$mail->Username = C('E_EMAIL'); //你的邮箱名
		$mail->Password = C('E_PASSWORD') ; //邮箱密码
		$mail->From = C('E_ADDRESS'); //发件人地址（也就是你的邮箱地址）
		$mail->FromName = C('E_SENDER'); //发件人姓名
		$mail->AddAddress($to,$toname);
		$mail->WordWrap = 50; //设置每行字符长度
		$mail->IsHTML(TRUE); // 是否HTML格式邮件
		$mail->CharSet='utf-8'; //设置邮件编码
		$mail->Subject =C('E_TITLE'); //邮件主题
		$content = C('E_CONTENT');
		$content = str_replace('#username#',$username,$content);
		$content = str_replace('#link#',$link,$content);
		$mail->Body = $content; //邮件内容
		$mail->AltBody = strip_tags($content); //邮件正文不支持HTML的备用显示
		return($mail->Send());
	}
    
    /* function sendMail($to,$toname,$title,$username,$link) {
		Vendor('PHPMailer.PHPMailerAutoload');     
		$mail = new \PHPMailer(); //实例化
		$mail->IsSMTP(); // 启用SMTP
		$mail->Host=C('E_SERVER'); //smtp服务器的名称（这里以126邮箱为例：smtp.126.com）
		$mail->SMTPAuth = TRUE;//C('MAIL_SMTPAUTH'); //启用smtp认证
		$mail->Username = C('E_EMAIL'); //你的邮箱名
		$mail->Password = C('E_PASSWORD') ; //邮箱密码
		$mail->From = C('E_ADDRESS'); //发件人地址（也就是你的邮箱地址）
		$mail->FromName = C('E_SENDER'); //发件人姓名
		$mail->AddAddress($to,$toname);
		$mail->WordWrap = 50; //设置每行字符长度
		$mail->IsHTML(TRUE); // 是否HTML格式邮件
		$mail->CharSet='utf-8'; //设置邮件编码
		$mail->Subject =C('E_TITLE'); //邮件主题
		$content = '<p>亲爱的#username#，您好</p><p>您正在进行密保邮箱验证服务，请点击下面链接完成邮箱验证：</p><p></p><p></p><p></p><p></p><p></p>';
		$content = str_replace('#username#',$username,$content);
		$content = str_replace('#link#',$link,$content);
		$mail->Body = $content; //邮件内容
		$mail->AltBody = strip_tags($content); //邮件正文不支持HTML的备用显示
		return($mail->Send());
	} */
}