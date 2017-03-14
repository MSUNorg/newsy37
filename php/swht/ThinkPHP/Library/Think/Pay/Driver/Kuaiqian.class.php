<?php

namespace Think\Pay\Driver;

class Kuaiqian extends \Think\Pay\Pay {

    protected $gateway = 'https://www.99bill.com/gateway/recvMerchantInfoAction.htm';
    protected $config = array(
        'key' => '',
        'partner' => ''
    );

    public function check() {
        if (!$this->config['key'] || !$this->config['partner']) {
            E("快钱设置有误！");
        }
        return true;
    }

    public function buildRequestForm(\Think\Pay\PayVo $vo) {
        $param = array(
            'inputCharset' => '1',
            'pageUrl' => $this->config['return_url'],
            'bgUrl' => $this->config['notify_url'],
            'version' => 'v2.0',
            'language' => 1,
            'signType' => 1,
            'merchantAcctId' => $vo->config['partner'],
            'orderId' => $vo->getOrderNo(),
            'orderAmount' => $vo->getFee() * 100,
            'orderTime' => date("Ymdhis"),
            'productName' => $vo->getTitle(),
            'productDesc' => $vo->getBody(),
            'payType' => '00'
        );

        $param['signMsg'] = $this->createSign($param);

        $sHtml = $this->_buildForm($param, $this->gateway);

        return $sHtml;
    }

    protected function createSign($params) {
        $arg = '';
        foreach ($params as $key => $value) {
            if ($value != "") {
                $arg .= "{$key}={$value}&";
            }
        }
        return strtoupper(md5($arg . 'key=' . $this->config['key']));
    }

    public function verifyNotify($notify) {
        $param = array(
            'merchantAcctId' => $notify['merchantAcctId'],
            'version' => $notify['version'],
            'language' => $notify['language'],
            'signType' => $notify['signType'],
            'payType' => $notify['payType'],
            'bankId' => $notify['bankId'],
            'orderId' => $notify['orderId'],
            'orderTime' => $notify['orderTime'],
            'orderAmount' => $notify['orderAmount'],
            'dealId' => $notify['dealId'],
            'bankDealId' => $notify['bankDealId'],
            'dealTime' => $notify['dealTime'],
            'payAmount' => $notify['payAmount'],
            'fee' => $notify['fee'],
            'payResult' => $notify['payResult'],
            'errCode' => $notify['errCode']
        );

        if ($notify['signMsg'] == $this->createSign($param)) {
            $info = array();
            //支付状态
            $info['status'] = $notify['payResult'] == '10' ? true : false;
            $info['money'] = $notify['orderAmount'];
            $info['out_trade_no'] = $notify['orderId'];
            $this->info = $info;
            return true;
        } else {
            return false;
        }
    }

    public function notifySuccess() {
        echo "<result>1</result><redirecturl>{$this->config['return_url']}</redirecturl>";
    }

}
