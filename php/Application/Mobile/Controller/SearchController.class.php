<?php
namespace Mobile\Controller;
use Think\Controller;
class SearchController extends BaseController {
	public function index($p=1,$keyword='') {
		$model["model"] = "Game";
		$model['where']=' game_status=1 ';
		$model["search_key"] = "game_name";					
		$model['search_value'] =$keyword;
        $result = parent::search($model,$p);
		if ($result) {
			$this->assign('search_data', $result['list']);			
			$this->assign('total', $result['total']);			
			$this->assign('page', intval($p)+1);
		} else {
			$model['model']='Game';
			$model['field']='game_name';
			$model['order']='dow_num';
			$game = parent::showlist($model,24);
			$search = array();
			$i=0;
			foreach($game as $k => $g ) {
				if ($k%6==0 && $k > 0) {
					$i++;
				}
				$search[$i][] = $g;				
			}
			$this->assign('search',$search);			
		}
		$this->assign('search_key',$keyword);

		$this->display();
	}
	
	// 异步返回
	public function search($p=2,$keyword='') {
		$game['model']='Game';
		$game['where']=' game_status=1 ';
		$game["search_key"] = "game_name";					
		$game['search_value'] =$keyword;
        $result = parent::search($game,$p);
		if($result['list']&&$result['total']) {
			$glist = '';
			foreach($result['list'] as $k => $g) {
				foreach($g as $k2 =>$v) {
					if ('Introduction' === $k2) {
						$g['Introduction'] = strip_tags($v);
					}
					if ('icon' === $k2) {
						$pic = get_cover($v);
						$g['picurl'] = $pic['path'];
					}
					if ('game_type_id'=== $k2) {
						$g['game_type_id']=get_game_type($v);
					}
				}
				$glist[]=$g;
			}
			$data = array(
				'data'		=>	$glist,
				'page' 		=> 	intval($p)+1,
				'total' 	=> 	$gl['total'],
				'status'	=> 1,
			);
		} else {
			$data = array(
				'data'		=>	'',
				'page' 		=> 	$p,
				'status'	=> 0,
			);
		}
		echo json_encode($data);
		
	}
}