<?php

namespace Home\Controller;
use OT\DataDictionary;
use User\Api\PromoteApi;
use Common\Api\PayApi;
use User\Api\UserApi;
/**
 * 前台首页控制器
 * 主要获取首页聚合数据
 */
class ChargeController extends BaseController {

    
    public function checkpwd(){
        $pid=session("promote_auth.pid");
        $user=new UserApi();
        $map['id']=$pid;
        $pro=M("promote","tab_")->where($map)->find();
        if($pro['second_pwd']===$this->think_ucenter_md5($_REQUEST['pwd'],UC_AUTH_KEY)){
            $this->ajaxReturn(array("status"=>1,"msg"=>"成功"));
        }
        else{
            $this->ajaxReturn(array("status"=>0,"msg"=>"失败"));
        }
    }
    public function checkSecond(){
        $pid=session("promote_auth.pid");
        $map['id']=$pid;
        $data=M('promote','tab_')->where($map)->find();
        if(empty($data['second_pwd'])){
            $this->ajaxReturn(array("status"=>0));
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
    public function think_ucenter_md5($str, $key = 'ThinkUCenter'){
    return '' === $str ? '' : md5(sha1($str) . $key);
}

 public function agent_pay()
    {
        if(IS_POST){
            $data = array();
            $real_amount = $_POST['amount'] * ($_POST['game_ratio']/100)*10;#计算折扣后的价格
            //var_dump($real_amount);exit();
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
            $data['zhekou']=$_POST['game_ratio'];
            /*实例化支付*/
            $pay = new PayApi();
            switch ($_POST['pay_type']) {
                case 'swiftpass':
                    $data['pay_way'] = 2;
                    //判断是否开启weifutong充值
                    if(pay_set_status('weixin')==0){
                        $this->error("网站未开启支付宝充值",'',1);
                        exit();
                    }
                    $data['fee'] = $data['real_amount'];
                    $all = $pay->weixin_pay($data,C('weixin'));
                    
                    $this->display();//
                    echo "<script> img_qrcode(".json_encode($all).") </script>";
                    break;
				case 'Jubaobarpay':
                    $data['pay_way'] = 3;
                    //判断是否开启jubaoyun充值
                    if(pay_set_status('jubaobar')==0){
                        $this->error("网站未开启聚宝云充值",'',1);
                        exit();
                    }
                    $data['fee'] = $data['real_amount'];
                    $pay->other_pay($data,C('jubaobar'));
                   
                    break;
                default:
                    $data['pay_way'] = 1;
                    //判断是否开启支付宝充值
                    if(pay_set_status('alipay')==0){
                        $this->error("网站未开启支付宝充值",'',1);
                        exit();
                    }
                    $pay->other_pay($data,C('alipay'));
                    break;
            }
            
        }
        else{
            $this->meta_title = "会长代充";
            $pro=M('Promote','tab_')->where(array('id'=>get_pid()))->find();
            $this->assign('pro',$pro);
            $this->display();
        }
      
    }

    public function beginpay(){
        
        
    }
    
    
    
    public function agent_pay_list($p=0){
        $map=array();
        if(isset($_REQUEST['user_account'])&&trim($_REQUEST['user_account'])){
            $map['user_account']=array('like','%'.$_REQUEST['user_account'].'%');
            unset($_REQUEST['user_account']);
        }
        if($_REQUEST['game_id']>0){
            $map['game_id']=$_REQUEST['game_id'];
        }
        if(!empty($_REQUEST['time-start'])&&!empty($_REQUEST['time-end'])){
            $map['create_time']  =  array('BETWEEN',array(strtotime($_REQUEST['time-start']),strtotime($_REQUEST['time-end'])+24*60*60-1));
            unset($_REQUEST['time-start']);unset($_REQUEST['time-end']);
        }
        $map['promote_id']=get_pid();
        //$map['status'] = 1;
        // $model = array(
        // 'm_name'=>'Agent',
        // 'fields'=>array('game_id','pay_order_number','amount','real_amount','pay_type','status','user_account','create_time'),
        // 'map'=>$map,
        // 'key' =>array('user_account'),
        // 'field_time'=>'create_time',
        // );
        $total = M("agent","tab_")->where(array('pay_status'=>1))->where($map)->sum('amount');
        $this->assign("total_amount",$total==null?0:$total);
        $this->lists('agent',$p,$map);
    }
}