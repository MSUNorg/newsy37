<?php

namespace Think\Pay\Driver;
class ClientResponseHandler  {
    
    /** 密钥 */
    var $key;
    
    /** 应答的参数 */
    var $parameters;
    
    /** debug信息 */
    var $debugInfo;
    
    //原始内容
    var $content;
    
    function __construct() {
        $this->ClientResponseHandler();
    }
    
    function ClientResponseHandler() {
        $this->key = "";
        $this->parameters = array();
        $this->debugInfo = "";
        $this->content = "";
    }
        
    /**
    *获取密钥
    */
    function getKey() {
        return $this->key;
    }
    
    /**
    *设置密钥
    */  
    function setKey($key) {
        $this->key = $key;
    }
    
    //设置原始内容
    function setContent($content) {
        $this->content = $content;
        
        $xml = simplexml_load_string($this->content);
        $encode = $this->getXmlEncode($this->content);
        
        if($xml && $xml->children()) {
            foreach ($xml->children() as $node){
                //有子节点
                if($node->children()) {
                    $k = $node->getName();
                    $nodeXml = $node->asXML();
                    $v = substr($nodeXml, strlen($k)+2, strlen($nodeXml)-2*strlen($k)-5);
                    
                } else {
                    $k = $node->getName();
                    $v = (string)$node;
                }
                
                if($encode!="" && $encode != "UTF-8") {
                    $k = iconv("UTF-8", $encode, $k);
                    $v = iconv("UTF-8", $encode, $v);
                }
                
                $this->setParameter($k, $v);            
            }
        }
    }
    
    //获取原始内容
    function getContent() {
        return $this->content;
    }
    
    /**
    *获取参数值
    */  
    function getParameter($parameter) {
        return isset($this->parameters[$parameter])?$this->parameters[$parameter] : '';
    }
    
    /**
    *设置参数值
    */  
    function setParameter($parameter, $parameterValue) {
        $this->parameters[$parameter] = $parameterValue;
    }
    
    /**
    *获取所有请求的参数
    *@return array
    */
    function getAllParameters() {
        return $this->parameters;
    }   
    
    /**
    *是否威富通签名,规则是:按参数名称a-z排序,遇到空值的参数不参加签名。
    *true:是
    *false:否
    */  
    function isTenpaySign() {
        $signPars = "";
        ksort($this->parameters);
        foreach($this->parameters as $k => $v) {
            if("sign" != $k && "" != $v) {
                $signPars .= $k . "=" . $v . "&";
            }
        }
        $signPars .= "key=" . $this->getKey();
        
        $sign = strtolower(md5($signPars));
        
        $tenpaySign = strtolower($this->getParameter("sign"));
                
        //debug信息
        $this->_setDebugInfo($signPars . " => sign:" . $sign .
                " tenpaySign:" . $this->getParameter("sign"));
        
        return $sign == $tenpaySign;
        
    }
    
    /**
    *获取debug信息
    */  
    function getDebugInfo() {
        return $this->debugInfo;
    }
    
    //获取xml编码
    function getXmlEncode($xml) {
        $ret = preg_match ("/<?xml[^>]* encoding=\"(.*)\"[^>]* ?>/i", $xml, $arr);
        if($ret) {
            return strtoupper ( $arr[1] );
        } else {
            return "";
        }
    }
    
    /**
    *设置debug信息
    */  
    function _setDebugInfo($debugInfo) {
        $this->debugInfo = $debugInfo;
    }
    
    /**
     * 是否财付通签名
     * @param signParameterArray 签名的参数数组
     * @return boolean
     */ 
    function _isTenpaySign($signParameterArray) {
    
        $signPars = "";
        foreach($signParameterArray as $k) {
            $v = $this->getParameter($k);
            if("sign" != $k && "" != $v) {
                $signPars .= $k . "=" . $v . "&";
            }           
        }
        $signPars .= "key=" . $this->getKey();
        
        $sign = strtolower(md5($signPars));
        
        $tenpaySign = strtolower($this->getParameter("sign"));
                
        //debug信息
        $this->_setDebugInfo($signPars . " => sign:" . $sign .
                " tenpaySign:" . $this->getParameter("sign"));
        
        return $sign == $tenpaySign;        
        
    
    }
    
}
class PayHttpClient {
    //请求内容，无论post和get，都用get方式提供
    var $reqContent = array();
    //应答内容
    var $resContent;
    
