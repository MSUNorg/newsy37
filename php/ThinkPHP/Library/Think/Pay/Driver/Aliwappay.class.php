<?php

// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.topthink.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云无心 <tzzhangyajun@vip.qq.com>
// +----------------------------------------------------------------------

namespace Think\Pay\Driver;

use Think\Pay\Pay;
use Think\Pay\PayVo;

class Aliwappay extends Pay {

    protected $gateway    = 'http://wappaygw.alipay.com/service/rest.htm?';
    protected $verify_url = 'http://notify.alipay.com/trade/notify_query.do';
    protected $config     = array(
        'email'   => '',
        'key'     => '',
        'partner' => ''
    );

    public function check() {
        if (!$this->config['email'] || !$this->config['key'] || !$this->config['partner']) {
            E("支付宝设置有误！");
        }
        return true;
    }

    public function buildRequestForm(PayVo $vo) {
        $req_id   = date('Ymdhis');
        //请求业务参数详细
        $req_data = '<direct_trade_create_req>'
                . '<notify_url>' . $this->config['notify_url'] . '</notify_url>'
                . '<call_back_url>' . $this->config['return_url'] . '</call_back_url>'
                . '<seller_account_name>' . $this->config['email'] . '</seller_account_name>'
                . '<out_trade_no>' . $vo->getOrderNo() . '</out_trade_no>'
                . '<subject>' . $vo->getTitle() . '</subject>'
                . '<total_fee>' . $vo->getFee() . '</total_fee>'
                . '<merchant_url></merchant_url>'
                . '</direct_trade_create_req>';
        $param    = array(
            "service"        => "alipay.wap.trade.create.direct",
            "partner"        => $this->config['partner'],
            "sec_id"         => "MD5",
            "format"         => "xml",
            "v"              => "2.0",
            "req_id"         => $req_id,
            "req_data"       => $req_data,
            "_input_charset" => 'utf-8'
        );

        $param['sign'] = $this->createSign($param);

        $return_html = $this->fsockOpen($this->gateway, "", $param);
        $return_data = $this->parseResponse(urldecode($return_html));
        if (isset($return_data['res_error'])) {
            $doc = new \DOMDocument();
            $doc->loadXML($return_data['res_error']);
            E('[(' . $doc->getElementsByTagName('code')->item(0)->nodeValue . ')' . $doc->getElementsByTagName('msg')->item(0)->nodeValue . ']' . $doc->getElementsByTagName('detail')->item(0)->nodeValue);
        }
        //获取request_token
        $request_token = $return_data['request_token'];
        //业务详细
        $req_data      = '<auth_and_execute_req><request_token>' . $request_token . '</request_token></auth_and_execute_req>';
        //构造要请求的参数数组
        $param         = array(
            "service"        => "alipay.wap.auth.authAndExecute",
            "partner"        => $this->config['partner'],
            "sec_id"         => "MD5",
            "format"         => "xml",
            "v"              => "2.0",
            "req_id"         => $req_id,
            "req_data"       => $req_data,
            "_input_charset" => 'utf-8'
        );
        $param['sign'] = $this->createSign($param);
        $sHtml         = $this->_buildForm($param, $this->gateway, 'get');

        return $sHtml;
    }

    /**
     * 创建MD5签名
     * @param array $para
     * @return string
     */
    protected function createSign($para) {
        ksort($para);
        reset($para);
        $arg = "";
        while (list ($key, $val) = each($para)) {
            if ($key == "sign" || $key == "sign_type" || $val == "")
                continue;
            $arg.=$key . "=" . $val . "&";
        }
        //去掉最后一个&字符
        $arg = substr($arg, 0, -1);

        return md5($arg . $this->config['key']);
    }

