<?php
namespace Admin\Event;
use Think\Controller;
/**
 * 后台事件控制器
 * @author zxc
 */
class UserEvent extends BaseEvent {
    
    public function lists($model=null,$p=1,$extends_map=array()){
        parent::custom_list($model,$p,$extends_map);
    }
    public function user_join($model = null, $p = 0){
        $model || $this->error('模型名标识必须！');
        $page = intval($p);
        $page = $page ? $page : 1; //默认显示第一页数据
        //解析列表规则
        $fields = $model['fields'];
        // 关键字搜索
        $map    =  $model['map']; //array();
        foreach ($key as $key => $value) {
            if(isset($_REQUEST[$value])){
                $map[$value]  =   array('like','%'.$_GET[$value].'%');
                unset($_REQUEST[$value]);
            }
        }
        // 条件搜索
        foreach($_REQUEST as $name=>$val){
            if(in_array($name,$fields)){
                $map[$name] =   $val;
            }
        }

        $row    = empty($model['list_row']) ? 15 : $model['list_row'];

        //读取模型数据列表
        $name = $model['m_name'];
        //$new_model = D($name);
        $data = D($name)
            ->field('tab_user.*,tab_user_play.game_id,tab_user_play.game_name')
            ->join('tab_user_play ON tab_user.id = tab_user_play.user_id','LEFT')
            // 查询条件
            ->where($model['map'])
            /* 默认通过id逆序排列 */
            ->order($model['order'])
            ->group('tab_user.id')
            /* 数据分页 */
            ->page($page, $row)
            /* 执行查询 */
            ->select();
        /* 查询记录总数 */
        $count = D($name)
            ->field('tab_user.*,tab_user_play.game_id,tab_user_play.game_name')
            ->join('tab_user_play ON tab_user.id = tab_user_play.user_id','LEFT')
            // 查询条件
            ->group('tab_user.id')
            ->where($map)
            ->select();
            $count=count($count);
         //分页
        if($count > $row){
            $page = new \Think\Page($count, $row);
            $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
            $this->assign('_page', $page->show());
        }
        $this->assign('model', $model);
        $this->assign('list_data', $data);
        $this->assign('count', $count);
        $this->meta_title = $model['title'].'列表';
        
        $this->display($model['template_list']);
    }
    /**
	*获取用户实体
	*@param int $id
	*@return array
	*@author 小纯洁 
    */
    public function user_entity($id=0){
    	$user = M("user","tab_");
    	$map['id'] = $id;
    	$data = $user->where($map)->find();
    	return $data;
    }
    
}
