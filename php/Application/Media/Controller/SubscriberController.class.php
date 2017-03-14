<?php
namespace Media\Controller;

use Common\Api\GameApi;
use User\Api\UserApi;
use Org\XiguSDK\Xigu;
class SubscriberController extends BaseController {
    
	public function __construct() {
		parent::__construct();
		$arr = array(
			"Subscriber/index","Subscriber/safe",
			"Subscriber/wallet","Subscriber/profile",
			"Subscriber/set","Subscriber/pwd"
		);
		if (in_array($_SERVER['PATH_INFO'],$arr,true)) {
			$up = D("User")->isLogin();
			if (empty($up) || 1 != $up['status']) {
				$this->redirect("Subscriber/login");
			}
			
		}
        define('SA_ID',session('user_auth.uid'));
		$User = new UserApi();	
		$time = time();
		$rl['day'] = date('m.d',$time);
		$weekcn = array('日','一','二','三','四','五','六');
		$rl['week'] = $weekcn[date('w',$time)];
		$this->assign('rl',$rl);
	}
	
	public function index() {
		$u = D("User");
		$user=$u->isLogin();
		$up = $u->getUserInfo($user['uid']);
		$time=time() - $up['register_time'];
		$day = intval($time/86400)+1;
		$month = $day>29?intval($time/2592000):'';
		if ($month) {
			$up['day']=$month.'个月';
		} else
			$up['day']=$day.'天';
		
		$sl = $this->checksafe($up);
		$up['safelevel']=$sl>-1?($sl==1?'high':'middle'):'low';
   /*     print_r($user);*/
        $this->assign('user',$up);$this->assign('up',$up);
/*        echo $_SESSION['uid'];*/
        $this->display();
	}
    
    private function checksafe($user) {
		if (!empty($user['phone']) && !empty($user['real_name']) &&!empty($user['idcard'])) {
			return 1;
		} else if(!empty($user['phone']) || (!empty($user['real_name']) &&!empty($user['idcard']))) {
			return 0;
		} else {
			return -1;
		}
	}
	
	/**
	* 重置记录
	*/
	public function record() {
		$up = D("User");
		$u = $up->isLogin();
		$map['user_account']=$u['username'];
		$re = D("Recharge");
		$recharge = D("Recharge")->field("r.*,g.game_name,a.area_name")
		->table('__RECHARGE__ r')
		->join("__GAME__ as g on g.game_appid=r.game_appid")
		->join("__AREA__ as a on a.game_id=g.id")
		->where($map)->select();
		/* $sql="select r.*,g.game_name,a.area_name from tab_recharge as r left join tab_game as g on r.game_appid=g.game_appid"
		." left join tab_area as a on a.game_id=g.id where user_account='".$u['username']
		."' ";
		$recharge= $re->query($sql); */
		$totle = $re->field('sum(pay_amount) as totle')->where($map)->find();
		
		$u = $up->getUserInfo($u['uid']);
		if (!empty($u['phone'])) {
			$phone = substr($u['phone'],3,7);
			$u['phone']=str_replace($phone,'****',$u['phone']);
		}
		if (!empty($u['email'])) {
			$email = substr($u['email'],2,5);
			$u['memail']=str_replace($email,'***',$u['email']);
		}
		$this->assign("recharge",$recharge);
		$this->assign("totle",$totle['totle']);
		$this->assign("up",$u);
		$this->display();		
	}
	
	/**
	* 账户安全
	*/
	public function safe() {
		$up = D("User");
		$u = $up->isLogin();

		$u = $up->getUserInfo($u['uid']);
		$sl = $this->checksafe($u);
		$num = $sl>-1?($sl>0?0:1):2;
		$score = $sl>-1?($sl>0?100:66):30;
		$this->assign('safenum',$num);
		$this->assign('safescore',$score);
		
		if (!empty($u['phone'])) {
			$phone=$u['phone'];
			$u['phone']=substr($phone,0,2).'*******'.substr($phone,-2);           
		}		
		if (!empty($u['real_name']) && !empty($u['idcard'])) {
			$real_name = $u['real_name'];
			$rnl = mb_strlen($real_name)-1;
			$idcard = $u['idcard'];
			$u['real_name']='*'.mb_substr($real_name,1,$rnl,'utf-8');
			$u['idcard']=substr($idcard,0,5).'***********'.substr($idcard,-2);
		}
		$this->assign("up",$u);	
		$this->display();
	}
	
