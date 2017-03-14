<?php
namespace Media\Model;
use Think\Model;
/**
* 玩家模型
* lwx
*/
class UserModel extends Model{
	
	protected $_validate = array(
		// 验证用户名
		array('account', '6,30', -1, self::EXISTS_VALIDATE, 'length'), //用户名长度不合法
		//array('account', 'checkDenyMember', -2, self::EXISTS_VALIDATE, 'callback'), //用户名禁止注册
		array('account', '', -3, self::EXISTS_VALIDATE, 'unique'), //用户名被占用

		// 验证密码
		array('password', '6,30', -4, self::EXISTS_VALIDATE, 'length'), //密码长度不合法

	// 验证邮箱
		array('email', 'email', -5, self::EXISTS_VALIDATE), //邮箱格式不正确
/* 			array('email', '1,32', -6, self::EXISTS_VALIDATE, 'length'), //邮箱长度不合法
		array('email', 'checkDenyEmail', -7, self::EXISTS_VALIDATE, 'callback'), //邮箱禁止注册
		array('email', '', -8, self::EXISTS_VALIDATE, 'unique'), //邮箱被占用 */

		//验证手机号码
		//array('phone', '/^13[\d]{9}$|^14[0-9][\d]{8}|^15[0-9][\d]{8}$|^18[0-9][\d]{8}$/', -9, self::MUST_VALIDATE,'regex',2), //手机格式不正确 TODO:
/*		array('phone', 'checkDenyMobile', -10, self::EXISTS_VALIDATE, 'callback'), //手机禁止注册
		array('phone', '', -11, self::EXISTS_VALIDATE, 'unique'), //手机号被占用 */
		
		// 验证
		//array('realname','/^[\u4e00-\u9fa5]{2,20}$/',-20,self::MUST_VALIDATE,'regex',self::MODEL_INSERT),
			
	);

	/* 用户模型自动完成 */
	protected $_auto = array(
		//array('password', 'think_ucenter_md5', self::MODEL_BOTH, 'function', UC_AUTH_KEY),
	);
	
	/**
     * 构造函数
     * @param string $name 模型名称
     * @param string $tablePrefix 表前缀
     * @param mixed $connection 数据库连接信息
     */
    public function __construct($name = '', $tablePrefix = '', $connection = '') {
        /* 设置默认的表前缀 */
        $this->tablePrefix ='tab_';
        /* 执行构造方法 */
        parent::__construct($name, $tablePrefix, $connection);
    }
	
	/**
	*	验证用户名
	*/
	public function checkUsername($username){
		$map = array();
		$map['account'] = $username;
		$user = $this->where($map)->find();
		return $user;
	}	
	
	public function updatenk($nickname) {
		$user_auth = session('user_auth');
		$auth = array(
            'uid'             => $user_auth['uid'],
            'username'        => $user_auth['username'],
			'nickname'		  => $nickname,
			'flatcoin'		  => $user_auth['flatcoin'],
			'status'		  => $user_auth['status'],
			'logintime'		  => $user_auth['logintime'],
			'loginip'		  => $user_auth['loginip']
        );
		session('user_auth', null);
        session('user_auth_sign', null);
		session('user_auth', $auth);
        session('user_auth_sign', data_auth_sign($auth));
	}
	
	// 昵称
	public function updatenikename($uid,$nickname) {
		$data = array(
            'id'        => $uid,
            'nickname'  => $nickname,
        );
		return $this->save($data);   
	}
	
	/** 更新 session 信息 */
	public function updatesession($uid) {
		$user = $this->field(true)->find($uid);
		$auth = array(
            'uid'             => $user['id'],
            'username'        => $user['account'],
			'flatcoin'		  => empty($user['balance'])?0:$user['balance'],
			'nickname'		  => $user['nickname'],
			'status'		  => $user['lock_status'],
			'logintime'		  => date('Y-m-d H:i',$user['login_time']),
			'loginip'		  => $user['login_ip'],
        );
		session('user_auth', null);
        session('user_auth_sign', null);
		session('user_auth', $auth);
        session('user_auth_sign', data_auth_sign($auth));
	}
    
    public function checkField($field, $type = 1){
		$data = array();
		switch ($type) {
			case 1:
				$data['account'] = $field;
				break;
			case 2:
				$data['email'] = $field;
				break;
			case 3:
				$data['phone'] = $field;
				break;
			default:
				return 0; //参数错误
		}

		return $this->create($data) ? 1 : $this->getError();
	}
	
