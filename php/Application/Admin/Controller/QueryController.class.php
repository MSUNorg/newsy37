<?php

namespace Admin\Controller;
use User\Api\UserApi as UserApi;

/**
 * 推广查询控制器
 * @author 王贺
 */
class QueryController extends ThinkController {
	/**
    *推广对账
    */
      public function bill11($p = 0) {
        $group = I('group',1);
        $this->assign('group',$group);
        if ($group == 1) {
            $model = array(
                'title'  => '渠道对账',
                'template_list' =>'bill',
            );
            if(!empty($_REQUEST['timestart']) && !empty($_REQUEST['timeend'])){
                if(strlen($_REQUEST['timeend'])>10){
                   $_REQUEST['timeend'] =substr($_REQUEST['timeend'], 0,10);
                }
                $starttime = strtotime($_REQUEST['timestart']);
                $endtime = strtotime($_REQUEST['timeend'])+24*60*60-1;
                $this->assign('start',$starttime);
                $this->assign('end',$endtime);
                $user_map['register_time']  =  array('BETWEEN',array($starttime,$endtime));               
                $spend_map['pay_time']  =  array('BETWEEN',array($starttime,$endtime));
                unset($_REQUEST['timestart']);unset($_REQUEST['timeend']);
                
                if (!empty($_REQUEST['promote_account']) && $_REQUEST['promote_account'] !== '全部') {
                    $promote_account = $_REQUEST['promote_account'];
                    $promote = D('Promote');
                    $account = $promote->field("id")->where("account like '%$promote_account%' ")->select();
                    
                    if (empty($account) || !is_array($account)) {
                        $this->error('该渠道不存在，请重新填写');
                    }
                    $pids = array();
                    foreach ($account as $v) {
                        $pids[] = $v['id'];
                    }
                    $account = '';
                    $account = D('Promote')->field('id')->where(array("parent_id"=>array('in',$pids)))->select();
                    if (!empty($account) && is_array($account))
                        foreach ($account as $v) {
                            $pids[] = $v['id'];
                        }
                    
                    $spend_map['promote_id'] = $user_map['promote_id'] = array('in',$pids);
                    
                    $this->assign('promote_account',$promote_account);
                    unset($_REQUEST['promote_account']);
                } else {
                    $spend_map['promote_id'] = $user_map['promote_id'] = array('gt',0);
                }
                 $extends = array(
                'user_map'  => $user_map,
                'spend_map' =>$spend_map,
            );
                $user = A('Spend','Event');
                $user->check_bill111($model,$p,$extends);               
            } else {
                $this->assign('model', $model);
                $this->display($model['template_list']);
            }
        }
        if ($group == 2) {
            
            $model = array(
                'm_name' => 'Bill',
                'order'  => 'id desc',
                'title'  => '渠道对账',
                'template_list' =>'bill',
            );
            $user = A('Bill','Event');
            $user->bill_list($model,$p,$map);
        }       
    }
       public function bill($p = 0) {
        $group = I('group',1);
        $this->assign('group',$group);
        if ($group == 1) {
            $model = array(
                'title'  => '渠道对账',
                'template_list' =>'bill',
            );
            if(!empty($_REQUEST['timestart']) && !empty($_REQUEST['timeend'])){
                $starttime = strtotime($_REQUEST['timestart']);
                $endtime = strtotime($_REQUEST['timeend'])+24*60*60-1;
                $this->assign('start',$starttime);
                $this->assign('end',$endtime);
                $map[0]['register_time']  =  array('BETWEEN',array($starttime,$endtime));               
                $map[1]['pay_time']  =  array('BETWEEN',array($starttime,$endtime));
                unset($_REQUEST['timestart']);unset($_REQUEST['timeend']);
                
                if (!empty($_REQUEST['promote_account']) && $_REQUEST['promote_account'] !== '全部') {
                    $promote_account = $_REQUEST['promote_account'];
                    $promote = D('Promote');
                    $account = $promote->field("id")->where("account like '%$promote_account%' ")->select();
                    
                    if (empty($account) || !is_array($account)) {
                        $this->error('该渠道不存在，请重新填写');
                    }
                    $pids = array();
                    foreach ($account as $v) {
                        $pids[] = $v['id'];
                    }
                    $account = '';
                    $account = D('Promote')->field('id')->where(array("parent_id"=>array('in',$pids)))->select();
                    if (!empty($account) && is_array($account))
                        foreach ($account as $v) {
                            $pids[] = $v['id'];
                        }
                    
                    $map[1]['s.promote_id'] = $map[0]['u.promote_id'] = array('in',$pids);
                    
                    $this->assign('promote_account',$promote_account);
                    unset($_REQUEST['promote_account']);
                } else {
                    $map[1]['s.promote_id'] = $map[0]['u.promote_id'] = array('gt',0);
                }
                
                $user = A('Spend','Event');
                $user->check_bill($model,$p,$map);               
            } else {
                $this->assign('model', $model);
                $this->display($model['template_list']);
            }
        }
        if ($group == 2) {
            
            $model = array(
                'm_name' => 'Bill',
                'order'  => 'id desc',
                'title'  => '渠道对账',
                'template_list' =>'bill',
            );
            $user = A('Bill','Event');
            $user->bill_list($model,$p,$map);
        }       
    }
    