	// 验证密码
	public function checkpassword($password,$type) {
		$up = D("User");
		$u = $up->isLogin();
		$bool = $up->checkPwd($u['username'],$password);
		if ($bool>0) {
			echo json_encode(array('status'=>1,'info'=>'','type'=>$type));
		} else {
			echo json_encode(array('status'=>0,'info'=>'你输入了错误的密码','type'=>$type));
		}
	}
    

	// 发送邮件
	public function sendemail($email,$action) {
		if (IS_POST) {
			$up = D("User");
			$u = $up->isLogin();
			$smail = A('SendMail');
			$randchar = $this->getRandChar($email,100);
			$bool = $smail->sendMail($email,$u['username'],'溪谷游戏邮箱验证确认邮件',$u['username'],'http://'.$_SERVER['HTTP_HOST'].U('safe?action='.$action.'&rc='.$randchar));
			if ($bool) {
				$e = explode('@',$email);
				$data['email']=substr($e[0],0,2).'*****'.substr($e[0],-1).'@'.$e[1];
				$data['emailurl']='http://mail.'.$e[1];
				$data['status']=1;
				$aemail['email']=$email;
				$aemail['action']=$action;
				$aemail['time']= time();
				$aemail['delay']=30;
				session('auth_email',$aemail);
				echo json_encode($data);
			} else {
				echo json_encode(array('status'=>0,'info'=>$this->getE($bool)));
			}			
		} else {
			echo json_encode(array('status'=>0,'info'=>'系统故障'));
		}
	}
	
	// 获取随机字符串
	private function getRandChar($randstr,$length) {
		$str2 = $str1 = null;
		$strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890_%";
		$max = strlen($strPol)-1;
		$str = think_ucenter_md5($randstr,$strPol);
		for($i=0;$i<$length;$i++) {
			$str1 .= $strPol[rand(0,$max)]; 
			$str2 .= $strPol[rand(0,$max)];
		}
		$str = $str1.''.$str.''.$str2;
		return $str;
	}
	
	// 实名认证
	public function updateinfo($realname,$idcard) {
		$up = D('User');
		$u = $up->isLogin();
		if (IS_POST) {
			$bool = $up->updaterealinfo($u['uid'],$realname,$idcard);
			if ($bool>0) {
				echo json_encode(array('status'=>1,'info'=>'实名认证成功'));
			} else {
				echo json_encode(array('status'=>0,'info'=>$bool));
			}
		} else
			echo json_encode(array('status'=>0,'info'=>'操作不合法'));
	}
	
	
	// 我的钱包
	public function wallet() {
		$up = D("User");
		$u = $up->isLogin();
/*      $reclist="";
        $name = $u['username'];
        $rec['model']="Recharge";
        $rec['prefix']="tab_";
        $rec['where']="pay_way=2 and user_account='$name'";
        $rec['order']="pay_time desc";*/
    /*  $reclist = parent::showlist($rec,10);*/
        $balance = $up->where('id='.$u['uid'])->find();
        $reclist = $up->table('tab_deposit')->where('user_id='.$u['uid'])->limit(10)->order('id desc')->select();
        $this->assign('balance',$balance['balance']);
        $this->assign('rec',$reclist);
                
		$this->display();
	}
	
