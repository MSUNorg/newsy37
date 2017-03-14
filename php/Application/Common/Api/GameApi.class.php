<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

namespace Common\Api;
class GameApi {

	public function game_login(){

	}

	public function game_pay_notify($param=null){
		$game = M('GameSet',"tab_");
		$map['game_id'] = $param['game_id'];
		$game_data = $game->where($map)->find();
		if(empty($game_data)){ $this->error_record("未找到指定游戏数据"); return false;}
		if(empty($game_data['pay_notify_url'])){$this->error_record("未设置游戏支付通知地址"); return false;}
		$md5_sign = md5($param['out_trade_no'].$param['price']."1".$param['extend'].$game_data['game_key']);
		$data = array(
			"out_trade_no" => $param['out_trade_no'],
			"price"        => $param['price'],
			"pay_status"   => 1,
			"extend"       => $param['extend'],
			"signType"     => "MD5",
			"sign"         => $md5_sign
		);
		$result = $this->post($data,$game_data['pay_notify_url']);
		return $result;
	}

	public function error_record($msg=""){
		\Think\Log::record($msg);
	}

	/**
	*post提交数据
    */
    protected function post($param,$url){
    	$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($param));
		$data = curl_exec($ch);
		curl_close($ch);
		return $data;
    }

}