<?php
// +----------------------------------------------------------------------
// | 手游平台
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.msun.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: zxc
// +----------------------------------------------------------------------

/**
 * 后台公共文件扩展
 * 主要定义后台公共函数库
 */

function get_promote_all() {
     $map['status']=1;
     $map['admin_id']=UID;
    $list = M("Promote","tab_")->where($map)->select();
    if (empty($list)){return '';}
    return $list;
}
 //返回推广当前id
 function get_pid(){
        $pro=M("promote","tab_")->where(array("admin_id"=>UID))->select();
        for ($i=0; $i <count($pro); $i++) { 
            $sd[]=$pro[$i]['id'];
        }
        $promote_id=implode(",", $sd);
        if($promote_id==null){$promote_id="-1";}
        return $promote_id;
    }
function get_admin(){
   return UID;
    }
/**
*获取管理员列表
*/
function get_admin_list()
{
    $list= M("Member")->where("status=1")->select();
    if(empty($list)){return false;}
    return $list;
}
/**
*获取商务专员列表
*/
function get_commission_list()
{
    $map['houtai']=1;
    $map['status']=1;
    $list=D("UcenterMember as a")
    ->join("sys_auth_group_access as b on a.id=b.uid")
    ->where($map)
    ->select();
    return $list;
}
function get_admin_account($id)
{
    $map['id']=$id;
    $admin=M("UcenterMember")->where($map)->find();
    return $admin['username'];
}
// 获取游戏名称
function get_game_name($game_id=null,$field='id'){
    $map[$field]=$game_id;
    $data=M('Game','tab_')->where($map)->find();
    if(empty($data)){return false;}
    return $data['game_name'];
}
//获取游戏原包大小
function get_game_size($game_id){
    $map['game_id']=$game_id;
    $data=M("game_source",'tab_')->where($map)->find();
    if(empty($data)||empty($data['file_size'])){
        return false;
    }else{
        return $data['file_size'];
    }
}
//获取广告图类型
function get_adv_type($type=0){
    switch ($type) {
        case 1:
            return '单图';
            break;
        case 2:
            return '多图';
            break;
        case 3:
            return '文字链接';
            break;
        case 4:
            return '代码';
            break;
        default:
            return '未知类型';
            break;
    }
} 
// 获取游戏appid
function get_game_appid($game_name=null,$field='game_name'){
    $map[$field]=$game_name;
    $data=M('Game','tab_')->where($map)->find();
    if(empty($data)){return false;}
    return $data['game_appid'];
}
function  get_open_type_list(){
    $game_model = M("opentype","tab_");
    $map['status'] = 1;
    $name = $game_model->where($map)->select();
    if(empty($name)){return false;}
    return $name;
}
//获取管理员id
function get_admin_id($account){
    if(empty($account)){return false;}
    $user=D('Member');
    $map['nickname']=$account;
    $data = $user->where($map)->find();
    if(empty($data['uid'])){return -1;}
    return $data['uid'];
}
/**
*获取用户组名称
*@param $uid 用户id
*@return array
*@author 赵超 2016-02-17
*/
function get_auth_group_name($uid){
    $model = D("auth_group_access");
    $res = $model->join("sys_auth_group on sys_auth_group.id = sys_auth_group_access.group_id")
    ->field("title")
    ->where("uid=".$uid)
    ->find();
    return $res["title"];
}
//计算数组个数用于模板
function arr_count($string){
    $arr=explode(',',$string);
    $cou=count($arr);
    return $cou;
}
//获取游戏ID
function get_game_id($name){
    $game=M('game','tab_');
    $map['game_name']=$name;
    $data=$game->where($map)->find();
    if($data['id']==null){
        return false;
    }
    return $data['id'];
}
//获取支付方式
function get_pay_way($id=null)
{
    if(!isset($id)){
        return false;
    }
    switch ($id) {
        case 0:
          return "平台币";
            break;
        case 1:
          return "支付宝";
            break;
            case 2:
          return "微信";
            break;
        default:
            return "所有类型";
            break;
    }
}
//根据推广员姓名获取上级推广员姓名
function get_parent_promote_a($name){
    $list=D("promote");
    $map['account']=$name;
    $pid=$list->where($map)->find();
    if($pid['parent_id']!=0){
        $mapp['id']=$pid['parent_id'];
        $fname=$list->where($mapp)->find();
        if($fname&&$fname!=0){
            return $fname['account'];    
        }else{
            return "";    
        }
    }else{
        return "";    
    }
}
//根据推广员id获取上级推广员姓名
function get_parent_promoteto($id)
{
    $list=D("promote");
    $map['id']=$id;    
    $pid=$list->where($map)->find();
    if($pid['parent_id']!=0){
    $mapp['id']=$pid['parent_id'];
    $pname=$list->where($mapp)->find();
   if($pname){
        return "[".$pname['account']."]";    
    }
    else{
        return "";
    }
    }else{
        return "";   
    }
}
/**
*获取用户账号
*/
function get_user_account($uid=null){
    if(empty($uid)){return false;}
    $user = D('User');
    $map['id'] = $uid;
    $data = $user->where($map)->find();
    if(empty($data['account'])){return false;}
    return $data['account'];
}
//获取推广员id
function get_promote_id($name){
    $promote=M('Promote','tab_');
    $map['account']=$name;
    $data=$promote->where($map)->find();
    if(empty($data)){
        return '';
    }else{
        return $data['id'];
    }
}
/**
*一级推广员列表
*@param  string $id 
*@return string 推广员名字，false 未找到
*@author 王贺
*/
function get_promote_parent_list(){
    $game_model = D("promote");
    $map['parent_id'] = 0;
    $map['status'] = 1;
    $name = $game_model->where($map)->select();
    if(empty($name)){return false;}
    return $name;
}
/**
 * 获取对应游戏类型的状态信息
 * @param int $group 状态分组
 * @param int $type  状态文字
 * @return string 状态文字 ，false 未获取到
 * @author 王贺
 */
