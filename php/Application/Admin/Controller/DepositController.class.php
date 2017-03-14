<?php

namespace Admin\Controller;
use User\Api\UserApi as UserApi;

/**
 * 后台首页控制器
 * @author zxc <zuojiazi@vip.qq.com>
 */
class DepositController extends ThinkController {
	const model_name = 'Deposit';

    public function lists(){
    	if(isset($_REQUEST['user_account'])){
    		$map['user_account']=array('like','%'.$_REQUEST['user_account'].'%');
    		unset($_REQUEST['user_account']);
    	}
        if(!isset($_REQUEST['promote_id'])){

        }else if(isset($_REQUEST['promote_id']) && $_REQUEST['promote_id']==0){
            $map['promote_id']=array('elt',0);
            unset($_REQUEST['promote_id']);
            unset($_REQUEST['promote_name']);
        }elseif(isset($_REQUEST['promote_name'])&&$_REQUEST['promote_id']==-1){
            $map['promote_id']=get_promote_id($_REQUEST['promote_name']);
            unset($_REQUEST['promote_id']);
            unset($_REQUEST['promote_name']);
        }else{
            $map['promote_id']=$_REQUEST['promote_id'];
            unset($_REQUEST['promote_id']);
            unset($_REQUEST['promote_name']);
        }
    	if(isset($_REQUEST['time-start'])&&isset($_REQUEST['time-end'])){
            $map['create_time'] =array('BETWEEN',array(strtotime($_REQUEST['time-start']),strtotime($_REQUEST['time-end'])+24*60*60-1));
            unset($_REQUEST['time-start']);unset($_REQUEST['time-end']);
        }
        if(isset($_REQUEST['start'])&&isset($_REQUEST['end'])){
            $map['create_time'] =array('BETWEEN',array(strtotime($_REQUEST['start']),strtotime($_REQUEST['end'])+24*60*60-1));
            unset($_REQUEST['start']);unset($_REQUEST['end']);
        }
        if(isset($_REQUEST['pay_way'])){
            $map['pay_way']=$_REQUEST['pay_way'];
            unset($$_REQUEST['pay_way']);
        }
        if(isset($_REQUEST['pay_status'])){
            $map['pay_status']=$_REQUEST['pay_status'];
            unset($$_REQUEST['pay_status']);
        }
        $map1=$map;
        $map1['pay_status']=1;
        $total=D(self::model_name)->where($map1)->sum('pay_amount');
        if(isset($map['pay_status'])&&$map['pay_status']==0){
            $total=sprintf("%.2f",0);
        }else{
            $total=sprintf("%.2f",$total);
        }
		
        $this->assign('total',$total);
    	parent::lists(self::model_name,$_GET["p"],$map);
    }
}
