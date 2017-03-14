<?php

namespace Callback\Controller;
use Think\Controller;
/**
 * 支付回调控制器
 * @author 小纯洁 
 */
class BaseController extends Controller {

    /**
    *充值到游戏成功后修改充值状态和设置游戏币
    */
    protected function set_spend($data){
        $spend = M('Spend',"tab_");
        $map['pay_order_number'] = $data['out_trade_no'];
        $d = $spend->where($map)->find();
        if(empty($d)){$this->record_logs("数据异常");return false;}
        if($d['pay_status'] == 0){
            $data_save['pay_status'] = 1;
            $data_save['order_number'] = $data['trade_no'];
            $map_s['pay_order_number'] = $data['out_trade_no']; 
            $r = $spend->where($map_s)->save($data_save);
            $this->set_ratio($d['pay_order_number']);

            if($r){
                //$game = new GameApi();
                return true;
            }else{
                $this->record_logs("修改数据失败");
            }
        }
        else{
            return true;
        }
    }

    /**
    *充值平台币成功后的设置
    */
    protected function set_deposit($data){
        $deposit = M('deposit',"tab_");
        $map['pay_order_number'] = $data['out_trade_no'];
        $d = $deposit->where($map)->find();
        if(empty($d)){return false;}
        if($d['pay_status'] == 0){
            $data_save['pay_status'] = 1;
            $data_save['order_number'] = $data['trade_no'];
            $map_s['pay_order_number'] = $data['out_trade_no'];
            $r = $deposit->where($map_s)->save($data_save);
            if($r == true){
                $user = M("user","tab_");
                $user->where("id=".$d['user_id'])->setInc("balance",$d['pay_amount']);
                $user->where("id=".$d['user_id'])->setInc("cumulative",$d['pay_amount']);
            }else{
                $this->record_logs("修改数据失败");
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
    protected function set_agent($data){
        $agent = M("agent","tab_");
        $map['pay_order_number'] = $data['out_trade_no'];
        $d = $agent->where($map)->find();
        if(empty($d)){return false;}
        if($d['pay_status'] == 0){
            $data_save['pay_status'] = 1;
            $data_save['order_number'] = $data['trade_no'];
            $map_s['pay_order_number'] = $data['out_trade_no'];
            $r = $agent->where($map_s)->save($data_save);
            if($r){
                $user = M("UserPlay","tab_");
                $map_play['user_id'] = $d['user_id'];
                $map_play['game_id'] = $d['game_id'];
                $user->where($map_play)->setInc("bind_balance",$d['amount']);
                //$user->where("id=".$d['user_id'])->secInt("cumulative",$d['pay_amount']);
                $pro_l=M('Promote','tab_')->where(array('id'=>$d['promote_id']))->setDec("pay_limit",$d['amount']);
            }else{
                $this->record_logs("修改数据失败");
            }
            return true;
        }
        else{
            return true;
        }
    }

    /**
    *游戏返利
    */
    protected function set_ratio($data){
        $map['pay_order_number']=$data;
        $spend=M("Spend","tab_")->where($map)->find();
        $reb_map['game_id']=$spend['game_id'];
        $rebate=M("Rebate","tab_")->where($reb_map)->find();
        if($rebate['ratio']==0||null==$rebate){
            return false;
        }else{
            if($rebate['money']>0&&$rebate['status']==1){
                if($spend['pay_amount']>=$rebate['money']){
                    $this->compute($spend,$rebate);
                }else{
                    return false;
                }
            }else{
                $this->compute($spend,$rebate);
            }
        }
    }

    //计算返利
    protected function compute($spend,$rebate){
        $user_map['user_id']=$spend['user_id'];
        $user_map['game_id']=$spend['game_id'];            
        $bind_balance=$spend['pay_amount']*($rebate['ratio']/100);
        $spend['ratio']=$rebate['ratio'];
        $spend['ratio_amount']=$bind_balance;
        M("rebate_list","tab_")->add($this->add_rebate_list($spend));
        $re=M("UserPlay","tab_")->where($user_map)->setInc("bind_balance",$bind_balance);
        return $re;
    }
    /**
    *返利记录
    */
    protected function add_rebate_list($data){
        $add['pay_order_number']=$data['pay_order_number'];
        $add['game_id']=$data['game_id'];
        $add['game_name']=$data['game_name'];
        $add['user_id']=$data['user_id'];
        $add['pay_amount']=$data['pay_amount'];
        $add['ratio']=$data['ratio'];
        $add['ratio_amount']=$data['ratio_amount'];
        $add['promote_id']=$data['promote_id'];
        $add['promote_name']=$data['promote_account'];
        $add['create_time']=time();
        return $add;
    }

    /**
    *日志记录
    */
    protected function record_logs($msg=""){
        \Think\Log::record($msg);
    }

}