 // 生成对账单
    
    public function generatebill($model="Bill") {
        $ids    =   I('request.ids');
        if(empty($ids)){
            $this->error('请选择要操作的数据');
        }
        
        if (is_numeric($ids)) {
            $start = $_REQUEST['start'];
            $end = $_REQUEST['end'];
            $promote_id=$_REQUEST['promote_id'];
            $data = array(
                'bill_number' => 'dz_'.date('YmdHis',time()).rand(100,999),
                'bill_time' => date('Y年m月d日',$start).'---'.date('Y年m月d日',$end),
                'promote_id' => $_REQUEST['promote_id'],
                'promote_account' => get_promote_name($_REQUEST['promote_id']),
                'game_id' => $_REQUEST['game_id'],
                'game_name' => get_game_name($_REQUEST['game_id']),
                'total_money' => $_REQUEST['total_money'],
                'total_number' => $_REQUEST['total_number'],
                'bill_start_time' => $start,
                'bill_end_time' => $end, 
                'create_time' => time(),
            );
            $user_map['register_time']=array('BETWEEN',array($start,$end));
            $user_map['is_check']=1;
            $user_map['fgame_id']=$_REQUEST['game_id'];
            $user_map['promote_id']=$promote_id;
            // $user_map['parent_id']=$promote_id;
            // if(isset($_REQUEST['total_number'])){            
                $set_user=M("user","tab_")->where($user_map)->setField(array("is_check"=>3));
                $user_map['is_check']=2;
                $set_user=M("user","tab_")->where($user_map)->setField(array("is_check"=>4));
                unset($user_map['promote_id']);
                $user_map['parent_id']=$promote_id;
                $user_map['is_check']=1;
                $set_user_partent=M("user","tab_")->where($user_map)->setField(array("is_check"=>3));
                $user_map['is_check']=2;
                $set_user=M("user","tab_")->where($user_map)->setField(array("is_check"=>4));
            // }else{
                // $user_map['is_check']=3;
                // $set_user=M("user","tab_")->where($user_map)->setField(array("is_check"=>4));//不参与已对账
                // unset($user_map['promote_id']);
                // $user_map['parent_id']=$promote_id;
                // $set_user_partent=M("user","tab_")->where($user_map)->setField(array("is_check"=>4));
            // }

            $spend_map['promote_id']=$promote_id;
            $spend_map['game_id']=$_REQUEST['game_id'];
            $spend_map['pay_time']=array('BETWEEN',array($start,$end));
            $spend_map['is_check']=1;

            if(get_zi_promote_id($promote_id)==0){
            $zi_map['promote_id']=0;
            }else{
            $zi_map['promote_id']=array("in",get_zi_promote_id($promote_id));
            }
            $zi_map['game_id']=$_REQUEST['game_id'];
            $zi_map['pay_time']=array('BETWEEN',array($start,$end));
            $zi_map['is_check']=1;
            $set_pro=M("spend","tab_")->where($spend_map)->setField(array("is_check"=>3));
            $spend_map['is_check']=2;
            $set_pro=M("spend","tab_")->where($spend_map)->setField(array("is_check"=>4));
            $set_pro=M("spend","tab_")->where($zi_map)->setField(array("is_check"=>3));
            $zi_map['is_check']=2;
            $set_pro=M("spend","tab_")->where($zi_map)->setField(array("is_check"=>4));
            if(M("bill","tab_")->add($data)){
             $this->success('生成对账单成功！',U('bill?group=1&success=1',array('timestart' => date('Y-m-d',$start),'timeend' => date('Y-m-d',$end))));
            }else{
              $this->error('生成对账单失败！！!',U('bill?group=1&fall=1',array('timestart' => date('Y-m-d',$start),'timeend' => date('Y-m-d',$end))));
            }

        } elseif (is_array($ids)) {

            foreach ($ids as $k=>$v) {
                $query = explode(',',$v);
                $start = $query[0];
                $end = $query[1];
                $promote_id=$query[2];
                $tempdata = array(
                    'bill_number' => 'dz_'.date('YmdHis',time()).rand(100,999),
                    'bill_time' => date('Y年m月d日',$start).'---'.date('Y年m月d日',$end),
                    'promote_id' => $query[2],
                    'promote_account' => get_promote_name($query[2]),
                    'game_id' => $query[3],
                    'game_name' => get_game_name($query[3]),
                    'total_money' => $query[4],
                    'total_number' => $query[5],
                    'bill_start_time' => $start,
                    'bill_end_time' => $end, 
                    'create_time' => time(),
                );
            $data[]=$tempdata;
            $user_map['register_time']=array('BETWEEN',array($start,$end));
            $user_map['is_check']=1;
            $user_map['fgame_id']=$query[3];
            $user_map['promote_id']=$promote_id;


            $par_map['register_time']=array('BETWEEN',array($start,$end));
            $par_map['is_check']=1;
            $par_map['fgame_id']=$query[3];
            $par_map['parent_id']=$promote_id;

            // if(!empty($query[5])){   
                // $set_user=M("user","tab_")->where($user_map)->setField(array("is_check"=>3));
                // $set_user_partent=M("user","tab_")->where($par_map)->setField(array("is_check"=>3));
                $set_user=M("user","tab_")->where($user_map)->setField(array("is_check"=>3));
                $user_map['is_check']=2;
                $set_user=M("user","tab_")->where($user_map)->setField(array("is_check"=>4));
                $set_user_partent=M("user","tab_")->where($par_map)->setField(array("is_check"=>3));
                $par_map['is_check']=2;
                $set_user=M("user","tab_")->where($par_map)->setField(array("is_check"=>4));
            // }else{               
                // $user_map['is_check']=3;
                // $set_user=M("user","tab_")->where($user_map)->setField(array("is_check"=>4));//不参与已对账
                // $par_map['is_check']=3;
                // $set_user_partent=M("user","tab_")->where($user_map)->setField(array("is_check"=>4));
            // }

            $spend_map['promote_id']=$promote_id;
            $spend_map['game_id']=$query[3];
            $spend_map['pay_time']=array('BETWEEN',array($start,$end));
            $spend_map['is_check']=1;
            
            $sp_map['promote_id']=$promote_id;
            $sp_map['game_id']=$query[3];
            $sp_map['pay_time']=array('BETWEEN',array($start,$end));
            $sp_map['is_check']=1;
            if(get_zi_promote_id($promote_id)==0){
            $sp_map['promote_id']=0;
            }else{
            $sp_map['promote_id']=array("in",get_zi_promote_id($promote_id));
            }

            $set_pro=M("spend","tab_")->where($spend_map)->setField(array("is_check"=>3));
            $spend_map['is_check']=2;
            $set_pro=M("spend","tab_")->where($spend_map)->setField(array("is_check"=>4));
            $set_pro=M("spend","tab_")->where($sp_map)->setField(array("is_check"=>3));
            $sp_map['is_check']=2;
            $set_pro=M("spend","tab_")->where($sp_map)->setField(array("is_check"=>4));
            }
            $add=M("bill","tab_")->addAll($data);
            if($add){
              $this->success('生成对账单成功！',U('bill?group=1&success=1',array('timestart' => date('Y-m-d',$start),'timeend' => date('Y-m-d',$end))));
            }else{
              $this->error('生成对账单失败！！!',U('bill?group=1&fall=1',array('timestart' => date('Y-m-d',$start),'timeend' => date('Y-m-d',$end))));
             }

        }       
    }
    

