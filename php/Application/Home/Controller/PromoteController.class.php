<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: zxc <zuojiazi@vip.qq.com> <http://www.msun.com>
// +----------------------------------------------------------------------

namespace Home\Controller;
use OT\DataDictionary;
use User\Api\PromoteApi;
use User\Api\UserApi;
/**
 * 前台首页控制器
 * 主要获取首页聚合数据
 */
class PromoteController extends BaseController {
    //系统首页
    public function index(){
            header("Content-type:text/html;charset=utf-8");
        $user = D('Promote')->isLogin();
        if(empty($user)) {
            $this->redirect("Home/Index/index");
        }
        $this->assign("today",$this->total(1));
        $this->assign("month",$this->total(3));
        $this->assign("total",$this->total());
         $this->assign("yesterday",$this->total(5));
        $this->assign("list",M("Document")->where("category_id=40")->select());
        $url="http://".$_SERVER['HTTP_HOST'].__ROOT__."/media.php/member/preg/pid/".get_pid();
        $this->assign("url",$url);
        $this->display();
    }    private function total($type) {    
        if($_REQUEST['promote_id'] ===null || $_REQUEST['promote_id']==='0'){    
             $ids = M('Promote','tab_')->where('parent_id='.get_pid())->getfield("id",true);     
             if(empty($ids)){
                $ids = array(get_pid());
             }       
          array_unshift($ids,get_pid());      
         } else{         
            $ids = array($_REQUEST['promote_id']);  
         }    $where['promote_id'] = array('in',$ids);   
              $where['pay_status'] = 1;    
                 switch ($type) {    
                 case 1: { // 今天      
                     $start=mktime(0,0,0,date('m'),date('d'),date('Y'));       
                       $end=mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;      
                     };
                     break;   
                      case 3: { 
                      // 本月        
                    $start=mktime(0,0,0,date('m'),1,date('Y'));       
                      $end=mktime(0,0,0,date('m')+1,1,date('Y'))-1;       
                    };
                    break;         
                       case 4: { 
                       // 本年    
                        $start=mktime(0,0,0,1,1,date('Y'));   
                        $end=mktime(0,0,0,1,1,date('Y')+1)-1;    
                    };
                    break;   
                       case 5: { // 昨天     
                        $start=mktime(0,0,0,date('m'),date('d')-1,date('Y')); 
                         $end=mktime(0,0,0,date('m'),date('d'),date('Y'));  
                              };                  
                            break;    
                       case 9: { // 前七天                                                                     
                               $start = mktime(0,0,0,date('m'),date('d')-6,date('Y')); 
                               $end=mktime(date('H'),date('m'),date('s'),date('m'),date('d'),date('Y'));      
                              };
                              break; 
                            default:;             
                }
                if (isset($start) && isset($end)) { 
                       $where['pay_time'] = array("BETWEEN",array($start,$end));     
                      }        
                         $total = M('spend',"tab_")->field("SUM(pay_amount) as amount")->where($where)->group("promote_id")->select();
                         $total = $this->huanwei($total[0]['amount']); 
                         return $total;
                     }   

                private function huanwei($total) {
                         $total = empty($total)?'0':trim($total.' '); 
                         $len = strlen($total)-3; 
                         if ($len>16) { 
                         // 兆       
                         $len = $len - 20; 
                          $total = $len>0?($len>4?($len>8?round(($total/1e28),4).'万亿兆':round(($total/1e24),4).'亿兆'):round(($total/1e20),4).'万兆'):round(($total/1e16),4).'兆';          
                           } else if ($len>8) { 
                           // 亿       
                          $len = $len-12;  
                            $total = $len>0?(round(($total/1e12),4).'万亿'):round(($total/1e8),4).'亿';    
                             } else if ($len>4) {
                              // 万 
                               $total = (round(($total/10000),4)).'万';    
                            }  
                         return $total; 
                    }


