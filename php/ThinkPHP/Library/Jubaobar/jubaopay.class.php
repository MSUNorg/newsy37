<?php
namespace Jubaobar;
use Jubaobar\RSA;
//include 'jubaopay/RSA.php';

class jubaopay
{
	private $_rsa;
	
	private $message;
	private $signature;
	private $digest;
	private $encrypts;
	
	public function __construct($conf)
	{	
		$this->_rsa = new RSA($conf);	
		$this->message="";
		$this->signature="";
		$this->digest="";
		$this->encrypts=array();		
	}
	
	public function __destruct()
	{
		
	}
	
	public function __set($name,$value)
	{
		$this->$name = $value ;
	}
	
	public function __get($name)
	{
		return $this->$name;
	}
	
	public function generateRandomString( $length = 16 ) {
		
		$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()-_ []{}<>~`+=,.;:/?|';
		$password = "";
		for ( $i = 0; $i < $length; $i++ )
			$password .= $chars[ mt_rand(0, strlen($chars) - 1) ];
	
		return $password;
	}
	
	public function setEncrypt($key,$value){		
		$this->encrypts[$key]=$value;
	}
	
	public function getEncrypt($key){
		return $this->encrypts[$key];
	}	
	
	public function interpret()
	{	
		$this->digest="";
		$plainString="";
		
		$count=0;
		foreach( $this->encrypts as $key => $value ) {
			$count=$count+1;
			$this->digest.=$value;
			$plainString.=urlencode($key)."&".urlencode($value);
			if ($count < count($this->encrypts))
				$plainString.="&";
		}
		
		$key = $this->generateRandomString();
		$iv = $this->generateRandomString();
		
		$encrypted = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $plainString, MCRYPT_MODE_CBC, $iv));
		
		$this->message=$this->_rsa->pubEncrypt($key).$this->_rsa->pubEncrypt($iv).$encrypted;
		$this->signature=$this->_rsa->sign($this->digest);

	}
	
	public function decrypt($message)
	{
		$this->message=$message;
		$key=$this->_rsa->privDecrypt(substr($message,0,172));
		$iv=$this->_rsa->privDecrypt(substr($message,172,172));	
		$decrypted = base64_decode(substr($message,172+172));		
		$plainString=rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $decrypted, MCRYPT_MODE_CBC, $iv),"\0");
		
		$this->digest="";		
		$this->encrypts=array();
		$items=explode('&', $plainString);
		
		for ($i=0; $i<count($items)/2; $i++){
			$field = urldecode($items[2*$i]);
			$value = urldecode($items[2*$i+1]);
			$this->encrypts[$field] = $value;
			$this->digest.=$value;			
		}		
	}
	
	public function verify($signature)
	{		
		$this->signature=$signature;
		return $this->_rsa->verify($this->digest,$this->signature);				
	}
	
	public function encryptComfirm()
	{		
		$this->digest="";
		$plainString="";
		
		$count=0;
		foreach( $this->encrypts as $key => $value ) {
			$count=$count+1;
			$this->digest.=$value;
			$plainString.=urlencode($key)."&".urlencode($value);
			if ($count < count($this->encrypts))
				$plainString.="&";
		}
		
		$key = $this->generateRandomString();
		$iv = $this->generateRandomString();
		
		$encrypted = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $plainString, MCRYPT_MODE_CBC, $iv));
		
		$this->message=$this->_rsa->pubEncrypt($key).$this->_rsa->pubEncrypt($iv).$encrypted;
			
	}
	
	public function signComfirm()
	{
		$this->signature=$this->_rsa->sign($this->digest);
		return $this->signature;
	}
}