	//个人信息
	public function profile() {
		$up = D("User");
		$u = $up->isLogin();
		if ($_POST) {
			$nickname = $_POST['nickname'];
			$bool = $up->updatenikename($u['uid'],$nickname);
			if ($bool>0) {
				$up->updatenk($nickname);
				$data['info'] = '昵称修改成功';
				$data['status']=1;
			} else {
				$data['info'] = '昵称修改失败';
				$data['status']=0;
			}
			echo json_encode($data);
		} else {
			$u = $up->getUserInfo($u['uid']);
			$idcard = $u['idcard'];
			$idcard = substr($idcard,3,-2);
			$u['idcard'] = str_replace($idcard,'**************',$u['idcard']);
			$this->assign("up",$u);
            $this->assign('user',$u);
			$this->display();
		}
	}
      
	// 发送手机安全码
	/*public function telsvcode($phone=null,$verify='',$delay=10,$flag=false) {
        if (empty($phone)) {
            $up = D("User");
            $u = $up->isLogin();
            $user = $up->getUserInfo($u['uid']);
            $phone = $user['phone'];
        }
        if (empty($phone)) {
            echo json_encode(array('status'=>0));exit; 
        }
        
        /// 产生手机安全码并发送到手机且存到session
		$rand = rand(100000,999999);
        $xigu = new Xigu(C('sms_set.smtp'));
        $param = $rand.",".$delay;
        $result = json_decode($xigu->sendSM(C('sms_set.smtp_account'),$phone,C('sms_set.smtp_port'),$param),true); 
        
        $data = array(
            'phone'=>$phone,
            'send_status' => '000000',
            'send_time'  => time(),
            'smsId'  => '0e05c1a32ce530828658f6141778ef0c',
            'create_time' => time()
        );
        $jresp = json_encode($data);
        $result = json_decode($jresp,true);
                       
        // 存储短信发送记录信息
        $result['create_time'] = time();
        $result['pid']=0;
        $r = M('Short_message')->add($result);
        
        if ($result['send_status'] != '000000') {
            echo json_encode(array('status'=>0,'msg'=>'发送失败，请重新获取'));exit;
        }        
		$telsvcode['code']=$rand;
		$telsvcode['phone']=$phone;
		$telsvcode['time']=$result['create_time'];
        $telsvcode['delay']=$delay;
		session('telsvcode',$telsvcode);
        
        
        if ($flag) {
            if (!empty($verify) && check_verify($verify)) {
                    echo json_encode(array('status'=>1,'msg'=>'安全码已发送，请查收','data'=>$telsvcode));                   
            } else
                echo json_encode(array('status'=>0));exit;            
        } else
            echo json_encode(array('status'=>1,'msg'=>'安全码已发送，请查收','data'=>$telsvcode));
	}*/
    // 发送手机安全码
    public function telsvcode($phone=null,$verify='',$delay=10,$flag=false) {
        if (empty($phone)) {
            $up = D("User");
            $u = $up->isLogin();
            $user = $up->getUserInfo($u['uid']);
            $phone = $user['phone'];
        }
        if (empty($phone)) {
            echo json_encode(array('status'=>0));exit; 
        }
        
        /// 产生手机安全码并发送到手机且存到session
        $rand = rand(100000,999999);
        $xigu = new Xigu(C('sms_set.smtp'));
        $param = $rand.",".$delay;
        $result = json_decode($xigu->sendSM(C('sms_set.smtp_account'),$phone,C('sms_set.smtp_port'),$param),true); 
        
        $data = array(
            'phone'=>$phone,
            'send_status' => '000000',
            'send_time'  => time(),
            'smsId'  => '0e05c1a32ce530828658f6141778ef0c',
            'create_time' => time()
        );
        $jresp = json_encode($data);
        $result = json_decode($jresp,true);
                       
        // 存储短信发送记录信息
        $result['create_time'] = time();
        $result['pid']=0;
        $r = M('Short_message')->add($result);
        
        if ($result['send_status'] != '000000') {
            echo json_encode(array('status'=>0,'msg'=>'发送失败，请重新获取'));exit;
        }        
        $telsvcode['code']=$rand;
        $telsvcode['phone']=$phone;
        $telsvcode['time']=$result['create_time'];
        $telsvcode['delay']=$delay;
        session('telsvcode',$telsvcode);
        
        
        if ($flag) {
            if (!empty($verify) && check_verify($verify)) {
                    echo json_encode(array('status'=>1,'msg'=>'安全码已发送，请查收','data'=>$telsvcode));                   
            } else
                echo json_encode(array('status'=>0));exit;            
        } else
            echo json_encode(array('status'=>1,'msg'=>'安全码已发送，请查收','data'=>$telsvcode));
    }
    
