<?php
namespace App\Controller;
use Think\Controller;
use User\Api\MemberApi;
use Org\XiguSDK\Xigu;
class UserController extends BaseController{

    /**
    *APP用户登陆
    */
    public function user_login(){
        #获取SDK上POST方式传过来的数据 然后base64解密 然后将json字符串转化成数组
        $user = json_decode(base64_decode(file_get_contents("php://input")),true);
        #判断数据是否为空
        if(empty($user)){$this->set_message(0,"fail","登陆数据不能为空");}
        #实例化用户接口
        $userApi = new MemberApi();
        $result = $userApi->login($user["account"],$user['password']);#调用登陆
        $res_msg = array();
        switch ($result) {
            case -1:
                $this->set_message(-1,"fail","用户不存在或被禁用");
                break;
            case -2:
                $this->set_message(-2,"fail","密码错误");
                break;
            default:
                if($result > 0){
                    $user["user_id"] = $result;
                    $data['id']=$user["user_id"]; 
                    $User=M('User',"tab_");
                    $list=$User->where($data)->find();
                    $res_msg = array("status"=>1,"msg"=>"登陆成功","list"=>$list);
                }
                else{
                    $this->set_message(0,"fail","未知错误");
                }
                break;
        }
        echo base64_encode(json_encode($res_msg));
    }

    public function user_register(){
        #获取SDK上POST方式传过来的数据 然后base64解密 然后将json字符串转化成数组
        $user = json_decode(base64_decode(file_get_contents("php://input")),true);
        #判断数据是否为空
        if(empty($user)){$this->set_message(0,"fail","注册数据不能为空");}
        #实例化用户接口
        $userApi = new MemberApi();
        $result = $userApi->register($user['account'],$user['password'],1,$user['promote_id'],$user['promote_account']);
        $res_msg = array();
        if($result > 0){
            $this->set_message(1,"success","注册成功");
        }
        else{
            switch ($result) {
                case -3:
                    $this->set_message(-3,"fail","用户名已存在");
                    break;
                default:
                    $this->set_message(0,"fail","注册失败");
                    break;
            }
            
        }
    }

    /**
    *手机用户注册
    */
    public function user_phone_register(){
        #获App上POST方式传过来的数据 然后base64解密 然后将json字符串转化成数组
        $user = json_decode(base64_decode(file_get_contents("php://input")),true);
        #判断数据是否为空
        if(empty($user)){$this->set_message(0,"fail","注册数据不能为空");}
        #验证短信验证码
        $this->sms_verify($user['account'],$user['vcode']);
        #实例化用户接口
        $userApi = new MemberApi();
        $result = $userApi->app_register($user['account'],$user['password'],2,$user['nickname'],$user['sex']);
        $res_msg = array();
        if($result > 0){
            session($user['account'],null);
            $where['id']=$result;
            $model = M("user","tab_");
            $data = $model
                  ->where($where)
                  ->find();
            /*$this->set_message(1,"success","注册成功");*/
            echo base64_encode(json_encode(array("status"=>1,"return_code" =>"success","msg"=>"注册成功","data"=>$data)));
        }
        else{
            switch ($result) {
                case -3:
                    $this->set_message(-3,"fail","用户名已存在");
                    break;
                default:
                    $this->set_message(0,"fail","注册失败");
                    break;
            }
            
        }
    }

    /**
    *修改用户数据
    */
    public function user_update_data(){
        $user = json_decode(base64_decode(file_get_contents("php://input")),true);
        #判断数据是否为空
        if(empty($user)){$this->set_message(0,"fail","操作数据不能为空");}
        #实例化用户接口
        $data['id'] = $user['user_id'];
        $userApi = new MemberApi();
        switch ($user['code']) {
            case 'phone':
                #验证短信验证码
                $this->sms_verify($user['phone'],$user['code']);
                $data['phone'] = $user['phone'];
                break;
            case 'nickname':
                $data['nickname'] = $user['nickname'];
                break;
            case 'pwd':
                $data['password'] = $user['password'];
                $data['old_password'] = $user['old_password'];
                if($data['password'] != $user['password_again'])
                $this->set_message(-3,"fail","两次密码不一致");
                break;
            default: 
                $this->set_message(0,"fail","修改信息不明确");
                break;
        }
        $result = $userApi->updateUser($data);
        if($result == -2){
            $this->set_message(-2,"fail","旧密码输入不正确");
        }
        else if($result == 1){
            $this->set_message(1,"success","修改成功");
        }
        else{
            $this->set_message(0,"fail","修改失败");
        }
    }

