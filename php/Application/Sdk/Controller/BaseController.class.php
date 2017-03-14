<?php
namespace Sdk\Controller;
use Think\Controller\RestController;
class BaseController extends RestController{
    protected $Key = "mengchuang";
    protected function _initialize(){
        $data = json_decode(base64_decode(file_get_contents("php://input")),true);
        #判断数据是否为空
        if(empty($data)){echo json_encode(array("status"=>0,"return_msg"=>"操作数据不能为空"));exit();}
        $md5Sign = $data['md5_sign'];
        unset($data['md5_sign']);
        $md5_sign = $this->encrypt_md5($data,"mengchuang");//mengchuang DZQkkiz!@#9527
        if($md5Sign !== $md5_sign){

            $this->set_message(0,"fail","验签失败");
        }
    }

    /**
    *设置接口提示信息
    *@param  int     $status 提示状态 
    *@param  string  $return_code 提示代码
    *@param  string  $return_msg  提示信息
    *@return string  base64加密后的json格式字符串
    *@author 小纯洁
    */
    public function set_message($status=0,$return_code="fail",$return_msg="操作失败"){
        $msg = array(
            "status"      => $status,
            "return_code" => $return_code,
            "return_msg"  => $return_msg
        );
        echo base64_encode(json_encode($msg));
        exit();
    }

    /**
    *验证签名
    */
    public function validation_sign($encrypt="",$md5_sign=""){
        $signString = $this->arrSort($encrypt);
        $md5Str = $this->encrypt_md5($signString,$key="");
        if($md5Str === $md5_sign){
            return true;
        }
        else{
            return false;
        }
    }

    /**
    *对数据进行排序
    */
    private function arrSort($para){
        ksort($para);
        reset($para);
        return $para;
    }

    /**
    *MD5验签加密
    */
    public function encrypt_md5($param="",$key=""){
        #对数组进行排序拼接
        if(is_array($param)){
            $md5Str = implode($this->arrSort($param));
        }
        else{
            $md5Str = $param;
        }
        $md5 = md5($md5Str . $key);
        return '' === $param ? 'false' : $md5;
    }


    

    /**
    *短信验证
    */
    public function sms_verify($phone="" ,$code=""){
        $session = session($phone);
        if(empty($session)){
            $this->set_message(0,"fail","数据获取失败！");
        }
        #验证码是否超时
        $time = NOW_TIME - session($phone.".create_time");
        if($tiem > 60){//$tiem > 60
            $this->set_message(0,"fail","验证超时！请重新获取");
        }
        #验证短信验证码
        if(session($phone.".code") != $code){
            $this->set_message(0,"fail","输入验证码不正确");
        }
        return true;
    }

    

    /**
    *消费记录表 参数
    */
    private function spend_param($param=array()){
        $user_entity = get_user_entity($param['user_id']);
        $data_spned['user_id']          = $param["user_id"];
        $data_spned['user_account']     = $user_entity["account"];
        $data_spned['user_nickname']    = $user_entity["nickname"];
        $data_spned['game_id']          = $param["game_id"];
        $data_spned['game_appid']       = $param["game_appid"];
        $data_spned['game_name']        = $param["game_name"];
        $data_spned['server_id']        = 0;
        $data_spned['server_name']      = "";
        $data_spned['promote_id']       = $user_entity["promote_id"];
        $data_spned['promote_account']  = $user_entity["promote_account"];
        $data_spned['order_number']     = $param["order_number"];
        $data_spned['pay_order_number'] = $param["pay_order_number"];
        $data_spned['props_name']       = $param["title"];
        $data_spned['pay_amount']       = $param["price"];
        $data_spned['pay_time']         = NOW_TIME;
        $data_spned['pay_status']       = $param["pay_status"];
        $data_spned['pay_game_status']  = 0;
        $data_spned['pay_way']          = $param["pay_way"];
        $data_spned['spend_ip']         = $param["spend_ip"];
        return $data_spned;
    }