    //错误信息
    var $errInfo;
    
    //超时时间
    var $timeOut;
    
    //http状态码
    var $responseCode;
    
    function __construct() {
        $this->PayHttpClient();
    }
    
    
    function PayHttpClient() {
        $this->reqContent = "";
        $this->resContent = "";
        
        $this->errInfo = "";
        
        $this->timeOut = 120;
        
        $this->responseCode = 0;
        
    }
    
    //设置请求内容
    function setReqContent($url,$data) {
        $this->reqContent['url']=$url;
        $this->reqContent['data']=$data;
    }
    
    //获取结果内容
    function getResContent() {
        return $this->resContent;
    }
    
    //获取错误信息
    function getErrInfo() {
        return $this->errInfo;
    }
    
    //设置超时时间,单位秒
    function setTimeOut($timeOut) {
        $this->timeOut = $timeOut;
    }
    
    //执行http调用
    function call() {
        //启动一个CURL会话
        $ch = curl_init();

        // 设置curl允许执行的最长秒数
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeOut);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
        // 获取的信息以文件流的形式返回，而不是直接输出。
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        
        //发送一个常规的POST请求。
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_URL, $this->reqContent['url']);
        //要传送的所有数据
        curl_setopt($ch, CURLOPT_POSTFIELDS, $this->reqContent['data']);
        
        // 执行操作
        $res = curl_exec($ch);
        $this->responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        if ($res == NULL) { 
           $this->errInfo = "call http err :" . curl_errno($ch) . " - " . curl_error($ch) ;
           curl_close($ch);
           return false;
        } else if($this->responseCode  != "200") {
            $this->errInfo = "call http err httpcode=" . $this->responseCode  ;
            curl_close($ch);
            return false;
        }
        
        curl_close($ch);
        $this->resContent = $res;

        
        return true;
    }
    
    function getResponseCode() {
        return $this->responseCode;
    }
    
}
class RequestHandler {
    
    /** 网关url地址 */
    var $gateUrl;
    
    /** 密钥 */
    var $key;
    
    /** 请求的参数 */
    var $parameters;
    
    /** debug信息 */
    var $debugInfo;
    
    function __construct() {
        $this->RequestHandler();
    }
    
    function RequestHandler() {
        $this->gateUrl = "'https://pay.swiftpass.cn/pay/gateway";
        $this->key = "";
        $this->parameters = array();
        $this->debugInfo = "";
    }
    
    /**
    *初始化函数。
    */
    function init() {
        //nothing to do
    }
    
    /**
    *获取入口地址,不包含参数值
    */
    function getGateURL() {
        return $this->gateUrl;
    }
    
    /**
    *设置入口地址,不包含参数值
    */
    function setGateURL($gateUrl) {
        $this->gateUrl = $gateUrl;
    }
    
    /**
    *获取密钥
    */
    function getKey() {
        return $this->key;
    }
    
    /**
    *设置密钥
    */
    function setKey($key) {
        $this->key = $key;
    }
    
    /**
    *获取参数值
    */
    function getParameter($parameter) {
        return isset($this->parameters[$parameter])?$this->parameters[$parameter]:'';
    }
    
    /**
    *设置参数值
    */
    function setParameter($parameter, $parameterValue) {
        $this->parameters[$parameter] = $parameterValue;
    }

    /**
     * 一次性设置参数
     */
    function setReqParams($post,$filterField=null){
        if($filterField !== null){
            forEach($filterField as $k=>$v){
                unset($post[$v]);
            }
        }
        
        //判断是否存在空值，空值不提交
        forEach($post as $k=>$v){
            if(empty($v)){
                unset($post[$k]);
            }
        }

        $this->parameters = $post;
    }
    
    /**
    *获取所有请求的参数
    *@return array
    */
    function getAllParameters() {
        return $this->parameters;
    }
    
    /**
    *获取带参数的请求URL
    */
    function getRequestURL() {
    
        $this->createSign();
        
        $reqPar = "";
        ksort($this->parameters);
        foreach($this->parameters as $k => $v) {
            $reqPar .= $k . "=" . urlencode($v) . "&";
        }
        
        //去掉最后一个&
        $reqPar = substr($reqPar, 0, strlen($reqPar)-1);
        
        $requestURL = $this->getGateURL() . "?" . $reqPar;
        
        return $requestURL;
        
    }
        
