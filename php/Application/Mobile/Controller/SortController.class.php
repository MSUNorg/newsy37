<?php
namespace Mobile\Controller;
use Think\Controller;
class SortController extends BaseController {

	public function index($p=1,$type=null) {
		$game['model']='Game';
		// 新游排行
		$game['where'] = ' game_status=1 ';
		$game['order'] = ' game_score desc,recommend_status desc,sort DESC';
		$this->assign('newsgame',parent::showlist($game,8));
		
		// 休闲排行
		$game['where'] = ' game_status=1 and game_type_id=13';
		$game['order'] = ' game_score desc,sort DESC';
		$this->assign('casual',parent::showlist($game,4));
		
		// 热门游戏
		$game['where']=' game_status=1 ';
		$game['order']=' game_score desc, dow_num desc,sort DESC ';
		$game['limit']=8;
		$game['page']=1;
		$gl = parent::getlists($game);
		$this->assign('hot',$gl['list']);
		$this->assign('page',2);
		$this->assign('total',$gl['total']);
		
		$this->display();
	}
	
	// 热门游戏 异步返回
	public function hot($p=1) {
		$game['model']='Game';
		$game['where']=' game_status=1 ';
		$game['order']=' dow_num desc,sort DESC ';
		$game['limit']=8;
		$game['page']=$p;
		$gl = parent::getlists($game);
		if($gl['list']&&$gl['total']) {
			$glist = '';
			foreach($gl['list'] as $k => $g) {
				foreach($g as $k2 =>$v) {
					if ('icon' === $k2) {
						$v2 = get_cover($v);
						$g['picurl'] = $v2['path'];
					}
					if ('game_type_id'=== $k2) {
						$g['game_type_id']=get_game_type($v);
					}
				}				$g['game_down']=U('Down/down_file?game_id='.$g['id']);
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
