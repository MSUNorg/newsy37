<?php

namespace Callback\Controller;

/**
 * 支付回调控制器
 * @author zxc
 */
class NotifyController extends Controller {

    /**
    *通知方法
    */
    public function notify($value='')
    {
        $apitype = I('get.apitype');#获取支付api类型
        $pay = new \Think\Pay($apitype, C('PAYMENU.'.$data['pay_type']));
        if (IS_POST && !empty($_POST)) {
            $notify = $_POST;
        } elseif (IS_GET && !empty($_GET)) {
            $notify = $_GET;
            unset($notify['method']);
            unset($notify['apitype']);
        } else {
            exit('Access Denied');
        }

        if ($pay->verifyNotify($notify)) {
            //获取回调订单信息
            $order_info = $pay->getInfo();
            if ($order_info['status']) {
                $pay_where = substr($order_info['out_trade_no'],0,2);
                $result = true;
                switch ($pay_where) {
                    case 'SP':
                        $result = $this->set_spend($order_info);
                        break;
                    case 'PF':
                        $result = $this->set_deposit($order_info);
                        break;
                    case 'AG':
                        $result = $this->set_agent($order_info);
                        break;
                    default:
                        exit('accident order data');
                        break;
                }
                if($result){
                    $pay->notifySuccess();
                }
            }else{
                $this->error("支付失败！");
            }
        }
    }

    /**
    *充值到游戏成功后修改充值状态和设置游戏币
    */
    private function set_spend($data){
        $spend = M('Spend',"tab_");
        $map['pay_order_number'] = $data['out_trade_no'];
        $d = $spend->where($map)->find();
        if(empty($d)){return false;}
        if($d['pay_status'] == 0){
            $data_save['pay_status'] = 1;
            $data_save['order_number'] = $data['trade_no'];
            $map_s['pay_order_number'] = $data['out_trade_no']; 
            $r = $spend->where($map_s)->save($data_save);
            if($r){
                //$game = new GameApi();
            }
        }
        else{
            return true;
        }
    }

    /**
    *充值平台币成功后的设置
    */
    private function set_deposit($data){
        $deposit = M('deposit',"tab_");
        $map['pay_order_number'] = $data['out_trade_no'];
        $d = $deposit->where($map)->find();
        if(empty($d)){return false;}
        if($d['pay_status'] == 0){
            $data_save['pay_status'] = 1;
            $data_save['order_number'] = $data['trade_no'];
            $map_s['pay_order_number'] = $data['out_trade_no'];
            $r = $deposit->where($map_s)->server($data_save);
            if($r){
                $user = M("user","tab_");
                $user->where("id=".$d['user_id'])->secInt("balance",$d['pay_amount']);
                $user->where("id=".$d['user_id'])->secInt("cumulative",$d['pay_amount']);
            }
            return true;
        }
        else{
            return true;
        }
    }

    /**
    *设置代充数据信息
    */
    private function set_agent($data){
        $agent = M("agent","tab_");
        $map['pay_order_number'] = $data['out_trade_no'];
        $d = $agent->where($map)->find();
        if(empty($d)){return false;}
        if($d['pay_status'] == 0){
            $data_save['pay_status'] = 1;
            $data_save['order_number'] = $data['trade_no'];
            $map_s['pay_order_number'] = $data['out_trade_no'];
            $r = $deposit->where($map_s)->server($data_save);
            if($r){
                $user = M("UserPlay","tab_");
                $map_play['user_id'] = $d['user_id'];
                $map_play['game_id'] = $d['game_id'];
                $user->where($map_play)->secInt("bind_balance",$d['amount']);
                //$user->where("id=".$d['user_id'])->secInt("cumulative",$d['pay_amount']);
            }
            return true;
        }
        else{
            return true;
        }
    }

}