    public function sendsafecode($phone) {
        if (IS_POST) {
            $verify = new \Think\Verify();
            if(!($verify->check(I('verify'),I('vid')))){               
                echo json_encode(array('status'=>0,'msg'=>'验证码不正确'));exit;
            }
            $res = D('User')->checkField($phone,3);
            if (!$res) {
                echo json_encode(array('status'=>0,'msg'=>'手机号码被占用'));exit;
            }
            
            $this->telsvcode($phone);
        } else{
            echo json_encode(array('status'=>0,'msg'=>'请按正确的流程'));exit;
        } 
    }

    // 短信验证
    public function checktelsvcode($phone,$vcode,$flag=true) {       
        $telsvcode = session('telsvcode');
        $time = (time() - $telsvcode['time'])/60;
        if ($time>$telsvcode['delay']) {
            session('telsvcode',null);unset($telsvcode);
            echo json_encode(array('status'=>0,'msg'=>'时间超时,请重新获取'));exit;
        }
        if (!($telsvcode['code'] == $vcode) || !($telsvcode['phone'] == $phone)) {
            echo json_encode(array('status'=>0,'msg'=>'安全码输入有误'));exit;
        }
        session('telsvcode',null);
        unset($telsvcode); 
        if ($flag) {
            echo json_encode(array('status'=>1));
        }
    } 

    public function csafecode($phone,$vcode,$flag=true) {       
        $this->checktelsvcode($phone,$vcode,false);
        $data=array(
            'id' => SA_ID,
            'phone' => $phone,
        );
        
        $bool = D("User")->updateUserInfo($data);
        
        if ($flag) {
            echo json_encode(array('status'=>1));
        } else {
            echo json_encode(array('status'=>1));
        }
    }    
    
	public function game_spend(){
		$up = D("User");
		$user = $up->isLogin();
		if(IS_POST){
			$game_id = $_POST['game_id'];
			$area_id = $_POST['server_id'];
			$user_id = $user['uid'];// $_POST['username'];
			$amount  = $_POST['amount'];
			$balance = M('user','tab_')->where('id='.$user_id)->find();
			$user_entity = M('play','tab_user_')->where("game_id=".$game_id." and area_id=".$area_id.' and user_id='.$user_id)->find();
			//判断余额
			if($balance['balance'] < $amount){$this->error("余额不足");}
			//实例化接口 调用游戏支付地址进行支付
			$gameapi = new GameApi();
			$out_trade_no = "GP_".date('Ymd').date ( 'His' ).sp_random_string(4);
			$gameapi_data = $gameapi->game_pay($game_id,$area_id,$user_id,$amount,$out_trade_no);
			if($gameapi_data['status'] == 1){
				M('user','tab_')->where('id='.$user_id)->setDec('balance',$amount);
				$data['user_account'] = $_POST['username'];
				$data['game_appid'] = get_game_appid($game_id);
				$data['game_id'] = $game_id;
				$data['game_name'] = get_gamename($game_id);
				$data['area_id'] = $area_id;
				$data['area_name'] = get_area_name($area_id);
				$data['promote_id'] =  $user_entity['promote_id'];
				$data['promote_account'] = $user_entity['promote_name'];
				$data['order_number'] = "";//$res['ordernum'];
				$data['pay_order_number'] = $out_trade_no;
				$data['props_name'] = '游戏元宝';
				$data['pay_amount'] = $amount;
				$data['pay_time'] = NOW_TIME;
				$data['pay_status'] = 1;
				$data['pay_game_status'] = 1;
				$data['pay_way'] = 2;
				$data['pay_source'] = 0;
				$data['recharge_ip'] = get_client_ip();
				M('recharge','tab_')->add($data);
				$this->success('成功');
			}
			else{
				$this->error("失败");
			}
		}
		else{
			
			$this->game_list();
			
			$this->display();
		}
	}

