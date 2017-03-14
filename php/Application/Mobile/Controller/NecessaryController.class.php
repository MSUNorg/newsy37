<?php
namespace Mobile\Controller;
use Think\Controller;
class NecessaryController extends BaseController {

	public function index() {
		$game['model']='Game';
		// 精选游戏
		$game['where'] = ' game_status=1 ';
		$game['order'] = ' sort DESC';
		$this->assign('necessary',parent::showlist($game,8));
		
		// 休闲游戏
		$game['where'] = ' game_status=1 and game_type_id=13';
		$game['order'] = ' recommend_level desc,sort DESC';
		$this->assign('casual',parent::showlist($game,4));
		
		// 装机必备
		$game['where']=' game_status=1 ';
		$game['order']=' game_score desc,sort DESC ';
		$game['limit']=8;
		$game['page']=1;
		$gl = parent::getlists($game);
		$this->assign('essential',$gl['list']);
		$this->assign('page',2);
		$this->assign('total',$gl['total']);
		
		$this->display();
	}
	
	public function essential($p=1) {
		$game['model']='Game';
		$game['where']=' game_status=1 ';
		$game['order']=' game_score desc,sort DESC ';
		$game['limit']=8;
		$game['page']=$p;
		$gl = parent::getlists($game);
		if($gl['list']&&$gl['total']) {
			$glist = '';
			foreach($gl['list'] as $k => $g) {
				foreach($g as $k2 =>$v) {
					if ('Introduction' === $k2) {
						$g['Introduction'] = strip_tags($v);
					}
					if ('icon' === $k2) {
						$v2 = get_cover($v);
						$g['picurl'] = $v2['path'];
					}
					if ('game_type_id'=== $k2) {
						$g['game_type_id']=get_game_type($v);
					}
				}
				$g['game_down']=U('Down/down_file?game_id='.$g['id']);
				$glist[]=$g;
			}
			$data = array(
				'data'		=>	$glist,
				'page' 		=> 	intval($p)+1,
				'total' 	=> 	$gl['total'],
				'status'	=> 1,
			);
		} else
			$data = array(
				'data'		=>	'',
				'page' 		=> 	$p,
				'status'	=> 0,
			);
		$this->ajaxReturn($data,C('DEFAULT_AJAX_RETURN'));	
	}
}
