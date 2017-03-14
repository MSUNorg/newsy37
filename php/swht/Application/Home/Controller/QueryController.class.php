<?php

namespace Home\Controller;
use OT\DataDictionary;
use User\Api\PromoteApi;

/**
 * 前台首页控制器
 * 主要获取首页聚合数据
 */
class QueryController extends BaseController {

    public function recharge($p=0)
    {
        $map['promote_id'] = session("promote_auth.pid");
        $map['pay_status'] = 1;
        $total = M('spend',"tab_")->where($map)->sum('pay_amount');
        $this->assign("total_amount",$total); 
        $this->meta_title = "用户充值";
        $this->lists("Spend",$p,$map);
    }

    public function register($p=0){

        $map['promote_id'] = session("promote_auth.pid");
        //$map['pay_status'] = 1; 
        $this->lists("User",$p,$map);
    }

    /**
    *我的收益
    */
    public function my_earning($p=1){
        $model=array(
            'm_name'=>'settlement',
            'map'   =>array('promote_id'=>array('in',PROMOTE_ID)),
            'order' =>'spend_time',
            'template_list'=>'my_earning'
        );
        $user = A('User','Event');
        //$user->shou_list($model,$p);
        $this->display();
    }

    /**
    *账单查询
    */
    public function bill(){
          $pid=M("promote","tab_")->where(array('parent_id'=>PROMOTE_ID))->select();
            for ($i=0; $i <count($pid) ; $i++) { 
                $parent_id[]=$pid[$i]['id'];
            }
            $ppid=implode(',',$parent_id);
        $map['promote_id']=array('in',array(PROMOTE_ID,$ppid));

        if(isset($_REQUEST['game_name'])&&!empty($_REQUEST['game_name'])){
            $map['game_id']=$_REQUEST['game_name'];
        }
        if(isset($_REQUEST['ppid'])&&!empty($_REQUEST['ppid'])){
            $map['promote_id']=$_REQUEST['ppid'];
        }
            if(isset($_REQUEST['time-start']) && isset($_REQUEST['time-end']) && !empty($_REQUEST['time-start']) && !empty($_REQUEST['time-end'])){
            $map['spend_time']  =  array('BETWEEN',array(strtotime($_REQUEST['time-start']),strtotime($_REQUEST['time-end'])));
            unset($_REQUEST['time-start']);unset($_REQUEST['time-end']);
        }

        // $model=array(
        //     'm_name'=>'settlement',
        //     'map'   =>$map,
        //     'order' =>'spend_time',
        //     'template_list'=>'bill'
        // );

        // $user = A('User','Event');
        // $user->shou_list($model,$p);
        $this->display();
    }

}