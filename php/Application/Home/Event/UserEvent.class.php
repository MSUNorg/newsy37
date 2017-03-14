<?php
namespace Home\Event;
use Think\Controller;
/**
 * 后台事件控制器
 * @author 王贺 
 */
class UserEvent extends BaseEvent {

    public function lists($model=null){
        //parent::join_more($model);
    }

    public function join_list($model,$p){
        $model || $this->error('模型名标识必须！');
        $page = intval($p);
        $page = $page ? $page : 1; //默认显示第一页数据

        //解析列表规则
        $fields = $model['fields'];
        $map    =   $model['map'];
        if(isset($_REQUEST['time-start']) && isset($_REQUEST['time-end']) && !empty($_REQUEST['time-start']) && !empty($_REQUEST['time-end'])){
            $map[$model['time_fields']]  =  array('BETWEEN',array(strtotime($_REQUEST['time-start']),strtotime($_REQUEST['time-end'])));
            unset($_REQUEST['time-start']);unset($_REQUEST['time-end']);
        }
        if(isset($_REQUEST['account']) && !empty($_REQUEST['account'])){
            $map['account'] = array('like','%'.$_REQUEST['account'].'%');
            unset($_REQUEST['account']);
        }
        if(isset($_REQUEST['game_appid']) && !empty($_REQUEST['game_appid'])){
            $map['game_appid'] = $_REQUEST['game_appid'];
            unset($_REQUEST['game_appid']);
        }
        $row    = empty($model['list_row']) ? 10 : $model['list_row'];

        //读取模型数据列表
        $name = $model['m_name'];
        $data = M($name,'tab_user_')
             /* 查询指定字段，不指定则查询所有字段 */
            ->field('tab_user_play.id,user_id,account,game_appid,promote_id,register_time,register_ip')
            ->join("tab_user on tab_user_play.user_id = tab_user.id")
            // 查询条件
            ->where($map)
            /* 默认通过id逆序排列 */
            ->order($order)
            /* 数据分页 */
            ->page($page, $row)
            /* 执行查询 */
            ->select();

        /* 查询记录总数 */
        $count = M($name,'tab_user_')->join("tab_user on tab_user_play.user_id = tab_user.id")->where($map)->count();

         //分页
        if($count > $row){
            $page = new \Think\Page($count, $row);
            $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
            $this->assign('_page', $page->show());
        }
        $this->assign('model', $model);
        $this->assign('list_data', $data);
        $this->meta_title = $model['title'].'列表';
        $this->display($model['template_list']);
    }

