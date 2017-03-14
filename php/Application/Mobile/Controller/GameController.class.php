<?php
namespace Mobile\Controller;
use Think\Controller;
class GameController extends BaseController {

	public function index($p=1,$type=null,$cate=null) {
		$game['model']="Game";
		$game['where']=" game_status=1 ";
		$type = intval($type)?intval($type):0;
		if ($type && $type<9)
			$game['where'] .=" and game_type_id=$type";
		$game['order'] = 'game_score desc';
		switch($cate) {
			case 'n': 
				$game['order'] .= ' ,recommend_level desc,';
				break;
			case 'h':
				$game['order'] .=' ,dow_num desc,';
				break;
			default:
				$game['order'] = '';
				break;
		}
		$game['order'] .='sort DESC';
		$game['limit']=8;
		$game['page']=1;
		$gl = parent::getlists($game);
		$this->assign('game',$gl['list']);
		$this->assign('page',2);
		$this->assign('total',$gl['total']);
		$this->assign('type',$type);	
		
		// 广告		
		
		$syt = parent::showlist(array("model"=>"AdvPos","where"=>"name='slider_media'"),1);		
		if ($syt) {			
			$sid = $syt[0]['id'];			
			$sy = parent::showlist(array("model"=>"adv","where"=>"pos_id='$sid'",'order'=>' sort desc'),4);			
			if ($sy) {				
				foreach($sy as $k=> $s) {					
					$pic = get_cover($s['data']);					
					$sy[$k]['picurl']= $pic['path'];				
				}				
				$this->assign('slide',$sy);							
			}		
		}
		$this->display();
	}
	
	public function game($p=1,$type=null,$cate=null) {
		$game['model']='Game';
		$game['where']=" game_status=1 ";
		$type = intval($type)?intval($type):0;
		if ($type && $type<9)
			$game['where'] .=" and game_type_id=$type";
		$game['order'] = 'game_score desc';
		switch($cate) {
			case 'n': 
				$game['order'] .= ' ,recommend_level desc,';
				break;
			case 'h':
				$game['order'] .=' ,dow_num desc,';
				break;
			default:
				$game['order'] = '';
				break;
		}
		$game['order'] .='sort DESC';
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
	
	public function detail($id=0) {
		$game['model']='Game';
		$game['dwhere']='id='.$id;
		$data = parent::detail($game);
		if ($data['screenshot'])
			$gamepic = explode(',',$data['screenshot']);
		else 
			$gamepic="";
		$this->assign('data',$data);
		// 游戏截图
		$this->assign('gamepic',$gamepic);
		// 热门推荐
		$game['where'] = ' game_status=1 ';
		$game['order'] = ' game_score desc, dow_num desc,sort DESC';
		$this->assign('hot',parent::showlist($game,8));
		
		// 相关推荐
		$game['where'] = ' game_status=0 and game_type_id='.$data['game_type_id'];
		$game['order'] = ' game_score desc, dow_num desc,sort DESC';
		$this->assign('xg',parent::showlist($game,-1));
		
		$this->display();
	}
}
