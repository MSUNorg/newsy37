<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: zxc <zuojiazi@vip.qq.com> <http://www.msun.com>
// +----------------------------------------------------------------------
namespace Home\Event;
use Think\Controller;
/**
 * 后台首页控制器
 * @author zxc <zuojiazi@vip.qq.com>
 */
class PromoteEvent extends BaseEvent {

	/**
	获取基本信息
	*/
    public function baseinfo($tem_edit,$id=0) {
        $model = M('Promote','tab_');
        $data = $model->find($id);
        $this->assign("data",$data);
        $this->display($tem_edit);
    }

    public function chlid_lists($model,$p){
    	parent::lists($model,$p);
    }
   
}
