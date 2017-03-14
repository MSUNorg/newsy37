<?php
// +----------------------------------------------------------------------
// | 徐州梦创信息科技有限公司—专业的游戏运营，推广解决方案.
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.vlcms.com  All rights reserved.
// +----------------------------------------------------------------------
// | Author: kefu@vlcms.com QQ：97471547
// +----------------------------------------------------------------------


/**
*写入txt文件
*/
function wite_text($txt,$name){
    $myfile = fopen($name, "w") or die("Unable to open file!");
    fwrite($myfile, $txt);
    fclose($myfile);
}
function think_md5($str, $key = 'ThinkUCenter'){
    return '' === $str ? '' : md5($str.$key);
}

/**
*根据appid获取游戏名称
*/
function get_game_name($appid){
    $model = M("game",'tab_');
    $map["game_appid"] = $appid; 
    $reg = $model->where($map)->find();
    return $reg["game_name"];
}

function get_game_entity($game_appid){
    $model = M('game','tab_');
    $map['game_appid'] = $game_appid;
    $data = $model->where($map)->find();
    return $data;
}

function sdk_game_entity($game_appid){
    $model = M('game','tab_');
    $map['game_appid'] = $game_appid;
    $data = $model->where($map)->find();
    return $data['id'];
}
/**
*根据推广员id获取推广员名称
*/
/*function get_promote_name($promote_id){
    $model = M("Promote",'tab_');
    $map["id"] = $promote_id; 
    $reg = $model->where($map)->find();
    if(empty($reg["account"])){
        return "自然注册";
    }
    return $reg["account"];
}
*/
/**
*根据推广员id获取推广员名称
*/
function get_promote_ParentID($promote_id){
    $model = M("Promote",'tab_');
    $map["id"] = $promote_id; 
    $reg = $model->where($map)->find();
    return $reg["parent_id"];
}

/**
*根据用户名获取用户id
*/
function get_user_id($account){
    $model = M("user",'tab_');
    $map["account"] = $account; 
    $reg = $model->where($map)->find();
    if($reg){
     return $reg["id"];    
    }else{
        return 0;
    }
    
}
//通过手机号获取用户id
function get_user_id_phone($phone)
{
    $map['phone']=$phone;
    $user=M("user","tab_")->where($map)->find();
    if($user){
   return $user['id'];
    }else{
    return false;
    }
}

/*function wite_text($txt,$name){
    $myfile = fopen($name, "w") or die("Unable to open file!");
    fwrite($myfile, $txt);
    fclose($myfile);
}
*/
//根据id获取游戏原包路径
function get_source_path($game_id){
    $model = M('gamesource');
    $map['game_id'] = $game_id;
    $res = $model->where($map)->find();
    return $res['path'];
}
 function get_cname($id)
{
     $model = M('opentype','tab_');
    $map['id'] = $id;
    $res = $model->where($map)->find();
    return $res['open_name'];
}
function check_order($order_number,$pay_order_number){
    if(empty($order_number)||empty($pay_order_number)){
          return false;
    }   
    $map['order_number']=$order_number;
    $map['pay_order_number']=$pay_order_number;
    $pri=M("deposit","tab_")->where($map)->find();
    if($pri){
        return false;
    }else{
        return true;
    }

}