    /**
    *获取debug信息
    */
    function getDebugInfo() {
        return $this->debugInfo;
    }
    
    /**
    *创建md5摘要,规则是:按参数名称a-z排序,遇到空值的参数不参加签名。
    */
    function createSign() {
        $signPars = "";
        ksort($this->parameters);
        foreach($this->parameters as $k => $v) {
            if("" != $v && "sign" != $k) {
                $signPars .= $k . "=" . $v . "&";
            }
        }
        $signPars .= "key=" . $this->getKey();
        $sign = strtoupper(md5($signPars));
        $this->setParameter("sign", $sign);
        
        //debug信息
        $this->_setDebugInfo($signPars . " => sign:" . $sign);
        
    }   
    
    /**
    *设置debug信息
    */
    function _setDebugInfo($debugInfo) {
        $this->debugInfo = $debugInfo;
    }

}
class Config{
    private $cfg = array(
        'url'=>'https://pay.swiftpass.cn/pay/gateway',  //支付请求url，无需更改
        'mchId'=>'7551000001',      //测试商户号，商户需更改为自己的 二维码：7551000001  移动支付：755437000006
        'key'=>'9d101c97133837e13dde2d32a5054abb',   //测试密钥，商户需更改为自己的 二维码：9d101c97133837e13dde2d32a5054abb 移动支付：7daa4babae15ae17eee90c9e
        'notify_url'=>'http://www.baidu.com',//测试通知url，商户需更改为自己的，保证能被外网访问到（否则支付成功后收不到威富通服务器所发通知）
        'version'=>'2.0'        //版本号
       );
    
    public function C($cfgName){
        return $this->cfg[$cfgName];
    }
}
class Utils{
    /**
     * 将数据转为XML
     */
    public static function toXml($array){
        $xml = '<xml>';
        forEach($array as $k=>$v){
            $xml.='<'.$k.'><![CDATA['.$v.']]></'.$k.'>';
        }
        $xml.='</xml>';
        return $xml;
    }
    
    public static function dataRecodes($title,$data){
        $handler = fopen('result.txt','a+');
        $content = "================".$title."===================\n";
        if(is_string($data) === true){
            $content .= $data."\n";
        }
        if(is_array($data) === true){
            forEach($data as $k=>$v){
                $content .= "key: ".$k." value: ".$v."\n";
            }
        }
        $flag = fwrite($handler,$content);
        fclose($handler);
        return $flag;
    }

    public static function parseXML($xmlSrc){
        if(empty($xmlSrc)){
            return false;
        }
        $array = array();
        $xml = simplexml_load_string($xmlSrc);
        $encode = Utils::getXmlEncode($xmlSrc);

        if($xml && $xml->children()) {
            foreach ($xml->children() as $node){
                //有子节点
                if($node->children()) {
                    $k = $node->getName();
                    $nodeXml = $node->asXML();
                    $v = substr($nodeXml, strlen($k)+2, strlen($nodeXml)-2*strlen($k)-5);
                    
                } else {
                    $k = $node->getName();
                    $v = (string)$node;
                }
                
                if($encode!="" && $encode != "UTF-8") {
                    $k = iconv("UTF-8", $encode, $k);
                    $v = iconv("UTF-8", $encode, $v);
                }
                $array[$k] = $v;
            }
        }
        return $array;
    }

    //获取xml编码
    function getXmlEncode($xml) {
        $ret = preg_match ("/<?xml[^>]* encoding=\"(.*)\"[^>]* ?>/i", $xml, $arr);
        if($ret) {
            return strtoupper ( $arr[1] );
        } else {
            return "";
        }
    }
}
namespace Think\Pay\Driver;
class Swiftpass extends \Think\Pay\Pay {
    protected $gateway = 'https://pay.swiftpass.cn/pay/gateway';
    protected $config = array(
        'email' => '',
        'key' => '',
        'partner' => ''
    );

    public function check() {
        if (!$this->config['key'] || !$this->config['partner']) {
            E("威富通设置有误！");
        }
        return true;
    }

