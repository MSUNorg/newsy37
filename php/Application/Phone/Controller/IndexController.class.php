<?php
namespace Phone\Controller;
use Think\Controller;
/** 
* 首页控制器 
* lwx 
*/
class IndexController extends BaseController{

	public function index(){
		//热门游戏排行		
		$game['model']="Game";
		$game['where']="game_status=1";
		$game['order']="dow_num desc";
		$hot = parent::showlist($game,10);
		//热门游戏
		$game['where']="game_status=1 and recommend_status=2";
		$game['order']="sort desc,id desc";
		$list = parent::showlist($game,12);
		//热门礼包
		$gift['model']="Giftbag";
		$gift['field']="g.*,game.icon,game.cover,game.game_name";
		$gift['join']="__GAME__ as game on(game.id=g.game_id)";
		$gift['joinnum']="left";
		$gift['where']="status=1";
		$gift['order']="sort desc";
		$gift = parent::showlist($gift,15);
		if ($gift)
			foreach($gift as $k=>$g) {
				$novice = explode(',',$g['novice']);
				$gift[$k]['novicenum']=count($novice);
				$gn = explode('-',$g['giftbag_name']);
				$gift[$k]['giftbag_name']=$gn[0];
			}
		
		//最新开服
		$area['model']="Area";
		$area['field']="a.*,g.game_name,g.icon,g.cover,g.and_dow_address";
		$area['join']="__GAME__ as g on (g.id=a.game_id)";
		$area['joinnum']="left";
		$area['where']="a.stop_status=1 and a.show_status=1";
		$garea = parent::showlist($area,11);
		
		//新闻资讯
		$news['model']="Document";
		$news['where']="category_id in('43') and status>0";
		$news['order']="level desc";
		$news1 = parent::pdetail($news);
		$title = $news1['title'];
		if (mb_strlen($title)>12) {
			$news1['title']=mb_substr($title,0,12,'utf-8').'...';
		}
		$news2 = parent::showlist($news,11);
		$this->assign("news1",$news1);
		$this->assign("news2",$news2);
        
		// 友情链接
		$links = parent::showlist(array("model"=>"Links","where"=>"type=2 and status=1","order"=>"id desc"),-1);
		$this->assign("links",$links);
        
		// 首页幻灯片
        $slide = parent::showlist(array('model'=>'Adpic','where'=>"status=1 and mark='indexslide'",'order'=>'level asc'),4);
		$this->assign('slide',$slide);
        
        // 首页广告
        $this->assign('indexad',parent::pdetail(array('model'=>'Adpic','where'=>"status=1 and mark='indexad'")));
		
        // 游戏推荐
        $recomm = array(
            'model'=>'Adpic',
            'field' => 'a.*,g.game_type,g.game_size,g.game_name,gi.introduction',
            'join' =>"tab_game as g on(g.id=a.game_id)",
            'joinnum'=>"left",
            'join1' => "tab_game_info as gi on(a.game_id = gi.id)",
            'joinnum1' => "left",
            'where'=>"status=1 and mark='gamerecom'",
            'order'=>'level asc'
        );
        $recom = $this->showlist($recomm,6);
        $this->assign("recom",$recom);
		
		$this->assign('garea',$garea);
		$this->assign("list",$list);		
		$this->assign("hot",$hot);		
		$this->assign("gift",$gift);
        
        
		$this->display();		
	}

}

