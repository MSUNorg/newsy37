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
class IndexController extends HomeController {

	//系统首页
    public function index(){
        $map['status'] = 1;
        $map['rommend_status'] = 1;
        $data = M('Game',"tab_")->where($map)->order('id DESC')->limit(16)->select();
        $this->assign("game_list",$data);
        $this->display();
    }

    public function login(){
    	$account  = $_POST['account'];
    	$password = $_POST['password'];
    	$promote = new PromoteApi();
    	$result = $promote->login($account,$password);
    	if ($result >0) {
            $this->success("登陆成功",U('Promote/index'));
    		//header("location:$url");
    	}
    	else{
            $msg = "";
            switch ($result) {
                case -1:
                    $msg = "用户不存在";
                    break;
                case -2:
                    $msg = "密码错误";
                    break;
                case -3:
                    $msg = "用户被禁用,请联系管理员";
                    break;
                default:
                    $msg = "未知错误！请联系管理员";
                    break;
            }
    		$this->error($msg);
    	}
    }

    public function register(){
        if(IS_POST){
            $Promote = new PromoteApi();
            $pid = $Promote->register($_POST);
            if($pid > 0){
                $this->ajaxReturn(array('status'=>1,'info'=>$pid,'url'=>'index.php/Index/index'));
            }
            else{
                $this->ajaxReturn(array('status'=>0,'info'=>$pid));
            }
        }
        else{
            $this->display();
        }
        
    }

    /**
    *检测账号是否存在
    */
    public function checkAccount($account){
        $Promote = new PromoteApi;
        $res = $Promote->checkAccount($account);
        if($res){
            $this->ajaxReturn(true);
        }
        else{
            $this->ajaxReturn(false);
        }
        
    }

}