    /**
    *平台币充值记录表 参数
    */
    private function deposit_param($param=array()){
        $user_entity = get_user_entity($param['user_id']);
        $data_deposit['order_number']     = $param["order_number"];
        $data_deposit['pay_order_number'] = $param["pay_order_number"];
        $data_deposit['user_id']          = $param["user_id"];
        $data_deposit['user_account']     = $user_entity["account"];
        $data_deposit['user_nickname']    = $user_entity["nickname"];
        $data_deposit['promote_id']       = $user_entity["promote_id"];
        $data_deposit['promote_account']  = $user_entity["promote_account"];
        $data_deposit['pay_amount']       = $param["price"];
        $data_deposit['reality_amount']   = $param["price"];
        $data_deposit['pay_status']       = $param["pay_status"];
        $data_deposit['pay_source']       = 2;
        $data_deposit['pay_way']          = $param["pay_way"];
        $data_deposit['pay_ip']           = $param["spend_ip"];
        $data_deposit['create_time']      = NOW_TIME;
        return $data_deposit;
    }

    /**
    *绑定平台币消费
    */
    private function bind_spend_param($param = array()){
        $user_entity = get_user_entity($param['user_id']);
        $data_bind_spned['user_id']          = $param["user_id"];
        $data_bind_spned['user_account']     = $user_entity["account"];
        $data_bind_spned['user_nickname']    = $user_entity["nickname"];
        $data_bind_spned['game_id']          = $param["game_id"];
        $data_bind_spned['game_appid']       = $param["game_appid"];
        $data_bind_spned['game_name']        = $param["game_name"];
        $data_bind_spned['server_id']        = 0;
        $data_bind_spned['server_name']      = "";
        $data_bind_spned['promote_id']       = $user_entity["promote_id"];
        $data_bind_spned['promote_account']  = $user_entity["promote_account"];
        $data_bind_spned['order_number']     = $param["order_number"];
        $data_bind_spned['pay_order_number'] = $param["pay_order_number"];
        $data_bind_spned['props_name']       = $param["title"];
        $data_bind_spned['pay_amount']       = $param["price"];
        $data_bind_spned['pay_time']         = NOW_TIME;
        $data_bind_spned['pay_status']       = $param["pay_status"];
        $data_bind_spned['pay_game_status']  = 0;
        $data_bind_spned['pay_way']          = 1;
        $data_bind_spned['spend_ip']         = $param["spend_ip"];
        return $data_bind_spned;
    }

    /**
    *消费表添加数据
    */
    public function add_spend($data){
        $spend = M("spend","tab_");
        $spend_data =  $this->spend_param($data);
        $ordercheck = $spend->where(array('pay_order_number'=>$spend_data["pay_order_number"]))->find();
        if($ordercheck)
        {
            $this->set_message(0,'fail',"订单已经存在，请刷新充值页面重新下单！");
        }
        $result = $spend->add($spend_data);
        return $result;
    }

    /*
    *平台币充值记录
    */
    public function add_deposit($data){
        $deposit = M("deposit","tab_");
        $deposit_data  = $this->deposit_param($data);
        $ordercheck = $deposit->where(array('pay_order_number'=>$deposit_data["pay_order_number"]))->find();
        if($ordercheck)$this->set_message(0,'fail',"订单已经存在，请刷新充值页面重新下单！");
        $result = $deposit->add($deposit_data);
        return $result;
    }


    
    /*
    *绑定平台币消费记录
    */
    public function add_bind_spned($data){
        $bind_spned = M("BindSpend","tab_");
        $data_bind_spned  = $this->bind_spend_param($data);
        $ordercheck = $bind_spned->where(array('pay_order_number'=>$data_bind_spned["pay_order_number"]))->find();
        if($ordercheck)$this->set_message(0,'fail',"订单已经存在，请刷新充值页面重新下单！");
        $result = $bind_spned->add($data_bind_spned);
        return $result;
    }

    /**
    *设置数据里游戏的图片
    */
    public function set_game_icon($game_id=0){
        $game = M("Game","tab_")->find($game_id);
        $icon_url ="http://".$_SERVER['HTTP_HOST'].get_cover($game['icon'],"path");
        return $icon_url;
    }
}
