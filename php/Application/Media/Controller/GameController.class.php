<?php
namespace Media\Controller;
use Think\Controller;/** 
* 游戏控制器 
* lwx 
*/
class GameController extends BaseController {   

    //主页
    public function index($typeid=0,$themeid=0,$recommend='',$p=1) {

        $this->assign(array(
            'list_data'=>D('Game')->game_list_limt2(I('typeid')),
            'typeid'=>I('typeid')?I('typeid'):0,
            'themeid'=>I('themeid'),
            ));
        $this->display();   
    }

    //游戏详情
    public function detail ()
    {   
        $data=D('Game')->game_detail();
        $this->assign('data',$data);
        $this->display();
    }
    //AJAX获取游戏列表
	public function game_ajax()
    {	
		$this->assign('game',D('Game')->ajax_game());
	}


    public function dow_url_generate($game_id=null){
/*        $url = "http://".$_SERVER['SERVER_NAME']."/media.php?s=/Down/down_file/game_id/".$game_id."/type/1.html";*///
        $data=D('Game')->field('and_dow_address')->where(array('id'=>$game_id))->find();
        $url=$rel_file="http://".$_SERVER["HTTP_HOST"]/*.__ROOT__*/.$data['and_dow_address'];
        $qrcode = $this->qrcode(base64_encode($url));
/*        echo $qrcode;
        exit;*/
        return $qrcode;
    }   
    //二维码
    public function qrcode($url='pc.vlcms.com',$level=3,$size=4){
        Vendor('phpqrcode.phpqrcode');
        $errorCorrectionLevel =intval($level) ;//容错级别 
        $matrixPointSize = intval($size);//生成图片大小 
        $url = base64_decode($url);     
        $object = new \QRcode();
        echo $object->png($url, false, $errorCorrectionLevel, $matrixPointSize, 2);   
    }

    public function error_mesg(){
        $mid = D("User")->isLogin();
        if(empty($mid['uid'])){
            $this->ajaxReturn(array('msg'=>'no-login'));
        }

        $model = M('message','tab_');
        $map['game_id'] = $_REQUEST['game_id'];
        $map['user_id'] = $mid['uid'];
        $d = $model->where($map)->find();
        if(!empty($d)){
            $this->ajaxReturn(array('msg'=>'no'));
        }
        
        $data['game_id'] = $_REQUEST['game_id'];
        //$data['game_name'] = $_REQUEST['game_game'];
        $data['user_id'] = $mid['uid'];
        $data['title'] = "游戏无法下载";
        $data['content'] = "";
        $data['status'] = 0;
        $data['type'] = 0;
        $data['create_time'] = NOW_TIME;
        if($model->add($data)){
            $this->ajaxReturn(array('msg'=>'ok'));
        }
    }
    //换一组
    public function game_change(){
        D('Game')->game_change();
    }
    
}
