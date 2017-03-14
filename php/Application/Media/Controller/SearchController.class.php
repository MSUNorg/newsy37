<?php
namespace Media\Controller;
use Think\Controller;
/**
* 搜索控制器
*/
class SearchController extends BaseController {
	public function index($keyword='',$p=1) {
		$this->assign('result',D('Game')->search_game());
		$this->assign("news",D('Document')->lists(43));
		$this->display();
		/*if (!empty($keyword)) {
			$model['model']='Game';
			$model['field']="g.*,gi.introduction";
			$model['join']="__GAME_INFO__ as gi on (gi.id=g.id)";
			$model['joinnum']="left";
			$model["search_key"] = "game_name,game_type";
			$model['search_isnum']="game_type";
			$model['search_logic']="OR";
			$model['search_value'] =$keyword;
			$model['where']=' game_status=1 ';
			$result = parent::search($model,$p);
			if($result['list']) {
				$this->assign('result',$result['list']);
				$this->assign('kw',$keyword);
				$this->assign('count',$result['count']);			
			} else {				
				$game['model']="Game";
				$game['where']="game_status=1 and recommend_status=1";
				$game['order']="id desc";
				$reco = parent::showlist($game,6);
				$this->assign('result',$reco);
			}			
		}*/
	}
}