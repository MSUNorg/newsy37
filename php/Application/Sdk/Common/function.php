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


/*
*获取游戏设置信息
*/
function get_game_set_info($game_id = 0){
	$game = M('GameSet','tab_');
	$map['game_id'] = $game_id;
	$data = $game->where($map)->find();
	return $data;
}