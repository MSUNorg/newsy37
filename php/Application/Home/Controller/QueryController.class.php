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
        $pro_id=get_prmoote_chlid_account(session('promote_auth.pid'));
        foreach ($pro_id as $key => $value) {
            $pro_id1[]=$value['id'];
        }
        if(!empty($pro_id1)){
            $pro_id2=array_merge($pro_id1,array(get_pid()));
        }else{
            $pro_id2=array(get_pid());
        }
        $map['promote_id'] = array('in',$pro_id2);
        if(isset($_REQUEST['user_account'])&&trim($_REQUEST['user_account'])){
            $map['user_account']=array('like','%'.$_REQUEST['user_account'].'%');
            unset($_REQUEST['user_account']);
        }
        if(isset($_REQUEST['game_appid'])&&$_REQUEST['game_appid']!=''){
            $map['game_appid']=$_REQUEST['game_appid'];
        }
        if($_REQUEST['promote_id']>0){
            $map['promote_id']=$_REQUEST['promote_id'];
        }
        if(!empty($_REQUEST['time-start'])&&!empty($_REQUEST['time-end'])){
            $map['pay_time']  =  array('BETWEEN',array(strtotime($_REQUEST['time-start']),strtotime($_REQUEST['time-end'])+24*60*60-1));
            unset($_REQUEST['time-start']);unset($_REQUEST['time-end']);
        }
        $map['pay_status'] = 1;
        $map['is_check']=array('neq',2);
        $total = M('spend',"tab_")->where($map)->sum('pay_amount');
        $this->assign("total_amount",$total); 
        $this->meta_title = "用户充值";
        $this->lists("Spend",$p,$map);
    }

    public function register($p=0){
        $pro_id=get_prmoote_chlid_account(session('promote_auth.pid'));
        foreach ($pro_id as $key => $value) {
            $pro_id1[]=$value['id'];
        }
        if(!empty($pro_id1)){
            $pro_id2=array_merge($pro_id1,array(get_pid()));
        }else{
            $pro_id2=array(get_pid());
        }
        $map['promote_id'] = array('in',$pro_id2);
        if(isset($_REQUEST['account'])&&trim($_REQUEST['account'])){
            $map['account']=array('like','%'.$_REQUEST['account'].'%');
            unset($_REQUEST['user_account']);
        }
        if(isset($_REQUEST['game_appid'])&&$_REQUEST['game_appid']!=0){
            $map['game_appid']=$_REQUEST['game_appid'];
        }
        if($_REQUEST['promote_id']>0){
            $map['promote_id']=$_REQUEST['promote_id'];
        }
        if(!empty($_REQUEST['time-start'])&&!empty($_REQUEST['time-end'])){
            $map['register_time']  =  array('BETWEEN',array(strtotime($_REQUEST['time-start']),strtotime($_REQUEST['time-end'])+24*60*60-1));
            // unset($_REQUEST['time-start']);unset($_REQUEST['time-end']);
        }
        if(!empty($_REQUEST['start'])&&!empty($_REQUEST['end'])){
            $map['register_time']  =  array('BETWEEN',array(strtotime($_REQUEST['start']),strtotime($_REQUEST['end'])+24*60*60-1));
            // unset($_REQUEST['time-start']);unset($_REQUEST['time-end']);
        }
        $map['is_check']=array('neq',2);
        $this->lists("User",$p,$map);
    }


    /**
    *我的对账单
    */
    public function bill(){
        $map['promote_id']=get_pid();
        if(isset($_REQUEST['bill_number'])&&!empty($_REQUEST['bill_number'])){
            $map['bill_number']=$_REQUEST['bill_number'];
        }
        if(isset($_REQUEST['game_id'])&&!empty($_REQUEST['game_id'])){
            $map['game_id']=$_REQUEST['game_id'];
        }
        if(!empty($_REQUEST['timestart'])&&!empty($_REQUEST['timeend'])){
            $map['bill_start_time'] = array('egt',strtotime($_REQUEST['timestart']));
            $map['bill_end_time'] = array('elt',strtotime($_REQUEST['timeend'])+24*3600-1);
            // unset($_REQUEST['time-start']);unset($_REQUEST['time-end']);
        }  
        $model=array(
            'm_name'=>'bill',
            'map'   =>$map,
            'template_list'=>'bill'
        );

        $user = A('User','Event');
        $user->bill_list($model,$_GET['p']);
    }


    /**
    *我的结算
    */
    public function my_earning($p=1){
       $pro_map['id']=get_pid();
       $pro=M("promote","tab_")->where($pro_map)->find();
       if($pro['parent_id']==0){
            $map['promote_id']=get_pid();
            if(isset($_REQUEST['settlement_number'])&&!empty($_REQUEST['settlement_number'])){
                $map['settlement_number']=$_REQUEST['settlement_number'];
            }
            if(isset($_REQUEST['game_id'])&&!empty($_REQUEST['game_id'])){
                $map['game_id']=$_REQUEST['game_id'];
            }
            if(isset($_REQUEST['pattern'])&&$_REQUEST['pattern']!=''){
                $map['pattern']=$_REQUEST['pattern'];
            }
            if(isset($_REQUEST['ti_status'])&&$_REQUEST['ti_status']!=''){
                $map['ti_status']=$_REQUEST['ti_status'];
            }
            $model=array(
                'm_name'=>'settlement',
                'map'   =>$map,
                'template_list'=>'my_earning'
            );
        }else{
            $model=array(
                'm_name'=>'son_settlement',
                'map'   =>$map,
                'template_list'=>'my_earning'
            );
        }
        $user = A('User','Event');
        $this->assign("parent_id",$pro['parent_id']);
        $user->shou_list($model,$p);
    }

    /**
    *子渠道结算单
    */
    public function son_earning_($p=1){
        if (PLEVEL == 0) {
            if(isset($_REQUEST['timestart']) && isset($_REQUEST['timeend']) && !empty($_REQUEST['timestart']) && !empty($_REQUEST['timeend'])){
                $starttime = strtotime($_REQUEST['timestart']);
                $endtime = strtotime($_REQUEST['timeend'])+24*60*60-1;
                $this->assign('starttime',$starttime);
                $this->assign('endtime',$endtime);
                $map[0]['register_time']  =  array('BETWEEN',array($starttime,$endtime));               
                $map[1]['pay_time']  =  array('BETWEEN',array($starttime,$endtime));
                unset($_REQUEST['timestart']);unset($_REQUEST['timeend']);
                
                $map[1]['parent_id'] = $map[0]['u.parent_id']=PID;
                if(isset($_REQUEST['ch_promote_id'])&&!empty($_REQUEST['ch_promote_id'])){
                    $map[1]['s.promote_id'] = $map[0]['u.promote_id']=$_REQUEST['ch_promote_id'];
                }
                $model = array(
                    'title'  => '子渠道结算单',
                    'template_list' =>'son_earning',
                );
                $user = A('User','Event');
                $user->check_bill($model,$p,$map);
            } else {
                $this->display(); 
            }           
        } else {
            $model = array(
                'm_name' => 'SonSettlement',
                'order'  => 'id ',
                'title'  => '结算账单',
                'template_list' =>'son_earning',
            );
            
            $user = A('User','Event');
            $user->money_list($model,$p);
        }        
    }

    //子渠道结算单
    public function son_list($p=0){
        if(isset($_REQUEST['settlement_number'])&&!empty($_REQUEST['settlement_number'])){
                $map['settlement_number']=trim($_REQUEST['settlement_number']);
            }
        if(isset($_REQUEST['game_id'])&&!empty($_REQUEST['game_id'])){
                $map['game_id']=$_REQUEST['game_id'];
            }
        if(isset($_REQUEST['pattern'])&&$_REQUEST['pattern']!=''){
                $map['pattern']=$_REQUEST['pattern'];
            }
        if(!empty($_REQUEST['time-start'])&&!empty($_REQUEST['time-end'])){
            $map['settlement_start_time'] = array('egt',strtotime($_REQUEST['time-start']));
            $map['settlement_end_time'] = array('elt',strtotime($_REQUEST['time-end'])+24*60*60-1);
           }  
          $model = array(
                'm_name' => 'SonSettlement',
                'order'  => 'id',
                'title'  => '结算账单',
                'template_list' =>'son_list',
            );
            $zi_p=get_zi_promote_id(PID);
            $map['promote_id']=array('in',"$zi_p");
            $user = A('User','Event');
            $user->money_list($model,$p,$map);
    }
    /**
    *子渠道结算单
    */
    public function son_earning($p=0){
        if (PLEVEL == 0) {
            if(isset($_REQUEST['timestart']) && isset($_REQUEST['timeend']) && !empty($_REQUEST['timestart']) && !empty($_REQUEST['timeend'])){
                $starttime = strtotime($_REQUEST['timestart']);
                $endtime = strtotime($_REQUEST['timeend'])+24*60*60-1;
                $this->assign('starttime',$starttime);
                $this->assign('endtime',$endtime);
                $mapp['u.register_time']  =  array('BETWEEN',array($starttime,$endtime));               
                $map['s.pay_time']  =  array('BETWEEN',array($starttime,$endtime));
                $map['s.pay_status']  =  1;
                $map['s.sub_status']  =  0;
                unset($_REQUEST['timestart']);unset($_REQUEST['timeend']);
                // $map[1]['parent_id'] =PID;
                if(isset($_REQUEST['ch_promote_id'])&&!empty($_REQUEST['ch_promote_id'])){
                    $map['s.promote_id']=$mapp['u.promote_id']=$_REQUEST['ch_promote_id'];
                }else{
                     $map['s.promote_id']=$mapp['u.promote_id']=array('in',get_zi_promote_id(PID));
                }
                $model = array(
                    'fields' =>'sum(s.pay_amount) as total_amount,s.promote_account,s.promote_id,s.game_name,s.game_id,s.sub_status',
                    'm_name' =>'Spend',
                    'title'  => '子渠道结算单',
                    'template_list' =>'son_earning',
                    'join'      =>'tab_apply on tab_Spend.game_id=tab_apply.game_id and tab_Spend.promote_id=tab_apply.promote_id',
                    'group' =>'s.promote_id,s.game_id',
                );
                $mmap=array($mapp,$map);
                $user = A('User','Event');
                $user->check_bill_($model,$p,$mmap);
            } else {
                $this->display(); 
            }           
        } else {
              if(isset($_REQUEST['settlement_number'])&&!empty($_REQUEST['settlement_number'])){
                $map['settlement_number']=trim($_REQUEST['settlement_number']);
            }
            if(isset($_REQUEST['game_id'])&&!empty($_REQUEST['game_id'])){
                $map['game_id']=$_REQUEST['game_id'];
            }
            if(isset($_REQUEST['pattern'])&&$_REQUEST['pattern']!=''){
                $map['pattern']=$_REQUEST['pattern'];
            }
            if(!empty($_REQUEST['time-start'])&&!empty($_REQUEST['time-end'])){
            $map['settlement_start_time'] = array('egt',strtotime($_REQUEST['time-start']));
            $map['settlement_end_time'] = array('elt',strtotime($_REQUEST['time-end'])+24*60*60-1);
        }  
            $model = array(
                'm_name' => 'SonSettlement',
                'order'  => 'id ',
                'title'  => '结算账单',
                'template_list' =>'son_earning',
            );
            $map['promote_id']=PID;
            $user = A('User','Event');
            $user->money_list($model,$p,$map);
        }        
    }
    public function generatesub() {
        $data = $_REQUEST;  
        $data['settlement_number'] = 'js_'.date('YmdHis',time()).rand(100,999);
        $data['create_time'] =  time();
        if($data['cooperation']=='CPS'){
            $data['pattern']=0;
        }else{
            $data['pattern']=1;
        }
        unset($data['cooperation']);
        if ($data['pattern'] == 0) {
            $cps = $data['ratio'] = $_REQUEST['cp'];
            $data['jie_money'] = ($cps*$data['sum_money'])/100;
        } elseif ($data['pattern'] == 1) {
            $cpa = $data['money'] = $_REQUEST['cp'];
            $data['jie_money'] = $cpa*$data['reg_number'];
        } else {
            $this->error("操作失败",'',true);        
        }
        $start = $data['settlement_start_time'];
        $end = $data['settlement_end_time'];
        $map0['register_time'] = array('BETWEEN',array($start,$end));
        $map1['pay_time'] = array('BETWEEN',array($start,$end));
        $map1['sub_status'] = $map0['sub_status'] = 0;
        $map1['game_id'] = $map0['game_id'] = $data['game_id'];
        $map1['promote_id'] = $map0['promote_id'] = $data['promote_id'];
        $map0['fgame_id']=$data['game_id'];
        $map1['game_id']=$data['game_id'];
        $partake = array('sub_status'=>1);
        $user = M('User',"tab_");
        $spend = M('Spend',"tab_");
        $bill = M('SonSettlement',"tab_");
        $flag1 = $user->where($map0)->save($partake);  
        $flag2 = $spend->where($map1)->save($partake);
        if ($flag1===0&&$flag2===0) {
            $this->error('请勿重复操作！');
        }else if(!$flag1&&!$flag2){
            $this->error('生成结算单失败！');
        }       
        if( $bill->add($data)){
            $user->commit();$spend->commit();$bill->commit();
            $this->success('生成结算单成功！',U('son_earning?success=1',array('timestart' => date('Y-m-d',$start),'timeend' => date('Y-m-d',$end))),true);
        } else {
            $user->rollback();$spend->rollback();$bill->rollback();
            $this->error('生成结算单失败！！!',U('son_earning?fall=1',array('timestart' => date('Y-m-d',$start),'timeend' => date('Y-m-d',$end))),true);
        }
        
    }



    //申请提现
    public function apply_withdraw($id){
        $map['id']=$id;
        $with= M("withdraw","tab_");        
        $seet=M("settlement","tab_")->where($map)->find();
        $with_map['settlement_number']=$seet['settlement_number'];
        $fid=$with->where($with_map)->find();
        if($fid==null){
        $add['settlement_number']=$seet['settlement_number'];
        $add['sum_money']=$seet['sum_money'];
        $add['promote_id']=$seet['promote_id'];
        $add['promote_account']=$seet['promote_account'];
        $add['create_time']=time();
        $add['status']=0;
        $with->add($add);
        M("settlement","tab_")->where($map)->save(array('ti_status'=>0));
        echo json_encode(array("status"=>1));  
        }else{
         echo json_encode(array("status"=>0));  
        }

    }
}