	protected function game_list(){
		$sql="select g.id as game_id,TRIM(g.game_name) as game_name,g.initials,g.short as game_short,"
		."gi.game_coin_name as coin_name,gi.game_coin_ration as coin_ratio from tab_game as g "
		."left join tab_game_info as gi on gi.id=g.id "
		." where g.game_status=1 and g.pay_status=1";
		$game = D('Game')->query($sql);
		$jsgame = array();
		foreach($game as $g) {
			foreach($g as $k => $v) {
				if ('coin_name' == $k) $v='元宝';
				if ('game_short'==$k) {
					$s = substr($v,0,1);
					$jsgame[$g['game_id']]['game_intial'] = urlencode($s);
				}
				$jsgame[$g['game_id']][$k] = urlencode($v);
			}
		}
		$this->assign('game',$game);
	}

	public function gameArea($game_id) {
		$sql = "select ga.id as gamearea_id,ga.game_id as game_id,ga.area_name as gamearea_name,"
		."ga.area_num from tab_area as ga "
		."right join tab_game as g on ga.game_id = g.id "
		."where ga.stop_status=1 and ga.show_status=1 and ga.game_id=".$game_id." order by ga.id";
		$garearea = D('Area')->query($sql);
		
		if($garearea)
			$this->ajaxReturn(array('status'=>1,'data'=>$garearea),C('DEFAULT_AJAX_RETURN'));
		else
			$this->ajaxReturn(array('status'=>0),C('DEFAULT_AJAX_RETURN'));
			
	}

	public function user_account(){
		$game_id = $_POST['game_id'];
		$area_id = $_POST['area_id'];
		$user_account = $_POST['account'];
		$map['game_id'] = $game_id;
		$map['area_id'] = $area_id;
		$map['account'] = $user_account;
		$entity = M('play','tab_user_')->field('tab_user_play.*,tab_user.account')
						   ->join('left join tab_user on tab_user_play.user_id = tab_user.id')
						   ->where($map)
						   ->find();
		if(empty($entity)){
			return $this->ajaxReturn(array('data'=>0,'role_name'=>''));
		}
		else{
			return $this->ajaxReturn(array('data'=>1,'role_name'=>empty($entity['role_name'])?'存在':$entity['role_name']));
		}
	}
    
    
    // 修改密码
    public function pwd() {
        $up=D("User");
        $u = $up->isLogin();
        if (IS_POST) {  
            $oldpassword = I('old_password');
            $password = I('password');
            if (empty($oldpassword) || empty($password)) {
                echo json_encode(array('status'=>0,'msg'=>'提交的数据有误'));exit;
            }
            $bool = $up->checkPwd($u['username'],$oldpassword);
            if ($bool<1) {
                echo json_encode(array('status'=>0,'msg'=>'原密码输入错误'));exit;
            }
            $this->changepwd($u['uid'],$password);   
        } else {           
            $this->assign('up',$u);
            $this->display();
        }
    }
       
    // 改密码
    private function changepwd($uid,$password) {
        $up=D("User");
        $bool1 = $up->update($uid,$password);
        if ($bool1>0) {
            $data['status'] =1;
            $data['msg'] = '密码重置成功';
        } else {
            $data['status']=0;
            $data['msg'] = '密码重置失败';
        }
        echo json_encode($data);  
    }
	
    // 忘记密码
    public function forget() {
        $up=D("User");
        $u = $up->isLogin();
        if ($u) {
            $this->redirect('Subscriber/resetpwd/t/m/name/'.$u['username']);
        }
        if (IS_POST) {
            $account = I('account');
            $user = M('User','tab_')->where("account='$account'")->find();
            if (!empty($user) && is_array($user) && (1 == $user['lock_status'])) {
                $data['status']=1;
                $data['phone']=$user['phone'];
            } else {
                $data['status']=0;
            }   
            echo json_encode($data);
        } else {            
            $this->display();
        }
    }
    