	/**
	*  用户注册
	*/
	public function register($username, $password,$type=0){
		$data = array(
			'account' => $username,
			'password' => think_ucenter_md5($password, UC_AUTH_KEY),
			'balance' => 0,
			'cumulative' => 0,
			'vip_level' => 0,
			'lock_status' => 1,
			'register_way' => 0,
			'register_time' => time(),
			'register_ip' => $this->getIPaddress(),
			'anti_addiction' => 0,
		);			
		if (intval($type)==1) {
			$data['phone']=$username;
		}

        $uid = $this->add($data);
		return $uid ? $uid : 0; //0-未知错误，大于0-注册成功
	}
	
	
	
	function getIPaddress(){ 
		$ip=false; 
		if(!empty($_SERVER['HTTP_CLIENT_IP'])){ 
			$ip=$_SERVER['HTTP_CLIENT_IP']; 
		}
		if(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){ 
			$ips=explode (', ', $_SERVER['HTTP_X_FORWARDED_FOR']); 
			if($ip){ array_unshift($ips, $ip); $ip=FALSE; }
			for ($i=0; $i < count($ips); $i++){
				if(!eregi ('^(10│172.16│192.168).', $ips[$i])){
					$ip=$ips[$i];
					break;
				}
			}
		}
		if ($_SERVER['REMOTE_ADDR'] == '::1') {
			$ip = '127.0.0.1';
		}
		return ($ip ? $ip : $_SERVER['REMOTE_ADDR']); 
	}
	
	/* function getIPaddress() {
		$IPaddress='';
		if (isset($_SERVER)){
			if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])){
				$IPaddress = $_SERVER["HTTP_X_FORWARDED_FOR"];
			} else if (isset($_SERVER["HTTP_CLIENT_IP"])) {
				$IPaddress = $_SERVER["HTTP_CLIENT_IP"];
			} else {
				$IPaddress = $_SERVER["REMOTE_ADDR"];
			}
		} else {
			if (getenv("HTTP_X_FORWARDED_FOR")){
				$IPaddress = getenv("HTTP_X_FORWARDED_FOR");
			} else if (getenv("HTTP_CLIENT_IP")) {
				$IPaddress = getenv("HTTP_CLIENT_IP");
			} else {
				$IPaddress = getenv("REMOTE_ADDR");
			}
		}
		return $IPaddress;
	} */
	
	/**
	*  用户登陆
	*/
	public function login($uid){
        $user = $this->field(true)->find($uid);
		if (!$user && 1 != $user['lock_status']) {
            $this->error = '用户不存在或已被禁用！'; //应用级别禁用
            return false;
        }
        $this->autoLogin($user);
		return true;
    }
	
	/**
	* 验证邮箱
	*/
	public function checkEmail($email) {
		$user = $this->where('email="'.$email.'"')->find();
		return $user;
	}
	
	/**
	* 修改密码
	*/
	public function update($uid,$password) {
		$data = array(
            'id'        => $uid,
            'password'		  => think_ucenter_md5($password, UC_AUTH_KEY),			
        );
		return $this->save($data);   
	}
	
	/**
	* 获取用户信息
	*/
	public function getUserInfo($uid) {
		$user = $this->field(true)->find($uid);
		return $user;
	}
	
	/**
	* 退出
	*/
    public function logout(){
        session('user_auth', null);
        session('user_auth_sign', null);
    }
	
	/**
	* 检测用户是否已登陆
	*/
	public function isLogin() {
		$users = session('user_auth');
		if(is_array($users) && !empty($users['username'])) {
			return $users;
		}else {
			return false;
		}
	}
	
	public function updateInfo($uid,$info='',$type) {
		$data['id'] = $uid;
		if('email'==$type) {
			if (preg_match("/^([0-9A-Za-z\\-_\\.]+)@([0-9a-z]+\\.[a-z]{2,3}(\\.[a-z]{2})?)$/i",$info)) {
				$data['email']=$info;
				$type = 1;
			} else
				$type = -5;
		}
		if ('phone'==$type) {
			if (preg_match("/^[1][358][0-9]{9}$/",$info)) {
				$data['phone']=$info;	
				$type = 1;
			}	else 
				$type = -9;
		}
		if ($type>0) {
			$this->save($data);
			return 1;
		} else {
			return $type;
		}		
	}
	
	public function updaterealinfo($uid,$realname,$idcard) {
		$data['id'] = $uid;
		if (preg_match("/^[\x{4e00}-\x{9fa5}]{2,30}$/u",$realname)) {
			$data['real_name']=$realname;
			$type = 1;
		} else
			$type = -20;

		if ($this->checkidcard($idcard)) {
			$data['idcard']=$idcard;	
			$type = 1;
		}	else 
			$type = -41;
		
		if ($type>0) {
			$this->save($data);
			return 1;
		} else {
			return $type;
		}	
	}
	
