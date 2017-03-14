<?php
namespace Media\Model;
use Think\Model;

/**
 * 插件模型
 */

class SlidemanageModel extends Model {
	protected $_validate = array(
		// array('GameName','require','游戏名称必须填写'),
		// array('ListOrder','number','排序必须是数字'),
		// array('AndDowUrl','require','安卓下载地址必须填写'),
		// array('IosDowUrl','require','IOS下载地址必须填写'),
		// array('AndDowUrl','url','安卓下载地址不合法'),
		// array('IosDowUrl','url','IOS下载地址不合法'),
		// array('Operator','require','运营商必须填写'),
	);

	//获取树的根到子节点的路径
	public function getPath($id){
		$path = array();
		$nav = $this->where("id={$id}")->field('id,pid,title')->find();
		$path[] = $nav;
		if($nav['pid'] >1){
			$path = array_merge($this->getPath($nav['pid']),$path);
		}
		return $path;
	}
}