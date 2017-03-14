<?php
namespace Sdk\Event;
use Think\Controller;
class AliPayEvent extends Controller {

  /**
  *验签排序
  */
  public function argSort($para) {
    ksort($para);
    reset($para);
    return $para;
  }

  //RSA签名
  public function sign($data) {
    //读取私钥文件
    $priKey = file_get_contents(dirname(dirname(__FILE__))."/SecretKey/alipay/rsa_private_key.pem");//私钥文件路径
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
    $pubKey = file_get_contents(dirname(dirname(__FILE__))."/SecretKey/alipay/rsa_public_key.pem");//私钥文件路径
    // 转换为openssl格式密钥
    $res = openssl_get_publickey($pubKey);
    // 调用openssl内置方法验签，返回bool值
    $result = ( bool ) openssl_verify ( $data, base64_decode ( $sign ), $res );
    // 释放资源
    openssl_free_key ( $res );   
    return $result;
  }

}