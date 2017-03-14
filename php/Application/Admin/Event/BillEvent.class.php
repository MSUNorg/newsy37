<?php
namespace Admin\Event;
use Think\Controller;
/**
 * 后台事件控制器
 * @author 鹿文学 
 */
class BillEvent extends ThinkEvent {
	public function check_bill($model = null,$p,$extend=array()){
        $model || $this->error('模型名标识必须！');
        $page = intval($p);
        $page = $page ? $page : 1; //默认显示第一页数据

        //解析列表规则
        $fields = $model['fields'];
        // 关键字搜索
        $map0    =   empty($extend[0])?array():$extend[0];
        $map1    =   empty($extend[1])?array():$extend[1];
        
        //$map1['promote_id'] = $map0['u.promote_id'] = array('gt',0);

        $user = D("User");
        $spend = D("Spend");
        
        //读取模型数据列表
        $data = array();
        
        $row    = empty($model['list_row']) ? 10 : $model['list_row'];
        
        $map1['partake_status'] = $map0['partake_status'] = 1;
        $map0['game_id'] = array('neq','');

        $offset = ($page-1)*$row;
        
        $sql0 = $user->table("__USER__ as u ")
            ->field('u.id,u.promote_id,up.game_id,if(p.parent_id=0,u.promote_id,p.parent_id) as parent_id,up.game_name')
            ->join('__USER_PLAY__ as up ON (u.id=up.user_id  )','left')
            ->join('__PROMOTE__ as p on(p.id=u.promote_id)','left')
            ->where($map0)
            ->order("u.id")
            ->group("u.id")       
            ->select(false);
        $sql0 = "select parent_id as promote_id,game_id,game_name,parent_id,count(id) as total_number from ("
            .$sql0
            ." ) as a group by game_id,promote_id order by parent_id " ;
        
        $sql00 = "select promote_id,game_id,game_name,sum(total_number) as total_number from ("
            .$sql0
            .") as b group by parent_id";
                    
        $data0 = $user->query($sql00." limit $offset,$row");  
                
        
        $count0 = count($user->query($sql0));              
        
        $sql1 = $spend->table("__SPEND__ as s")
            ->field("s.id,s.promote_id,s.game_id,if(p.parent_id=0,s.promote_id,p.parent_id) as parent_id,s.game_name,s.pay_amount ")
            ->join('__PROMOTE__ as p on(p.id=s.promote_id)','left')
            ->where($map1)
            ->group("id")
            ->select(false);
        
        $sql1 = "select parent_id as promote_id,game_id,parent_id,game_name,sum(pay_amount) as total_amount from ( "
            .$sql1
            ." ) as a group by game_id,promote_id ";
        
        $sql11 = "select promote_id,game_id,game_name,sum(total_amount) as total_amount from ("
            .$sql1
            ." ) as b group by parent_id ";
        
        $data1 = $spend->query($sql11." limit $offset,$row");
        
        
        
        $count1 = count($spend->query($sql1));
        
        if (!empty($data1) && !empty($data0)) {
            foreach ($data1 as $j => $u) {
                foreach ($data0 as $k => $v) {
                    if (($u['promote_id'] == $v['promote_id']) && ($u['game_id'] == $v['game_id'])) {
                        $data[] = array_merge($u,$v);unset($data1[$j]);unset($data0[$k]);
                    }
                }       
            }             
            $data = array_merge($data,$data0,$data1); 
        } elseif (!empty($data0)) {$data = $data0;}
          elseif (!empty($data1)) {$data = $data1;}
        

        
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
    
    public function bill_list($model = null,$p,$extend=array()){
        $model || $this->error('模型名标识必须！');
        $page = intval($p);
        $page = $page ? $page : 1; //默认显示第一页数据

        //解析列表规则
        
        $fields = $model['fields'];
        
        // 关键字搜索
        
        $map    =   empty($extend)?array():$extend;
        $map['status'] = 1;
        
        // 条件搜索
        
        foreach($_REQUEST as $name=>$val){
            switch ($name) {
                case 'group':break;
                case 'timestart':
                    $map['bill_start_time'] = array('egt',strtotime($val));
                    break;
                case 'timeend':
                    $map['bill_end_time'] = array('elt',strtotime($val)+24*60*60-1);
                    break;
                default :
                    if ($val == '全部') {$map[$name]=array('like','%%');}
                    else
                        $map[$name]=array('like','%'.$val.'%');
                    break;
            }
        }

        $row    = empty($model['list_row']) ? 10 : $model['list_row'];

        //读取模型数据列表
        $name = $model['m_name'];
        $new_model = D($name);
        $data = D($name)
                        
            ->where($map)
                      
            ->order($model['order'])
                        
            ->page($page, $row)
                      
            ->select();
        
        
        $count = D($name)->where($map)->count();
        
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
    
    public function show_bill($model = null,$p,$extend=array()) {
        
        $model || $this->error('模型名标识必须！');
        
        $page = intval($p);
        
        $page = $page ? $page : 1; //默认显示第一页数据

        //解析列表规则
        
        $fields = $model['fields'];
        
        // 关键字搜索
        
        $map    =   empty($extend)?array():$extend;
        
        $map['b.status'] = 1;
       // $map['a.status'] = 1;        
        $map['b.settlement_status']=0;
        
        // 条件搜索
        
        foreach($_REQUEST as $name=>$val){
            switch ($name) {
                case 'group':break;
                case 'timestart':
                    $map['bill_start_time'] = array('egt',strtotime($val));
                    break;
                case 'timeend':
                    $map['bill_end_time'] = array('elt',strtotime($val)+24*60*60-1);
                    break;
                case 'pattern':
                    $map['pattern']=array('like',$val);break;
                case 'p':
                    break;
                default :
                    if ($val == '全部') {$map['b.'.$name]=array('like','%%');}
                    else
                        $map['b.'.$name]=array('like','%'.$val.'%');
                    break;
            }
        }
        $row    = empty($model['list_row']) ? 10 : $model['list_row'];

        //读取模型数据列表
        
        $name = $model['m_name'];$table = "__".strtoupper($name)."__ as ".strtolower(substr($name,0,1));
        $new_model = D($name);
       
        $data = D($name)->field("b.*,a.money,a.ratio")
        
            ->table($table)        
            
            ->join("__GAME__ as a on(a.id=b.game_id)",'LEFT')
            
            ->where($map)
            
            ->order($model['order'])
            
            ->page($page, $row)
            
            ->select();
            // var_dump(D($name)->getlastsql());exit;
        $count = D($name)->table($table)        
            
            ->join("__APPLY__ as a on(a.game_id=b.game_id and a.promote_id = b.promote_id)","LEFT")
            
            ->where($map)->count();
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
    
    public function settlement($model = null,$p,$extend=array()) {
        $model || $this->error('模型名标识必须！');
        $page = intval($p);
        $page = $page ? $page : 1; //默认显示第一页数据

        //解析列表规则
        
        $fields = $model['fields'];
        
        // 关键字搜索
        
        $map    =   empty($extend)?array():$extend;
        
        $map['status'] = 1;
        
        
        
        // 条件搜索
        
        foreach($_REQUEST as $name=>$val){
            switch ($name) {
                case 'group':break;
                case 'timestart':
                    $map['create_time'] = array('egt',strtotime($val));
                    break;
                case 'timeend':
                    $map['create_time'] = array('elt',strtotime($val)+24*60*60-1);
                    break;
                default :
                    if ($val == '全部') {$map[$name]=array('like','%%');}
                    else
                        $map[$name]=array('like','%'.$val.'%');
                    break;
            }
        }

        $row    = empty($model['list_row']) ? 10 : $model['list_row'];

        //读取模型数据列表
        
        $name = $model['m_name'];
        $new_model = D($name);
        $data = D($name)            
            
            ->where($map)            
            
            ->order($model['order'])
            
            ->page($page, $row)
            
            ->select();
        
        $count = D($name)->where($map)->count();
        $total = 0;
        foreach ($data as $v) {
            $total += $v['sum_money'];
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
    
    public function money_list($model = null,$p,$extend=array()) {
        $model || $this->error('模型名标识必须！');
        $page = intval($p);
        $page = $page ? $page : 1; //默认显示第一页数据
        //解析列表规则
        $fields = $model['fields'];
        // 关键字搜索
        $map    =   empty($extend)?array():$extend;
        // $map['status'] = 1;
        // 条件搜索   
        foreach($_REQUEST as $name=>$val){
            switch ($name) {
                case 'group':break;
                case 'timestart':
                    $map['create_time'] = array('egt',strtotime($val));
                    break;
                case 'timeend':
                    $map['create_time'] = array('elt',strtotime($val)+24*60*60-1);
                    break;
                case 'withdraw_status':
                    $map['withdraw_status'] = $val;break;
                default :
                    if ($val == '全部') {$map[$name]=array('like','%%');}
                    else
                        $map[$name]=array('like','%'.$val.'%');
                    break;
            }
        }
        $row    = empty($model['list_row']) ? 10 : $model['list_row'];
        //读取模型数据列表 
        $name = $model['m_name'];
        $new_model = D($name);
        $data = D($name)                   
            ->where($map)                  
            ->order($model['order'])      
            ->page($page, $row)     
            ->select();
        $count = D($name)->where($map)->count();
        $to_map=$map;
        $to_map['status']=1;
        $total =  D($name)                   
            ->where($to_map)                  
            ->order($model['order'])      
            ->page($page, $row)
            ->sum("sum_money"); 
        //分页
         
        if($count > $row){
            $page = new \Think\Page($count, $row);
            $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
            $this->assign('_page', $page->show());
        }
        $this->assign('model', $model);
        $this->assign('list_data', $data);
        $this->assign('total',$total==null?0:$total);
        $this->meta_title = $model['title'].'列表';
        $this->display($model['template_list']);
    }
       
}