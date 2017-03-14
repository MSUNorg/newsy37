<?php

namespace Think\Pay\Driver;

class Heepay extends \Think\Pay\Pay {
    #网页支付地址
    protected $gateway     = 'https://pay.Heepay.com/Payment/Index.aspx';
    #SDK支付地址
    protected $gateway_sdk = 'https://pay.heepay.com/Phone/SDK/PayInit.aspx';

    protected $config = array(
        'partner' => '',
        'key'=>'',
        'email'=>'',
    );

    public function check() {
        if (!$this->config['partner']) {
            E("汇付宝设置有误！");
        }
        return true;
    }

    public function buildRequestForm(\Think\Pay\PayVo $vo) {
        $param = array(
            'version' => 1,
            'agent_id'        => $this->config['partner'],
            'agent_bill_id'   => $vo->getOrderNo(),
            'agent_bill_time' => date('YmdHis', time()),
            'pay_type'        => $vo->getWay(),
            'pay_amt'         => $vo->getFee(),
            'notify_url'      => $this->config['notify_url'],
            'return_url'      => $this->config['return_url'],
            'goods_name'      => $vo->getTitle(),
            'goods_num'       => 1,
            'goods_note'      => $vo->getBody(),
            'remark'          => $vo->getBody(),
        );
        $param['user_ip'] = get_client_ip(); //$this->createSign($param);
        $param['sign'] = $this->createSign($param);
        if($vo->getWay() == 30){
            if($vo->getpaWay == 1){
                $param['is_phone'] = 1;
                $param['is_frame'] = 0;
            }
            if($vo->getpaWay == 2){
                $param['is_phone'] = 1;
                $param['is_frame'] = 0;
            }
        }
        $url = $vo->getpayType ? $this->gateway_sdk : $this->gateway;
        $sHtml = $this->_buildForm($param,$url);
        return $sHtml;
    }

    protected function createSign($params) {
        $arg = '';
        $arr = array('goods_name','goods_num','goods_note','remark','is_phone','is_frame','sign');
        foreach ($params as $key => $value) {
            if ($value != "" && !in_array($key,$arr)) {
                $arg .= "{$key}={$value}&";
            }
        }
        return md5($arg . 'key=' . $this->config['key']);
    }

    protected function getSign($params) {

        $arg = '';
        foreach ($params as $key => $value) {
            if ($value != "") {
                $arg .= "{$key}={$value}&";
            }
        }
        return strtolower(md5($arg . 'key=' . $this->config['key']));
    }

    public function verifyNotify($notify) {
        $param = array(
            'result'        => $notify['result'],
            'pay_message'   => $notify['pay_message'],
            'agent_id'      => $notify['agent_id'],
            'jnet_bill_no'  => $notify['jnet_bill_no'],
            'agent_bill_id' => $notify['agent_bill_id'],
            'pay_type'      => $notify['pay_type'],
            'pay_amt'       => $notify['pay_amt'],
            'remark'        => $notify['remark'],
        );
        $mySign = $this->getSign($param);
        $return_sign = $notify['sign'];
        if($mySign==$return_sign){   //比较签名密钥结果是否一致，一致则保证了数据的一致性
            return true;
        }
        else{
            return false;
        }
    }

}