        public function bill_list($model,$p){
        $model || $this->error('模型名标识必须！');
        $page = intval($p);
        $page = $page ? $page : 1; //默认显示第一页数据

        //解析列表规则
        $fields = $model['fields'];
        $map    =   $model['map'];

        $row    = empty($model['list_row']) ? 10 : $model['list_row'];

        //读取模型数据列表
        $name = $model['m_name'];
        $data = M($name,'tab_')
             /* 查询指定字段，不指定则查询所有字段 */
            // 查询条件
            ->where($map)
            /* 默认通过id逆序排列 */
            ->order($order)
            /* 数据分页 */
            ->page($page, $row)
            /* 执行查询 */
            ->select();

        /* 查询记录总数 */
        $count = M($name,'tab_')->where($map)->count();
        if($count > $row){
            $page = new \Think\Page($count, $row);
            foreach($map as $key=>$val) {
                $page->parameter[$key]   =   urlencode($val);
            }
            $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
            $this->assign('_page', $page->show());
        }
        $this->assign('model', $model);
        $this->assign('count',$count);
        $this->assign('list_data', $data);
        $this->meta_title = $model['title'].'列表';
        $this->display($model['template_list']);
    }
        public function shou_list($model,$p){
        $model || $this->error('模型名标识必须！');
        $page = intval($p);
        $page = $page ? $page : 1; //默认显示第一页数据

        //解析列表规则
        $fields = $model['fields'];
        $map    =   $model['map'];

        $row    = empty($model['list_row']) ? 10 : $model['list_row'];

        //读取模型数据列表
        $name = $model['m_name'];
        $data = M($name,'tab_')
             /* 查询指定字段，不指定则查询所有字段 */
            // 查询条件
            ->where($map)
            /* 默认通过id逆序排列 */
            ->order($order)
            /* 数据分页 */
            ->page($page, $row)
            /* 执行查询 */
            ->select();
        /* 查询记录总数 */
        $count = M($name,'tab_')->where($map)->count();
        if($name=="son_settlement"){
            $sum_money=M($name,'tab_')->where(array("promote_id"=>get_pid()))->sum("jie_money");
        }else{
            //随搜索条件变化
            static $sum_money=0;
            foreach ($data as $key => $value) {
                $sum_money=$sum_money+$value['sum_money'];
                if($value['ti_status']==1){
                    $kk[]=$data[$key];
                }
            }
            foreach ($kk as $k=> $v) {
                $y_money=$y_money+$value['sum_money'];
            }
            $w_money=$sum_money-$y_money;
            //固定不变
        // $sum_money=M($name,'tab_')->where(array("promote_id"=>get_pid()))->sum("sum_money");        
        }
        // $w_map['ti_status']=array("neq",1);
        // $w_map['promote_id']=get_pid();
        // $y_map['ti_status']=array("eq",1);
        // $y_map['promote_id']=get_pid();
        // $w_money=M($name,'tab_')->where($w_map)->sum("sum_money");
        // $y_money=M($name,'tab_')->where($y_map)->sum("sum_money");
         //分页
        if($count > $row){
            $page = new \Think\Page($count, $row);
            foreach($map as $key=>$val) {
                $page->parameter[$key]   =   urlencode($val);
            }
            $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
            $this->assign('_page', $page->show());
        }
        $this->assign('model', $model);
        $this->assign('count',$count);
        $this->assign('sum_money',$sum_money==""?'0.00':$sum_money);
        $this->assign('w_money',$w_money==""?'0.00':$w_money);
        $this->assign('y_money',$y_money==""?'0.00':$y_money);
        $this->assign('list_data', $data);
        $this->meta_title = $model['title'].'列表';
        $this->display($model['template_list']);
    }
    public function check_bill($model = null,$p,$extend=array()){
        $model || $this->error('模型名标识必须！');
        $page = intval($p);
        $page = $page ? $page : 1; //默认显示第一页数据
        //解析列表规则
        $fields = $model['fields'];
        $map0    =   empty($extend[0])?array():$extend[0];
        // var_dump($extend);exit; 
        $map1    =   empty($extend[1])?array():$extend[1];
        $row    = empty($model['list_row']) ? 10 : $model['list_row'];
        //读取模型数据列表
        $name = $model['m_name'];
        $data= M($name,'tab_')
             /* 查询指定字段，不指定则查询所有字段 */
            ->field($fields)
            ->join($model['join'])
            // 查询条件
            ->where($map1)
            /* 默认通过id逆序排列 */
            ->order($order)
            ->group($model['group'])
            /* 数据分页 */
            ->page($page,$row)
            /* 执行查询 */
            ->select();
        $data1 = M($name,'tab_')//spend表与apply表连，获得数据，没有注册人数
             /* 查询指定字段，不指定则查询所有字段 */
            ->field($fields)
            ->join($model['join'])
            // 查询条件
            ->where($map1)
            /* 默认通过id逆序排列 */
            ->order($order)
            ->group($model['group'])
            /* 数据分页 */
            /* 执行查询 */
            ->select(false);
        $data2 =M("user",'tab_')//$data1 sql语句 与user表连
            ->field('tab_user.id,tab_user.promote_id,tab_user.fgame_id,tab_user.fgame_name,tab_user.promote_account')
            ->join('INNER JOIN'.$data1."as p on p.game_id = tab_user.fgame_id and p.promote_id = tab_user.promote_id")
            ->where($map0)
            ->select(false);
        $data3=M()//获得注册人数
            ->field("COUNT(q.id) as total_number,q.promote_id,q.fgame_id")
            ->join($data2.'as q')
            ->group("q.promote_id,q.fgame_id")
            ->query("select %FIELD% from %JOIN% %GROUP%",true);
            if(!empty($data)&&!empty($data3)){
                foreach ($data3 as $key => $value) {//如果游戏、推广员 同时符合,把注册人数插入data
                    foreach ($data as $k => $v) {
                        if($v['promote_id']==$value['promote_id']&&$v['game_id']==$value['fgame_id']){
                            $data[$k]['total_number']=$value['total_number'];
                        }
                    }
                } 
            }
        /* 查询记录总数 */
        $count = M($name,'tab_')
             /* 查询指定字段，不指定则查询所有字段 */
            ->field($fields)
            ->join($model['join'])
            // 查询条件
            ->where($map1)
            /* 默认通过id逆序排列 */
            ->order($order)
            ->group($model['group'])
            /* 执行查询 */
            ->select();
        $count=(count($count));
         //分页
        if($count > $row){
            $page = new \Think\Page($count, $row);
            //分页跳转的时候保证查询条件
            foreach($_POST as $key=>$val) {
                $page->parameter[$key]   =   $val;
            }
            $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
            $this->assign('_page', $page->show());
        }
        $this->assign('model', $model);
        $this->assign('list_data', $data);
        $this->meta_title = $model['title'].'列表';
        $this->display($model['template_list']);
    }
    

