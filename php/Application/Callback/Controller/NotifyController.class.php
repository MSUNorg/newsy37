<?php

namespace Callback\Controller;

/**
 * 支付回调控制器
 * @author 小纯洁 
 */
class NotifyController extends BaseController {
    /**
    *通知方法
    */
    public function notify($value='')
    {
        $apitype = I('get.apitype');#获取支付api类型
        if (IS_POST && !empty($_POST)) {
            $notify = $_POST;
        } elseif (IS_GET && !empty($_GET)) {
            $notify = $_GET;
            unset($notify['method']);
            unset($notify['apitype']);
        } else {
            $this->record_logs("Access Denied");
            exit('Access Denied');
        }
        $pay = new \Think\Pay($apitype, C($apitype));
        if ($pay->verifyNotify($notify)) {
            //获取回调订单信息
            $order_info = $pay->getInfo();
            if ($order_info['status']) {
                $pay_where = substr($order_info['out_trade_no'],0,2);
                $result = false;
                switch ($pay_where) {
                    case 'SP':
                        $result = $this->set_spend($order_info);
                        break;
                    case 'PF':
                        $result = $this->set_deposit($order_info);
                        break;
                    case 'AG':
                        $result = $this->set_agent($order_info); 
                        break;
                    default:
                        exit('accident order data');
                        break;
                }
                if (I('get.method') == "return") {
                    redirect('http://'.$_SERVER['HTTP_HOST'].'/media.php');
                } else {
                    // $pay->notifySuccess();
                    return "success";
                }
            }else{
                //$this->error("支付失败！");
                $this->record_logs("支付失败！");
            }
        }else{
            $this->record_logs("支付宝验证失败");
            redirect('http://'.$_SERVER['HTTP_HOST'].'/media.php',3,'支付宝验证失败');
        }
    }

    /**
    *
    */
    public function mobile_pay_notify(){
        //$this->wite_text(json_encode($_POST),dirname(__FILE__)."/notify.txt");
        if (IS_POST && !empty($_POST)) {
            $notify = $_POST;
        } elseif (IS_GET && !empty($_GET)) {
            $notify = $_GET;
            unset($notify['method']);
            unset($notify['apitype']);
        } else {
            $this->record_logs("Access Denied");
            exit('Access Denied');
        }
        
        //if ($this->verifyNotify($notify,$notify['sign'])) {
            //获取回调订单信息
        if ($_POST['trade_status'] == "TRADE_SUCCESS") {
            $order_info = $notify['out_trade_no'];
            if (true) {
                $pay_where = substr($order_info,0,2);
                $result = false;
                switch ($pay_where) {
                    case 'SP':
                        $result = $this->set_spend($notify);
                        break;
                    case 'PF':
                        $result = $this->set_deposit($notify);
                        break;
                    case 'AG':
                        $result = $this->set_agent($notify); 
                        break;
                    default:
                        exit('accident order data');
                        break;
                }
                if($result){
                    return "success";
                }
            }else{
                $this->record_logs("支付失败！");
            }
        }
        // }else{
        //     $this->record_logs("支付宝验证失败");
        // }
    }

    /**
    *微信支付
    */
    public function weixi_notify(){
        $request = file_get_contents("php://input");
        $apitype = "swiftpass";
        $pay = new \Think\Pay($apitype, C("weixin"));
        if ($pay->verifyNotify($request)) {
            //获取回调订单信息
            $order_info = $pay->getInfo();
            if ($order_info['status']) {
                $pay_where = substr($order_info['out_trade_no'],0,2);
                $result = false;
                switch ($pay_where) {
                    case 'SP':
                        $result = $this->set_spend($order_info);
                        break;
                    case 'PF':
                        $result = $this->set_deposit($order_info);
                        break;
                    case 'AG':
                        $result = $this->set_agent($order_info); 
                        break;
                    default:
                        exit('accident order data');
                        break;
                }
                if($result){
                    $pay->notifySuccess();
                }
            }else{
                //$this->error("支付失败！");
                $this->record_logs("支付失败！");
            }
        }else{
            $this->record_logs("微信验证失败");
        }
    }

    private function verifyNotify($param,$sign){
        //除去待签名参数数组中的空值和签名参数
        $param_filter = array();
        while (list ($key, $val) = each($param)) {
            if ($key == "sign" || $key == "sign_type" || $val == "") {
                continue;
            } else {
                $param_filter[$key] = $param[$key];
            }
        }

        ksort($param_filter);
        reset($param_filter);

        //把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
        $prestr = "";
        while (list ($key, $val) = each($param_filter)) {
            $prestr.= $key."=".$val."&"; //"\"".$val."\""
        }
        //去掉最后一个&字符
        $prestr = substr($prestr, 0, -1); 
        //如果存在转义字符，那么去掉转义
        if(get_magic_quotes_gpc()){$prestr = stripslashes($prestr);}
       
        $result = $this->rsa_verify($prestr,$sign); 
        return $result;
    }

    //RSA签名
    public function sign($data) {
        //读取私钥文件
        $priKey = file_get_contents("./Application/Sdk/SecretKey/alipay/rsa_private_key.pem");//私钥文件路径
        //转换为openssl密钥，必须是没有经过pkcs8转换的私钥
        $res = openssl_get_privatekey($priKey);
        //$res = openssl_pkey_get_private($priKey);
        //调用openssl内置签名方法，生成签名$sign
        openssl_sign($data, $sign, $res);
        //释放资源
        openssl_free_key($res);
        //base64编码
        $sign = base64_encode($sign);
        return $sign;
    }

    //验签
    public function rsa_verify($data, $sign) {
        // 读取公钥文件
        $pubKey = file_get_contents("./Application/Sdk/SecretKey/alipay/rsa_public_key.pem");//私钥文件路径alipay_public_key.pem
        // 转换为openssl格式密钥
        $res = openssl_get_publickey($pubKey);
        // 调用openssl内置方法验签，返回bool值
        $result = ( bool ) openssl_verify ( $data, base64_decode ( $sign ), $res );
        // 释放资源
        openssl_free_key ( $res );   
        return $result;
    }

    /**
    * RSA解密
    * @param $content 需要解密的内容，密文
    * @param $private_key_path 商户私钥文件路径
    * return 解密后内容，明文
    */
    public function rsaDecrypt($content) {
        $priKey = file_get_contents("./Application/Sdk/SecretKey/alipay/rsa_private_key.pem");
        $res = openssl_get_privatekey($priKey);
        //用base64将内容还原成二进制
        $content = base64_decode($content);
        //把需要解密的内容，按128位拆开解密
        $result  = '';
        for($i = 0; $i < strlen($content)/128; $i++  ) {
            $data = substr($content, $i * 128, 128);
            openssl_private_decrypt($data, $decrypt, $res);
            $result .= $decrypt;
        }
        openssl_free_key($res);
        return $result;
    }

    function wite_text($txt,$name){
        $myfile = fopen($name, "w") or die("Unable to open file!");
        fwrite($myfile, $txt);
        fclose($myfile);
    }
}