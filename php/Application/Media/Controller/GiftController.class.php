<?php
namespace Media\Controller;
use Think\Controller;
/** 
* 礼包控制器 
*/
class GiftController extends BaseController{
	public function __construct() {
		parent::__construct();
		$this->GiftModel=D('Giftbag');
		define("GIFT", "gift_top_media");
	}

	#主页
	public function index() {
		$this->assign('everybody',$this->GiftModel->everybody());
		$this->assign('banner',$this->GiftModel->gift_banner());
		$this->display();
	}

	#我的礼包
	public function gift() {
		$session=D("User")->isLogin();
		$this->assign('everybody',$this->GiftModel->everybody());
		$this->assign('my_gift',$this->GiftModel->my_gift($session['uid']));
		$this->display();

	}

	#全部礼包 
	public function lists() {
		$this->assign(array(
				'everybody'=>$this->GiftModel->everybody(),
				'gift'=>$this->GiftModel->gift_list_limt_clone(I('category')),
				'nameid'=>I('nameid')?I('nameid'):-1,
				'typeid'=>I('category'),
			));
		$this->display();
	}

	#礼包详情
	public function detail() {
		$this->assign(array(
				'data'=>$this->GiftModel->gift_detail(),
				'everybody'=>$this->GiftModel->everybody(),
				'recom'=>$this->GiftModel->gift_recommend_limt(1,'`id` DESC',1,true,6),
				'news'=>D('Document')->decument_entity_top($category='43'),
			 ));
		$this->display();
	}

}