    /**
    *忘记密码接口
    */
    public function forget_password(){
        $user = json_decode(base64_decode(file_get_contents("php://input")),true);
        $userApi = new MemberApi();
        #验证短信验证码   
        $this->sms_verify($user['phone'],$user['code']);
        if($user['password'] != $user['password_again'])
        {
            $this->set_message(0,"fail","两次密码不一致");
            return false;
        }
        $where['phone']=$user['phone'];
        $data=M("user","tab_")->where($where)->find();
        $result = $userApi->updatePassword($data['id'],$user['password']);
        if($result == true){
            $this->set_message(1,"success","修改成功");
        }
        else{
            $this->set_message(0,"fail","修改失败");
        }
    }

    /**
    *添加玩家信息
    */
    private function add_user_play($user = array()){
        $user_play = M("UserPlay","tab_");
        $map["game_id"] = $user["game_id"];
        $map["user_id"] = $user["user_id"];
        $res = $user_play->where($map)->find();
        if(empty($res)){
            $user_entity = get_user_entity($user["user_id"]);
            $data["user_id"] = $user["user_id"];
            $data["user_account"] = $user_entity["account"];
            $data["user_nickname"] = $user_entity["nickname"];
            $data["game_id"] = $user["game_id"];
            $data["game_appid"] = $user["game_appid"];
            $data["game_name"] = $user["game_name"];
            $data["server_id"] = 0;
            $data["server_name"] = "";
            $data["role_id"] = 0;
            $data["role_name"] = "";
            $data["rolaae_level"] = 0;
            $data["bind_balance"] = 0;
            $data["promote_id"] = $user_entity["promote_id"];
            $data["promote_account"] = $user_entity["promote_account"];
            $user_play->create();
            $user_play->add($data);
        }
    }

    /**
    *短信发送
    * $demand 为2时，是发送修改密码时的安全码，不进行手机号检测
    */
 public function send_sms()
    {
        $data = json_decode(base64_decode(file_get_contents("php://input")),true);
        $phone = $data['phone'];
        $demand = $data['demand'];
        $map['account']=$phone;
        if($demand != 2 ){    
            $user=M("user","tab_")->where($map)->find();
            if($user!==null) {
               $this->set_message(-1,"fail","该手机号已注册");
               return false;
            }
        } 
        /// 产生手机安全码并发送到手机且存到session
        $rand = rand(100000,999999);
        $xigu = new Xigu(C('sms_set.smtp'));
        $param = $rand.",".'10';
        $result = json_decode($xigu->sendSM(C('sms_set.smtp_account'),$phone,C('sms_set.smtp_port'),$param),true); 
        $result['create_time'] = time();
        $r = M('Short_message')->add($result);
        #TODO 短信验证数据 
        if($result['send_status'] == '000000') {
            session($phone,array('code'=>$rand,'create_time'=>NOW_TIME));
            echo base64_encode(json_encode(array('status'=>1,'return_code'=>'success','msg'=>'验证码发送成功','phone'=>$phone,'code'=>$rand)));
        }
        else{
            $this->set_message(0,"fail","验证码发送失败，请重新获取");
        }
    }
    /**
    *用户基本信息
    */
    public function user_info(){
        $user = json_decode(base64_decode(file_get_contents("php://input")),true);
        $model = M("user","tab_");
        $data = array();
        switch ($user['type']) {
            case 0:
               $data = $model
                ->field("account,nickname,phone,balance,bind_balance,game_name")
                ->join("INNER JOIN tab_user_play ON tab_user.id = tab_user_play.user_id and tab_user.id = {$user['user_id']} and tab_user_play.game_id = {$user['game_id']}")
                ->find();
                break;
            default:
                $map['account'] = $user['user_id'];
                $data = $model->field("id,account,nickname,phone,balance")->where($map)->find();
                break;
        }
        
        if(empty($data)){
            $this->set_message(0,"fail","用户数据异常");
        }
        $data['phone'] = empty($data["phone"])?" ":$data["phone"];
        $data['status'] = 1;
        echo base64_encode(json_encode($data));
    }

