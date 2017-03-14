<?php
namespace Home\Event;
use Think\Controller;
/**
 * 后台事件控制器
 * @author 王贺 
 */
class RechargeEvent extends BaseEvent {

    public function lists($model=null){
        //parent::join_more($model);
    }


    public function group_list($model = null,$p){
        $model || $this->error('模型名标识必须！');
        $page = intval($p);
        $page = $page ? $page : 1; //默认显示第一页数据

        //解析列表规则
        $fields = $model['fields'];
        // 关键字搜索
        $map    =   $model['map'];
        $map['tab_recharge.pay_status'] = 1;
        if(isset($_REQUEST['time-start']) && isset($_REQUEST['time-end']) && !empty($_REQUEST['time-start']) && !empty($_REQUEST['time-end'])){
            $map[$model['time_fields']]  =  array('BETWEEN',array(strtotime($_REQUEST['time-start']),strtotime($_REQUEST['time-end'])));
            unset($_REQUEST['time-start']);unset($_REQUEST['time-end']);
        }
        if(isset($_REQUEST['user_account']) && !empty($_REQUEST['user_account'])){
            $map['user_account'] = array('like','%'.$_REQUEST['user_account'].'%');
            unset($_REQUEST['user_account']);
        }
        if(isset($_REQUEST['game_appid']) && !empty($_REQUEST['game_appid'])){
            $map['tab_recharge.game_appid'] = $_REQUEST['game_appid'];
            unset($_REQUEST['game_appid']);
        }
        

      
        
        $row    = empty($model['list_row']) ? 10 : $model['list_row'];

        //读取模型数据列表
        $name = $model['m_name'];
        $data = M($name,'tab_')
            ->field($model['fields'])
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
        $count =M($name,'tab_')->where($map)->count();

        $total = M($name,'tab_')->where($map)->sum('pay_amount');
         //分页
        if($count > $row){
            $page = new \Think\Page($count, $row);
            $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
            $this->assign('_page', $page->show());
        }
        $this->assign('model', $model);
        $this->assign('count',$count);
        $this->assign('total_amount',$total);
        $this->assign('list_data', $data);
        $this->display($model['template_list']);
    }

    public function join_group_list($model = null,$p){
        $model || $this->error('模型名标识必须！');
        $page = intval($p);
        $page = $page ? $page : 1; //默认显示第一页数据

        //解析列表规则
        $fields = $model['fields'];
        // 关键字搜索
        $map    =   $model['map'];
        $map['tab_recharge.pay_status'] = 1;
        if(isset($_REQUEST['time-start']) && isset($_REQUEST['time-end']) && !empty($_REQUEST['time-start']) && !empty($_REQUEST['time-end'])){
            $map[$model['time_fields']]  =  array('BETWEEN',array(strtotime($_REQUEST['time-start']),strtotime($_REQUEST['time-end'])));
            unset($_REQUEST['time-start']);unset($_REQUEST['time-end']);
        }
        if(isset($_REQUEST['user_account']) && !empty($_REQUEST['user_account'])){
            $map['user_account'] = array('like','%'.$_REQUEST['user_account'].'%');
            unset($_REQUEST['user_account']);
        }
        if(isset($_REQUEST['game_appid']) && !empty($_REQUEST['game_appid'])){
            $map['tab_recharge.game_appid'] = $_REQUEST['game_appid'];
            unset($_REQUEST['game_appid']);
        }
        

      
        
        $row    = empty($model['list_row']) ? 10 : $model['list_row'];

        //读取模型数据列表
        $name = $model['m_name'];
        $data = M($name,'tab_')
            ->field($model['fields'])
            ->join('tab_game ON tab_recharge.game_appid = tab_game.game_appid')
            ->join('tab_promote ON tab_recharge.promote_id = tab_promote.id')
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
        $count =M($name,'tab_')
            ->join('tab_game ON tab_recharge.game_appid = tab_game.game_appid')
            ->join('tab_promote ON tab_recharge.promote_id = tab_promote.id')
            ->where($map)->group($model['group'])->count();

        $total = M($name,'tab_')->where($map)->sum('pay_amount');
         //分页
        if($count > $row){
            $page = new \Think\Page($count, $row);
            $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
            $this->assign('_page', $page->show());
        }
        $this->assign('model', $model);
        $this->assign('count',$count);
        $this->assign('total_amount',$total);
        $this->assign('list_data', $data);
        $this->display($model['template_list']);
    }

    public function extend_edit($model=null,$id=null,$data=null){
        $id || $this->error("请选择要编辑的数据");

        parent::extend_edit($model,$id,$data);
    }
}
