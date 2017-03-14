<?php

namespace Think\Pay\Driver;

class Yeepay extends \Think\Pay\Pay {

    protected $gateway = 'https://www.yeepay.com/app-merchant-proxy/node';
    protected $config  = array(
        'key'     => '',
        'partner' => ''
    );

    public function check() {
        if (!$this->config['key'] || !$this->config['partner']) {
            E("易付宝设置有误！");
        }
        return true;
    }

    public function buildRequestForm(\Think\Pay\PayVo $vo) {
        $param = array(
            'p0_Cmd'          => 'Buy',
            'p1_MerId'        => $this->config['partner'],
            'p4_Cur'          => 'CNY',
            'p8_Url'          => $this->config['return_url'],
            'p2_Order'        => $vo->getOrderNo(),
            'p5_Pid'          => $this->toGbk($vo->getTitle()),
            'p3_Amt'          => $vo->getFee(),
            'p7_Pdesc'        => $this->toGbk($vo->getBody()),
            'pr_NeedResponse' => 1
        );

        $param['hmac'] = $this->createSign($param);

        $sHtml = $this->_buildForm($param, $this->gateway, 'post', 'gbk');

        return $sHtml;
    }

    /**
     * 易宝支付平台统一使用GBK/GB2312编码方式。
     * @param type $str
     * @return type
     */
    protected function toGbk($str, $from = "utf-8", $to = 'gbk') {
        if (function_exists('mb_convert_encoding')) {
            return mb_convert_encoding($str, $to, $from);
        } elseif (function_exists('iconv')) {
            return iconv($from, $to, $str);
        } else {
            return $str;
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
        foreach ($params as $value) {
            if (IS_POST) {
                $arg .= $value;
            } else {
                if (in_array($key, array('p1_MerId', 'r0_Cmd', 'r1_Code', 'r2_TrxId', 'r3_Amt', 'r4_Cur', 'r5_Pid', 'r6_Order', 'r7_Uid', 'r8_MP', 'r9_BType')) == true) {
                    $arg .= $value;
                }
            }
        }
        $key = $this->config['key'];

        $arg = $this->toGbk($arg, "gbk", "utf-8");

        $b = 64; // byte length for md5
        if (strlen($key) > $b) {
            $key = pack("H*", md5($key));
        }
        $key    = str_pad($key, $b, chr(0x00));
        $ipad   = str_pad('', $b, chr(0x36));
        $opad   = str_pad('', $b, chr(0x5c));
        $k_ipad = $key ^ $ipad;
        $k_opad = $key ^ $opad;

        return md5($k_opad . pack("H*", md5($k_ipad . $arg)));
    }

    public function verifyNotify($notify) {
        $hmac = $notify['hmac'];
        unset($notify['hmac']);
        if ($hmac == $this->createSign($notify)) {
            $info                 = array();
            //支付状态
            $info['status']       = $notify['r1_Code'] == 1 ? true : false;
            $info['money']        = $notify['r3_Amt'];
            $info['out_trade_no'] = $notify['r6_Order'];
            $this->info           = $info;
            if ($notify['r9_BType'] == 2) {
                $_GET['method'] = 'notify';
            }
            return true;
        } else {
            return false;
        }
    }

}
