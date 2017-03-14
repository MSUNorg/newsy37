<?php
namespace Sdk\Controller;
use Think\Controller;
use Common\Api\GaemApi;
class PayiosController extends BaseController{

    /**
    *ios移动支付
    */
    public function ios_pay(){
        #获取SDK上POST方式传过来的数据 然后base64解密 然后将json字符串转化成数组
        $request = json_decode(base64_decode(file_get_contents("php://input")),true);
        #获取订单信息
        $prefix = $request['code'] == 1 ? "SP_" : "PF_";
        $out_trade_no = $prefix.date('Ymd').date('His').sp_random_string(4);
        $data = array("out_trade_no"=>$out_trade_no);
        $request['pay_order_number'] = $out_trade_no;
        $request['pay_status'] = 0;
        $request['pay_way']    = 3;
        $request['title'] = $request['productId'];
        $request['spend_ip']   = get_client_ip();
        if($request['code'] == 1 ){
            #TODO添加消费记录
            $this->add_spend($request);
        }else{
            #TODO添加平台币充值记录
            $this->add_deposit($request);
        }
        echo base64_encode(json_encode($data));
    }

    /**
    *支付通知
    */
    public function pay_notify(){
        #获取SDK上POST方式传过来的数据 然后base64解密 然后将json字符串转化成数组
        $request = json_decode(base64_decode(file_get_contents("php://input")),true);
        $out_trade_no = $request['out_trade_no'];
        $pay_where = substr($out_trade_no,0,2);
        $result = 0;
        $map['pay_order_number'] = $out_trade_no;
        $field = array("pay_status"=>1,"pay_amount"=>$request['price']);
        switch ($pay_where) {
            case 'SP':
                //$field = array("pay_status"=>1,"pay_amount"=>$request['price']);
                $result = M('spend','tab_')->where($map)->setField($field);
                break;
            case 'PF':
                $result = M('deposit','tab_')->where($map)->setField($field);
                break;
            case 'AG':
                $result = M('agent','tab_')->where($map)->setField($field);
                break;
            default:
                exit('accident order data');
                break;
        }
        if($result){
            echo base64_encode(json_encode(array("status"=>1,"return_code"=>"success","return_msg"=>"支付成功")));
            exit();
        }else{
            echo base64_encode(json_encode(array("status"=>0,"return_code"=>"fail","return_msg"=>"支付失败")));
            exit();
        }
    }


    
}
