<?php
/**
 * 后台公共文件扩展
 * 主要定义后台公共函数库
 */
 
 

function get_game_type_all() {
    $list = M("Game_type","tab_")->where("status=1")->select();
    if (empty($list)) {return '';}
    return $list;
}
/**
 * 获取游戏列表
 * @return array，false
 * @author 小纯洁 
 */
 function get_game_list()
 {
    $game = M("game","tab_");
    $map['game_status'] = 1;
    $lists = $game->where($map)->select();
    if(empty($lists)){return false;}
    return $lists;
 }

/**
*游戏区服名称
*/
function get_area_name($area_id= null){
    if(empty($area_id)){return false;}
    $area_model = D('Server');
    $map['server_num'] = $area_id;
    $name = $area_model->where($map)->find();
    if(empty($name['server_name'])){return false;}
    return $name['server_name'];
}
/**
 * 获取对应游戏类型的文字信息
 */
function get_game_type($type = null){
    if(!isset($type)){
        return false;
    }
    $cl = M("game_type","tab_")->where("status=1 and id=$type")->limit(1)->select();
    return $cl[0]['type_name'];
}
/**
*获取推广员列表
*@return array
*@author 小纯洁
*/
 function get_promote_list(){
    $promote = M("promote","tab_");
    $map['status'] = 1;
    $data = $promote->where($map)->select();
    if(empty($data)){return false;}
    return $data;
 }
 
 /**
*检查链接地址是否有效
*/
function varify_url($url){  
    $check = @fopen($url,"r");  
    if($check){  
     $status = true;  
    }else{  
     $status = false;  
    }    
    return $status;  
} 

/**
获取推广员类型 一级 二级
*/
 function get_promote_type($id=0){
    $promote = M("Promote","tab_");
    $map["id"] = $id;
    $data = $promote->where($map)->find();
    if(empty($data)){return false;}
    $str="";
    switch ($data['parent_id']) {
        case 0:
            $str = "一级公会";
            break;
        
        default:
           $str = "二级公会";
            break;
    }
    
    return $str;
 }

 /**
*获取推广员账号
*@param  $promote_id 推广id
*@return string
*@author 小纯洁
*/
 function get_promote_name($prmote_id=0)
 {
    $promote = M("promote","tab_");
    $map['id'] = $prmote_id;
    $data = $promote->where($map)->find();
    if(empty($data)){return '自然注册';}
    if(empty($data['account'])){return "未知推广";}
    $result = $data['account'];
    return $result;
 }


/**
*获取推广员父类账号
*@param  $promote_id 推广id
*@param  $isShow bool 
*@return string
*@author 小纯洁
*/
 function get_parent_promote($prmote_id=0,$isShwo=true)
 {
    $promote = M("promote","tab_");
    $map['parent_id'] = $prmote_id;
    $data = $promote->where($map)->find();
    if(empty($data)){return false;}
    $result = "";
    if($isShwo){
        $result = "[{$data['account']}]";
    }
    else{
        $result = $data['account'];
    }
    return $result;
 }

/**
*获取推广员子账号
*/
 function get_prmoote_chlid_account($id=0){
    $promote = M("promote","tab_");
    $map['status'] = 1;
    $map["parent_id"] = $id;
    $data = $promote->where($map)->select();
    if(empty($data)){return "";}
    return $data;
 }

/**
*获取管理员昵称
*/
 function get_admin_name($id=0){
    $data = M("Member")->find($id);
    if(empty($data)){return "";}
    return $data['nickname'];
 }

 
 /**
 *获取用户实体
 */
 function get_user_entity($id=0,$isAccount = false){
    $user = M('user',"tab_");
    if($isAccount){
        $map['account'] = $id;
        $data = $user->where($map)->find();
    }
    else{
        $data = $user->find($id);
    }
    if(empty($data)){
        return false;
    }
    return $data;
 }

/**
*设置状态文本
*/
 function get_status_text($index=1,$mark=1){
    $data_text = array(
        0  => array( 0 => '失败' ,1 => '成功'),
        1  => array( 0 => '锁定' ,1 => '正常'),
        2  => array( 0 => '未申' ,1 => '已审' , 2 => '拉黑'),
    );
    return $data_text[$index][$mark];
 }


/**
* 生成唯一的APPID
* @param  $str_key 加密key
* @return string
* @author 小纯洁 
*/
function generate_game_appid($str_key=""){
    $guid = '';  
    $data = $str_key;  
    $data .= $_SERVER ['REQUEST_TIME'];     
    $data .= $_SERVER ['HTTP_USER_AGENT']; 
    $data .= $_SERVER ['SERVER_ADDR'];       
    $data .= $_SERVER ['SERVER_PORT'];      
    $data .= $_SERVER ['REMOTE_ADDR'];     
    $data .= $_SERVER ['REMOTE_PORT'];     
    $hash = strtoupper ( hash ( 'MD4', $guid . md5 ( $data ) ) ); //ABCDEFZHIJKLMNOPQISTWARY
    $guid .= substr ( $hash, 0, 9 ) . substr ( $hash, 17, 8 ) ; 
    return $guid;
}


/**
*随机生成字符串
*@param  $len int 字符串长度
*@return string
*@author 小纯洁
*/
function sp_random_string($len = 6) {
    $chars = array(
        "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k",
        "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v",
        "w", "x", "y", "z", "A", "B", "C", "D", "E", "F", "G",
        "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R",
        "S", "T", "U", "V", "W", "X", "Y", "Z", "0", "1", "2",
        "3", "4", "5", "6", "7", "8", "9"
    );
    $charsLen = count($chars) - 1;
    shuffle($chars);    // 将数组打乱
    $output = "";
    for ($i = 0; $i < $len; $i++) {
        $output .= $chars[mt_rand(0, $charsLen)];
    }
    return $output;
}
