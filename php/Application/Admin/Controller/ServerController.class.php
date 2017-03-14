<?php

namespace Admin\Controller;
use User\Api\UserApi as UserApi;

/**
 * 后台首页控制器
 * @author zxc <zuojiazi@vip.qq.com>
 */
class ServerController extends ThinkController {
	const model_name = 'Server';

    public function lists(){
        if(isset($_REQUEST['server_name'])){
            $extend['server_name']=array('like','%'.$_REQUEST['server_name'].'%');
            unset($_REQUEST['server_name']);
        }
        if(isset($_REQUEST['time-start']) && isset($_REQUEST['time-end'])){
            $extend['start_time']  =  array('BETWEEN',array(strtotime($_REQUEST['time-start']),strtotime($_REQUEST['time-end'])+24*60*60-1));
            unset($_REQUEST['time-start']);unset($_REQUEST['time-end']);
        }
        if(isset($_REQUEST['start']) && isset($_REQUEST['end'])){
            $extend['start_time']  =  array('BETWEEN',array(strtotime($_REQUEST['start']),strtotime($_REQUEST['end'])+24*60*60-1));
            unset($_REQUEST['start']);unset($_REQUEST['end']);
        }
        if(isset($_REQUEST['game_name'])){
            if($_REQUEST['game_name']=='全部'){
                unset($_REQUEST['game_name']);
            }else{
                $extend['game_name']=$_REQUEST['game_name'];
                unset($_REQUEST['game_name']);
            }
        }
    	parent::lists(self::model_name,$_GET["p"],$extend);
    }

    public function add(){
    	$model = M('Model')->getByName(self::model_name);
    	parent::add($model["id"]);
    }

    public function edit($id=0){
		$id || $this->error('请选择要编辑的用户！');
		$model = M('Model')->getByName(self::model_name); /*通过Model名称获取Model完整信息*/
		parent::edit($model['id'],$id);
    }

    public function del($model = null, $ids=null){
        $model = M('Model')->getByName(self::model_name); /*通过Model名称获取Model完整信息*/
        parent::del($model["id"],$ids);
    }
}
