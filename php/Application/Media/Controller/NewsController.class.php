<?php
namespace Media\Controller;
use Think\Controller;
/**
* 新闻控制器
* lwx
*/
class NewsController extends BaseController {
	
	public function __construct() {
		parent::__construct();
		
		// 热门资讯
		$news['model']="Document";
		//$news['where']="category_id in('42','43','44') and status>0";
		$news['where']="category_id in('43') and status>0";
		$news['order']="level desc";
/*		$newsi = parent::showlist($news,6);*/
		$this->assign('news',$newsi);
		
		// 热门游戏
		$game['model']="Game";
		$game['where']="game_status=1";
		$game['order']="dow_num desc";
/*		$hot = parent::showlist($game,10);*/
		$this->assign('hot',$hot);
	}

	public function index() {

		$this->assign("news",D('Document')->listspage(43));
		$this->display();
	}
	
	public function detail() {
/*		$time = time();
		$news['model']="Document";
		$news['field']="d.*,da.content";
		$news['join']="__DOCUMENT_ARTICLE__ as da on (da.id=d.id)";
		$news['joinnum']="left";
		$news['join1']="__MEMBER__ as m on (m.uid=d.uid)";
		$news['joinnum1']="left";
		$news['where']="d.status>0 and d.create_time<$time and (d.deadline =0 or d.deadline > $time) and d.id=$id ";
		$news['order']="d.id desc ";
		$data = parent::lists($news);
		$this->assign("data",$data);
		$model = D("Document");
		$category = $data['category_id'];
		$info = array("id"=>$id,"category_id"=>$category);
		$this->assign("prev",$model->prev($info));
		$this->assign("next",$model->next($info));
		$model->where("id=$id")->setInc('view',1,60);*/
		$this->assign("news",D('Document')->lists(43));
		$this->assign("data",D('Document')->detail(I('id')));
		$this->display();
	}
}
