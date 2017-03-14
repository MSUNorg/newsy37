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
        $sum=M($name,'tab_')->field('sum(money) as sum')->where($map)->find();
         //分页
        if($count > $row){
            $page = new \Think\Page($count, $row);
            $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
            $this->assign('_page', $page->show());
        }
        $this->assign('model', $model);
        $this->assign('count',$count);
        $this->assign('sum',$sum);
        $this->assign('list_data', $data);
        $this->meta_title = $model['title'].'列表';
        $this->display($model['template_list']);
    }
}