	private function checkidcard($idcard) {
		if (empty($idcard)) {
			return false;
		}
		
		$city = array(
			11=>'北京',12=>'天津',13=>'河北',14=>'山西',15=>'内蒙古',
			21=>'辽宁',22=>'吉林',23=>'黑龙江',
			31=>'上海',32=>'江苏',33=>'浙江',34=>'安徽',35=>'福建',36=>'江西',37=>'山东',
			41=>'河南',42=>'湖北',43=>'湖南',44=>'广东',45=>'广西',46=>'海南',50=>'重庆',
			51=>'四川',52=>'贵州',53=>'云南',54=>'西藏',
			61=>'陕西',62=>'甘肃',63=>'青海',64=>'宁夏',65=>'新疆',
			71=>'台湾',81=>'香港',82=>'澳门',91=>'国外'		
		);
		$isum = 0;
		$idcardlength=strlen($idcard);
		// 长度验证
		if (!preg_match('/^\d{17}(\d|x)$/i',$idcard) && !preg_match('/^\d{15}$/i',$idcard)) {
			return false;
		}
		// 地区验证
		if (!array_key_exists(intval(substr($idcard,0,2)),$city)) {
			return false;
		}
		
		// 15位身份证验证生日，转换为18位
		if ($idcardlength == 15) {
			$sbirthday = '19'.substr($idcard,6,2).'-'.substr($idcard,8,2).'-'.substr($idcard,10,2);
			//$d = new DateTime($sbirthday);
			//$dd = $d->format('Y-m-d');
			$d = strtotime($sbirthday);
			$dd = date('Y-m-d',$d);
			if ($sbirthday != $dd) {
				return false;
			}
			$idcard = substr($idcard,0,6).'19'.substr($idcard,6,9); // 15 to 18 
			$bit18 = $this->getVerifyBit($idcard);	// 计算第18位效验码
			$idcard = $idcard.$bit18;			
		}
		// 判断是否大于2078年，小于1900年
		$year = substr($idcard,6,4);
		if ($year<1900 || $year>2078) {
			return false;
		}
		//18位身份证处理
		$sbirthday = substr($idcard,6,4).'-'.substr($idcard,10,2).'-'.substr($idcard,12,2);
		//$d = new DateTime($sbirthday);
		//$dd = $d->format('Y-m-d');
		$d = strtotime($sbirthday);
		$dd = date('Y-m-d',$d);
		if ($sbirthday != $dd) {
			return false;
		}
		// 身份证编码规范验证
		$idcard_base = substr($idcard,0,17);
		$verify_number = $this->getVerifyBit($idcard_base);
		if (strtoupper(substr($idcard,17,1)) != $verify_number) {
			return false;
		}
		return true;		
	}
	
	// 计算身份证效验码，根据国标GB 11643-1999
	private function getVerifyBit($idcard_base) {
		if (strlen($idcard_base) != 17) {
			return false;
		}
		// 加权因子
		$factor = array(7,9,10,5,8,4,2,1,6,3,7,9,10,5,8,4,2);
		// 效验码对应值
		$verify_number_list = array('1','0','X','9','8','7','6','5','4','3','2');
		$checksum = 0;
		for ($i=0;$i<strlen($idcard_base);$i++) {
			$checksum += substr($idcard_base,$i,1)*$factor[$i];
		}
		$mod = $checksum%11;
		$verify_number = $verify_number_list[$mod];
		return $verify_number;
	}
	
	/**
	*	密码
	*/
	public function checkPwd($username,$password) {
		$account['account']=$username;
		$user = $this->where($account)->find();
		if(is_array($user)){
			if(think_ucenter_md5($password, UC_AUTH_KEY) === $user['password']){
				return $user['id']; //登录成功，返回用户ID
			} else {
				return -31; //密码错误
			}
		} else {
			return -32; //用户不存在或被禁用
		}
	}

	 /**
     * 自动登录用户
     */
    private function autoLogin($user){
		// 更新登陆信息
		$data = array(
            'id'        => $user['id'],
            'login_time' => time(),
            'login_ip'   => $this->getIPaddress(),
        );
        $this->save($data);
		
		// 设置session
        $auth = array(
            'uid'             => $user['id'],
            'username'        => $user['account'],
			'flatcoin'		  => empty($user['balance'])?0:$user['balance'],
			'nickname'		  => $user['nickname'],
/* 			'viplevel'		  => $user['vip_level'], */
			'status'		  => $user['lock_status'],
			'logintime'		  => date('Y-m-d H:i',$user['login_time']),
			'loginip'		  => $user['login_ip'],
        );
        session('user_auth', $auth);
        session('user_auth_sign', data_auth_sign($auth));
		cookie('user_auth',$auth,3600);
    }

	//充值明细
	public function detailed(){
		
	} 	
    
    public function updateUserInfo($data){
        if(empty($data['password'])){unset($data['password']);}
        $return = $this->save($data);
        return $return;
    }

}