function get_info_status($type=null,$group=0){
    if(!isset($type)){
        return false;
    }
    $arr=array(
        0 =>array(0=>'关闭'   ,1=>'开启'),
        1 =>array(0=>'不推荐' ,1=>'推荐',2=>"热门",3=>'最新'),//游戏设置状态
        2 =>array(0=>'否'     ,1=>'是'),
        3 =>array(0=>'未审核' ,1=>'正常',2=>'拉黑'),//推广员状态
        4 =>array(0=>'锁定'   ,1=>'正常'),//用户状态
        5 =>array(0=>'未审核' ,1=>'通过'   ,2=>'驳回'),//游戏审核状态
        6 =>array(0=>'未修复' ,1=>'已修复'),//纠错状态
        7 =>array(0=>'失败'   ,1=>'成功'),//纠错状态
        8 =>array(0=>'禁用'   ,1=>'启用'),//显示状态
        9 =>array(0=>'未充值' ,1=>'已充值'),//显示状态
       10 =>array(0=>'正常'   ,1=>'拥挤',2=>'爆满'),//区服状态
       12 =>array(0=>'未支付',1=>'成功'),
    );
    return $arr[$group][$type];
}

function get_limts_status($type){
   switch ($type) {
       case 0:
           return "未充值";
           break;
       case 1:
           return "已充值";
           break;        
       default:
           return "未充值";
           break;
   }
}

