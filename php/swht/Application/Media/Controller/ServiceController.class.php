<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: zxc <zuojiazi@vip.qq.com> <http://www.msun.com>
// +----------------------------------------------------------------------

namespace Media\Controller;
use Admin\Model\GameModel;

/**
 * 文档模型控制器
 * 文档模型列表和详情
 */
class ServiceController extends BaseController {

	public function index($value='')
	{
		$this->display();
	}

	public function uploadAvatar() {
		//dump($_FILES);
		// 成功  $this->ajaxReturn(array('state'=>'SUCCESS','url'=>'图片地址'),C('DEFAULT_AJAX_RETURN'));
		$this->ajaxReturn(array('state'=>'上传失败'),C('DEFAULT_AJAX_RETURN'));
	}
	
	public function sask() {
		if(IS_POST) {
			
		}
		
		$this->display();
	}
	
	public function sask2() {
		if(IS_POST) {
			
		}
		
		$this->display();
	}
	
	public function sask3() {
		if(IS_POST) {
			
		}
		
		$this->display();
	}
	
	public function spwd($p=1) {
		/* $model["model"] = "Service";
		$model['where']="sMark='spwd' and sPlatMark='".C('PLATMARK')."'";
        parent::pagelists($model,$p);
		$this->assign('spage','spwd'); */
		
		$this->display();
	}
	
	public function spay($p=1) {
		/* $model["model"] = "Service";
		$model['where']="sMark='spay' and sPlatMark='".C('PLATMARK')."'";
        parent::pagelists($model,$p);
		$this->assign('spage','spay'); */
		
		$this->display();
	}
	
	public function saccont($p=1) {
		/* $model["model"] = "Service";
        $model['where']="sMark='saccont' and sPlatMark='".C('PLATMARK')."'";
        parent::pagelists($model,$p);
		$this->assign('spage','saccont'); */
		
		$this->display();
	}
	
	public function sgift($p=1) {
		/* $model["model"] = "Service";
        $model['where']="sMark='sgift' and sPlatMark='".C('PLATMARK')."'";
        parent::pagelists($model,$p);
		$this->assign('spage','sgift'); */
		
		$this->display();
	}
	
	public function sother($p=1) {
		/* $model["model"] = "Service";
        $model['where']="sMark='sother' and sPlatMark='".C('PLATMARK')."'";
        parent::pagelists($model,$p);
		$this->assign('spage','sother'); */
		
		$this->display();
	}

}
