<?php
namespace Sdk\Controller;
use Think\Controller;
use Common\Api\GaemApi;
class PayController extends BaseController{

    /**
    *支付宝移动支付
    */
    public function alipay_pay(){
        #获取SDK上POST方式传过来的数据 然后base64解密 然后将json字符串转化成数组
        $request = json_decode(base64_decode(file_get_contents("php://input")),true);
        #获取订单信息
        $prefix = $request['code'] == 1 ? "SP_" : "PF_";
        $out_trade_no = $prefix.date('Ymd').date('His').sp_random_string(4);
        $orderInfo = "partner=\"".C('alipay.partner')."\"";
        $orderInfo = $orderInfo."&seller_id=\"".C('alipay.email')."\"";
        $orderInfo = $orderInfo."&out_trade_no=\"".$out_trade_no."\"";
        $orderInfo = $orderInfo."&subject=\"".$request['title']."\"";
        $orderInfo = $orderInfo."&body=\"".$request['body']."\"";
        $orderInfo = $orderInfo."&total_fee=\"".$request['price']."\"";
        $orderInfo = $orderInfo."&notify_url=\"http://{$_SERVER ['HTTP_HOST']}/callback.php?s=/Notify/mobile_pay_notify\"";
        $orderInfo = $orderInfo."&service=\"mobile.securitypay.pay\"";
        $orderInfo = $orderInfo."&payment_type=\"1\"";
        $orderInfo = $orderInfo."&_input_charset=\"utf-8\"";
        $orderInfo = $orderInfo."&it_b_pay=\"30m\"";
        $orderInfo = $orderInfo."&return_url=\"http://{$_SERVER ['HTTP_HOST']}/callback.php?s=/Notify/mobile_pay_notify\"";
        $alipay = A("AliPay","Event");
        #对订单信息进行排序
        $ali  = $alipay->argSort($orderInfo);
        #对订单信息进行验签
        $sign = $alipay->sign($ali);
        #对 sign进行md5加密
        $md5_sign = $this->encrypt_md5(base64_encode($orderInfo),"mengchuang");
        $data = array("orderInfo"=>base64_encode($orderInfo),"order_sign"=>$sign,"md5_sign"=>$md5_sign);
        $request['pay_order_number'] = $out_trade_no;
        $request['pay_status'] = 0;
        $request['pay_way']    = 1;
        $request['spend_ip']   = get_client_ip();
        if($request['code'] == 1 ){
            #TODO添加消费记录
            $this->add_spend($request);
        }else{
            #TODO添加平台币充值记录
            $this->add_deposit($request);
        }
        echo json_encode($data);
    }

    /**
    *其他支付
    */
    public function outher_pay(){
        #获取SDK上POST方式传过来的数据 然后base64解密 然后将json字符串转化成数组
        $request = json_decode(base64_decode(file_get_contents("php://input")),true);

        $prefix = $request['code'] == 1 ? "SP_" : "PF_";
        $out_trade_no = $prefix.date('Ymd').date('His').sp_random_string(4);

        $pay = new \Think\Pay('swiftpass',C('alipay'));
        $vo = new \Think\Pay\PayVo();

        $vo->setBody("充值记录描述")
            ->setFee($request['price'])//支付金额$pay_amount
            ->setOrderNo($out_trade_no)
            ->setService("unified.trade.pay");
        $result_data = $pay->buildRequestForm($vo);

        $request['pay_order_number'] = $out_trade_no;
        $request['pay_status'] = 0;
        $request['pay_way']    = 2;
        $request['spend_ip']   = get_client_ip();
        if($request['code'] == 1 ){
            #TODO添加消费记录
            $this->add_spend($request);
        }else{
            #TODO添加平台币充值记录
            $this->add_deposit($request);
        }
        $data['status'] = 1;
        $data['return_code'] = "success";
        $data['return_msg'] = "下单成功";
        $data['token_id'] = $result_data['token_id'];
        echo base64_encode(json_encode($data));
    }

    /**
    *平台币支付
    */
    public function platform_coin_pay(){
        #获取SDK上POST方式传过来的数据 然后base64解密 然后将json字符串转化成数组
        $request = json_decode(base64_decode(file_get_contents("php://input")),true);
        #记录信息
        $user_entity = get_user_entity($request['user_id']);
        $out_trade_no = "PF_".date('Ymd').date('His').sp_random_string(4);
        $request['order_number']     = $out_trade_no;
        $request['pay_order_number'] = $out_trade_no;
        $request['title'] = $request['title'];
        $request['pay_status'] = 1;
        $request['pay_way'] = 0;
        $request['spend_ip']   = get_client_ip();
        $result = false;
        switch ($request['code']) {
            case 1:#非绑定平台币
                $user = M("user","tab_");
                if($user_entity['balance'] < $request['price']){
                    echo base64_encode(json_encode(array("status"=>-2,"return_code"=>"fail","return_msg"=>"余额不足")));
                    exit();
                }
                #扣除平台币
                $user->where("id=".$request["user_id"])->setDec("balance",$request['price']);
                #TODO 添加绑定平台币消费记录
                $result = $this->add_spend($request);
                break;
             case 2:#绑定平台币
                $user_play = M("UserPlay","tab_");
                $user_play_map['user_id'] = $request['user_id'];
                $user_play_map['game_id'] = $request['game_id'];
                $user_play_data = $user_play->where($user_play_map)->find();

                if($user_play_data['bind_balance'] < $request['price']){
                    echo base64_encode(json_encode(array("status"=>-2,"return_code"=>"fail","return_msg"=>"余额不足")));
                    exit();
                }
                #扣除平台币
                $user_play->where($user_play_map)->setDec("bind_balance",$request['price']);
                #TODO 添加绑定平台币消费记录
                $result = $this->add_bind_spned($request);
                break;
            default:
                echo base64_encode(json_encode(array("status"=>-3,"return_code"=>"fail","return_msg"=>"支付方式不明确")));
                exit();
            break;
        }
        
        if($result){
            echo base64_encode(json_encode(array("status"=>1,"return_code"=>"success","return_msg"=>"支付成功")));
        }
        else{
            echo base64_encode(json_encode(array("status"=>-1,"return_code"=>"fail","return_msg"=>"支付失败")));
        }
    }

    /**
    *游戏支付通知
    */
    private function game_pay_notice($param = array()){
        $request = json_decode(base64_decode(file_get_contents("php://input")),true);
        $request['out_trade_no'];
        $request['price'];
        $request['extend'];
        $game = new GaemApi();
        #游戏支付通知
        $result = $game->game_pay_notice($param);
    }


    
}