    /**
    *用户平台币充值记录
    */
    public function user_deposit_record(){
        $data = json_decode(base64_decode(file_get_contents("php://input")),true);
        $map["user_id"] = $data["user_id"];
        $map["game_id"] = $data["game_id"];
        $deposit = M("deposit","tab_")->where($map)->select();
        if(empty($deposit)){
            echo base64_encode(json_encode(array("status"=>0,"return_code"=>"fail","msg"=>"暂无记录")));exit();
        }
        $return_data['status'] = 1;
        $return_data['data'] = $deposit;
        echo base64_encode(json_encode($return_data));
    }

    /**
    *用户领取礼包
    */
    public function user_gift_record(){
        $data = json_decode(base64_decode(file_get_contents("php://input")),true);
        $map["user_id"] = $data["user_id"];
        $map["game_id"] = $data["game_id"];
        $gift = M("GiftRecord","tab_")
        ->field("tab_gift_record.game_id,tab_gift_record.game_name,tab_giftbag.giftbag_name ,tab_giftbag.digest,tab_gift_record.novice,tab_gift_record.status,tab_giftbag.start_time,tab_giftbag.end_time")
        ->join("LEFT JOIN tab_giftbag ON tab_gift_record.gift_id = tab_giftbag.id where user_id = {$data['user_id']} and tab_gift_record.game_id = {$data['game_id']}")
        ->select();
        if(empty($gift)){
            echo base64_encode(json_encode(array("status"=>0,"return_code"=>"fail","msg"=>"暂无记录")));exit();
        }
        foreach ($gift as $key => $val) {
            $gift[$key]['icon'] = $this->set_game_icon($val[$key]['game_id']);
            $gift[$key]['now_time'] = NOW_TIME;
        }
        
        $return_data['status'] = 1;
        $return_data['data'] = $gift;
        echo base64_encode(json_encode($return_data));
    }

    /**
    *用户平台币(绑定和非绑定)
    */
    public function user_platform_coin(){
        $data = json_decode(base64_decode(file_get_contents("php://input")),true);
        $user_play = M("UserPlay","tab_");
        $platform_coin = array();
        $user_data = array();
        #非绑定平台币信息
        $user_data = get_user_entity($data["user_id"]);
        $platform_coin['status'] = 1;
        $platform_coin["balance"] = $user_data["balance"];
        #绑定平台币信息
        $map["user_id"] = $data["user_id"];
        $map["game_id"] = $data["game_id"];
        $user_data = $user_play->where($map)->find();
        $platform_coin["bind_balance"] = $user_data["bind_balance"];
        echo base64_encode(json_encode($platform_coin));
    }

    //意见反馈 Buzhaohe <61673158@qq.com>
    public function feedback(){
        $feed = json_decode(base64_decode(file_get_contents("php://input")),true);
        if(empty($feed)){
            $data = array("status"=>"-1","return_msg"=>"数据不能为空");
            echo json_encode($data);
            exit();
        }
        $message = M("message","tab_");
        $data["content"] = $feed["content"];
        $data["qq"] = $feed["qq"];
        $data["type"] = 1;
        $data["create_time"] = time();
        $result=$message->add($data);
        if($result >= 1)
            echo base64_encode(json_encode(array("status"=>1,"msg"=>"提交成功")));
        else
            echo base64_encode(json_encode(array("status"=>-1,"msg"=>"提交失败")));       
    }

   
    //判断手机有没有注册
    public function check_phone_account($phone)
    {
        $where['phone']=$phone;
        $user=M("user","tab_");
        $data=$user->where($where)->find();
        if($data !=null){
            echo json_encode(array("status"=>-1,"msg"=>"手机号已被注册"));
        }else{
            echo json_encode(array("status"=>1,"msg"=>"可以注册"));
        }
    }
}
