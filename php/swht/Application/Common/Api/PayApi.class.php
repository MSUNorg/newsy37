<?php
// +----------------------------------------------------------------------
// | 手游平台
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.msun.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: zxc
// +----------------------------------------------------------------------

namespace Common\Api;
class PayApi {

	/**
	*支付宝等其他执法
	*/
	public function other_pay($data=array(),$config=array())
	{
		switch ($data['options']) {
			case 'spend':
				$this->add_spend($data);
				break;
			case 'deposit':
				$this->add_deposit($data);
				break;
			case 'agent':
				$this->add_agent($data);
				break;
		}
		//页面上通过表单选择在线支付类型，支付宝为alipay 财付通为tenpay
        $pay = new \Think\Pay($data['pay_type'],$config);
        $vo = new \Think\Pay\PayVo();
        $vo->setBody("充值记录描述")
            ->setFee($data['fee'])//支付金额$pay_amount
            ->setOrderNo($data['order_no'])
            // ->setWay(0)//充值方式 平台币1、游戏币0
            // ->setGid(1)
            // ->setSid(1)
            // ->setAccount($data[''])
            // ->setUid(1)
            // ->setCoin(0)
            // ->setBank('ICBC-NET-B2C')
            //->setUrl(U("/pay/index/"))
            ->setMoney($data['fee'])//$money
            ->setTitle('游戏充值')
            ->setCallback("http://a2.vlcms.com/media.php")
            ->setParam(array('orderid'=>$data['order_no'],'tag'=>$data['order_no'],'user_login'=>'http://www.baidu.com'));
        echo $pay->buildRequestForm($vo);
	}

	/**
	*微信支付
	*/
	public function weixin_pay($data=array(),$config=array()){
		switch ($data['options']) {
			case 'spend':
				$this->add_spend($data);
				break;
			case 'deposit':
				$this->add_deposit($data);
				break;
			case 'agent':
				$this->add_agent($data);
				break;
		}
		//页面上通过表单选择在线支付类型，支付宝为alipay 财付通为tenpay
        $pay = new \Think\Pay($data['pay_type'],$config);
        $vo = new \Think\Pay\PayVo();
        $vo->setBody("充值记录描述")
            ->setFee($data['fee'])//支付金额$pay_amount
            ->setOrderNo($data['order_no'])
            ->setMoney($data['fee'])//$money
            ->setTitle('游戏充值')
            ->setCallback("Pay/index/pay")
            ->setService("pay.weixin.scancode")
            ->setParam(array('orderid'=>$data['order_no'],'tag'=>$data['order_no'],'user_login'=>'http://www.baidu.com'));
        return $pay->buildRequestForm($vo);
	}

	/**
	*消费表添加数据
	*/
	public function add_spend($data){
		$spend = M("spend","tab_");
		$ordercheck = $spend->where(array('pay_order_number'=>$data["order_no"]))->find();
        if($ordercheck)$this->error("订单已经存在，请刷新充值页面重新下单！");
		$spend_data['user_id']          = $data["user_id"];
		$spend_data['user_account']     = $data["user_account"];
		$spend_data['user_nickname']    = $data["user_nickname"];
		$spend_data['game_id']          = $data["game_id"];
		$spend_data['game_appid']       = $data["game_appid"];
		$spend_data['game_name']        = $data["game_name"];
		$spend_data['server_id']        = $data["server_id"];
		$spend_data['server_name']      = $data["server_name"];
		$spend_data['promote_id']       = $data["promote_id"];
		$spend_data['promote_account']  = $data["promote_account"];
		$spend_data['pay_order_number'] = $data["order_no"];
		$spend_data['props_name']       = $data["title"];
		$spend_data['pay_amount']       = $data["fee"];
		$spend_data['pay_way']          = $data["pay_way"];
		$spend_data['spend_ip']         = get_client_ip();
		$spend->add($spend_data);
	}

	/**
	*平台币充值记录
	*/
	public function add_deposit($data){
		$deposit = M("deposit","tab_");
		$ordercheck = $deposit->where(array('pay_order_number'=>$data["order_no"]))->find();
        if($ordercheck)$this->error("订单已经存在，请刷新充值页面重新下单！");

		$deposit_data['order_number']     = "";
		$deposit_data['pay_order_number'] = $data["order_no"];
		$deposit_data['user_id']          = $data["user_id"];
		$deposit_data['user_account']     = $data["user_account"];
		$deposit_data['user_nickname']    = $data["user_nickname"];
		$deposit_data['promote_id']       = $data["promote_id"];
		$deposit_data['promote_account']  = $data["promote_account"];
		$deposit_data['pay_amount']       = $data["fee"];
		$deposit_data['reality_amount']   = 0;
		$deposit_data['pay_status']       = 0;
		$deposit_data['pay_way']          = $data['pay_way'];
		$deposit_data['pay_source']		  = $data["pay_source"];
		$deposit_data['pay_ip']           = get_client_ip();
		$deposit_data['pay_source']       = $data['pay_source'];
		$deposit_data['create_time']      = NOW_TIME;
		
        $deposit->add($deposit_data);
	}

	/**
	*添加代充记录
	*/
	public function add_agent($data){
		$agent = M("agent","tab_");
		$ordercheck = $agent->where(array('pay_order_number'=>$data["order_no"]))->find();
        if($ordercheck)$this->error("订单已经存在，请刷新充值页面重新下单！");
		$agnet_data['order_number']     = "";
		$agnet_data['pay_order_number'] = $data["order_no"];
		$agnet_data['game_id']          = $data["game_id"];
		$agnet_data['game_appid']       = $data["game_appid"];
		$agnet_data['game_name']        = $data["game_name"];
		$agnet_data['promote_id']       = $data["promote_id"];
		$agnet_data['promote_account']  = $data["promote_account"];
		$agnet_data['user_id']          = $data["user_id"];
		$agnet_data['user_account']     = $data["user_account"];
		$agnet_data['user_nickname']    = $data["user_nickname"];
		$agnet_data['pay_type']         = 0;
		$agnet_data['amount']       	= $data["amount"];
		$agnet_data['real_amount']      = $data["real_amount"];
		$agnet_data['pay_status']       = 0;
		$agnet_data['pay_type']			= $data['pay_way'];
		$agnet_data['create_time']      = time();
		$agent_data['zhekou']			=0;
		

		$agent->create($agnet_data);
		$resutl = $agent->add();
	}
}