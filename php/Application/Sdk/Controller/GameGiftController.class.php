<?php
namespace Sdk\Controller;
use Think\Controller;
use Common\Api\GaemApi;
class GameGiftController extends BaseController{
    
    /**
 	*游戏礼包列表
    */
    public function gift_list($game_id=0){
        #获取SDK上POST方式传过来的数据 然后base64解密 然后将json字符串转化成数组
        $request = json_decode(base64_decode(file_get_contents("php://input")),true);
    	$gift = M("Giftbag","tab_");
    	$map['game_id'] = $request['game_id'];
    	$list = $gift
    			->field("tab_giftbag.id,tab_giftbag.giftbag_name,tab_giftbag.start_time,tab_giftbag.end_time,tab_giftbag.desribe,tab_game.icon")
    			->join("LEFT JOIN tab_game ON tab_giftbag.game_id = tab_game.id")
    			->where($map)
    			->select();
    	//遍历数据获取游戏图片地址
   		foreach ($list as $key => $val) {
   			$list[$key]['icon'] = "http://".$_SERVER['HTTP_HOST'].get_cover($val['icon'],"path");
            $list[$key]['now_time'] = NOW_TIME;
   		}
    	$data = array(
    		"status"=>1,
    		"list"=>$list,
    	);
    	echo base64_encode(json_encode($data));
    }

    /**
	*领取礼包
    */
    public function receive_gift($user_id=0,$gift_id=0,$game_id=0,$game_name=""){
        #获取SDK上POST方式传过来的数据 然后base64解密 然后将json字符串转化成数组
        $request = json_decode(base64_decode(file_get_contents("php://input")),true);
        $user_id = $request['user_id'];
        $gift_id = $request['gift_id'];
        $game_id = $request['game_id'];
        $game_name = $request['game_name'];
    	$gift = M('giftbag','tab_');				
		$novice = $gift->where(array("id"=>$gift_id))->field("id,giftbag_name,novice")->find();
		if(empty($novice['novice'])){
			$this->set_message(0,"fail","礼包已被领取完");
		}
		else{
			#将激活码分成数据
			$novice_arr = explode(",",$novice['novice']);
			#礼包记录数据
			$data_record['user_id'] = $user_id;
			$data_record['game_id'] = $game_id;
			$data_record['game_name'] = $game_name;
			$data_record['gift_id'] = $gift_id;
			$data_record['gift_name'] = $novice['gift_name'];
			$data_record['novice'] = $novice_arr[0];
			$this->add_gift_record($data_record);
			#领取成功后移除这个激活码
			unset($novice_arr[0]);
			#将新的激活码转换成字符串 保存
			$act['novice']=implode(",", $novice_arr);
			$gift->where("id=".$gift_id)->save($act);
			echo base64_encode(json_encode(array('status'=>'1','receive_status'=>1,'return_code'=>'success','return_msg'=>'领取成功','novice'=>$data_record['novice'])));
            exit();
    	}   
    }

    /**
	*添加礼包领取记录
    */
    public function add_gift_record($data = array()){
    	$record = M('GiftRecord',"tab_");
    	$map['user_id'] = $data['user_id'];
    	$map['gift_id'] = $data['gift_id'];
    	$isReceive = $record->where($map)->find();
    	if(!empty($isReceive)){
            echo base64_encode(json_encode(array('status'=>'0','receive_status'=>0,'return_code'=>'success','return_msg'=>'您已经领取过该礼包','novice'=>$isReceive['novice'])));
            exit();
    	}
    	$user_data = get_user_entity($data['user_id']);
    	$data_record['game_id']       = $data['game_id'];
    	$data_record['game_name']     = $data['game_name'];
    	$data_record['server_id']     = 0;
    	$data_record['server_name']   = "";
    	$data_record['gift_id']       = $data['gift_id'];
    	$data_record['gift_name']     = $data['gift_name'];
    	$data_record['status']        = 0;
    	$data_record['novice']        = $data['novice'];
    	$data_record['user_id']       = $data['user_id'];
    	$data_record['user_account']  = $user_data['account'];
    	$data_record['user_nickname'] = $user_data['nickname'];
    	$data_record['create_time']   = NOW_TIME;
    	return $record->add($data_record);
    }

}