//获取礼包类型
function get_gifttype($type){
    if(!isset($type)){
        return false;
    }
    $arr=array(
        1=>'新手包',
        2=>'媒体包',
        3=>'其他包',
        4=>'公众礼包'
    );
    return $arr[$type];
}
//获取注册方式
function get_registertype($type){
    if(!isset($type)){
        return false;
    }
    $arr=array(
        0=>'WEB',
        1=>'SDK',
        2=>'APP',
    );
    return $arr[$type];
}
//获取区服名称
 function get_server_name($id){
    $map['id']=$id;
    $area=M("Server","tab_")->where($map)->find();
    return $area['server_name'];
}
/*
*获取管理员昵称
*/
function get_admin_nickname($uid = null){
    if(empty($uid)){return false;}
    $user = D('member');
    $map['uid'] = $uid;
    $data = $user->where($map)->find();
    if(empty($data['nickname'])){return false;}
    return $data['nickname'];
}
/**
*返回比例样式（百分比）
*@param $num int
*@return string 
*/
function ratio_stytl($num = 0){
    return $num."%";
}
/**
*根据推广员获取所属专员
*/
function get_belong_admin($id)
{
    $map['id']=$id;
    $pro=M("promote","tab_")->where($map)->find();
    if($pro){
     return get_admin_nickname($pro['admin_id']);
    }else{
        return false;
    }
}
//判断用户是否存在
function get_user_one_list($args){
    if(empty($args))return false;
    $user = D('User');
    $map['account']=$args;
    $data = $user->where($map)->find();
    return $data;
}
//判断渠道用户是否存在
function get_user_pro_list($args){
    if(empty($args))return false;
    $user = D('Promote');
    $map['account']=$args;
    $data = $user->where($map)->find();
    return $data;
}
function get_user_id($account){
    $map['account']=$account;
    $user=D("User")->where($map)->find();
    return $user['id'];
}
function get_user_nickname($account){
    $map['account']=$account;
    $user=M("user_play","tab_")->where($map)->find();
    return $user['user_nickname'];
}
/**
*将时间戳装成年月日(不同格式)
*@param  int    $time 要转换的时间戳 
*@param  string $date 类型 
*@return string 
*@author 王贺
*/
function set_show_time($time = null,$type='time'){
    $date = "";
    switch ($type) {
        case 'date':
            $date = date('Y-m-d ',$time);
            break;
        case 'time':
            $date = date('Y-m-d H:i:s',$time);
            break;
        default:
            $date = date('Y-m-d H:i:s',$time);
            break;
    }
    if(empty($time)){
        return "";
    }
    return $date;
}
//生成订单号
    function build_order_no(){
        return date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
    }

//返回扩展工具开启状态
function get_tool_status($name){
    $map['name']=$name;
    $tool=M("tool","tab_")->where($map)->find();
    return $tool['status'];
}
/**
*获取广告位标题
*@param int $pos_id
*@return string
*@author 小纯洁 
*/
function get_adv_pos_title($pos_id=0){
    $adv_pos = M('AdvPos',"tab_");
    $map['id'] = $pos_id;
    $data = $adv_pos->where($map)->find();
    if(empty($data)){return "没有广告位";}
    return $data['title'];
}


function get_file_name($guid='')
{
    return "test";
}
//这个星期的星期一  
// @$timestamp ，某个星期的某一个时间戳，默认为当前时间  
// @is_return_timestamp ,是否返回时间戳，否则返回时间格式  
function this_monday($timestamp=0,$is_return_timestamp=true){
    static $cache ;  
    $id = $timestamp.$is_return_timestamp;  
    if(!isset($cache[$id])){  
        if(!$timestamp) $timestamp = time();  
        $monday_date = date('Y-m-d', $timestamp-86400*date('w',$timestamp)+(date('w',$timestamp)>0?86400:-/*6*86400*/518400));  
        if($is_return_timestamp){  
            $cache[$id] = strtotime($monday_date);  
        }else{  
            $cache[$id] = $monday_date;  
        }  
    }  
    return $cache[$id];  
  
}  
  //array_column修改在低版本运用
