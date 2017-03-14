<?php

namespace Think\Pay\Driver;

class Tenpay extends \Think\Pay\Pay {

    protected $gateway = 'https://gw.tenpay.com/gateway/pay.htm';
    protected $verify_url = 'https://gw.tenpay.com/gateway/simpleverifynotifyid.xml';
    protected $config = array(
        'key' => '',
        'partner' => ''
    );

    public function check() {
        if (!$this->config['key'] || !$this->config['partner']) {
            E("财付通设置有误！");
        }
        return true;
    }

    public function buildRequestForm(\Think\Pay\PayVo $vo) {
        $param = array(
            'input_charset' => "UTF-8",
            'body' => $vo->getBody(),
            'subject' => $vo->getTitle(),
            'return_url' => $this->config['return_url'],
            'notify_url' => $this->config['notify_url'],
            'partner' => $this->config['partner'],
            'out_trade_no' => $vo->getOrderNo(),
            'total_fee' => $vo->getFee() * 100,
            'spbill_create_ip' => get_client_ip()
        );

        $param['sign'] = $this->createSign($param);

        $sHtml = $this->_buildForm($param, $this->gateway);

        return $sHtml;
    }

    /**
     * 获取返回时的签名验证结果
     * @param $para_temp 通知返回来的参数数组
     * @param $sign 返回的签名结果
     * @return 签名验证结果
     */
    protected function getSignVeryfy($param, $sign) {
        //除去待签名参数数组中的空值和签名参数
        $param_filter = array();
        while (list ($key, $val) = each($param)) {
            if ($key == "sign" || $val == "") {
                continue;
            } else {
                $param_filter[$key] = $param[$key];
            }
        }

        $mysgin = $this->createSign($param_filter);

        if ($mysgin == $sign) {
            return true;
        } else {
            return false;
        }
    }

    public function verifyNotify($notify) {

        //生成签名结果
        $isSign = $this->getSignVeryfy($notify, $notify["sign"]);
        $response = true;
        if (!empty($notify["notify_id"])) {
            $response = $this->getResponse($notify["notify_id"]);
        }
        if ($response && $isSign) {
            $this->setInfo($notify);
            return true;
        } else {
            return false;
        }
    }

    /**
     * 创建签名
     * @param type $params
     */
    protected function createSign($params) {

        ksort($params);
        reset($params);

        $arg = '';
        foreach ($params as $key => $value) {
            $arg .= "{$key}={$value}&";
        }
        return strtoupper(md5($arg . 'key=' . $this->config['key']));
    }

    protected function setInfo($notify) {
        $info = array();
        //支付状态
        $info['status'] = $notify['trade_state'] == 0 ? true : false;
        $info['money'] = $notify['total_fee'] / 100;
        $info['out_trade_no'] = $notify['out_trade_no'];
        $this->info = $info;
    }

    /**
     * 获取远程服务器ATN结果,验证返回URL
     * @param $notify_id 通知校验ID
     * @return 服务器ATN结果
     * 验证结果集：
     * invalid命令参数不对 出现这个错误，请检测返回处理中partner和key是否为空 
     * true 返回正确信息
     * false 请检查防火墙或者是服务器阻止端口问题以及验证时间是否超过一分钟
     */
    protected function getResponse($notify_id) {
        $partner = $this->config['partner'];
        $params = array(
            'input_charset' => 'UTF-8',
            'partner' => $partner,
            'notify_id' => $notify_id
        );
        $sign = $this->createSign($params);
        $veryfy_url = $this->verify_url . "?input_charset=UTF-8&sign={$sign}&partner=" . $partner . "&notify_id=" . $notify_id;
        $responseTxt = $this->fsockOpen($veryfy_url);

        $responseTxt = simplexml_load_string($responseTxt);
        return (int) $responseTxt->retcode == 0;
    }

}