    // 发送安全码
    public function sendvcode($phone,$verify) {
        if (!IS_POST) {
            echo json_encode(array('status'=>0,'msg'=>'请按正确的流程'));exit;
        }
        $verify = new \Think\Verify();
		if(!$verify->check(I('verify'),I('vid'))){
            echo json_encode(array('status'=>2,'msg'=>'验证码不正确')); exit;
        }
        $phone = I('phone');
        $this->telsvcode($phone);
    }
    
    
    // 重置密码  lwx 
    public function resetpwd() {
        $t = I('t');
        $name = I('name');
        if (empty($t) || empty($name) || $t !== 'm') {
            $this->redirect('forget');
        }
        $up=D("User");
        $user = $up->isLogin();
        if (!$user) {
            $this->redirect('forget');
        }
        if ($name !== $user['username']) {
            session('user_auth', null);
            session('user_auth_sign', null);
            session('[destroy]');$this->redirect('forget');
        }
        if (IS_POST) {
            
            $this->changepwd($user['uid'],I('password'));
            
        } else {            
            $user = $up->where("id=".$user['uid'])->find();
            if (!empty($user['phone'])) {
                $phone = substr($user['phone'],2,-2);
                $this->assign('phone',str_replace($phone,'*******',$user['phone']));
                $this->assign('ph',$user['phone']);                
            } 
            $this->assign('name',$user['account']);
            $this->display();
             
        }
    }
    
    
    
    public function findpwd() {
        $t = I('t');
        $name = I('name');
        if (empty($t) || empty($name) || $t !== 'f') {
            $this->redirect('forget');
        }
        $user = M('User','tab_')->where("account='$name'")->find();
        if (IS_POST) {
            
            $this->changepwd($user['id'],I('password'));
            
        } else {                        
            if (!empty($user['phone'])) {
                $phone = substr($user['phone'],2,-2);
                $this->assign('phone',str_replace($phone,'*******',$user['phone']));
                $this->assign('ph',$user['phone']);                
            } 
            $this->assign('name',$user['account']);
            $this->display();             
        }
    }	
	
    //领取礼包
public function getGameGift() { 
        $mid = D("User")->isLogin();  
/*        print_r($mid);  */
   /*     print_r($_POST);*/
        if($mid['uid']==0){
            echo  json_encode(array('status'=>'0','msg'=>'请先登录'));
            exit();
        }
        $list=M('record','tab_gift_');
        $is=$list->where(array('user_id'=>$mid['uid'],'gift_id'=>$giftid));
        if($is) {  //已经领取过 
                $map['user_id']=$mid['uid'];
                $map['gift_id']=$_POST['giftid'];
                $msg=$list->where($map)->find();
            if($msg){
                $data=$msg['novice'];
                echo  json_encode(array('status'=>'1','msg'=>'no','data'=>$data));
            }
            else{ //礼包没有了           
                $bag=M('giftbag','tab_');               
                $giftid= $_POST['giftid'];
                $ji=$bag->where(array("id"=>$giftid))->field("novice")->find();
                if(empty($ji['novice'])){
                    echo json_encode(array('status'=>'1','msg'=>'noc'));
                }
                else{//领取成功
                    $at=explode(",",$ji['novice']);
                    $gameid=$bag->where(array("id"=>$giftid))->field('game_id')->find();
                    $add['game_id']=$gameid['game_id'];
                    $add['game_name']=$_POST['gamename'];
                    $add['gift_id']=$_POST['giftid'];
                    $add['gift_name']=$_POST['giftname'];
                    $add['status']=1;
                    $add['novice']=$at[0];
                    $add['user_id'] =$mid['uid'];
                    $add['user_account'] =$mid['username'];
                    $add['user_nickname'] =$mid['nickname'];
                    $add['create_time']=strtotime(date('Y-m-d h:i:s',time()));
                    $list->add($add);
                    $new=$at;
                    if(in_array($new[0],$new)){
                        $sd=array_search($new[0],$new);
                        unset($new[$sd]);
                    }
                    $act['novice']=implode(",", $new);
                    $bag->where("id=".$giftid)->save($act);
                    echo  json_encode(array('status'=>'1','msg'=>'ok','data'=>$at[0]));
                }   
            } 
        }
    }