function i_array_column($input, $columnKey, $indexKey=null){
    if(!function_exists('array_column')){ 
        $columnKeyIsNumber  = (is_numeric($columnKey))?true:false; 
        $indexKeyIsNull            = (is_null($indexKey))?true :false; 
        $indexKeyIsNumber     = (is_numeric($indexKey))?true:false; 
        $result                         = array(); 
        foreach((array)$input as $key=>$row){ 
            if($columnKeyIsNumber){ 
                $tmp= array_slice($row, $columnKey, 1); 
                $tmp= (is_array($tmp) && !empty($tmp))?current($tmp):null; 
            }else{ 
                $tmp= isset($row[$columnKey])?$row[$columnKey]:null; 
            } 
            if(!$indexKeyIsNull){ 
                if($indexKeyIsNumber){ 
                  $key = array_slice($row, $indexKey, 1); 
                  $key = (is_array($key) && !empty($key))?current($key):null; 
                  $key = is_null($key)?0:$key; 
                }else{ 
                  $key = isset($row[$indexKey])?$row[$indexKey]:0; 
                } 
            } 
            $result[$key] = $tmp; 
        } 
        return $result; 
    }else{
        return array_column($input, $columnKey, $indexKey);
    }
}
//这个星期的星期天  
// @$timestamp ，某个星期的某一个时间戳，默认为当前时间  
// @is_return_timestamp ,是否返回时间戳，否则返回时间格式  
function this_sunday($timestamp=0,$is_return_timestamp=true){
    static $cache ;  
    $id = $timestamp.$is_return_timestamp;  
    if(!isset($cache[$id])){  
        if(!$timestamp) $timestamp = time();  
        $sunday = this_monday($timestamp) + /*6*86400*/518400;  
        if($is_return_timestamp){  
            $cache[$id] = $sunday;  
        }else{
            $cache[$id] = date('Y-m-d 23:59:59',$sunday);  
        }  
    }  
    return $cache[$id];  
}
//上周一
// @$timestamp ，某个星期的某一个时间戳，默认为当前时间
// @is_return_timestamp ,是否返回时间戳，否则返回时间格式
function last_monday($timestamp=0,$is_return_timestamp=true){
    static $cache ;
    $id = $timestamp.$is_return_timestamp;
    if(!isset($cache[$id])){
        if(!$timestamp) $timestamp = time();
        $thismonday = this_monday($timestamp) - /*7*86400*/604800;
        if($is_return_timestamp){
            $cache[$id] = $thismonday;
        }else{
            $cache[$id] = date('Y-m-d',$thismonday);
        }
    }
    return $cache[$id];
}
/**
*获取时间范围
*@param  $field string 查询字段
*@param  $type  string 时间范围类型
*@return array
*/
function get_period($field,$type){
    $start_time =0;$end_time=0;$map = array();
    switch ($type) {
        case 'yesterday'://昨天的开始结束时间
            $start_time = mktime(0,0,0,date('m'),date('d')-1,date('Y'));
            $end_time   = mktime(0,0,0,date('m'),date('d'),date('Y'))-1;
            break;
        case 'today'://今天的开始结束时间
            $start_time = mktime(0,0,0,date('m'),date('d'),date('Y'));
            $end_time   = mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;
            break;
        case 'last_week'://上周的开始结束时间
            $start_time = mktime(0,0,0,date('m'),date('d')-date('w')+1-7,date('Y'));
            $end_time   = mktime(23,59,59,date('m'),date('d')-date('w')+7-7,date('Y'));
            break;
        case 'this_week'://***本周的开始结束时间
            $start_time = this_monday();
            $end_time   = this_sunday();
            break;
        case 'last_moenth'://上月的开始结束时间
            $start_time = mktime(0, 0 , 0,date("m")-1,1,date("Y"));
            $end_time   = mktime(23,59,59,date("m") ,0,date("Y"));
            break;
        case 'this_mneth'://本月的开始结束时间
            $start_time = mktime(0,0,0,date('m'),1,date('Y'));
            $end_time   = mktime(23,59,59,date('m'),date('t'),date('Y'));
            break;
        default:
            $start_time = mktime(0,0,0,date('m'),date('d'),date('Y'));
            $end_time   = mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;
            break;
    }

    $map[$field] = array("BETWEEN",array($start_time,$end_time));
    
    return $map;
}

/**
 * 获取上周时间指定日期的一天的时间戳（开始-结束
 * @param $str 周几（last xxx）英文
 * @param  $time 查询字段
 * @return 查询条件
 */
 function  get_lastweekchuo($str,$time){

    return  $map;
}
/**
 * 获取上周指定日期时间
 * @param  $str 指定时间
 * @return unknown 时间
 */
