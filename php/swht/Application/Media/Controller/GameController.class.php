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
class GameController extends BaseController {

	public function game_list($game_type=0,$p=0){
		$map['game_status'] = 1;
		empty($_REQUEST['game_type']) ?  "":$map['game_type_id'] = $_REQUEST['game_type'];
		empty($_REQUEST['search_key'])? "":$map['game_name'] = array('like','%'.trim($_REQUEST['search_key']).'%');
		$model = array(
			'm_name' => 'Game',
			'prefix' => 'tab_',
			'map' => $map,
			'order' => 'sort asc',
			'template_list' => 'Game/game_list'
		);
		parent::lists($model,$p);
	}

	public function gift_list($game_id=0,$p=0){
		$map['game_status'] = 1; 
		if($game_id !=0){ $map['game_id'] = $game_id; }
		empty($_REQUEST['search_key'])? "":$map['game_name'] = array('like','%'.$_REQUEST['search_key'].'%') ;
		//$map['game_type'] = $game_type==0?array("in",'1,2,3,4,5,6,7,8,9,10'):$game_type;
		$model = array(
			'm_name' => 'Giftbag',
			'prefix' => 'tab_',
			'field' =>array('tab_giftbag.id,giftbag_name,desribe,start_time,end_time,icon,game_id'),
			'join' =>'tab_game ON tab_giftbag.game_id = tab_game.id',
			'map' => $map,
			'order' => 'id desc',
			'template_list' => 'Game/gift_list'
		);
		parent::join_list($model,$p);
	}

	/* 文档模型详情页 */
	public function game_detail($id = 0, $p = 1){
		/* 获取详细信息 */
		$game = new GameModel();//M('Game','tab_');
		$game->detail();
		$info = $game->detail($id);
		if(!$info){
			$this->error($game->getError());
		}
		$tmpl = 'Game/game/game_detail';

		/* 更新浏览数 */
		//$map = array('id' => $id);
		//$Document->where($map)->setInc('view');

		/* 模板赋值并渲染模板 */
		$this->assign('data', $info);
		$this->assign('page', $p); //页码
		$this->display($tmpl);
	}

	/* 文档分类检测 */
	private function category($id = 0){
		/* 标识正确性检测 */
		$id = $id ? $id : I('get.category', 0);
		if(empty($id)){
			$this->error('没有指定文档分类！');
		}

		/* 获取分类信息 */
		$category = D('Category')->info($id);
		if($category && 1 == $category['status']){
			switch ($category['display']) {
				case 0:
					$this->error('该分类禁止显示！');
					break;
				//TODO: 更多分类显示状态判断
				default:
					return $category;
			}
		} else {
			$this->error('分类不存在或被禁用！');
		}
	}

	public function dow_url_generate($game_id=null){
		$url = "http://".$_SERVER['SERVER_NAME']."/media.php?s=/Down/down_file/game_id/".$game_id."/type/1.html";//
		$qrcode = $this->qrcode(base64_encode($url));
		return $qrcode;
	}

	public function qrcode($url='pc.vlcms.com',$level=3,$size=4){
		Vendor('phpqrcode.phpqrcode');
		$errorCorrectionLevel =intval($level) ;//容错级别 
		$matrixPointSize = intval($size);//生成图片大小 
		$url = base64_decode($url);
		//生成二维码图片 
		//echo $_SERVER['REQUEST_URI'];
		$object = new \QRcode();
		echo $object->png($url, false, $errorCorrectionLevel, $matrixPointSize, 2);   
	}

	public function error_mesg(){
		$mid = session('member_auth.mid');
		if(empty($mid)){
			$this->ajaxReturn(array('msg'=>'no-login'));
		}

		$model = M('message','tab_');
		$map['game_id'] = $_REQUEST['game_id'];
		$map['user_id'] = session('member_auth.mid');
		$d = $model->where($map)->find();
		if(!empty($d)){
			$this->ajaxReturn(array('msg'=>'no'));
		}

		
		$data['game_id'] = $_REQUEST['game_id'];
		//$data['game_name'] = $_REQUEST['game_game'];
		$data['user_id'] = session('member_auth.mid');
		$data['title'] = "游戏无法下载";
		$data['content'] = "";
		$data['status'] = 0;
		$data['type'] = 0;
		$data['create_time'] = NOW_TIME;
		if($model->add($data)){
			$this->ajaxReturn(array('msg'=>'ok'));
		}
	}

}
