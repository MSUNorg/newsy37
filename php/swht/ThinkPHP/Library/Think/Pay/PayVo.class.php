<?php

/**
 * 订单数据模型
 */

namespace Think\Pay;

class PayVo {

    protected $_orderNo;
    protected $_fee;
    protected $_title;
    protected $_body;
    protected $_callback;
    protected $_url;
    protected $_param;
    protected $_way;
    protected $_gid;
    protected $_sid;
    protected $_account;
    protected $_uid;
    protected $_bank;
    protected $_money;
    protected $_coin;
    protected $_service;
    /**
     * 设置订单号
     * @param type $order_no
     * @return \Think\Pay\PayVo
     */
    public function setOrderNo($order_no) {
        $this->_orderNo = $order_no;
        return $this;
    }

    /**
     * 设置商品价格
     * @param type $fee
     * @return \Think\Pay\PayVo
     */
    public function setFee($fee) {
        $this->_fee = $fee;
        return $this;
    }

    /**
     * 设置商品名称
     * @param type $title
     * @return \Think\Pay\PayVo
     */
    public function setTitle($title) {
        $this->_title = $title;
        return $this;
    }

    /**
     * 设置商品描述
     * @param type $body
     * @return \Think\Pay\PayVo
     */
    public function setBody($body) {
        $this->_body = $body;
        return $this;
    }

    /**
     * 设置支付完成后的后续操作接口
     * @param type $callback
     * @return \Think\Pay\PayVo
     */
    public function setCallback($callback) {
        $this->_callback = $callback;
        return $this;
    }

    /**
     * 设置支付完成后的跳转地址
     * @param type $url
     * @return \Think\Pay\PayVo
     */
    public function setUrl($url) {
        $this->_url = $url;
        return $this;
    }

    /**
     * 设置订单的额外参数
     * @param type $param
     * @return \Think\Pay\PayVo
     */
    public function setParam($param) {
        $this->_param = $param;
        return $this;
    }
    /**
     * 设置游戏充值方式
     * @param type $body
     * @return \Think\Pay\PayVo
     */
    public function setWay($way) {
        $this->_way = $way;
        return $this;
    }

    /**
     * 设置游戏gid
     * @param type $body
     * @return \Think\Pay\PayVo
     */
    public function setGid($gid) {
        $this->_gid = $gid;
        return $this;
    }

    /**
     * 设置游戏sid
     * @param type $callback
     * @return \Think\Pay\PayVo
     */
    public function setSid($sid) {
        $this->_sid = $sid;
        return $this;
    }

    /**
     * 设置游戏账号
     * @param type $url
     * @return \Think\Pay\PayVo
     */
    public function setAccount($account) {
        $this->_account = $account;
        return $this;
    }

    /**
     * 设置账号uid
     * @param type $param
     * @return \Think\Pay\PayVo
     */
    public function setUid($uid) {
        $this->_uid = $uid;
        return $this;
    }
    /**
     * 设置充值银行
     * @param type $param
     * @return \Think\Pay\PayVo
     */
    public function setBank($bank) {
        $this->_bank = $bank;
        return $this;
    }
    /**
     * 设置充值实际金额（除去手续费）
     * @param type $param
     * @return \Think\Pay\PayVo
     */
    public function setMoney($money) {
        $this->_money = $money;
        return $this;
    }
    /**
     * 设置充值游戏币数量
     * @param type $param
     * @return \Think\Pay\PayVo
     */
    public function setCoin($coin) {
        $this->_coin = $coin;
        return $this;
    }

    /**
     * 设置支付服务类型
     * @param type $param
     * @return \Think\Pay\PayVo
     */
    public function setService($service){
        $this->_service = $service;
        return $this;
    }

    /**
     * 获取游戏充值方式
     * @return type
     */
    public function getWay() {
        return $this->_way;
    }

    /**
     * 获取游戏gid
     * @return type
     */
    public function getGid() {
        return $this->_gid;
    }

    /**
     * 获取游戏sid
     * @return type
     */
    public function getSid() {
        return $this->_sid;
    }

    /**
     * 获取游戏账号
     * @return type
     */
    public function getAccount() {
        return $this->_account;
    }

    /**
     * 获取账号uid
     * @return type
     */
    public function getUid() {
        return $this->_uid;
    }
    /**
     * 获取充值银行
     * @return type
     */
    public function getBank() {
        return $this->_bank;
    }
    /**
     * 获取充值实际金额（除去手续费）
     * @return type
     */
    public function getMoney() {
        return $this->_money;
    }
    /**
     * 获取充值游戏币数量
     * @return type
     */
    public function getCoin() {
        return $this->_coin;
    }

    /**
     * 获取订单号
     * @return type
     */
    public function getOrderNo() {
        return $this->_orderNo;
    }

    /**
     * 获取商品价格
     * @return type
     */
    public function getFee() {
        return $this->_fee;
    }

    /**
     * 获取商品名称
     * @return type
     */
    public function getTitle() {
        return $this->_title;
    }

    /**
     * 获取支付完成后的后续操作接口
     * @return type
     */
    public function getCallback() {
        return $this->_callback;
    }

    /**
     * 获取支付完成后的跳转地址
     * @return type
     */
    public function getUrl() {
        return $this->_url;
    }

    /**
     * 获取商品描述
     * @return type
     */
    public function getBody() {
        return $this->_body;
    }

    /**
     * 获取订单的额外参数
     * @return type
     */
    public function getParam() {
        return $this->_param;
    }

    /**
    *支付服务类型
    *@return type
    */
    public function getService(){
        return $this->_service;
    }

}
