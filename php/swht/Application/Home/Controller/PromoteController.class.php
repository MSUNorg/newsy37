<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: zxc <zuojiazi@vip.qq.com> <http://www.msun.com>
// +----------------------------------------------------------------------

namespace Home\Controller;
use OT\DataDictionary;
use User\Api\PromoteApi;
/**
 * 前台首页控制器
 * 主要获取首页聚合数据
 */
class PromoteController extends BaseController {
	//系统首页
    public function index(){
        $this->display();
    }

    /**
	* 我的基本信息
    */
    public function base_info(){
    	if(IS_POST){
    		$type = I('request.type',0);
    		$user = new PromoteApi();
    		$data = $_POST;
    		$res  = $user->edit($data);
    		if($res !==false){
    			$this->success('修改成功');
    		}
    		else{
    			$this->error('修改失败');
    		}
        }
        else{
            $model = M('Promote','tab_');
	        $data = $model->find(session("promte_auth.pid"));
	        $this->meta_title = "基本信息";
	        $this->assign("data",$data);
	        $this->display();
        }
    }

    /**
	*子账号
    */
    public function mychlid($p=0){
        $map['parent_id'] = session("promote_auth.pid");
        parent::lists("Promote",$p,$map);
    }

    public function add_chlid(){
        if(IS_POST){
            $user = new PromoteApi();
            $res = $user->promote_add($_POST);
            if($res){
                $this->success("子账号添加成功",U('Promote/mychlid'));
            }
            else{
                $this->error("添加子账号失败");
            }
        }
        else{
            $this->display();
        }
        
    }

    public function edit_chlid($id = 0){
        if(IS_POST){
            $user = new PromoteApi();
            $res = $user->edit();
            if($res){
                $this->success("子账号修改成功",U('Promote/mychlid'));
            }
            else{
                $this->error("修改子账号失败");
            }
        }
        else{
            $promote = A('Promote','Event');
            $promote->baseinfo('edit_chlid',$id);
        }
        
    }
}