<?php

namespace Home\Controller;
use OT\DataDictionary;
use User\Api\PromoteApi;
use Common\Api\PayApi;
/**
 * 前台首页控制器
 * 主要获取首页聚合数据
 */
class ChargeController extends BaseController {

    
    public function checkpwd(){
        $pid=session("promote_auth.pid");
        $user=new PromoteApi();
        $res = $user->verifyUser($pid,$_POST['pwd']);
        if($res){
            $this->ajaxReturn(array("status"=>1,"msg"=>"成功"));
        }
        else{
            $this->ajaxReturn(array("status"=>0,"msg"=>"失败"));
        }
    }

    public function checkAccount(){
       $game_id = $_POST['game_id'];
       $user_account = $_POST['user_account'];
       $map['game_id'] = $game_id;
       $map['user_account'] = $user_account;
       $map["promote_id"]   = session("promote_auth.pid");
       $data = M("UserPlay","tab_")->where($map)->find();
       //var_dump($data);
       if(empty($data)){
            $this->ajaxReturn(array("status"=>0));
       }else{
            $this->ajaxReturn(array("status"=>1));
       }
    }
    

    public function agent_pay()
    {
        if(IS_POST){
            $data = array();
            $real_amount = $_POST['amount'] * ($_POST['game_ratio']/100);#计算折扣后的价格
            #支付基本信息
            $data['options']    = "agent";
            $data['pay_type']   = $_POST['pay_type'];
            $data['order_no']   = "AG_".date('Ymd').date ( 'His' ).sp_random_string(4);
            $data['fee']        = $real_amount;
            $data['notice_url'] ='';
            #插入代充记录表数据
            $user = get_user_entity($_POST['user_account'],true);
            $data["user_id"]         = $user['id'];
            $data["user_account"]    = $user['account'];
            $data["user_nickname"]   = $user['nickname'];
            $data["game_id"]         = $_POST['game_id'];
            $data["game_appid"]      = $_POST['game_appid'];
            $data["game_name"]       = $_POST['game_name'];
            $data["promote_id"]      = session("promote_auth.pid");
            $data["promote_account"] = session('promote_auth.account');
            $data["title"]           = "代充记录";
            $data["amount"]          = $_POST['amount'];
            $data["real_amount"]     = $real_amount;
            $data["pay_way_num"]     = 0;

            /*实例化支付*/
            $pay = new PayApi();
            switch ($data['pay_way']) {
                case 'weixin':
                    # code...
                    break;
                default:
                   $pay->other_pay($data,C('PAYMENU.'.$data['pay_type']));
                    break;
            }
            
        }
        else{
            $this->meta_title = "会长代充";
            $this->display();
        }
      
    }

    public function beginpay(){
        
        
    }
    
    
    
    public function agent_pay_list($p=0){
        $map=array();
        if($_REQUEST['game_id']>0){
            $map['game_id']=$_REQUEST['game_id'];
        }
        $map['promote_id']=session("promote_auth.pid");
        //$map['status'] = 1;
        // $model = array(
        // 'm_name'=>'Agent',
        // 'fields'=>array('game_id','pay_order_number','amount','real_amount','pay_type','status','user_account','create_time'),
        // 'map'=>$map,
        // 'key' =>array('user_account'),
        // 'field_time'=>'create_time',
        // );
        $total = M("agent","tab_")->where(array('pomote_id'=>session('promote_auth.pid'),'status'=>1))->sum('amount');
        $this->assign("total_amount",$total);
        $this->lists('agent',$p,$map);
    }
}