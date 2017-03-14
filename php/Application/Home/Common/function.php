<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: zxc <zuojiazi@vip.qq.com> <http://www.msun.com>
// +----------------------------------------------------------------------

/**
 * 前台公共库文件
 * 主要定义前台公共函数库
 */


/**
 * 检测用户是否登录
 * @return integer 0-未登录，大于0-当前登录用户ID
 * @author zxc <zuojiazi@vip.qq.com>
 */
function is_login_promote(){
    $user = session('promote_auth');
    if (empty($user)) {
        return 0;
    } else {
        return session('promote_auth_sign') == data_auth_sign($user) ? $user['pid'] : 0;
    }
}

function get_pay_sett($id){
    switch ($id) {
        case 0:
        return "未提现";
            break;

        case 1:
        return "已提现";
            break;
        
    }
}
/**
 * 检测验证码
 * @param  integer $id 验证码ID
 * @return boolean     检测结果
 * @author zxc <zuojiazi@vip.qq.com>
 */
function check_verify($code, $id = 1){
	$verify = new \Think\Verify();
	return $verify->check($code, $id);
}

/**
 * 获取列表总行数
 * @param  string  $category 分类ID
 * @param  integer $status   数据状态
 * @author zxc <zuojiazi@vip.qq.com>
 */
function get_list_count($category, $status = 1){
    static $count;
    if(!isset($count[$category])){
        $count[$category] = D('Document')->listCount($category, $status);
    }
    return $count[$category];
}

/**
 * 获取段落总数
 * @param  string $id 文档ID
 * @return integer    段落总数
 * @author zxc <zuojiazi@vip.qq.com>
 */
function get_part_count($id){
    static $count;
    if(!isset($count[$id])){
        $count[$id] = D('Document')->partCount($id);
    }
    return $count[$id];
}

/**
 * 获取导航URL
 * @param  string $url 导航URL
 * @return string      解析或的url
 * @author zxc <zuojiazi@vip.qq.com>
 */
function get_nav_url($url){
    switch ($url) {
        case 'http://' === substr($url, 0, 7):
        case '#' === substr($url, 0, 1):
            break;        
        default:
            $url = U($url);
            break;
    }
    return $url;
}
// 获取游戏名称
function get_game_name($game_id=null,$field='id'){
    $map[$field]=$game_id;
    $data=M('Game','tab_')->where($map)->find();
    if(empty($data)){return false;}
    return $data['game_name'];
}
function get_apply_dow_url($game_id=0,$promote_id=0)
{
    $model = M('Apply','tab_');
    $map['game_id'] = $game_id;
    $map['promote_id'] = $promote_id;
    $data = $model->where($map)->find();
    if(empty($data['dow_url'])){
        $game_address = M('game','tab_')->field('game_address')->where('id='.$game_id)->find();
        return $game_address['game_address'];
    }
    return $_SERVER['HTTP_HOST'].$data['dow_url'];
}

function get_promote_list_by_id(){
    $map['parent_id']=get_pid();
    $pro=M("promote","tab_")->where($map)->select();
    return $pro;
}