    /**
    * 我的基本信息
    */
    public function base_info(){
        if(IS_POST){
            $type = $_REQUEST['type'];
            $map['id']=get_pid();
            $se=array();
            switch ($type) {
                case 0:
                $se['nickname']=$_REQUEST['nickname'];
                $se['real_name']=$_REQUEST['real_name'];
                $se['email']=$_REQUEST['email'];
                    break;
                case 1: 
                if($_REQUEST['s_county']==="市、县级市"){
                  $this->error('开户城市填写不完整',U('Promote/base_info'));
                    return false;
                    exit();
                }
                $se['mobile_phone']=$_REQUEST['mobile_phone'];
                $se['bank_name']=$_REQUEST['bank_name'];
                $se['bank_card']=$_REQUEST['bank_card']; 
                $se['bank_account']=$_REQUEST['bank_account']; 
                $se['account_openin']=$_REQUEST['account_openin'];
                $se['bank_area']=$_REQUEST['s_province'].','.$_REQUEST['s_city'].','.$_REQUEST['s_county'];
                    break;                
                case 2:
                $prp=M("promote","tab_")->where($map)->find();
                $ue=new UserApi();
                if($this->think_ucenter_md5($_REQUEST['old_password'],UC_AUTH_KEY)!==$prp['password']){
                    $this->error('密码错误',U('Promote/base_info'));
                    return false;
                    exit();

                }else if($_REQUEST['password']!==$_REQUEST['confirm_password']){
                    $this->error('密码不一致',U('Promote/base_info'));
                    return false;
                }else{
                    $se['password']=$this->think_ucenter_md5($_REQUEST['confirm_password'],UC_AUTH_KEY);
                }
                    break;  
                 case 3:
                $prp=M("promote","tab_")->where($map)->find();
                $ue=new UserApi();
                if($this->think_ucenter_md5($_REQUEST['old_second_pwd'],UC_AUTH_KEY)!=$prp['second_pwd']){
                    $this->error('二级密码错误',U('Promote/base_info'));
                    return false;
                    exit();

                }else if($_REQUEST['second_pwd']!==$_REQUEST['confirm_second_pwd']){
                    $this->error('密码不一致',U('Promote/base_info'));
                    return false;
                }else{
                    $se['second_pwd']=$this->think_ucenter_md5($_REQUEST['confirm_second_pwd'],UC_AUTH_KEY);
                }
                    break;               
                    default:
                $se['nickname']=$_REQUEST['nickname'];
                $se['real_name']=$_REQUEST['real_name'];
                $se['email']=$_REQUEST['email'];
                    break;
            }
            $res=M("promote","tab_")->where($map)->save($se);
            if($res !==false){
                $this->success('修改成功',U('Promote/base_info?type='.$type));
            }
            else{
                $this->error('修改失败',U('Promote/base_info'));
            }
        }
        else{
            $model = M('Promote','tab_');
            $data = $model->find(session("promote_auth.pid"));
            $data['bank_area']=explode(',',$data['bank_area']);
            $this->meta_title = "基本信息";
            $this->assign("data",$data);
            $this->display();
        }
    }

public function think_ucenter_md5($str, $key = 'ThinkUCenter'){
    return '' === $str ? '' : md5(sha1($str) . $key);
}
    /**
    *子账号
    */
    public function mychlid($p=0){
       if($_REQUEST['account']!=null){
        $map['account']=array('like','%'.$_REQUEST['account'].'%');
       }
        $map['parent_id'] = session("promote_auth.pid");
        parent::lists("Promote",$p,$map);
    }

    public function add_chlid(){
        if(IS_POST){
            $user = new PromoteApi();
            $res = $user->promote_add($_POST);
            if(is_numeric($res)){
                $this->success("子账号添加成功",U('Promote/mychlid'));
            }
            else{
                $this->error($res);
            }
        }
        else{
            $this->display();
        }
        
    }

    public function edit_chlid($id = 0){
        if(IS_POST){
            $user = new PromoteApi();
            var_dump($_POST);exit;
            $res = $user->edit();
            if($res){
                $this->success("子账号修改成功",U('Promote/mychlid'));
            }
            else{
                $this->error("修改子账号失败");
            }
        }
        else{
            $promote = A('Promote','Event');
            $promote->baseinfo('edit_chlid',$id);
        }
        
    }
}