    public function settlement($p=0) {
        $group = I('group',1);
        $this->assign('group',$group);
        if ($group == 1) {
                                    
            $model = array(
                'm_name' => 'Bill',
                'order'  => 'id ',
                'title'  => '渠道结算',
                'template_list' =>'settlement',
                'group' =>'game_id',
            );
            
            $user = A('Bill','Event');
            $user->show_bill($model,$p,$map);
        }
        if ($group == 2) {            
            
            $model = array(
                'm_name' => 'settlement',
                'order'  => 'id ',
                'title'  => '结算账单',
                'template_list' =>'settlement',
            );
            
            $user = A('Bill','Event');
            $user->money_list($model,$p,$map);
        }
    }
    public function cpsettlement($p=0) {
        $group = I('group',1);
        $this->assign('group',$group);
        if(isset($_REQUEST['timestart'])&&$_REQUEST['timestart']!=''&&$_REQUEST['group']==1){
            $starttime=strtotime($_REQUEST['timestart'].'-01');
             if($starttime>=strtotime(date('Y-m-01'))){
                 $this->error('时间选择不正确','',1);exit;
             }
            $endtime=strtotime($_REQUEST['timestart']."+1 month -1 day")+24*3600-1;
            if(isset($_REQUEST['game_name'])&&$_REQUEST['game_name']!='全部'){
                $map['g.game_name']=$_REQUEST['game_name'];
            }
            if(isset($_REQUEST['selle_status'])){
                if($_REQUEST['selle_status']=="未结算"){
                    $map['s.selle_status']=0;
                }else if($_REQUEST['selle_status']=="已结算"){
                    $map['s.selle_status']=1;
                }
            }
            $map['s.pay_status']=1;
            $map['pay_time']=array('BETWEEN',array($starttime,$endtime));
            $model = array(
                'm_name' => 'Spend as s',
                'order'  => 's.id',
                'title'  => '渠道结算',
                'group'  => 'g.developers,g.id',
                'fields'  =>'sum(s.pay_amount) as total,s.selle_ratio,s.id,g.developers,s.selle_status,g.id as gid,g.game_name,s.pay_status,s.pay_amount',
                'template_list' =>'cpsettlement',
            );
            
            $user = A('Spend','Event');
            $user->cpsettl_list($model,$p,$map);
        }else if($_REQUEST['group']==2){
            if(isset($_REQUEST['timestart'])&&$_REQUEST['timestart']!=''){
                $starttime=strtotime($_REQUEST['timestart'].'-01');
                if($starttime>=strtotime(date('Y-m-01'))){
                    $this->error('时间选择不正确','',1);exit;
                }
                $starttime=strtotime($_REQUEST['timestart'].'-01');
                $endtime=strtotime($_REQUEST['timestart']."+1 month -1 day")+24*3600-1;
                $map['pay_time']=array('BETWEEN',array($starttime,$endtime));  
            }
            $map['s.pay_status']=1;
            $map['s.selle_status']=1;//已结算
            $model = array(
                'm_name' => 'Spend as s',
                'order'  => 's.id',
                'title'  => '渠道结算',
                'group'  => 'g.developers,g.id',
                'fields'  =>'sum(s.pay_amount) as total, s.id,s.selle_ratio,g.developers,s.selle_status,s.selle_time,g.id as gid,g.game_name,s.pay_status,s.pay_amount',
                'template_list' =>'cpsettlement',
            );
            
            $user = A('Spend','Event');
            $user->cpsettl_list($model,$p,$map);
        }else{
            $this->meta_title = '渠道结算列表';
            $this->display();
        }
    }
    public function generatesettlement() {
        $request    =   I('request.ids');
        if(empty($request)){
            $this->error('请选择要操作的数据');
        }
        if (is_array($request)) {
            foreach($request as $v) {
                $query = explode(',',$v);
                $ids[] = $query[0];
                $_REQUEST[$query[0]]['ids']=$query[0];
                $_REQUEST[$query[0]]['cooperation']=$query[1];
                $_REQUEST[$query[0]]['cps_ratio']=$query[2];
                $_REQUEST[$query[0]]['cpa_price']=$query[3];
            } 
            unset($_REQUEST['ids']);
        } elseif (is_numeric($request)) {
            $id = $ids[] = $request;
            $_REQUEST[$id]['ids']=$id;
            $_REQUEST[$id]['cooperation']=$_REQUEST['cooperation'];
            $_REQUEST[$id]['cps_ratio']=$_REQUEST['cps_ratio'];
            $_REQUEST[$id]['cpa_price']=$_REQUEST['cpa_price'];
        } else {
            $this->error('参数有误！！！');
        }
        
        sort(array_unique($ids));
        
        $map['b.id'] = array('in',$ids);
        
        $bill = D("Bill");
        
        $data = $bill
        
            ->field("replace(b.bill_number,'dz','js') as settlement_number,b.id,b.total_money,b.total_number,"
            
            ."b.game_id,b.game_name,b.promote_id,b.promote_account")
        
            ->table("__BILL__ as b ")        
           
            ->where($map)
           
            ->order("b.id asc")
            
            ->select();
            
        if (empty($data) || !is_array($data)) {
            $this->error('没有结果！！！');
        }
        foreach ($data as $k => $v) {
            $data[$k]['create_time'] = time();
            $id = $v['id'];
            $data[$k]['pattern']=$_REQUEST[$id]['cooperation']=='CPA'?1:0;
            $data[$k]['ratio']=$_REQUEST[$id]['cps_ratio'];
            $data[$k]['money']=$_REQUEST[$id]['cpa_price'];
            if ($data[$k]['pattern'] == '0') {
                $data[$k]['sum_money'] = ($_REQUEST[$id]['cps_ratio']/100) * $v['total_money'];
            } elseif ($data[$k]['pattern'] == '1') {
                $data[$k]['sum_money'] = $_REQUEST[$id]['cpa_price'] * $v['total_number'];
            }
            unset($data[$k]['id']);
        }   
        // var_dump($data);exit;            
        $bill->startTrans();
        $settlementstatus = array('settlement_status'=>1);
        $flag = $bill->table("__BILL__ as b ")->where($map)->save($settlementstatus);
        
        if (!$flag) {$bill->rollback();$this->error('结算失败！');}
        
        if (D('settlement')->addAll($data)) {
            $bill->commit();
            $this->success('结算成功！',U('settlement',array('promote_account'=>I('request.promote_account'),'game_name'=>I('request.game_name'),'bill_number'=>I('request.bill_number'))));
        } else {
            $bill->rollback();
            $this->error('结算失败！！!',U('settlement',array('promote_account'=>I('request.promote_account'),'game_name'=>I('request.game_name'),'bill_number'=>I('request.bill_number'))));
        }        
    }
    // public function generatesettlement() {
    //     $ids    =   I('request.ids');
    //     if(empty($ids)){
    //         $this->error('请选择要操作的数据');
    //     }
    //     $money=$_REQUEST['cpa_price'];
    //     $ratio=$_REQUEST['cps_ratio'];
    //     $map['b.id'] = array('in',$ids);
    //     $bill = D("Bill");
    //     $data = $bill
    //         ->field("replace(b.bill_number,'dz','js') as settlement_number,b.total_money,b.total_number,"
    //         ."b.game_id,b.game_name,b.promote_id,b.promote_account,a.money as money,a.ratio as ratio")
    //         ->table("__BILL__ as b ")        
    //         ->join("__GAME__ as a on(a.id=b.game_id )","LEFT")
    //         ->where($map)                     
    //         ->select();
    //     foreach ($data as $k => $v) {
    //         $data[$k]['create_time'] = time();
    //         $data[$k]['pattern'] = $pattern;
    //         if ($pattern == '0') {
    //             $data[$k]['ratio'] = $ratio;
    //             $data[$k]['sum_money'] = ($ratio/100) * $v['total_money'];
    //         } elseif ($pattern == '1') {
    //             $data[$k]['money'] = $money;
    //             $data[$k]['sum_money'] = $money * $v['total_number'];
    //         }
    //     }
    //     var_dump($data);exit;
    //     $bill->startTrans();
    //     $settlementstatus = array('settlement_status'=>1);
    //     $flag = $bill->table("__BILL__ as b ")->where($map)->save($settlementstatus);
    //     if (!$flag) {$bill->rollback();$this->error('结算失败！');}
    //     // var_dump($data);exit;
    //     $add_all=D('settlement')->addAll($data,$options=array(),$replace=false);
    //     if ($add_all) {
    //         $bill->commit();
    //         $this->success('结算成功！',U('settlement',array('promote_account'=>I('request.promote_account'),'game_name'=>I('request.game_name'),'pattern'=>I('request.pattern'),'bill_number'=>I('request.bill_number'))));
    //     } else {
    //         $bill->rollback();
    //         $this->error('结算失败！！!',U('settlement',array('promote_account'=>I('request.promote_account'),'game_name'=>I('request.game_name'),'pattern'=>I('request.pattern'),'bill_number'=>I('request.bill_number'))));
    //     }        
    // }
    public function generatecpsettlement() {//cp结算
        $game_id    =   I('request.ids');
        if(empty($game_id)){
            $this->error('请选择要操作的数据');
        }
        $starttime=strtotime($_REQUEST['timestart'].'-01');
        $endtime=strtotime($_REQUEST['timestart']."+1 month -1 day")+24*3600-1;
        $map['s.pay_status']=1;
        $map['s.selle_status']=0;
        if(is_array($game_id)){
            $map['s.game_id']=array('in',$game_id);
        }else{
            $map['s.game_id']=$game_id;
        }
        $map['pay_time']=array('BETWEEN',array($starttime,$endtime));
        $spe=M('spend as s','tab_');
        $smap= array('s.selle_time'=>$_REQUEST['timestart'],'s.selle_status'=>1);
        $data=$spe
        ->field('s.id,s.selle_status,s.selle_time')
        ->join('tab_game as g on g.id=s.game_id','LEFT')
        ->where($map)
        ->setField($smap);
        if($data){
            $this->success('结算成功');
        }else{
            $this->error('结算失败');
        }
        
    }
    public function changeratio(){
        $gid    =   I('request.game_id');
        if(empty($gid)){
             $this->ajaxReturn(0,"请选择要操作的数据",0);exit;
        }
        $starttime=strtotime($_REQUEST['timestart'].'-01');
        $endtime=strtotime($_REQUEST['timestart']."+1 month -1 day")+24*3600-1;
        $map['s.pay_status']=1;
        $map['s.selle_status']=0;
        $map['s.game_id']=$_REQUEST['game_id'];
        $map['pay_time']=array('BETWEEN',array($starttime,$endtime));
        $spe=M('spend as s','tab_');
        $data=$spe
        ->field('s.id,s.selle_status,s.selle_ratio')
        ->join('tab_game as g on g.id=s.game_id','LEFT')
        ->where($map)
        ->setField('s.selle_ratio',$_POST['ratio']);
        if($data){
            $this->ajaxReturn($data);
        }else{
            $this->ajaxReturn(-1);
        }
    }
    public function withdraw() {
        
        $model = array(
            'm_name' => 'withdraw',
            'order'  => 'id ',
            'title'  => '渠道提现',
            'template_list' =>'withdraw',
        );
        
        $user = A('Bill','Event');
        $user->money_list($model,$p,$map);
        
    }
    
       public function set_withdraw_status($model='withdraw') {
        $withdraw=M('withdraw',"tab_");
        $seet=M('settlement',"tab_");
        $count=count($_REQUEST['ids']);
        if($count>1){
            for ($i=0; $i <$count; $i++) { 
            $map['id']=$_REQUEST['ids'][$i];
            $dind=$withdraw->where($map)->find();
            $se_map['settlement_number']=$dind['settlement_number'];
            $seet->where($se_map)->save(array("ti_status"=>$_REQUEST['status']));
            $withdraw->where($map)->save(array("end_time"=>time()));
            }
        }else{
            $map['id']=$_REQUEST['ids'];
            $dind=$withdraw->where($map)->find();
            $se_map['settlement_number']=$dind['settlement_number'];
            $seet->where($se_map)->save(array("ti_status"=>$_REQUEST['status']));
            $withdraw->where($map)->save(array("end_time"=>time()));
        }

        parent::set_status($model);
    }


    protected function upPromote($promote_id){
        $model = D('Promote');
        $data['id'] = $promote_id;
        $data['money'] = 0;
        return $model->save($data);
    }
}