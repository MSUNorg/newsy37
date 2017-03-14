<?php

namespace Admin\Controller;
use User\Api\UserApi as UserApi;

/**
 * 后台首页控制器
 * @author zxc
 */
class BindSpendController extends ThinkController {
	const model_name = 'BindSpend';

    public function lists(){
    	if(isset($_REQUEST['user_account'])){
    		$map['user_account']=array('like','%'.$_REQUEST['user_account'].'%');
    		unset($_REQUEST['user_account']);
    	}
    	if(isset($_REQUEST['time-start'])&&isset($_REQUEST['time-end'])){
            $map['pay_time'] =array('BETWEEN',array(strtotime($_REQUEST['time-start']),strtotime($_REQUEST['time-end'])+24*60*60-1));
            unset($_REQUEST['time-start']);unset($_REQUEST['time-end']);
        }
        if(isset($_REQUEST['start'])&&isset($_REQUEST['end'])){
            $map['pay_time'] =array('BETWEEN',array(strtotime($_REQUEST['start']),strtotime($_REQUEST['end'])+24*60*60-1));
            unset($_REQUEST['start']);unset($_REQUEST['end']);
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
        if(isset($_REQUEST['game_name'])){
        	if($_REQUEST['game_name']=='全部'){
        		unset($_REQUEST['game_name']);
        	}else{
        		$map['game_name']=$_REQUEST['game_name'];
        	}
        	unset($_REQUEST['game_name']);
        }
        $map1=$map;
        $map1['pay_status']=1;
        $total=D(self::model_name)->where($map1)->sum('pay_amount');
        $total=sprintf("%.2f",$total);
        $this->assign('total',$total);
        $map['order']='pay_time DESC';
    	parent::lists(self::model_name,$_GET["p"],$map);
    }
}
