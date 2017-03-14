<?php
namespace Media\Controller;
use Think\Controller;
use Common\Api\PayApi;

class RechargeController extends BaseController{
	
	public function index() {
		#支付配置
/*		$data['options']  = 'deposit';
		$data['order_no'] = 'PF_'.date('Ymd').date ( 'His' ).sp_random_string(4);
		$data['fee']      = 1;//$_POST['alipay_amount'];
		$data['pay_type'] = 'heepay';
		$data['way'] = 30; #支付方式 支付宝 微信
		$data['payType'] = 0; # 0:网页 1:SDK  
		$data['payWay']  = 0; # 0:微信扫码 1:微信WAP网页 2:微信公众支付
		#平台币记录数据
		$data['order_number'] = "";
		$data['pay_order_number'] = $data['order_no'];
		$data['user_id'] = $user['id'];
		$data['user_account'] = $user['account'];
		$data['user_nickname'] = $user['nickname'];
		$data['promote_id'] = $user['promote_id'];
		$data['promote_account'] = $user['promote_account'];
		$data['pay_amount'] = $_POST['alipay_amount'];
		$data['pay_status'] = 0;
		$data['pay_way'] = 1;
		$data['pay_source'] = 1;
		$pay = new PayApi();`
		$pay->other_pay($data,C('heepay'));*/
		$this->display();
	}
	
	public function step() {
		
		$this->display();
	}

	public function check() {
	
		echo json_encode(array('msg'=>'无此用户','code'=>1,'order'=>'CZ201604121525572809'));
	}

	/**
	*支付宝支付
	*/
	public function alipay(){
		#判断账号是否存在
		$user = get_user_entity($_POST['username'],true);
		if(empty($user)){$this->error("用户不存在");exit();}
		//判断是否开启支付宝充值
		if(pay_set_status('alipay')==0){
			$this->error("网站未启用支付宝充值",'',1);
			exit();
		}
		#支付配置
		$data['options']  = 'deposit';
		$data['order_no'] = 'PF_'.date('Ymd').date ( 'His' ).sp_random_string(4);
		$data['fee']      = $_POST['pay_money'];//$_POST['alipay_amount'];
		$data['pay_type'] = 'alipay'; 
		#平台币记录数据
		$data['order_number'] = "";
		$data['pay_order_number'] = $data['order_no'];
		$data['user_id'] = $user['id'];
		$data['user_account'] = $user['account'];
		$data['user_nickname'] = $user['nickname'];
		$data['promote_id'] = $user['promote_id'];
		$data['promote_account'] = $user['promote_account'];
		$data['pay_amount'] = $_POST['pay_money'];
		$data['pay_status'] = 0;
		$data['pay_way'] = 1;
		$data['pay_source'] = 1;
		$pay = new PayApi();
		$pay->other_pay($data,C('alipay'));
	}

	/**
	*微信支付
	*/
	public function wxpay(){

		#判断账号是否存在
		$user = get_user_entity($_POST['username'],true);
		if(empty($user)){$this->error("用户不存在");exit();}
		//判断是否开启微信充值
		if(pay_set_status('weixin')==0){
			$this->error("网站未开启微信充值",'',1);
			exit();
		}
		#支付配置
		$data['options']  = 'deposit0';
		$data['order_no'] = 'PF_'.date('Ymd').date ( 'His' ).sp_random_string(4);
		$data['fee']      = $_POST['pay_money'];
		$data['pay_type'] = 'swiftpass'; 
		#平台币记录数据
		$data['order_number'] = "";
		$data['pay_order_number'] = $data['order_no'];
		$data['user_id'] = $user['id'];
		$data['user_account'] = $user['account'];
		$data['user_nickname'] = $user['nickname'];
		$data['promote_id'] = $user['promote_id'];
		$data['promote_account'] = $user['promote_account'];
		$data['pay_amount'] = $_POST['pay_money'];
		$data['pay_status'] = 0;
		//$data['pay_way'] = 1;

		$data['pay_source'] = 1;
		$pay = new PayApi();
		$arr = $pay->weixin_pay($data,C('weixin'));
		if($arr['status1'] === 500){
			\Think\Log::record($arr['msg']);
			$html ='<div class="d_body" style="height:px;">
					<div class="d_content">
						<div class="text_center">'.$arr["msg"].'</div>
					</div>
					</div>';
			$json_data = array("status"=>500,"html"=>$html);
		}else{
			$html ='<div class="d_body" style="height:px;">
					<div class="d_content">
						<div class="text_center">
							<table class="list" width="100%">
								<tbody>
								<tr>
									<td class="text_right">订单号</td>
									<td class="text_left">'.$data["pay_order_number"].'</td>
								</tr>
								<tr>
									<td class="text_right">充值金额</td>
									<td class="text_left">本次充值'.$data["pay_amount"].'元，实际付款'.$data["pay_amount"].'元</td>
								</tr>
								</tbody>
							</table>
							<img src="'.$arr["code_img_url"].'" height="301" width="301">
							<img src="/Public/Media/images/wx_pay_tips.png">
						</div>
					</div>
				</div>';
			$json_data = array("status"=>1,"html"=>$html);
		}
		
		/*$this->ajaxReturn($json_data);*/
		echo json_encode($json_data);
	}
	public function jubaobar(){
		#判断账号是否存在
		$user = get_user_entity($_POST['username'],true);
		if(empty($user)){$this->error("用户不存在");exit();}

		#支付配置
		$data['options']  = 'deposit';
		$data['order_no'] = 'PF_'.date('Ymd').date ( 'His' ).sp_random_string(4);
		$data['fee']      = $_POST['pay_money'];
		$data['pay_type'] = 'Jubaobarpay'; 
		#平台币记录数据
		$data['order_number'] = "";
		$data['pay_order_number'] = $data['order_no'];
		$data['user_id'] = $user['id'];
		$data['user_account'] = $user['account'];
		$data['user_nickname'] = $user['nickname'];
		$data['promote_id'] = $user['promote_id'];
		$data['promote_account'] = $user['promote_account'];
		$data['pay_amount'] = $_POST['pay_money'];
		$data['pay_status'] = 0;
		$data['pay_way'] = 3;
		$data['pay_source'] = 1;
		$pay = new PayApi();
		$pay->other_pay($data,C('jubaobar'));
	}

}