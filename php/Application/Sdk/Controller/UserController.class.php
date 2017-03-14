<?php
namespace Sdk\Controller;
use Think\Controller;
use User\Api\MemberApi;
use Org\XiguSDK\Xigu;
class UserController extends BaseController{

    /**
    *SDK用户登陆
    */
    public function user_login(){
        #获取SDK上POST方式传过来的数据 然后base64解密 然后将json字符串转化成数组
        $user = json_decode(base64_decode(file_get_contents("php://input")),true);
        #判断数据是否为空
        if(empty($user)){$this->set_message(0,"fail","登陆数据不能为空");}
        #实例化用户接口
        $userApi = new MemberApi();
        // $result = $userApi->login($user["account"],$user['password'],1,$user["game_id"],$user["game_name"]);#调用登陆
        $result = $userApi->login_($user["account"],$user['password'],1,$user["game_id"],$user["game_name"]);#调用登陆
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
                    $this->add_user_play($user);
                    $new_time = NOW_TIME;
                    $key  = $this->Key;
                    $sign = MD5($result.$user["account"].$key.$new_time);
                    $res_msg = array(
                        "status"=>1,
                        "return_code" => "success",
                        "return_msg"  => "登陆成功",
                        "user_id"     => $result,
                        "user_account"=> $user["account"],
                        "timeStamp"   => $new_time,
                        "sign"        => $sign
                    );
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
        // $result = $userApi->register($user['account'],$user['password'],1,$user['promote_id'],$user['promote_account']);
        // user表加game_id
        $result = $userApi->register_($user['account'],$user['password'],1,$user['promote_id'],$user['promote_account'],$phone="",$user["game_id"],$user["game_name"]);
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
        #获取SDK上POST方式传过来的数据 然后base64解密 然后将json字符串转化成数组
        $user = json_decode(base64_decode(file_get_contents("php://input")),true);
        #判断数据是否为空
        if(empty($user)){$this->set_message(0,"fail","注册数据不能为空");}
        #验证短信验证码
        $this->sms_verify($user['account'],$user['code']);
        #实例化用户接口
        $userApi = new MemberApi();
        // $result = $userApi->register($user['account'],$user['password'],1,$user['promote_id'],$user['promote_account'],$user['account']);
        // // user表加game_id
        $result = $userApi->register_($user['account'],$user['password'],1,$user['promote_id'],$user['promote_account'],$user['account'],$user["game_id"],$user["game_name"]);
        $res_msg = array();
        if($result > 0){
            session($user['account'],null);
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
                $this->sms_verify($user['phone'],$user['sms_code']);
                $data['phone'] = $user['phone'];
                break;
            case 'nickname':
                $data['nickname'] = $user['nickname'];
                break;
            case 'pwd':
                $data['old_password'] = $user['old_password'];
                $data['password'] = $user['password'];
                break;
            default:
                $this->set_message(0,"fail","修改信息不明确");
                break;
        }
        $result = $userApi->updateUser($data);
        if($result == -2){
            $this->set_message(-2,"fail","旧密码输入不正确");
        }
        else if($result == true){
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
        $result = $userApi->updatePassword($user['user_id'],$user['password']);
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
            $data['parent_id']=$user_entity["parent_id"];
            $data['parent_name']=$user_entity["parent_name"];
            $data["role_name"] = "";
            $data["role_level"] = 0;
            $data["bind_balance"] = 0;
            $data["promote_id"] = $user_entity["promote_id"];
            $data["promote_account"] = $user_entity["promote_account"];
            $user_play->create();
            $user_play->add($data);
        }
    }
    //添加登录信息
    /**
    *短信发送
    */
    public function send_sms()
    {
        $data = json_decode(base64_decode(file_get_contents("php://input")),true);
        $phone = $data['phone'];
        /// 产生手机安全码并发送到手机且存到session
        $rand = rand(100000,999999);
        $xigu = new Xigu(C('sms_set.smtp'));
        $param = $rand.",".'1';
        $result = json_decode($xigu->sendSM(C('sms_set.smtp_account'),$phone,C('sms_set.smtp_port'),$param),true); 
        $result['create_time'] = time();
        $r = M('Short_message')->add($result);
        wite_text(json_encode(C("sms_set")),dirname(__FILE__)."/sms.txt");
        wite_text(json_encode($result),dirname(__FILE__)."/result.txt");
        #TODO 短信验证数据 
        if($result['send_status'] == '000000') {
            session($phone,array('code'=>$rand,'create_time'=>NOW_TIME));
            echo base64_encode(json_encode(array('status'=>1,'return_code'=>'success','return_msg'=>'验证码发送成功')));
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
        //$map["game_id"] = $data["game_id"];
        $deposit = M("deposit","tab_")->where($map)->select();
        if(empty($deposit)){
            echo base64_encode(json_encode(array("status"=>0,"return_code"=>"fail","return_msg"=>"暂无记录")));exit();
        }
        $return_data['status'] = 1;
        $return_data['data'] = $deposit;
        echo base64_encode(json_encode($return_data));
    }

    /**
    *用户领取礼包- 
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
            echo base64_encode(json_encode(array("status"=>0,"return_code"=>"fail","return_msg"=>"暂无记录")));exit();
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
}
