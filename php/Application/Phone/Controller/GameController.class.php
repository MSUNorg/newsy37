<?php
namespace Phone\Controller;
use Think\Controller;/** 
* 游戏控制器 
* lwx 
*/
class GameController extends BaseController {		
	public function index($typeid=0,$themeid=0,$recommend='',$p=1) {		
		if (!is_numeric($typeid) || $typeid<=0) {			
			$typeid=0; $type="";		
		} else {			
			$type = " and g.game_type=$typeid ";		
		}		
		if (!is_numeric($themeid) || $themeid<=0) {			
			$themeid=0; $theme = "";		
		} else {			
			$theme=" and g.category=$themeid ";		
		}
        if (!is_numeric($recommend) || $recommend>4 || $recommend <0) {
            $recom = "";
        } else {
            $recom = " and g.recommend_status = $recommend ";
        }
		$model['model']="Game";		
		$model['where']="g.game_status=1 $type $theme $recom ";
		$model['limit']=21;		
        parent::plists($model,$p);		
		$this->assign('typeid',$typeid);		
		$this->assign('themeid',$themeid);				
		// 顶部		
		$gameslide = parent::showlist(array('model'=>'Adpic','where'=>"status=1 and mark='gameslide'",'order'=>'level asc'),5);
		$this->assign('slide',$gameslide);	
        
		// 热门游戏		
		$game['model']="Game";		
		$game['where']="game_status=1";		
		$game['order']="dow_num desc";		
		$hot = parent::showlist($game,10);		
		$this->assign('hot',$hot);		
		$this->display();	
	}

    public function detail($id) {
        if (empty($id) || !is_numeric($id)) {
            $this->redirect('index');
        }
        $game = array(
            'model' => 'Game',
            'field' => 'g.*,gi.introduction,gi.keyword',
            'join'  => "__GAME_INFO__ as gi on gi.id=g.id",
            'joinnum' => 'left',
            'where' => "g.game_status=1 and g.id=$id",
        );
		$data = parent::pdetail($game);
        
        $this->assign('data',$data);

        $top=array(
            'model' => 'Game',
            'where' => "g.game_status=1",
            'order' => 'sort desc,id desc',
        );
        $rank = parent::showlist($top,10);
        foreach ($rank as $k => $r) {
            $rank[$k]['recommend_level']=$r['recommend_level']*10;
        }
		$this->assign('rank',$rank);
        
        $this->display();
    }
		
	public function dow_url_generate($game_id=null){
		$url = "http://".$_SERVER['SERVER_NAME']."/media.php?s=/Down/down_file/game_id/".$game_id."/type/1.html";//
		$qrcode = $this->qrcode(base64_encode($url));
		return $qrcode;
	}	
	public function qrcode($url='pc.vlcms.com',$level=3,$size=4){
		Vendor('phpqrcode.phpqrcode');
		$errorCorrectionLevel =intval($level) ;//容错级别 
		$matrixPointSize = intval($size);//生成图片大小 
		$url = base64_decode($url);		
		$object = new \QRcode();
		echo $object->png($url, false, $errorCorrectionLevel, $matrixPointSize, 2);   
	}

	public function error_mesg(){
		$mid = session('member_auth.mid');
		if(empty($mid)){
			$this->ajaxReturn(array('msg'=>'no-login'));
		}

		$model = M('message','tab_');
		$map['game_id'] = $_REQUEST['game_id'];
		$map['user_id'] = session('member_auth.mid');
		$d = $model->where($map)->find();
		if(!empty($d)){
			$this->ajaxReturn(array('msg'=>'no'));
		}

		
		$data['game_id'] = $_REQUEST['game_id'];
		//$data['game_name'] = $_REQUEST['game_game'];
		$data['user_id'] = session('member_auth.mid');
		$data['title'] = "游戏无法下载";
		$data['content'] = "";
		$data['status'] = 0;
		$data['type'] = 0;
		$data['create_time'] = NOW_TIME;
		if($model->add($data)){
			$this->ajaxReturn(array('msg'=>'ok'));
		}
	}

}
