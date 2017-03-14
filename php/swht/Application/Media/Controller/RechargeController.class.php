<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: zxc <zuojiazi@vip.qq.com> <http://www.msun.com>
// +----------------------------------------------------------------------

namespace Media\Controller;
use Admin\Model\GameModel;
use Common\Api\PayApi;
/**
 * 文档模型控制器
 * 文档模型列表和详情
 */
class RechargeController extends BaseController {

	public function index($value='')
	{
		$this->display();
	}

	public function alipay(){
		#判断账号是否存在
		$user = get_user_entity($_POST['uname1'],true);
		if(empty($user)){$this->error("用户不存在");exit();}

		#支付配置
		$data['options']  = 'deposit';
		$data['order_no'] = 'PF_'.date('Ymd').date ( 'His' ).sp_random_string(4);
		$data['fee']      = $_POST['alipay_amount'];
		$data['pay_type'] = 'alipay'; 
		#平台币记录数据
		$data['order_number'] = "";
		$data['pay_order_number'] = $data['order_no'];
		$data['user_id'] = $user['id'];
		$data['user_account'] = $user['account'];
		$data['user_nickname'] = $user['nickname'];
		$data['promote_id'] = $user['promote_id'];
		$data['promote_account'] = $user['promote_account'];
		$data['pay_amount'] = $_POST['amount'];
		$data['pay_status'] = 0;
		//$data['pay_way'] = 1;
		$data['pay_source'] = 1;
		$pay = new PayApi();
		$pay->other_pay($data,C('PAYMENU.alipay'));
	}

	public function wxpay(){
		$this->error("暂未对接微信支付");
		if(isset($_POST['uname1'])){
			$data['reciver_name'] = $_POST['uname1'];
			$data['amount'] = $_POST['wxpay_amount'];
			$data['pay_way'] = 1;
			if(is_numeric($data['amount']) && $data['amount'] < 0){
				$this->error("金额参数填写有误");exit();
			}
			$data['pay_order_number'] = $this->add_deposit_record($data);
			if(!empty($data['pay_order_number'])){
				{hook('wxpaynative',$data);}
				return $this->ajaxReturn(array('status'=>1,'info'=>'sssss','html'=>HTML));
			}
			else{
				return $this->ajaxReturn(array('status'=>0,'info'=>'加载数据失败'));
			}
		}
		else{
			return $this->ajaxReturn(array('status'=>0,'info'=>'数据异常'));
		}
	}

}