	/**
	* 注册
	*/
	public function register() {
		$data = array();
		$up = D("User");
		if (IS_POST) {
			if(!check_verify(I('verify'))){
				return $this->ajaxReturn(array('status'=>0,'info'=>'验证码错误'),'json');
			}
			if(!preg_match("/^[a-zA-Z]+[0-9a-zA-Z_]{5,29}$/u",$_POST['username'])) {
				$data['info']  = $this->getE(-21);
				$data['status'] =  -21;
				return $this->ajaxReturn($data,C('DEFAULT_AJAX_RETURN'));
			} else {
				$uid = $up->register($_POST['username'], $_POST['password']);
				if($uid>0) {
					$r = $up->login($uid);
					if(!$r) {
						$data['info']="注册成功";
						$data['status']=1;
						$data['url']=U('Subscriber/login');
						return $this->ajaxReturn($data,C('DEFAULT_AJAX_RETURN'));
					} else {
						$data['info']="注册成功";
						$data['status']=1;
						$data['url']="";
						return $this->ajaxReturn($data,C('DEFAULT_AJAX_RETURN'));
					}
				} else {
					$n = $up->getError();
					$data['info']  = $this->getE($n);
					$data['status'] =  0;
					return $this->ajaxReturn($data,C('DEFAULT_AJAX_RETURN'));
				}
			}			
		} else {
			//$this->redirect('Index/index');
            $user = $up->isLogin();
            if ($user) {
                $this->redirect('index');
            } else {
                $this->display();
            }
		}
	}
	
	public function telregister() {
/*       $data['info']="注册成功";
                    $data['status']=1;*/
        $data = array();
        $up = D("User");
/*       print_r($phone);
        exit;*/
        if (IS_POST) {
/*        echo json_encode(array('status'=>66,'info'=>'测试'));exit();*/
			$telsvcode = session('telsvcode');
			if (!($telsvcode['code'] == $_POST['vcode']) || !($telsvcode['phone'] == $_POST['username'])) {
				echo json_encode(array('status'=>0,'info'=>'安全码输入有误'));
                return false;
			}
            if($up->field('1')->where(array('phone'=>$_POST['username']))->find()){
                 echo json_encode(array('status'=>0,'info'=>'该手机已注册'));
                return false;
            }else{
			     $uid = $up->register($_POST['username'], $_POST['password'],1);
            }
/*            echo json_encode(array('status'=>666,'info'=>'到这了'));
            exit();*/
			if($uid>0) {
				$r = $up->login($uid);
				if(!$r) {
					$data['info']="注册成功";
					$data['status']=1;
					$data['url']=U('Subscriber/login');
					$this->ajaxReturn($data,C('DEFAULT_AJAX_RETURN'));
				} else {
					$data['info']="注册成功";
					$data['status']=1;
					$data['url']="";
					$this->ajaxReturn($data,C('DEFAULT_AJAX_RETURN'));
				}
			} else {
                echo json_encode(array('status'=>0,'info'=>'注册失败,请核对信息'));
                return;
				/*$n = $up->getError();
				$data['info']  = $this->getE($n);
				$data['status'] =  0;
				$this->ajaxReturn($data,C('DEFAULT_AJAX_RETURN'));*/
			}						
		} else {
             echo json_encode(array('status'=>0,'info'=>'链接有误'));
			/*$this->redirect('Index/index');*/
		
		}		
	}  
	
