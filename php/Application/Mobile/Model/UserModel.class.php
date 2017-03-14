<?php
namespace Mobile\Model;
use Think\Model;

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
		array('mobile', '/^13[\d]{9}$|^14[0-9][\d]{8}|^15[0-9][\d]{8}$|^18[0-9][\d]{8}$/', -9, self::MUST_VALIDATE,'regex',2), //手机格式不正确 TODO:
/*		array('mobile', 'checkDenyMobile', -10, self::EXISTS_VALIDATE, 'callback'), //手机禁止注册
		array('mobile', '', -11, self::EXISTS_VALIDATE, 'unique'), //手机号被占用 */
		
		// 验证
		//array('realname','/^[\u4e00-\u9fa5]{2,20}$/',-20,self::MUST_VALIDATE,'regex',self::MODEL_INSERT),
			
	);

	/* 用户模型自动完成 */
	protected $_auto = array(
		array('password', 'think_ucenter_md5', self::MODEL_BOTH, 'function', UC_AUTH_KEY),
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
	
	
	/**
	*  用户注册
	*/
	public function register($username, $password, $email, $realname, $idcard){
		$data = array(
			'account' => $username,
			'password' => $password,
			'flatcoin' => 0,
			'viplevel' => 0,
			'addup'	=> 0,
			'lock' => 1,
			'createdate' => date("Y-m-d H:i:s",time()),
			'email' => $email,
			'realname' => $realname,
			'idcard' => $idcard,
			'registerip' => $this->getIPaddress(),
		);
		// 添加用户
		if($this->create($data)){
			$uid = $this->add();
			return $uid ? $uid : 0; //0-未知错误，大于0-注册成功
		} else {
			return $this->getError();
		}
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
        //if(!$user || 1 != $user['status']) {
		if (!$user && 1 != $user['lock']) {
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
            'playerid'        => $uid,
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
		$data['playerid'] = $uid;
		if('email'==$type) {
			if (preg_match("/^([0-9A-Za-z\\-_\\.]+)@([0-9a-z]+\\.[a-z]{2,3}(\\.[a-z]{2})?)$/i",$info)) {
				$data['email']=$info;
				$type = 1;
			} else
				$type = -5;
		}
		if ('phone'==$type) {
			if (preg_match("/^13[0-9]{1}[0-9]{8}$|15[0189]{1}[0-9]{8}$|189[0-9]{8}$/",$info)) {
				$data['phone']=$info;	
				$type = 1;
			}	else 
				$type = -9;
		}
		if ($type>0) {
			$this->save($data);
			return true;
		} else {
			return $type;
		}		
	}
	
	
	/**
	*	密码
	*/
	public function checkPwd($username,$password) {
		$account['account']=$username;
		$user = $this->where($account)->find();
		if(is_array($user)){
			if(think_ucenter_md5($password, UC_AUTH_KEY) === $user['password']){
				return $user['playerid']; //登录成功，返回用户ID
			} else {
				return -2; //密码错误
			}
		} else {
			return -1; //用户不存在或被禁用
		}
	}

	 /**
     * 自动登录用户
     */
    private function autoLogin($user){
		// 更新登陆信息
		$data = array(
            'playerid'        => $user['playerid'],
            'addup'           => array('exp', '`addup`+1'),
            'lastlogintime' => date("Y-m-d H:i:s",time()),
            'lastloginip'   => $this->getIPaddress(),
        );
        $this->save($data);
		
		// 设置session
        $auth = array(
            'uid'             => $user['playerid'],
            'username'        => $user['account'],
			'flatcoin'		  => empty($user['flatcoin'])?0:$user['flatcoin'],
			'status'		  => $user['lock'],
			'logintime'		  => $user['lastlogintime'],
        );
        session('user_auth', $auth);
        session('user_auth_sign', data_auth_sign($auth));
		cookie('user_auth',$auth,3600);
    }
	

}
