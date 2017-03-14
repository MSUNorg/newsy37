<?php

namespace Admin\Controller;
use User\Api\UserApi as UserApi;

/**
 * 推广查询控制器
 * @author zxc
 */
class QueryController extends ThinkController {
	 /**
    *推广对账
    */
    public function bill($p = 0){
    	$map=array();
    	if(isset($_REQUEST['time-start']) && isset($_REQUEST['time-end'])){
            $map['pay_time']  =  array('BETWEEN',array(strtotime($_REQUEST['time-start']),strtotime($_REQUEST['time-end'])+24*60*60-1));
            unset($_REQUEST['time-start']);unset($_REQUEST['time-end']);
        }
        if(isset($_REQUEST['start']) && isset($_REQUEST['end'])){
            $map['pay_time']  =  array('BETWEEN',array(strtotime($_REQUEST['start']),strtotime($_REQUEST['end'])+24*60*60-1));
            unset($_REQUEST['start']);unset($_REQUEST['end']);
        }
        if(isset($_REQUEST['game_name'])){
                if($_REQUEST['game_name']=='全部'){
                    unset($_REQUEST['game_name']);
                }else{
                    $map['game_name']=$_REQUEST['game_name'];
                    unset($_REQUEST['game_name']);
                }
            }
        if(isset($_REQUEST['promote_name'])){
                if($_REQUEST['promote_name']=='全部'){
                    unset($_REQUEST['promote_name']);
                }else if($_REQUEST['promote_name']=='自然注册'){
                    $map['promote_id']=array("elt",0);
                    unset($_REQUEST['promote_name']);
                }else{
                    $map['promote_id']=get_promote_id($_REQUEST['promote_name']);
                    unset($_REQUEST['promote_name']);
                }
          }else{
                    $map['promote_id']=array("in",get_pid());                
          }
        $model = array(
            'm_name' => 'Spend',
            'order'  => 'id desc',
            'group'  => 'case parent_id  when 0 then promote_id else parent_id end ,DATE_FORMAT( FROM_UNIXTIME(pay_time),"%Y-%m-%d"),game_id',
            'title'  => '推广对账',
            'template_list' =>'bill',
        );
        $user = A('Spend','Event');
        $user->group_list($model,$p,$map);
    }
    public function settlement($p=0){
        $row=10;
        if(isset($_REQUEST['game_name'])){
            $map['game_id']=get_game_id($_REQUEST['game_name']);
        }
            if(isset($_REQUEST['time-start'])&&isset($_REQUEST['time-end'])){
            $map['spend_time']=array('BETWEEN',array(strtotime($_REQUEST['time-start']),strtotime($_REQUEST['time-end'])+24*60*60-1));
            unset($_REQUEST['time-start']);unset($_REQUEST['time_end']);
        }
        $data = D('Settlement') 
            // 查询条件
            ->where($map)
            /* 默认通过id逆序排列 */
            ->order($order)
            /* 数据分页 */
            ->page($p, $row)
            ->select();
        /* 查询记录总数 */
        $count = D('Settlement')->where($map)->count();
        //分页
        if($count > $row){
            $page = new \Think\Page($count, $row);
            $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
            $this->assign('_page', $page->show());
        }
        $this->assign('list_data', $data);
        $this->display();
    }
    public function settl(){
        /*汇总时间、推广员、充值金额、充值次数、状态、游戏、获得金额*/
        //实例化结算表
        $sett_Model = D('Settlement');
        //时间默认截止前一天23：59：59分
        $end_time = strtotime(date('Y-m-d 23:59:59',time()))-24*3600;
        //获取上次结算时间
        $start_time = $sett_Model->order('spend_time desc')->getField('spend_time');
        if(empty($start_time)) $start_time = strtotime(date('Y-m-d 23:59:59',time()))-180*24*3600;
        //默认前半年前
        $start_time = $start_time+1;
        $total_data = $this->settl_data($start_time,$end_time);
        if(!empty($total_data)){
            foreach ($total_data as $k => $v) {
               $sett_data['spend_time'] = strtotime($v['period']);
               $sett_data['promote_id'] = $v['parent_id'];
               $sett_data['game_id']    = $v['game_id'];
               $sett_data['spend_num']  = $v['spend_num'];
               $sett_data['money']      = $v['total_amount'];
               $ration = $this->ration($v['game_id'],$v['parent_id']);
               $sett_data['ratio']      =$ration;               
               $sett_data['real_money'] = $v['total_amount'] * ($ration/100);
               $sett_data['status']     = 0;
               $sett_data['type']       = 0;
               $flag = $sett_Model
                        ->where(array('spend_time'=>strtotime($v['period']),'promote_id'=>$v['parent_id'],'game_id'=>$v['game_id']))
                        ->getField('id');
               if(empty($flag)){
                    $sett_Model->add($sett_data);  
                    D('Promote')->where(array('id'=>$v['parent_id']))->setInc('money',$sett_data['real_money']);
                    D('Promote')->where(array('id'=>$v['parent_id']))->setInc('total_money',$sett_data['real_money']);
               }
            }
        }
        $this->success ( "结算成功",U("Query/settlement"));
    }
    public function settl_data($start_time,$end_time){
        $map['case parent_id  when 0 then promote_id else parent_id end'] = array('gt',0);
        $map['pay_time'] = array('BETWEEN',array($start_time,$end_time));
        $map['tab_spend.pay_status'] = 1;
        $data = D('Spend')
            ->field('tab_spend.game_id,case parent_id  when 0 then tab_spend.promote_id else parent_id end AS parent_id,sum(pay_amount) AS total_amount,DATE_FORMAT( FROM_UNIXTIME(pay_time),"%Y-%m-%d") AS period,count(*) as spend_num')
            ->join('tab_promote ON tab_spend.promote_id = tab_promote.id')
            // 查询条件
            ->where($map)
            //根据字段分组
            ->group('promote_id,tab_spend.game_id')
            /* 执行查询 */
            ->select();
        return $data;
    }
    public function ration($game_id,$promote_id){
        $map['game_id'] = $game_id;
        $map['promote_id'] = $promote_id;
        $data = D('Apply')->field('ratio')->where($map)->find();
        if(empty($data)){ return 1;}
        $data['ration']==0?1: $data['ratio'];
        return $data['ratio'];
    }
    public function withdraw(){
        if(isset($_REQUEST['op_account'])){
            $map['op_account']=array('like','%'.$_REQUEST['op_account'].'%');
            unset($_REQUEST['op_account']);
        }
        if(isset($_REQUEST['promote_name'])){
                if($_REQUEST['promote_name']=='全部'){
                    unset($_REQUEST['promote_name']);
                }else if($_REQUEST['promote_name']=='自然注册'){
                    $map['promote_id']=array("elt",0);
                    unset($_REQUEST['promote_name']);
                }else{
                    $map['promote_id']=get_promote_id($_REQUEST['promote_name']);
                    unset($_REQUEST['promote_name']);
                }
        }
        if(isset($_REQUEST['time-start'])&&isset($_REQUEST['time-end'])){
            $map['create_time']=array(
                'BETWEEN',array(strtotime($_REQUEST['time-start']),strtotime($_REQUEST['time-end'])+24*60*60-1)
            );
            unset($_REQUEST['time-start']);unset($_REQUEST['time-end']);
        }
        if(isset($_REQUEST['start'])&&isset($_REQUEST['end'])){
            $map['create_time']=array(
                'BETWEEN',array(strtotime($_REQUEST['start']),strtotime($_REQUEST['end'])+24*60*60-1)
            );
            unset($_REQUEST['start']);unset($_REQUEST['end']);
        }

        $model = array(
            'm_name' => 'withdraw',
            'fields' => array('id','promote_id','amount','remark','op_id','op_account','create_time'),
            'map'    => $map,
            'key'    => array('op_account'),
            'order'  => 'id desc',
            'title'  => '提现管理',
            'template_list' =>'withdraw',
        );
        $base = A('Think','Event');
        $base->lists($model);
    }
    public function withdraw_add(){
        if(IS_POST){
            $model = D('Promote');
            $map['id'] = $_POST['promote_id'];
            $p_data = $model->where($map)->find();
            if(empty($p_data['money'])){
                $this->error('要提现的推广员暂无数据');exit();
            }
            $p_amount = $p_data['money'];
            if($this->upPromote($map['id'])){
                $data['promote_id'] = $_POST['promote_id'];
                $data['amount'] = $p_amount;
                $data['remark'] = $_POST['remark'];
                $data['op_id']  = UID;
                $user = session('user_auth');
                $data['op_account']  = $user['username'];
                $data['promote_id']  = $_POST['promote_id'];
                $data['create_time'] = NOW_TIME;
                $res = D('Withdraw')->add($data);
                $res = M('settlement','tab_')->where($map['id'])->setField("status",1);
                if($res){
                    $this->success('提现成功',U('withdraw'));
                }
                else{
                    $this->error('提现失败');
                }
            }
            else{
                $this->error('要提现的推广员暂无数据');
            }
            
        }
        else{
            $this->display();
        }
    }

    protected function upPromote($promote_id){
        $model = D('Promote');
        $data['id'] = $promote_id;
        $data['money'] = 0;
        return $model->save($data);
    }
}