    public function buildRequestForm(\Think\Pay\PayVo $vo) {
        header("Content-type: text/html;charset=utf-8");
        $this->resHandler = new ClientResponseHandler();
        $this->reqHandler = new RequestHandler();
        $this->pay = new PayHttpClient();
        $this->cfg = new Config();

        $this->reqHandler->setGateUrl($this->gateway);
        $this->reqHandler->setKey($this->config['key']);//$this->cfg->C('key')
        unset($_POST);
        $_POST['out_trade_no'] = $vo->getOrderNo();
        $_POST['body'] = "游戏支付";
        $_POST['total_fee'] = $vo->getFee() * 100;
        $_POST['mch_create_ip'] = get_client_ip ();
        $this->reqHandler->setReqParams($_POST,array('method'));
        
        $this->reqHandler->setParameter('service',$vo->getService());//接口类型：pay.weixin.scancode
        $this->reqHandler->setParameter('mch_id',$this->config['partner']);//必填项，商户号，由威富通分配$this->cfg->C('mchId')
        $this->reqHandler->setParameter('notify_url',$vo->getNotifyUrl());
        $this->reqHandler->setParameter('version',$this->cfg->C('version'));
        $this->reqHandler->setParameter('nonce_str',mt_rand(time(),time()+rand()));//随机字符串，必填项，不长于 32 位
        $this->reqHandler->createSign();//创建签名
        
        $data = Utils::toXml($this->reqHandler->getAllParameters());
        
        $this->pay->setReqContent($this->reqHandler->getGateURL(),$data);
        if($this->pay->call()){

            $this->resHandler->setContent($this->pay->getResContent());
            $this->resHandler->setKey($this->reqHandler->getKey());
            if($this->resHandler->isTenpaySign()){
                //当返回状态与业务结果都为0时才返回支付二维码，其它结果请查看接口文档
                if($this->resHandler->getParameter('status') == 0 && $this->resHandler->getParameter('result_code') == 0){
                    $url2['code_img_url'] = $this->resHandler->getParameter('code_img_url');
                    $url2['code_status'] = $this->resHandler->getParameter('code_status');
                    $url2['code_url'] = $this->resHandler->getParameter('code_url');
                    $url2['orderid']  = $vo->getOrderNo(); 
                    $url2['status1']  = 0;
                    $url2['pay_money'] = $vo->getFee();
                    $url2['token_id'] = $this->resHandler->getParameter('token_id');

                    return $url2;
                    exit();
                }else{
                    $url2['status1'] =500;
                    $url2['msg'] = 'Error Code:'.$this->resHandler->getParameter('err_code').' Error Message:'.$this->resHandler->getParameter('err_msg');
                    
                    return $url2;

                    exit();
                }
            }

            $url2['status1'] =500;
            $url2['msg'] = 'Error Code:'.$this->resHandler->getParameter('status').' Error Message:'.$this->resHandler->getParameter('message');
            return $url2;exit();
        }else{ 

            $url2['status1'] =500;
            $url2['msg'] = 'Response Code:'.$this->pay->getResponseCode().' Error Info:'.$this->pay->getErrInfo();

            return $url2;
        }
    }

    protected function setInfo($notify) {
        $info = array();
        //支付状态
        $info['status'] = $notify['status'] == 1 ? true : false;
        $info['money']  = $notify['total_fee'];
        $info['out_trade_no'] = $notify['out_trade_no'];
        $this->info = $info;
    }

    /**
     * 获取返回时的签名验证结果
     * @param $para_temp 通知返回来的参数数组
     * @param $sign 返回的签名结果
     * @return 签名验证结果
     */
    protected function getSignVeryfy($param=array()) {
        $this->resHandler = new ClientResponseHandler();
        //$xml = file_get_contents('php://input');
        $this->resHandler->setContent($param);
        $this->resHandler->setKey($this->config['key']);
        if($this->resHandler->isTenpaySign()){
            if($this->resHandler->getParameter('status') == 0 && $this->resHandler->getParameter('result_code') == 0){

                $data['status'] = 1;
                $data['out_trade_no']  = $this->resHandler->getParameter('out_trade_no');//$data['out_trade_no'];
                $data['total_fee']     = $this->resHandler->getParameter('total_fee')/100;
                $this->setInfo($data);
                Utils::dataRecodes('接口回调收到通知参数',$this->resHandler->getAllParameters());
                return 'success';
            }else{
                return 'failure1';
            }
        }else{
            return 'failure2';
        }
    }

    /**
    * 针对notify_url验证消息是否是支付宝发出的合法消息
    * @return 验证结果
    */
    public function verifyNotify($notify) {
        $isSign = $this->getSignVeryfy($notify);
        if($isSign == "success"){
            return true;
        }
        else{
            return false;
        }
    }

}
