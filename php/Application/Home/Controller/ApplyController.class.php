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
        if(isset($_REQUEST['game_name'])){
            //$map['tab_game.game_name']=trim($_REQUEST['game_name']);
            $map['tab_game.game_name'] = array('like','%'.$_REQUEST['game_name'].'%') ;
        }
        $page = intval($p);
        $page = $page ? $page : 1; //默认显示第一页数据
        $row    = 10;
        $data = M("game","tab_")
            /* 查询指定字段，不指定则查询所有字段 */
            ->field("tab_game.id,tab_game.game_name,icon,game_type_name,game_size,version,recommend_status,game_address,promote_id,status,dow_status")
            ->join("tab_apply ON tab_game.id = tab_apply.game_id and tab_apply.promote_id = ".get_pid(),"LEFT")
            // 查询条件
            ->where($map)
            /* 默认通过id逆序排列 */
            ->order("id asc")
            /* 数据分页 */
            ->page($page, $row)
            /* 执行查询 */
            ->select();
        // $pa_id=get_parent_id(get_pid());//一级推广员id
        // if($pa_id){
        //     $ap_da= M('apply','tab_')->where(array('promote_id'=>$pa_id,'status'=>1))->select();//获取一级推广员 游戏申请记录
        //     foreach ($ap_da as $key => $value) {
        //         $pag[]=$value['game_id'];
        //         $patt[$value['game_id']]=$value['pattern'];
        //     }
        //     foreach ($data as $key => $value) {
        //         $dada[]=$value['id'];
        //     }
        //     $gare=array_intersect($pag,$dada);
        //     foreach ($gare as $key => $value) {
        //        $daga[$value]=array_search($value,$dada);
        //     }
        //     foreach ($daga as $key => $value) {
        //         foreach ($data[$value] as $k => $val) {
        //             if($k=='pattern'){
        //                 $val=$patt[$key];
        //                 $ddd=&$data[$value];
        //                 $ddd['pattern']=$val;
        //             }
        //         }
        //     }
        // }else{

        // } 
        /* 查询记录总数 */
        $count = M("game","tab_")
            /* 查询指定字段，不指定则查询所有字段 */
            ->field("tab_game.id,game_name,icon,game_type_name,file_size,version,recommend_status,game_address,promote_id,status,dow_status")
            ->join("tab_apply ON tab_game.id = tab_apply.game_id and tab_apply.promote_id = ".get_pid(),"LEFT")
              ->where($map)
            ->count();
        //分页
        if($count > $row){
            $page = new \Think\Page($count, $row);
            $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
            $this->assign('_page', $page->show());
        }
        $this->assign("count",$count);
        $this->assign('model', $model);
        $this->assign('list_data', $data);
        $this->display();
    }
    public function gapply(){
        $model = new ApplyModel(); //D('Apply');
        $map['game_id'] = array('in',$_REQUEST['game_id']);
        $map['promote_id']= session("promote_auth.pid");
        $c=$model->where($map)->select();
        $_REQUEST['game_id']=explode(',', $_REQUEST['game_id']);
        foreach ($c as $key => $value) {
            $va[]=$value['game_id'];
        }
        if(!empty($va)){
            $game_id=array_diff($_REQUEST['game_id'], $va);
        }else{
            $game_id=$_REQUEST['game_id'];
        }

        if(empty($game_id)){
            $this->error('游戏已申请过，请勿重复申请',U("index"));
            exit;
        }
        // $pattern=explode(',', $_REQUEST['pattern']);
        $_REQUEST['game_id']=implode(',', $game_id);
        $data['game_id'] = array('in',$_REQUEST['game_id']);
        $data['promote_id'] = session("promote_auth.pid");
        $data['promote_account'] = session("promote_auth.account");
        $data['apply_time'] = NOW_TIME;
        $data['status'] = 0;
        $data['enable_status'] = 1;
        $game=M('Game','tab_');
        foreach ($game_id as $key => $value) {
            $data['game_id']=$value;
            $gdata=$game->where(array('id'=>$value))->find();
            $data['game_name']=get_game_name($value);
            $data['ratio']=$gdata['ratio'];
            $data['money']=$gdata['money'];
            // $data['pattern']=current($pattern);
            // next($pattern);
            $res = $model->add($data);
        }
        $this->success("申请成功",U("index"));
    }
    public function my_game($type=-1,$p=0){
        $map['promote_id'] = session("promote_auth.pid");
        if($type==-1){
            unset($map['status']);
        }else{
            $map['status'] =  $type;
        }
        if($_REQUEST['game_name']!=null){
            $map['tab_game.game_name']=array('like','%'.$_REQUEST['game_name'].'%');
        }
        if($_REQUEST['pattern']!=null){
            $map['tab_apply.pattern']=$_REQUEST['pattern'];
        }
    	$page = intval($p);
        $page = $page ? $page : 1; //默认显示第一页数据
        $row    = 10;
        $data = M("game","tab_")
            /* 查询指定字段，不指定则查询所有字段 */
            ->field("tab_game.*,tab_apply.promote_id,tab_apply.status")
            ->join("tab_apply ON tab_game.id = tab_apply.game_id and tab_apply.promote_id = ".session('promote_auth.pid'))
            // 查询条件
            ->where($map)
            /* 默认通过id逆序排列 */
            ->order("sort asc")
            /* 数据分页 */
            ->page($page, $row)
            /* 执行查询 */
            ->select();

        /* 查询记录总数 */
        $count =  M("game","tab_")
            /* 查询指定字段，不指定则查询所有字段 */
            ->field("tab_game.*,tab_apply.promote_id,tab_apply.status")
            ->join("tab_apply ON tab_game.id = tab_apply.game_id and tab_apply.promote_id = ".session('promote_auth.pid'))
            // 查询条件
            ->where($map)
            ->count();

        //分页
        if($count > $row){
            $page = new \Think\Page($count, $row);
            $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
            $this->assign('_page', $page->show());
        }
        $url="http://".$_SERVER['HTTP_HOST'].__ROOT__."/media.php/member/preg/pid/".session("promote_auth.pid");
        $this->assign("url",$url);
        $this->assign("count",$count);
        $this->assign('model', $model);
        $this->assign('list_data', $data);
        $this->display();
    }

    /**
	申请游戏
    */
    public function apply(){
    	if(isset($_POST['game_id'])){
            $game=M('Game','tab_');
            $gdata=$game->where(array('id'=>$_POST['game_id']))->find();
            $pattern=$_POST['pattern'];
            $ratio=$gdata['ratio'];
            $money=$gdata['money'];
    		$model = new ApplyModel(); //D('Apply');
    		$data['game_id'] = $_POST['game_id'];
            $data['game_name'] = get_game_name($_POST['game_id']);
    		$data['promote_id'] = session("promote_auth.pid");
            $data['promote_account'] = session("promote_auth.account");
    		$data['apply_time'] = NOW_TIME;
            $data['pattern'] = $_POST['pattern'];
    		$data['status'] = 0;
    		$data['enable_status'] = 1;
            // $data['pattern']=$pattern;
            $data['ratio']=$ratio;
            $data['money']=$money;
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