	protected function getE($num="") {
		switch($num) {
			case -1:  $error = '用户名长度必须在6-30个字符以内！'; break;
			case -2:  $error = '用户名被禁止注册！'; break;
			case -3:  $error = '用户名被占用！'; break;
			case -4:  $error = '密码长度不合法'; break;
			case -5:  $error = '邮箱格式不正确！'; break;
			case -6:  $error = '邮箱长度必须在1-32个字符之间！'; break;
			case -7:  $error = '邮箱被禁止注册！'; break;
			case -8:  $error = '邮箱被占用！'; break;
			case -9:  $error = '手机格式不正确！'; break;
			case -10: $error = '手机被禁止注册！'; break;
			case -11: $error = '手机号被占用！'; break;
			case -20: $error = '请填写正确的姓名';break;
			case -21: $error = '用户名必须由字母、数字或下划线组成,以字母开头';break;
			case -22: $error = '用户名必须由6~30位数字、字母或下划线组成';break;
			case -31: $error = '密码错误';break;
			case -32: $error = '用户不存在或被禁用';break;
			case -41: $error = '身份证无效';break;
			default:  $error = '未知错误';
		}
		return $error;
	}
	
	/**
	* 登陆
	*/
	public function login($username = '', $password = '') {
		$up = D('User');
		if(IS_POST){
            $uid = $up->checkPwd($username, $password);
            if(0 < $uid){ 
                if($up->login($uid)){ //登录用户
					$data['info']="登陆成功";
					$data['status']=1;					
                    $this->ajaxReturn($data,C('DEFAULT_AJAX_RETURN'));
                } else {
					$data['info']=$up->getError();
					$data['status']=0;
                    $this->ajaxReturn($data,C('DEFAULT_AJAX_RETURN'));
                }

            } else { //登录失败
                $data['info']=$this->getE($uid);
				$data['status']=0;
				$this->ajaxReturn($data,C('DEFAULT_AJAX_RETURN'));
            }
        } else {
			//$this->redirect('Index/index');
            $user = $up->isLogin();
            if ($user) {
                $this->redirect('index');
            } else
                $this->display();
		}			
	}
	
	/**
	* 退出
	*/
	public function logout() {
		if(is_login()){
			D('User')->logout();
            session('[destroy]');
			echo json_encode(array('reurl'=>''));
        }
	}
	
	/**
	* 验证用户名
	*/
	public function checkUser() {
		if (IS_POST) {
			$username = $_POST['username'];
			$len = strlen($username);
			if ($len !== mb_strlen($username)) {
				return $this->ajaxReturn(array('status'=>0,'info'=>$this->getE(-22)),C('DEFAULT_AJAX_RETURN'));
			}
			if ($len<6 || $len >30) {
				return $this->ajaxReturn(array('status'=>0,'info'=>$this->getE(-22)),C('DEFAULT_AJAX_RETURN'));
			}
			if(!preg_match("/^[a-zA-Z]+[0-9a-zA-Z_]{5,29}$/u",$_POST['username'])) {
				return $this->ajaxReturn(array('status'=>-21,'info'=>$this->getE(-21)),C('DEFAULT_AJAX_RETURN'));
			}
			$user = D('User')->checkUsername($username);
			if (empty($user)) {
				return $this->ajaxReturn(array('status'=>1),C('DEFAULT_AJAX_RETURN'));
			} else {
				return $this->ajaxReturn(array('status'=>0,'info'=>$this->getE(-3)),C('DEFAULT_AJAX_RETURN'));
			}
		}
	}
	
	/**
	* 检测
	*/
	public function isLogin() {
		$users = D("User")->isLogin();
		if($users) {
			$users['status'] = 1;
		} else {
			$users['status'] = 0;
		}
		$this->ajaxReturn($users,C('DEFAULT_AJAX_RETURN'));
	}
    
    // 验证码检测
    public function checkverify($verify) {
        if(check_verify($verify)){
            echo json_encode(array('status'=>1));
        } else {
            echo json_encode(array('status'=>0));           
        }
    }
	
    public function verify($vid=1) {
        $config = array(
            'seKey'     => 'ThinkPHP.CN',
            'fontSize'  => 16,
            'imageH'    => 42,
            'imageW'    => 107,
            'length'    => 4,
            'fontttf'   => '4.ttf',
        );
        $verify = new \Think\Verify($config);
        ob_end_clean();  
        $verify->entry($vid);
    }

}