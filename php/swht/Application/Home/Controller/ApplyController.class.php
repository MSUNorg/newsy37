<?php

namespace Home\Controller;
use OT\DataDictionary;
use Admin\Model\ApplyModel;

/**
 * 前台首页控制器
 * 主要获取首页聚合数据
 */
class ApplyController extends BaseController {

    public function jion_list($model=array(),$p,$map = array()){

        $page = intval($p);
        $page = $page ? $page : 1; //默认显示第一页数据
        $name = $model['name'];
        $row    = empty($model['list_row']) ? 15 : $model['list_row'];
        $data = M($name,'tab_')
            /* 查询指定字段，不指定则查询所有字段 */
            ->field(empty($fields) ? true : $fields)
            ->join($model['jion'])
            // 查询条件
            ->where($map)
            /* 默认通过id逆序排列 */
            ->order($model['need_pk']?'id DESC':'')
            /* 数据分页 */
            ->page($page, $row)
            /* 执行查询 */
            ->select();

        /* 查询记录总数 */
        $count = M($name,"tab_")->where($map)->count();

        //分页
        if($count > $row){
            $page = new \Think\Page($count, $row);
            $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
            $this->assign('_page', $page->show());
        }

        $this->assign('list_data', $data);
        $this->meta_title = $model['title'];
        $this->display($model['tem_list']);
    }
	//首页
    public function index($p = 0){
    	$model = array(
            'name'  => 'Game',
            'field' => 'tab_game.id,game_name,icon,game_type,game_size,version,recommend_status,game_address,promote_id,status,dow_status',
            'join'  => 'tab_apply ON tab_game.id = tab_apply.game_id and tab_apply.promote_id = '.PROMOTE_ID,
            'join_type' => 'LEFT',
            'order' => 'sort asc',
            'tem_list' =>'index'
        );
        $this->lists($model,$p);
    }

    public function my_game($type=-1,$p=0){
    	$model = array(
            'name'  => 'Game',
            'field' => 'tab_game.*,tab_apply.promote_id,tab_apply.status',
            'join'  => 'tab_apply ON tab_game.id = tab_apply.game_id and tab_apply.promote_id = '.session('promote_auth.pid'),
            'need_pk' => true,
            'title' =>'游戏申请',
            'tem_list' =>'my_game'
        );
        $url="http://".$_SERVER['HTTP_HOST'].__ROOT__."/media.php/member/preg/pid/".session("promote_auth.pid");
        $this->assign("url",$url);
        $map['promote_id'] = session("promote_auth.pid");
        if($type !== -1){
            $map['status'] =  $type;
        }
        
    	$this->lists("apply",$p,$map);
    }

    /**
	申请游戏
    */
    public function apply(){
    	if(isset($_POST['game_id'])){
    		$model = new ApplyModel(); //D('Apply');
    		$data['game_id'] = $_POST['game_id'];
    		$data['promote_id'] = session("promote_auth.pid");
            $data['promote_account'] = session("promote_auth.account");
    		$data['apply_time'] = NOW_TIME;
    		$data['status'] = 0;
    		$data['enable_status'] = 1;
    		$res = $model->add($data);
    		if($res){
    			$this->ajaxReturn(array("status"=>"1","msg"=>"申请成功"));
    		}
    		else{
    			$this->ajaxReturn(array("status"=>"0","msg"=>"申请失败"));
    		}
    	}
    	else{
    		$this->ajaxReturn(array("status"=>"0","msg"=>"操作失败"));
    	}
    	

    }

    

}