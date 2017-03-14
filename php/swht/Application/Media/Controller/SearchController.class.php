<?php
namespace Media\Controller;
use Think\Controller;
/**
* 
*/
class SearchController extends BaseController {
	public function index($p=1) {
		header("Content-type: text/html;charset=utf-8");
		switch ($_REQUEST['search_page']) {
			case 'Game/game_list':
				$model = array(
					'm_name' => 'Game',
					'prefix' => 'tab_',
					'map' => array('game_name'=>array('like','%'.$_REQUEST['search_key'].'%')),
					'order'=>'sort asc',
					'tmeplate_list' =>'Search/index'
				);
				$this->assign('search_page','game_list');
				parent::lists($model,$p);
				break;
			case 'Game/gift_list':
				$model = array(
					'm_name' => 'Giftbag',
					'prefix' => 'tab_',
					'field' =>array('tab_giftbag.id,giftbag_name,game_id,game_name,desribe,start_time,end_time,icon'),
					'join'	=>'tab_game on tab_giftbag.game_id = tab_game.id',
					'map' => array('game_name'=>array('like','%'.$_REQUEST['search_key'].'%')),
					'order'=>'tab_giftbag.create_time desc',
					'tmeplate_list' =>'Search/index'
				);
				$this->assign('search_page','gift_list');
				parent::join_list($model,$p);
				break;
			default:
				$model = array(
					'm_name' => 'Game',
					'prefix' => 'tab_',
					'map' => array('game_name'=>array('like','%'.$_REQUEST['search_key'].'%')),
					'order'=>'sort asc',
					'tmeplate_list' =>'Search/index'
				);
				$this->assign('search_page','game_list');
				parent::lists($model,$p);
				break;
		}
	}
}