     // 子渠道结算确认
    
    public function check_bill_($model = null,$p,$extend=array()){
        $model || $this->error('模型名标识必须！');
        $page = intval($p);
        $page = $page ? $page : 1; //默认显示第一页数据

        //解析列表规则
        $fields = $model['fields'];
        // 关键字搜索
        $map0    =   empty($extend[0])?array():$extend[0];
        $map1    =   empty($extend[1])?array():$extend[1];        

        $user = M("User","tab_");
        $spend = M("Spend","tab_");
        
        //读取模型数据列表
        $data = array();
        
        $row    =   10 ;
        
        $map1['sub_status'] = $map0['sub_status'] = 0;
        $map0['fgame_id'] = array('neq',0);
        $map1['pay_status']=1;
         $map1['is_check']=array('in',array(1,3));
        $map0['is_check']=array('in',array(1,3));
        $offset = ($page-1)*$row;
        
        $sql0 = $user->table("__USER__ as u ")
            ->field('u.id,u.promote_id,p.account as promote_account,fgame_id,fgame_name,p.parent_id')
            ->join('__PROMOTE__ as p on(p.id=u.promote_id)','left')
            ->where($map0)
            ->order("u.id")
            ->group("u.id")       
            ->select(false);
        $sql0 = "select a.promote_id,a.promote_account,a.fgame_id,a.fgame_name,count(a.id) as total_number from ("
            .$sql0
            ." ) as a left join tab_apply as ap on(a.parent_id=ap.promote_id and ap.game_id=fgame_id) "
            ." group by fgame_id,a.promote_id order by a.promote_account ";
                  
        $data0 = $user->query($sql0." limit $offset,$row");  
        
        
        $count0 = count($user->query($sql0));              
        
        $sql1 = $spend->table("__SPEND__ as s")
            ->field("s.id,s.promote_id,p.account as promote_account,p.parent_id,s.game_id,s.game_name,s.pay_amount ")
            ->join('__PROMOTE__ as p on(p.id=s.promote_id)','left')
            ->where($map1)
            ->group("id")
            ->select(false);
        
        $sql1 = "select a.promote_id,a.promote_account,a.game_id,a.game_name,sum(a.pay_amount) as total_amount from ( "
            .$sql1
            ." ) as a left join tab_apply as ap on(a.parent_id=ap.promote_id and ap.game_id=a.game_id) "
            ." group by a.game_id,a.promote_id order by a.promote_account ";
        $data1 = $spend->query($sql1." limit $offset,$row");               
        $count1 = count($spend->query($sql1));
        if (!empty($data1) && !empty($data0)) {
            foreach ($data1 as $j => $u) {
                foreach ($data0 as $k => $v) {
                    if (($u['promote_id'] == $v['promote_id']) && ($u['game_id'] == $v['fgame_id'])) {
                        $data[] = array_merge($u,$v);unset($data1[$j]);unset($data0[$k]);
                    }
                }       
            } 
            $data = array_merge($data,$data0,$data1); 
        } elseif (!empty($data0)) {$data = $data0;}
          elseif (!empty($data1)) {$data = $data1;}
        

        // var_dump($data);exit;
        $count = $count0>$count1?$count0:$count1;
        //分页
         
        if($count > $row){
            $page = new \Think\Page($count, $row);
            $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
            $this->assign('_page', $page->show());
        }
        $this->assign('model', $model);
        $this->assign('list_data', $data);
        $this->meta_title = $model['title'].'列表';
        $this->display($model['template_list']);
    }
    