function  get_lastweek_name($str){
  switch ($str) {
        case '1':
            $time = date("Y-m-d",mktime(0,0,0,date('m'),date('d')-1,date('Y')));
            break;
        case '2':
            $time = date("Y-m-d",mktime(0,0,0,date('m'),date('d')-2,date('Y')));
            break;
         case '3':
            $time = date("Y-m-d",mktime(0,0,0,date('m'),date('d')-3,date('Y')));
            break;
         case '4':
              $time = date("Y-m-d",mktime(0,0,0,date('m'),date('d')-4,date('Y')));
            break;
         case '5':
            $time = date("Y-m-d",mktime(0,0,0,date('m'),date('d')-5,date('Y')));
            break;
        case '6':
            $time = date("Y-m-d",mktime(0,0,0,date('m'),date('d')-6,date('Y')));
            break;
        default:
            $time =date("Y-m-d",mktime(0,0,0,date('m'),date('d'),date('Y')));
            break;

    }
    return $time;
}
/**
 * 获取指定日期时间开始 结束时间戳
 * @param  $str 指定时间
 * @param  $n 几天后
 * @return unknown 时间
 */
function get_start_end_time($time,$n=null){
$t = strtotime($time);
if(null!=$n){
$start = mktime(0,0,0,date("m",$t),date("d",$t)+$n,date("Y",$t));
$end = mktime(23,59,59,date("m",$t),date("d",$t)+$n,date("Y",$t)); 
}else{
$start = mktime(0,0,0,date("m",$t),date("d",$t),date("Y",$t));
$end = mktime(23,59,59,date("m",$t),date("d",$t),date("Y",$t));     
}

$map=array("between",array($start,$end));
return $map;
}
/**
 * 获取指定日期后时间开始 结束时间戳
 * @param  $str 指定时间
 * @param  $n 第几天
 * @return unknown 时间
 */
// function get_start_end_timess($time,$n){
// $t = strtotime($time);
// $start = mktime(0,0,0,date("m",$t),date("d",$t)+$n,date("Y",$t));
// $end = mktime(23,59,59,date("m",$t),date("d",$t)+$n,date("Y",$t)); 
// $map=array("between",array($start,$end));
// return $map;
// }
/**
 * 获取前七天日期时间开始 结束时间戳
 * @param  $str 几天前
 * @return unknown 时间
 */
function get_last_day_time($type,$time){
    switch ($type) {
        case '1':
            $start_time = mktime(0,0,0,date('m'),date('d')-1,date('Y'));
            $end_time   =  mktime(0,0,0,date('m'),date('d'),date('Y'))-1;
            break;
        case '2':
            $start_time =  mktime(0,0,0,date('m'),date('d')-2,date('Y'));
            $end_time   = mktime(0,0,0,date('m'),date('d')-1,date('Y'))-1;
            break;
         case '3':
            $start_time = mktime(0,0,0,date('m'),date('d')-3,date('Y'));
            $end_time   =mktime(0,0,0,date('m'),date('d')-2,date('Y'))-1;
            break;
         case '4':
              $start_time = mktime(0,0,0,date('m'),date('d')-4,date('Y'));
            $end_time   =  mktime(0,0,0,date('m'),date('d')-3,date('Y'))-1;
            break;
         case '5':
            $start_time = mktime(0,0,0,date('m'),date('d')-5,date('Y'));
            $end_time   = mktime(0,0,0,date('m'),date('d')-4,date('Y'))-1;
            break;
        case '6':
            $start_time =  mktime(0,0,0,date('m'),date('d')-6,date('Y'));
            $end_time   = mktime(0,0,0,date('m'),date('d')-5,date('Y'))-1;
            break;
        default:
            $start_time = mktime(0,0,0,date('m'),date('d'),date('Y'));
            $end_time   = mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;
            break;

    }
    $map[$time]=array("between",array($start_time,$end_time));
return $map;
}