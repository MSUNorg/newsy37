<?php
namespace Media\Controller;
use Think\Controller;
/** 
* 首页控制器 
* lwx 
*/
class IndexController extends BaseController{

	
    //主页
	public function index(){

		$this->new_server();
		$this->assign('links',D('Links')->links());
		$this->assign('logo',"http://".$_SERVER["HTTP_HOST"].get_cover(C('PC_SET_LOGO'),'path'));
		$this->assign('data',D('Game')->game_recommend_limts(2));
		$this->assign('newslist',D('Document')->newslist());
		$this->display();		
	}

	/**
	*最新开服
	*/
	private function new_server(){
		$server = M('Server',"tab_");
		//$map['stop_status'] = 1;
		$map['show_status'] = 1;
		$data = $server->field("tab_server.*,tab_game.icon,tab_game.cover,tab_game.and_dow_address")
			   ->join("LEFT JOIN tab_game ON tab_server.game_id = tab_game.id")
			   ->where($map)
			   ->limit(10)
			   ->select();
		$this->assign("server_list",$data);
	}
    
    public function urlqrcode() {
        
        $url = C('DL_URL');
        
        $qrcode = $this->qrcode(base64_encode($url));
        
		return $qrcode;
    }
    
    public function down() {

        $this->display();
    }

}