 public function money_list($model = null,$p,$extend=array()) {
        $model || $this->error('模型名标识必须！');
        $page = intval($p);
        $page = $page ? $page : 1; //默认显示第一页数据

        //解析列表规则
        
        $fields = $model['fields'];
        
        // 关键字搜索
        
        $map    =   empty($extend)?array():$extend;
        
        $map['status'] = 1;               
        $row    = empty($model['list_row']) ? 10 : $model['list_row'];

        //读取模型数据列表
        
        $name = $model['m_name'];
        $new_model = M($name,"tab_");
        $data = $new_model            
            
            ->where($map)            
            
            ->order($model['order'])
            
            ->page($page, $row)
            
            ->select();
        $count = $new_model->where($map)->count();
        $total = 0;
        foreach ($data as $v) {
            $total += $v['jie_money'];
        }
        
        //分页
         
        if($count > $row){
            $page = new \Think\Page($count, $row);
            $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
            $this->assign('_page', $page->show());
        }
        $this->assign('model', $model);
        $this->assign('list_data', $data);
        $this->assign('total',$total);
        $this->meta_title = $model['title'].'列表';
        $this->display($model['template_list']);
    }




    

      public function son_list($model,$p){
        $page = intval($p);
        $page = $page ? $page : 1; //默认显示第一页数据
        $spen_map    =   $model['spen_map'];
        $user_map    =   $model['user_map'];
        $row    = empty($model['list_row']) ? 10 : $model['list_row'];
        $apply=M("apply","tab_")
            ->field("promote_id,game_id,game_name,pattern")
            ->group("promote_id,game_id")
            ->select();

         $spend=M("spend","tab_")
            ->field("promote_id,promote_account,game_id,game_name,sum(pay_amount)")
            ->group("promote_id,game_id")
            ->where($spen_map)
            // ->page($page, $row)
            ->select();

        $user_count=M("user as a ","tab_")
            ->field("fgame_id,fgame_name,promote_id,promote_account,count(*) as count")
            ->group("promote_id,fgame_id")
            ->where($user_map)
            ->select();
            foreach ($apply as $key => $value) {
                foreach ($spend as $k => $v) {
                    if($v['promote_id']==$value['promote_id']&&$v['game_id']==$value['game_id']){
                        $spend[$k]['pattern']=$value['pattern'];
                    }
                    foreach ($user_count as $s => $d) {
                        if($v['game_id']==$d['fgame_id']&&$v['promote_id']==$d['promote_id']){
                         $spend[$k]['count']=$d['count'];
                        }
                    }
                }
            }
            // $count=M("spend","tab_")->where($spen_map)->count();
            $count=count($spend);
         //分页
        if($count > $row){
            $page = new \Think\Page($count, $row);
             $list=array_slice($spend,$page->firstRow,$page->listRows);
            $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
            $this->assign('_page',$page->show());
        }
        $this->assign('model', $model);
        $this->assign('list_data', $list);
        $this->assign('p', $p);
        $this->meta_title = $model['title'].'列表';
        $this->display($model['template_list']);
    }

}
