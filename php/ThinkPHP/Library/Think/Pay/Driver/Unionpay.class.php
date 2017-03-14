<?php

namespace Think\Pay\Driver;

class Unionpay extends \Think\Pay\Pay {

    protected $gateway = 'https://unionpaysecure.com/api/Pay.action';
    protected $config = array(
        'key' => '',
        'partner' => ''
    );

    public function check() {
        if (!$this->config['key'] || !$this->config['partner']) {
            E("银联支付设置有误！");
        }
        return true;
    }

    public function buildRequestForm(\Think\Pay\PayVo $vo) {
        $param = array(
            'version' => '1.0.0',
            'charset' => 'UTF-8',
            'merId' => $this->config['partner'],
            'transType' => "01",
            'orderAmount' => $vo->getFee() * 100,
            'orderNumber' => $vo->getOrderNo(),
            'orderTime' => date('YmdHis'),
            'orderCurrency' => "156",
            'customerIp' => get_client_ip(),
            'frontEndUrl' => $this->config['return_url'],
            'backEndUrl' => $this->config['notify_url'],
            'merAbbr' => $vo->getTitle(),
            'merReserved' => ''
        );


        $param['signature'] = $this->createSign($param);
        $param['signMethod'] = "md5";

        $sHtml = $this->_buildForm($param, $this->gateway);

        return $sHtml;
    }

    /**
     * 创建签名
     * @param type $params
     */
    protected function createSign($params) {
        ksort($params);
        $sign_str = "";
        foreach ($params as $key => $val) {
            $sign_str .= sprintf("%s=%s&", $key, $val);
        }
        return md5($sign_str . md5($this->config['key']));
    }

    public function verifyNotify($notify) {

        //提取服务器端的签名
        if (!isset($notify['signature']) || !isset($notify['signMethod'])) {
            return false;
        }
        $sign = $notify['signature'];
        unset($notify['signature']);
        unset($notify['signMethod']);

        //验证签名
        $mysign = $this->createSign($notify);
        if ($sign != $mysign) {
            return false;
        } else {
            $info = array();
            //支付状态
            $info['status'] = $notify['respCode'] == '00' ? true : false;
            $info['money'] = $notify['orderAmount'] / 100;
            $info['out_trade_no'] = $notify['orderNumber'];
            $this->info = $info;
            return true;
        }
    }

}
