<?php
// +----------------------------------------------------------------------
// | 手游平台
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.msun.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: zxc
// +----------------------------------------------------------------------
namespace Addons\SiteStat;
use Common\Controller\Addon;

/**
 * 系统环境信息插件
 * @author thinkphp
 */
class SiteStatAddon extends Addon{

    public $info = array(
        'name'=>'SiteStat',
        'title'=>'站点统计信息',
        'description'=>'统计站点的基础信息',
        'status'=>1,
        'author'=>'thinkphp',
        'version'=>'0.1'
    );

    public function install(){
        return true;
    }

    public function uninstall(){
        return true;
    }

    //实现的AdminIndex钩子方法
    public function AdminIndex($param){
        $config = $this->getConfig();
        $this->assign('addons_config', $config);
        if($config['display']){
            $user = M("User","tab_");
            $game = M("Game","tab_");
            $spend = M('Spend',"tab_");
            $deposit = M('Deposit',"tab_");
            $promote = M("Promote","tab_");
            $yesterday = $this->total(5);
            $today = $this->total(1);
            $month = $this->total(3);
            $p=$promote->where(array('admin_id'=>UID))->select();
            foreach ($p as $key => $value) {
                $pro[]=$value['id'];
            }
            if(empty($pro)){
                $upid['promote_id']=-1;
            }else{
                $upid['promote_id']=array('in',$pro);
            }
            $info['user'] =	$user->where($upid)->count();
            $info['yesterday']= $user->where("register_time".$yesterday)->where($upid)->count();
            $info['today']= $user->where("register_time".$today)->where($upid)->count();
            $info['login']= $user->where("login_time".$today)->where($upid)->count();
            $info['game'] = $game->count();
            $info['add'] = $game->where("create_time".$today)->count();
            $info['monthadd'] = $game->where("create_time".$month)->count();
            //游戏数结束
            //总流水开始
            $u=$user->where($upid)->select();
            foreach ($u as $key => $value) {
                $uro[]=$value['id'];
            }
            if(empty($uro)){
                $uuid['user_id']=-1;
            }else{
                $uuid['user_id']=array('in',$uro);
            }
            //总流水
            $samount = $spend->field('sum(pay_amount) as amount')->where("pay_status=1 and pay_way <> 0")->where($uuid)->select();
            $damount = $deposit->field('sum(pay_amount) as amount')->where("pay_status=1")->where($uuid)->select();
            $total = $samount[0]['amount'] + $damount[0]['amount'];         
            $info['total'] = $this->huanwei($total);
            //昨日流水
            $ysamount = $spend->field('sum(pay_amount) as amount')->where("pay_status=1 and pay_time $yesterday  and pay_way <> 0")->where($uuid)->select();
            $ydamount = $deposit->field('sum(pay_amount) as amount')->where("pay_status=1 and create_time $yesterday")->where($uuid)->select();
            $ytotal = $ysamount[0]['amount'] + $ydamount[0]['amount'];
            $info['ytotal'] = $this->huanwei($ytotal);
            //今日流水
            $tsamount = $spend->field('sum(pay_amount) as amount')->where("pay_status=1 and pay_time $today  and pay_way <> 0")->where($uuid)->select();
            $tdamount = $deposit->field('sum(pay_amount) as amount')->where("pay_status=1 and create_time $today")->select();
            $ttotal = $tsamount[0]['amount'] + $tdamount[0]['amount'];
            $info['ttotal'] = $this->huanwei($ttotal);
            //推广流水
            $psamount = $spend->field('sum(pay_amount) as amount')->where("pay_status=1 and promote_id > 0  and pay_way <> 0")->where($uuid)->select();
            $pdamount = $deposit->field('sum(pay_amount) as amount')->where("pay_status=1")->where($uuid)->select();
            $ptotal = $psamount[0]['amount'] + $pdamount[0]['amount'];
            $info['ptotal'] = $this->huanwei($ptotal);
            //总流水结束
            //渠道数量开始
            $info['promote'] = $promote->where(array('admin_id'=>UID))->count();
            $info['padd'] = $promote->where(array('admin_id'=>UID))->where("create_time".$today)->count();
            $info['monthpadd'] = $promote->where("create_time".$month)->where(array('admin_id'=>UID))->count();
            //渠道数量结束
            //文章开始       
            $doc = D("Document");
            $b =$this->cate("blog"); 
            $m =$this->cate("media"); 
            $blog = $doc->table("__DOCUMENT__ as d")
                ->where("d.status=1 and d.display=1 and d.category_id in (".$b.")")->count();
            $media = $doc->table("__DOCUMENT__ as d")
                ->where("d.status=1 and d.display=1 and d.category_id in (".$m.")")->count();
            $info['document'] = $this->huanwei($blog + $media);
            $info['blog']=$this->huanwei($blog);
            $info['media']=$this->huanwei($media);
            //文章结束
            // 图表
            $info['pay']=$this->idata($this->linepay(),true,'pay_amount');
            $info['reg']=$this->idata($this->lineregister());
            $this->assign('info',$info);
            $this->display('info');
        }
    }
    
    private function cate($name) {
        $cate = M("Category");
        $c = $cate->field('id')->where("status=1 and display=1 and name='$name'")->buildSql();
        $ca = $cate->field('id')->where("status=1 and display=1 and pid=$c")->select();
        foreach($ca as $c) {
            $d[]=$c['id'];
        }
        return "'".implode("','",$d)."'";
    }

