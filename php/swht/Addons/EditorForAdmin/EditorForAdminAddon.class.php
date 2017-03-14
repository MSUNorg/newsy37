<?php
// +----------------------------------------------------------------------
// | 手游平台
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.msun.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: zxc
// +----------------------------------------------------------------------

namespace Addons\EditorForAdmin;
use Common\Controller\Addon;

/**
 * 编辑器插件
 * @author yangweijie <yangweijiester@gmail.com>
 */

	class EditorForAdminAddon extends Addon{

		public $info = array(
			'name'=>'EditorForAdmin',
			'title'=>'后台编辑器',
			'description'=>'用于增强整站长文本的输入和显示',
			'status'=>1,
			'author'=>'thinkphp',
			'version'=>'0.2'
		);

		public function install(){
			return true;
		}

		public function uninstall(){
			return true;
		}

		/**
		 * 编辑器挂载的后台文档模型文章内容钩子
		 * @param array('name'=>'表单name','value'=>'表单对应的值')
		 */
		public function adminArticleEdit($data){
			$this->assign('addons_data', $data);
			$this->assign('addons_config', $this->getConfig());
			$this->display('content');
		}
	}