    /**
     * 解析远程模拟提交后返回的信息
     * @param $str_text 要解析的字符串
     * @return 解析结果
     */
    protected function parseResponse($str_text) {
        //以“&”字符切割字符串
        $para_split = explode('&', $str_text);
        //把切割后的字符串数组变成变量与数值组合的数组
        foreach ($para_split as $item) {
            //获得第一个=字符的位置
            $nPos            = strpos($item, '=');
            //获得字符串长度
            $nLen            = strlen($item);
            //获得变量名
            $key             = substr($item, 0, $nPos);
            //获得数值
            $value           = substr($item, $nPos + 1, $nLen - $nPos - 1);
            //放入数组中
            $para_text[$key] = $value;
        }

        if (!empty($para_text['res_data'])) {

            //token从res_data中解析出来（也就是说res_data中已经包含token的内容）
            $doc                        = new \DOMDocument();
            $doc->loadXML($para_text['res_data']);
            $para_text['request_token'] = $doc->getElementsByTagName("request_token")->item(0)->nodeValue;
        }

        return $para_text;
    }

    /**
     * 获取返回时的签名验证结果
     * @param $para_temp 通知返回来的参数数组
     * @param $sign 返回的签名结果
     * @return 签名验证结果
     */
    protected function getSignVeryfy($param, $sign, $isSort) {
        //除去待签名参数数组中的空值和签名参数
        $param_filter = array();
        while (list ($key, $val) = each($param)) {
            if ($key == "sign" || $key == "sign_type" || $val == "") {
                continue;
            } else {
                $param_filter[$key] = $param[$key];
            }
        }

        if ($isSort) {
            ksort($param_filter);
            reset($param_filter);
        } else {
            $para_sort                = array();
            $para_sort['service']     = $param_filter['service'];
            $para_sort['v']           = $param_filter['v'];
            $para_sort['sec_id']      = $param_filter['sec_id'];
            $para_sort['notify_data'] = $param_filter['notify_data'];
            $param_filter             = $para_sort;
        }



        //把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
        $prestr = "";
        while (list ($key, $val) = each($param_filter)) {
            $prestr.=$key . "=" . $val . "&";
        }
        //去掉最后一个&字符
        $prestr = substr($prestr, 0, -1);

        $prestr = $prestr . $this->config['key'];
        $mysgin = md5($prestr);

        if ($mysgin == $sign) {
            return true;
        } else {
            return false;
        }
    }

    public function verifyNotify($notify) {
        //生成签名结果
        if (IS_GET) {
            $isSign      = $this->getSignVeryfy($notify, $notify["sign"], true);
            $responseTxt = 'true';
        } elseif (IS_POST) {
            $isSign      = $this->getSignVeryfy($notify, $notify["sign"], false);
            //获取支付宝远程服务器ATN结果（验证是否是支付宝发来的消息）
            $doc         = new \DOMDocument();
            $doc->loadXML($notify['notify_data']);
            $notify_id   = $doc->getElementsByTagName("notify_id")->item(0)->nodeValue;
            $responseTxt = $this->getResponse($notify_id);
        }

        if (preg_match("/true$/i", $responseTxt) && $isSign) {
            $info = array();
            if (IS_POST) {
                //支付状态
                $trade_status         = $doc->getElementsByTagName("trade_status")->item(0)->nodeValue;
                $info['status']       = ($trade_status == 'TRADE_FINISHED' || $trade_status == 'TRADE_SUCCESS') ? true : false;
                $info['out_trade_no'] = $doc->getElementsByTagName("out_trade_no")->item(0)->nodeValue;
            } elseif (IS_GET) {
                //支付状态
                $info['status']       = ($notify['result'] == 'success' ) ? true : false;
                $info['out_trade_no'] = $notify['out_trade_no'];
            }

            $this->info = $info;
            return true;
        } else {
            return false;
        }
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
        $partner     = $this->config['partner'];
        $veryfy_url  = $this->verify_url . "?partner=" . $partner . "&notify_id=" . $notify_id;
        $responseTxt = $this->fsockOpen($veryfy_url);
        return $responseTxt;
    }

}