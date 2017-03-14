<?php
namespace Mobile\Controller;
use Think\Controller;
class GiftController extends BaseController {
	public function index($p=1) {
		$gift["model"] = "Giftbag";
		$gift['field'] = "tab_giftbag.*,tab_game.game_name,tab_game.cover,tab_game.icon";
		$gift['joins'] = " __GAME__ on __GIFTBAG__.game_id=__GAME__.id ";
		$gift['order']='  tab_giftbag.create_time desc';
		$gift['where']='';
		$gift['limit']=10;
		$gift['page']=$p;
		$gl = parent::getlists($gift);
		$ng = array();
		if($gl['list']&&$gl['total']) {
			$this->jump($gl['list'],$ng);
			$key = end(array_keys($ng));
			$this->assign('key',$key);
		}
		$this->assign('gift',$ng);
		$this->assign('page',intval($p)+1);
		$this->assign('total',$gl['total']);
		$this->display();
	}
	
	public function detail($id) {
		$giftbag = D('Giftbag');
		$sql = "select gb.*,g.game_name,g.icon,g.cover from tab_giftbag as gb left join tab_game as g on gb.game_id = g.id where gb.status=1 and gb.id=".$id;		
		$data = $giftbag->query($sql);
		$data = $data[0];
		// $data['giftbagtype_name']=get_giftbag_type($data['giftbag_type']);
		$n = explode(',',$data['novice']);
		$data['novice_num']=count($n);
		$novice = explode(",",$data['novice']);
		$data['nvalue']=$novice[0];
		array_shift($novice);
		array_push($novice,$data['nvalue']);
		$novice1['novice'] = implode(",",$novice);
		$giftbag->where("id=$id")->save($novice1);
		$this->assign("data",$data);
		$this->display();
	}
	
	public function getgift($id) {
		$giftbag = D('Giftbag');
		$sql = "select novice from tab_giftbag where status=1 and id=$id";		
		$data = $giftbag->query($sql);
		if ($data) {
			$data = $data[0];
			$novice = explode(",",$data['novice']);
			$data=null;
			$data['nvalue']=$novice[0];
			array_shift($novice);
			array_push($novice,$data['nvalue']);
			$novice1['novice'] = implode(",",$novice);
			$giftbag->where("id=$id")->save($novice1);
			$data['status']=1;			
		} else {
			$data['status']=0;
		}
		echo json_encode($data);
	}
	
	
	protected function jump(&$ga,&$ng,$n=0) {
		$num = count($ga);
		if($num==0 || $n == $num) {
			return ;
		} else {
			$t = date('Y-m-d',$ga[0]['create_time']);
			foreach($ga as $k => $g) {
				$st = date('Y-m-d',$g['create_time']);
				foreach($g as $k2 =>$v) {
					if ('icon' === $k2) {
						$pic = get_cover($v);
						$g['picurl'] = $pic['path'];
					}
					// if ('giftbag_type' === $k2) {
					// 	$g['giftbagtype_name']=get_giftbag_type($v);
					// }
					if ('novice' === $k2) {
						$n = explode(',',$v);
						$g['novice_num']=count($n);
					}
				}
				if($t==$st) {					
					$ng[$t][]=$g;
					unset($ga[$k]);
				}
			}
			$ga = array_values($ga);			
			return $this->jump($ga,$ng,$num);
		} 
	}
	
	//  异步返回
	public function gift($p=1) {
		$gift["model"] = "Giftbag";
		$gift['field'] = 'tab_giftbag.*,tab_game.cover,tab_game.icon';
		$gift['joins'] = " __GAME__ on __GIFTBAG__.game_id=__GAME__.id ";
		$gift['order']='  tab_giftbag.create_time desc';
		$gift['where']='';
		$gift['limit']=10;
		$gift['page']=$p;
		$gl = parent::getlists($gift);
		if($gl['list']&&$gl['total']) {
			$ng = array();
			$this->jump($gl['list'],$ng);
			$key = end(array_keys($ng));
			$data = array(
				'data'		=>	$ng,
				'page' 		=> 	intval($p)+1,
				'total' 	=> 	$gl['total'],
				'status'	=> 1,
				'key'		=> $key
			);
		} else
			$data = array(
				'data'		=>	'',
				'page' 		=> 	$p,
				'status'	=> 0,
			);
		echo json_encode($data);	
	}
	
	
	
}