<?php
namespace Admin\Event;
use Think\Controller;
/**
 * 后台事件控制器
 * @author 王贺 
 */
class SpendEvent extends ThinkEvent {
    public function group_list($model = null,$p,$extend=array()){
        $model || $this->error('模型名标识必须！');
        $page = intval($p);
        $page = $page ? $page : 1; //默认显示第一页数据

        //解析列表规则
        $fields = $model['fields'];
        // 关键字搜索
        $map    =   empty($extend)?array():$extend;
        $map['tab_spend.pay_status'] = 1;
        // 条件搜索
        foreach($_REQUEST as $name=>$val){
            switch ($name) {
                case 'account':
                    $map['tab_spend.user_account']  =   array('like','%'.$val.'%');
                    break;
                case 'game_id':
                    $map['tab_game.id']  =  $val;
                    break;
                case 'promote_id':
                    $map['tab_spend.promote_id']  =  $val;
                    break;
            }
        }
        $row    = empty($model['list_row']) ? 10 : $model['list_row'];

        //读取模型数据列表
        $name = $model['m_name'];
        $new_model = D($name);
        $data = D($name)
            ->field('tab_spend.*,case parent_id  when 0 then promote_id else parent_id end AS parent_id,sum(pay_amount) AS total_amount,DATE_FORMAT( FROM_UNIXTIME(pay_time),"%Y-%m-%d") AS period')
            ->join('left join tab_promote ON tab_spend.promote_id = tab_promote.id') 
            // 查询条件
            ->where($map)
            /* 默认通过id逆序排列 */
            ->order($model['order'])
            //根据字段分组
            ->group($model['group'])
            /* 数据分页 */
            ->page($page, $row)
            /* 执行查询 */
            ->select();
        /* 查询记录总数 */
        $count = count($data);
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
    public function spend_list($model = null,$p,$extend=array()){
        $model || $this->error('模型名标识必须！');
        $page = intval($p);
        $page = $page ? $page : 1; //默认显示第一页数据
        //解析列表规则
        $fields = $model['fields'];
        // 关键字搜索
        $map    =   empty($model['map'])?array():$model['map'];
        $map['tab_spend.pay_status'] = 1;
        // 条件搜索
        foreach($_REQUEST as $name=>$val){
            switch ($name) {
                case 'account':
                    $map['tab_spend.user_account']  =   array('like','%'.$val.'%');
                    break;
                case 'game_id':
                    $map['tab_game.id']  =  $val;
                    break;
                case 'promote_id':
                    $map['tab_spend.promote_id']  =  $val;
                    break;
            }
        }
        $row    = empty($model['list_row']) ? 10 : $model['list_row'];
        //读取模型数据列表
        $name = $model['m_name'];
        $new_model = D($name);
        $data = D($name) 
            // 查询条件
            ->where($map)
            /* 默认通过id逆序排列 */
            ->order($model['order'])
            //根据字段分组
            ->group($model['group'])
            /* 数据分页 */
            ->page($page, $row)
            /* 执行查询 */
            ->select();
        /* 查询记录总数 */
        $count = D($name) 
            // 查询条件
            ->where($map)
            /* 默认通过id逆序排列 */
            ->order($model['order'])
            //根据字段分组
            ->group($model['group'])
            /* 执行查询 */
            ->count();
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

 public function check_bill111($model = null,$p,$extend=array()){
     global $countpage;

        $model || $this->error('模型名标识必须！');
        $page = intval($p);
        $page = $page ? $page : 1; //默认显示第一页数据

        //解析列表规则
        $fields = $model['fields'];
        // 关键字搜索
        $user_map    =   empty($extend['user_map'])?array():$extend['user_map'];
        $spend_map    =   empty($extend['spend_map'])?array():$extend['spend_map'];
        //$map1['promote_id'] = $map0['u.promote_id'] = array('gt',0);

        $user = D("User");
        $spend = D("Spend");
        
        //读取模型数据列表
        $data = array();
        
        $row    = empty($model['list_row']) ? 3 : $model['list_row'];
        
        $user_map['is_check'] = $spend_map['is_check'] = 1;
        $spend_map['pay_status']=1;
        $user_map['fgame_id'] = array('neq',0);

        // $offset = ($page-1)*$row;
        $user_map1=$user_map;
        $user_map1['parent_id']=0;
        $user1=M("user","tab_")
             ->field('id,account,promote_id,promote_account,parent_id,fgame_id,fgame_name,count(*) as count')
             ->group("fgame_id,promote_id")
             ->where($user_map1)
             ->select();
        $user_map['parent_id']=array("neq","0");

        $user=M("user","tab_")
             ->field('id,account,promote_id,promote_account,parent_id,fgame_id,fgame_name,count(*) as count')
             ->group("fgame_id,promote_id")
             ->where($user_map)
             ->select();

             // $user1  注册人数
             foreach ($user1 as $k1 => $v1) {
                foreach ($user as $k2 => $v2) {
                    if($v1['promote_id']==$v2['parent_id']&&$v1['fgame_id']==$v2['fgame_id']){
                       $user1[$k1]['count']+=$v2['count'];
                    }
                }
             }
             unset($user_map['parent_id']);
             //查出 二级推广的父类id
             $p1=M("promote","tab_")
                ->field("id,account,parent_id")
               ->where("parent_id <> 0 ")
               ->select();
             
             $spend=M("spend","tab_")
                ->field('promote_id,promote_account,game_id,game_name,sum(pay_amount) as total_money')
                ->group('promote_id,game_id')
                ->where($spend_map)
                ->select();
    
                        //选出二级推广充值记录
                foreach ($p1 as $k1 => $v1) {
                    foreach ($spend as $k2 => $v2) {
                        if($v1['id']==$v2['promote_id']){
                            $sd[$k2]=$v2;
                          $sd[$k2]['parent_id']=$v1['parent_id'];
                        }
                    }
                }

                //二级推广充值记录合并到一级
                foreach ($sd as $k => $v) {
                    foreach ($spend as $k2 => $v2) {
                        if($v['parent_id']==$v2['promote_id']&&$v['game_id']==$v2['game_id']){
                            $spend[$k2]['total_money']=$v2['total_money']+$v['total_money'];
                           unset($spend[$k2=$k]);
                        } 
                    }
                }


                // //讲二级的推广游戏且一级没有推广的游戏充值记录 合并到一级
                foreach ($spend as $key => $value) {
                    foreach ($p1 as $k => $v) {
                        if($value['promote_id']==$v['id']){
                            $spend[$key]['promote_id']=$v['parent_id'];
                            $spend[$key]['promote_account']=get_promote_name($v['parent_id']);
                            $spend[$key]['pp']=$value['promote_id'];
                        }
                    }
                }
                //如一级用户没有该游戏注册人数 将二级注册人数加到一级 
                foreach ($spend as $key => $value) {
                    foreach ($user as $k => $v) {
                        if($v['fgame_id']==$value['game_id']&&$v['parent_id']==$value['promote_id']){
                            $spend[$key]['total_number']=$v['count'];
                            // continue;
                        }
                    }
                }
                //将注册人数加入数组
                foreach ($spend as $key => $value) {
                    foreach ($user1 as $k => $v) {
                        if($v['fgame_id']==$value['game_id']&&$v['promote_id']==$value['promote_id']){
                            $spend[$key]['total_number']=$v['count'];

                        }
                    }
                   }
                   
        $timestart=$_GET['timestart'];
        $timeend=$_GET['timeend'];
        $this->assign('model', $model);
        // $this->assign('list_data', $spend);
       if(strlen($_GET['timeend'])>10){
            $page=substr($_GET['timeend'],-1);
            
           }
        $this->assign('list_data', $this->page_array(10,$page,$spend));
        $this->assign('_page',$this->show_array($countpage,U("Query/bill/group/1&timestart=$timestart&timeend=$timeend"),$page));
        $this->meta_title = $model['title'].'列表';
        $this->display($model['template_list']);
    }


    public function check_bill($model = null,$p,$extend=array()){
        $model || $this->error('模型名标识必须！');
        $page = intval($p);
        $page = $page ? $page : 1; //默认显示第一页数据

        //解析列表规则
        $fields = $model['fields'];
        // 关键字搜索
        $map0    =   empty($extend[0])?array():$extend[0];
        $map1    =   empty($extend[1])?array():$extend[1];
        
        $user = D("User");
        $spend = D("Spend");
        
        //读取模型数据列表
        $data = array();
        
        $row    = 10;
        
        $map1['is_check'] = $map0['is_check'] = 1;
        $map0['fgame_id'] = array('neq',0);
        
        $map1['pay_status'] = 1;

        $offset = ($page-1)*$row;
        
        $sql0 = $user->table("__USER__ as u ")
            ->field('u.id,u.promote_id,u.fgame_id as game_id,if(p.parent_id=0,u.promote_id,p.parent_id) as parent_id,u.fgame_name as game_name')
            ->join('__PROMOTE__ as p on(p.id=u.promote_id)','left')
            ->where($map0)
            ->order("u.id")
            ->group("u.id")       
            ->select(false);
        $sql0 = "select parent_id as promote_id,group_concat(promote_id) as chpids,game_id,game_name,count(id) as total_number from ("
            .$sql0
            ." ) as a group by a.game_id,a.parent_id order by parent_id " ;
            
        $data0 = $user->query($sql0." limit $offset,$row");  
        
        $count0 = count($user->query($sql0));              
        
        $sql1 = $spend->table("__SPEND__ as s")
            ->field("s.id,s.promote_id,s.game_id,if(p.parent_id=0,s.promote_id,p.parent_id) as parent_id,s.game_name,s.pay_amount ")
            ->join('__PROMOTE__ as p on(p.id=s.promote_id)','left')
            ->where($map1)
            ->group("id")
            ->select(false);
        $sql1 = "select parent_id as promote_id,group_concat(promote_id) as chpids,game_id,game_name,sum(pay_amount) as total_money from ( "
            .$sql1
            ." ) as a group by game_id,parent_id order by parent_id ";
        
        $data1 = $spend->query($sql1." limit $offset,$row");
        
        $count1 = count($spend->query($sql1));
        if (!empty($data1) && !empty($data0)) {
            foreach ($data1 as $j => $u) {
                foreach ($data0 as $k => $v) {
                    if (($u['promote_id'] == $v['promote_id']) && ($u['game_id'] == $v['game_id'])) {
                        if ($u['chpids'] !== $v['chpids']) {
                            $uchpids = (explode(',',$u['chpids']));
                            $schpids = (explode(',',$v['chpids']));
                            $chpids = array_unique(array_merge($uchpids,$schpids));
                            sort($chpids);
                            $u['chpids'] = $v['chpids'] = implode(',',$chpids);
                        }
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

        public function page_array($count,$page,$array,$order=0){
          global $countpage; #定全局变量
          $page=(empty($page))?'1':$page; #判断当前页面是否为空 如果为空就表示为第一页面 
            $start=($page-1)*$count; #计算每次分页的开始位置
          if($order==1){
           $array=array_reverse($array);
          }  
          $totals=count($array); 
          $countpage=ceil($totals/$count); #计算总页面数
          $pagedata=array();
         $pagedata=array_slice($array,$start,$count);
          return $pagedata; #返回查询数据
        }

        /**
         * 分页及显示函数
         * $countpage 全局变量，照写
         * $url 当前url
         */
        public function show_array($countpage,$url,$page){
           $page=empty($page)?1:$page;
         if($page > 1){
           $uppage=$page-1;
         }else{
          $uppage=1;
         }
         if($page < $countpage){
           $nextpage=$page+1;
         
         }else{
           $nextpage=$countpage;
         }
           $str='<div style="border:1px; height:30px; color:#9999CC">';
           // $str.="<span class='current'>1</span>";
           // $str.="<a class='num' href='/admin.php?s=/Promote/ch_reg_list/p/2'>2</a> ";
           // $str.=" <a class='next href='#'>&gt;&gt;</a>";
           // $str.="<span class='rows'>共 {$countpage} 条记录</span>";
         $str.="<p>共 {$countpage} 页 / 第 {$page} 页</p>";
         $str.="<span class='current'><a href='$url/page/1'>  首页 </a></span>";
         $str.="<span><a href='$url/page/{$uppage}'> 上一页 </a></span>";
         $str.="<span><a href='$url/page/{$nextpage}'>下一页 </a></span>";
         $str.="<span><a href='$url/page/{$countpage}'>尾页 </a></span>";
         $str.='</div>';
         return $str;
        }

        

    public function cpsettl_list($model = null,$p,$extend=array()){
        $model || $this->error('模型名标识必须！');
        $page = intval($p);
        $page = $page ? $page : 1; //默认显示第一页数据
        //解析列表规则
        $fields = $model['fields'];
        // 关键字搜索
        $map    =   empty($extend)?array():$extend;
        $row    = empty($model['list_row']) ? 10 : $model['list_row'];
        //读取模型数据列表
        $name = $model['m_name'];
        // var_dump($fields);exit;
        $new_model = M($name,'tab_');
        $data = $new_model 
            ->field($fields)
            // 查询条件
            ->where($map)
            /* 默认通过id逆序排列 */
            ->order($model['order'])
            ->join('tab_game as g on g.id=s.game_id','LEFT')
            //根据字段分组
            ->group($model['group'])
            /* 数据分页 */
            ->page($page,$row)
            /* 执行查询 */
            ->select();
            // var_dump( $new_model->getlastsql());exit;
        /* 查询记录总数 */
        $count = $new_model 
            ->field($fields)
            // 查询条件
            ->where($map)
            /* 默认通过id逆序排列 */
            ->order($model['order'])
            ->join('tab_game as g on g.id=s.game_id','LEFT')
            //根据字段分组
            ->group($model['group'])
            /* 执行查询 */
            ->select();
            $count=count($count);
        static $alltotal=0;
        // var_dump($data);exit;
        foreach ($data as $key => $value) {
            $alltotal=$alltotal+$value['total']*$value['selle_ratio']/100;
        }
         //分页
        if($count > $row){
            $page = new \Think\Page($count, $row);
            $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
            $this->assign('_page', $page->show());
        }
        $this->assign('model', $model);
        $this->assign('data', $data);
        $this->assign('alltotal', $alltotal);
        $this->meta_title = $model['title'].'列表';
        $this->display($model['template_list']);
    }
}