    private function idata($data,$flag=false,$field) {
        $d = $c = '';
        $max = 0;
        $min = 0;
        if (!empty($data)) {
            $data = array_reverse($data);
            if ($flag) {
                foreach ($data as $k => $v) {
                    if (!empty($v)) {
                        foreach($v as $j => $u) {
                            $total += $u[$field];
                        }
                    } else {
                        $total = 0;
                    }         
                    if ($min>$total){$min = $total;}
                    if ($max<$total){$max = $total;}
                    $d .= $total.',';
                    $c .= '"'.$k.'",';          
                }                
            } else {
                foreach ($data as $k => $v) {
                    $count = empty($v)?0:count($v);        
                    if ($min>$count){$min = $count;}
                    if ($max<$count){$max = $count;}
                    $d .= $count.',';
                    $c .= '"'.$k.'",';          
                }
            }
            $d = substr($d,0,-1);
            $c = substr($c,0,-1);           
        }
        $max++;
        $pay = array(
            'min' => $min,
            'max' => $max,
            'data' => $d,
            'cate' => $c
        );
        return $pay;
    }
    private function linepay() {
        $p=M('Promote','tab_')->where(array('admin_id'=>UID))->select();
        foreach ($p as $key => $value) {
            $pro[]=$value['id'];
        }
        if(empty($pro)){
            $upid['promote_id']=-1;
        }else{
            $upid['promote_id']=array('in',$pro);
        }
        $u=M('User','tab_')->where($upid)->select();
        foreach ($u as $key => $value) {
            $uro[]=$value['id'];
        }
        if(empty($uro)){
            $uuid['user_id']=-1;
        }else{
            $uuid['user_id']=array('in',$uro);
        }
        $spend = M('Spend',"tab_");
        $deposit = M('Deposit',"tab_");
        $week = $this->total(9);
        $samount = $spend->field("pay_amount,pay_time as time")->where("pay_status=1 and pay_way <> 0 and pay_time $week")->where($uuid)->select();
        $damount = $deposit->field("pay_amount,create_time as time")->where($uuid)->where("pay_status=1 and create_time $week")->select();
        
        if (!empty($samount) && !empty($damount) )
            $data = array_merge($samount,$damount);
        else {
            if (!empty($samount))
                $data = $samount;
            else if (!empty($damount))
                $data = $damount;
            else 
                $data = '';
        }

        $result = array();
		$this->jump($data,$result,8);
        return $result;
    }
    
    private function lineregister() {
        $week = $this->total(9);
        $p=M('promote','tab_')->where(array('admin_id'=>UID))->select();
        foreach ($p as $key => $value) {
            $pro[]=$value['id'];
        }
        if(empty($pro)){
            $upid['promote_id']=-1;
        }else{
            $upid['promote_id']=array('in',$pro);
        }
        $user = M("User","tab_")->field("account,register_time as time,promote_id")->where("lock_status=1 and register_time $week")->where($upid)->select();

        if (!empty($user))
            $data = $user;
        else 
            $data = array(0,0,0,0,0,0,0);
        
        $result = array();
		$this->jump($data,$result,8);
        return $result;
    }
    
    protected function jump(&$a,&$b,$m,$n=0) {
		$num = count($a);
		if($m == 1) {
			return ;
		} else {
            $time = time();    
            if ($m < 8) {
                $c = 8 - $m;
                $time = $time - ($c * 86400);
            }
            $m -= 1;
            $t = date("Y-m-d",$time);
            if (empty($a) && count($b)<8) {
                $b[$t]= "";
            } else {
                foreach($a as $k => $g) {
                    $st = date("Y-m-d",$g['time']);
                    if($t===$st) {				
                        $b[$t][]=$g;
                        unset($a[$k]);
                    } else {
                        $b[$t]= "";
                    }
                }
                $a = array_values($a);		
            }
			return $this->jump($a,$b,$m,$num);
		} 
	}
    
    private function total($type) {
        switch ($type) {
            case 1: { // 今天
                $start=mktime(0,0,0,date('m'),date('d'),date('Y'));
                $end=mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;
            };break;
            case 3: { // 本月
                $start=mktime(0,0,0,date('m'),1,date('Y'));
                $end=mktime(0,0,0,date('m')+1,1,date('Y'))-1;
            };break;
            case 4: { // 本年
                $start=mktime(0,0,0,1,1,date('Y'));
                $end=mktime(0,0,0,1,1,date('Y')+1)-1;
            };break;
            case 5: { // 昨天
                $start=mktime(0,0,0,date('m'),date('d')-1,date('Y'));
                $end=mktime(0,0,0,date('m'),date('d'),date('Y'));
            };break;
            case 9: { // 前七天
                $start = mktime(0,0,0,date('m'),date('d')-6,date('Y'));
                $end=mktime(date('H'),date('m'),date('s'),date('m'),date('d'),date('Y'));
            };break;
            default:
                $start='';$end='';
        }
        
        return " between $start and $end ";
    }
    
    private function huanwei($total) {
        $total = empty($total)?'0':trim($total.' ');
        $len = strlen($total);
        if ($len>8) { // 亿
           $len = $len-12;
           $total = $len>0?(round(($total/1e12),2).'万亿'):round(($total/1e8),2).'亿';            
        } else if ($len>4) { // 万
            $total = (round(($total/10000),2)).'w';
        }
        return $total;
    }
}