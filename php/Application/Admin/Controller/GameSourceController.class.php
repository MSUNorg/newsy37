<?php

namespace Admin\Controller;
use User\Api\UserApi as UserApi;

/**
 * 后台首页控制器
 * @author zxc <zuojiazi@vip.qq.com>
 */
class GameSourceController extends ThinkController {
	const model_name = 'GameSource';

    public function lists(){
        if(isset($_REQUEST['game_name'])){
            $extend['game_name']=array('like','%'.$_REQUEST['game_name'].'%');
            unset($_REQUEST['game_name']);
        }
    	parent::lists(self::model_name,$_GET["p"],$extend);
    }

    public function add($value='')
    {
    	if(IS_POST){
    		if(empty($_POST['game_id'])){
                $this->error('游戏名称不能为空');
            }
            $map['game_id']=$_POST['game_id'];
            $map['file_type'] = $_POST['file_type'];
            $d = D('Game_source')->where($map)->find();
            $source = A('Source','Event');
            if(empty($d)){
                $source->add_source();
            }
            else{
            $this->error('游戏已存在原包',U('GameSource/lists'));
            }
    	}
    	else{
            $this->meta_title = '新增游戏原包';
    		$this->display();
    	}
    	
    }

    public function edit($id){
         $map['id']=$id;
        if(IS_POST){
            $map['file_type'] = $_POST['file_type'];
            $d = D('Game_source')->where($map)->find();
            $source = A('Source','Event');
            if(empty($d)){
                $source->add_source();
            }
            else{
                $source->update_source($d['id'],$d['file_name']);
            }
        }
        else{
            $d = M('GameSource',"tab_")->where($map)->find();
            $this->assign("data",$d);
            $this->display();
        }
        
    }
}
