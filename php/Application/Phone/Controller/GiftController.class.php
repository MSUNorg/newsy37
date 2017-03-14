<?php
namespace Phone\Controller;
use Think\Controller;
/** 
* 礼包控制器 
* lwx 
*/
class GiftController extends BaseController{
	public function __construct() {
		parent::__construct();
		// 大家都在抢
		$gift['model']="Giftbag";
		$gift['field']="g.*,game.icon,game.cover,game.game_name,gr.user_id,u.account";
		$gift['join']="__GAME__ as game on(g.game_id=game.id) ";
		$gift['joinnum']="left";
		$gift['join1']="__GIFT_RECORD__ as gr on(gr.game_id = game.id)";
		$gift['joinnum1']="right";
		$gift['join2']="__USER__ as u on(u.id = gr.user_id)";
		$gift['joinnum2']="left";
		$gift['where']="g.status=1 ";
		$gift['order']="gr.create_time desc,g.sort desc";
		$qgift = parent::showlist($gift,20);
		if($qgift) {
			foreach($qgift as $k=>$q) {
				$accs = substr($q['account'],0,2);
				$acce = substr($q['account'],-2);
				$qgift[$k]['account']=$accs."****".$acce;
			}
		}
		$this->assign('qgift',$qgift);
	}
	
	public function index() {
		// 顶部幻灯片
        $slide = parent::showlist(array('model'=>'Adpic','where'=>"status=1 and mark='giftrecom'",'order'=>'level asc'),7);    
        $this->assign('libao',$slide);
		//热点礼包
		$time=time();
		$gift['model']="Giftbag";
		$gift['field']="g.*,game.icon,game.cover,game.game_name";
		$gift['join']="__GAME__ as game on(g.game_id=game.id) ";
		$gift['joinnum']="left";
		//$gift['where']="g.status=1 and g.end_time>$time ";
		$gift['where']="g.status=1 ";
		$gift['order']="g.sort desc";
		$hotgift = parent::showlist($gift,15);
		if ($hotgift) {
			foreach($hotgift as $k=>$h) {
				$novice = explode(",",$h['novice']);
				$hotgift[$k]['novicenum']=count($novice);
			}
		}
		$this->assign('hotgift',$hotgift);
		
		// 礼包广告
		$giftad = parent::pdetail(array('model'=>'Adpic','where'=>"status=1 and mark='giftad'"));
		$this->assign('giftad',$giftad);
		
		// 最新礼包
		//$gift['where']="g.status=1 ";
		$gift['order']="g.create_time desc ";
		$newsgift = parent::showlist($gift,20);
		if ($newsgift) {
			foreach($newsgift as $k=>$h) {
				$novice = explode(",",$h['novice']);
				$newsgift[$k]['novicenum']=count($novice);
			}
		}
		$this->assign('newsgift',$newsgift);
		
		
		$this->display();
	}
	
	/** * 礼包列表 */
	public function lists($typeid=0,$nameid=0,$id='',$p=1) {
		if (!is_numeric($typeid) || $typeid>10 || $typeid==0) {
			$typeid=0; $type="";
		} else {
			$type = " and game.game_type=$typeid ";
		}
		if (preg_match('/^[a-z]$/',$nameid)) {
			$name=" and game.short like '%$nameid%' ";
		} else {
			$nameid=-1; $name = "";
		}
		$gid="";
		if (is_numeric($id)) {
			$gid = " and g.game_id=$id";
		}
		$time = time();
		$model['model']="Giftbag";
		$model['field']="g.*,game.icon,game.cover,game.game_name,game.short,game.game_type";
		$model['join']="__GAME__ as game on(g.game_id=game.id) ";
		$model['joinnum']="right";
		//$model['where']="g.status=1 and g.end_time>$time $type $name $gid";
		$model['where']="g.status=1 $type $name $gid";
		$model['limit']=25;
		parent::plists($model,$p);
		$this->assign('typeid',$typeid);
		$this->assign('nameid',$nameid);
		$this->display();
	}
		
	public function detail($id) {
		// 礼包详情
		$model['model']="Giftbag";
		$model['field']="g.*,game.icon,game.cover,game.game_name";
		$model['join']="__GAME__ as game on(game.id=g.game_id)";
		$model['where']="g.status=1 and g.id=$id";
		$data = parent::pdetail($model);
		if ($data) {
			$novice = explode(',',$data['novice']);
			$data['novicenum']=count($novice);
			// 计算剩余所占百分比
			$nums = parent::pdetail(array('model'=>'Gift_record','field'=>'count(gift_id) as nums','where'=>"gift_id=$id"));
			$num = intval($nums['nums'])+$data['novicenum'];
			$data['noviceper']=($data['novicenum']/$num)*100;
		}
		$this->assign('data',$data);
		// 推荐礼包 
		$model['where']="g.status=1";
		$model['order']="g.end_time desc";
		$recom = parent::showlist($model,6);
		if ($recom) {
			foreach($recom as $k=>$r) {
				$novice = explode(',',$r['novice']);
				$recom[$k]['novicenum']=count($novice);
				$giftid = $r['id'];
				$nums = parent::pdetail(array('model'=>'Gift_record','field'=>'count(gift_id) as nums','where'=>"gift_id=$giftid"));
				$recom[$k]['novicetotal'] = intval($nums['nums'])+$recom[$k]['novicenum'];
			}
		}
		$this->assign('recom',$recom);
		// 游戏资讯
		$news['model']="Document";
		$news['where']="category_id in('42','43','44') and status>0";
		$news['order']="level desc";
		$news1 = parent::showlist($news,1);
		$news2 = parent::showlist($news,5);
		$this->assign("news1",$news1);
		$this->assign("news2",$news2);
		$this->display();
	}
	
	// 我的遊戲
	public function gift() {
		$u = D("User");
		$user=$u->isLogin();
		if ($user) {
			$uid = $user['uid'];			
			$model['model']="Gift_record";
			$model['field']="g.*,game.icon,game.cover,game.and_dow_address,game.ios_dow_address";
			$model['join']="__GAME__ as game on(game.id=g.game_id)";
			$model['joinnum']="left";
			$model['where']="user_id=$uid";
			$activation = parent::showlist($model,-1);
			$this->assign('activation',$activation);
			//$this->assign('count',count($activation));
			
			$this->display();
		} else {
			$this->redirect('index');
		}
	}

}

