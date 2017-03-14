<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: zxc <zuojiazi@vip.qq.com> <http://www.msun.com>
// +----------------------------------------------------------------------

namespace User\Api;
use User\Api\Api;
use Admin\Model\PromoteModel;

class PromoteApi extends Api{
    /**
     * 构造方法，实例化操作模型
     */
    protected function _init(){
        $this->model = new PromoteModel(); //M('Promote','tab_');
    }

    public function checkAccount($account){
        return $this->model->checkAccount($account);
    }
    
    // 邮箱  lwx 2016-05-13
    public function provingEmail($email) {
        return $this->model->provingEmail($email);
    }
    // 手机  lwx 2016-05-13
    public function provingMobile($phone) {
        return $this->model->provingMobile($phone);
    }

    /**
     * 注册一个新用户
     * @param  string $username 用户名
     * @param  string $password 用户密码
     * @param  string $email    用户邮箱
     * @param  string $mobile   用户手机号码
     * @return integer          注册成功-用户信息，注册失败-错误编号
     */
    // public function register($account,$password,$real_name,$email,$mobile_phone){
    //     return $this->model->register($account,$password,$real_name,$email,$mobile_phone);
    // }
    // public function promote_add($account,$password,$real_name,$email,$mobile_phone,$bank_name,$bank_card,$admin){
    //     return $this->model->promote_add($account,$password,$real_name,$email,$mobile_phone,$bank_name,$bank_card,$admin);
    // }
    // lwx 2016-06-13
    public function register($data) {
        return $this->model->register($data);
    }

    /**
    *新增子推广员
    */
    public function increase(){
        $result = $this->model->increase();
        return $result;
    }

    /**
    *编辑子推广员
    */
    public function edit($type){
        return $this->model->edit($type);
    }

    /**
     * 用户登录认证
     * @param  string  $username 用户名
     * @param  string  $password 用户密码
     * @param  integer $type     用户名类型 （1-用户名，2-邮箱，3-手机，4-UID）
     * @return integer           登录成功-用户ID，登录失败-错误编号
     */
    public function login($username, $password){
        return $this->model->login($username, $password);
    }
    
    /**
    *添加推广员
    */
    public function promote_add($data = array()){
        return $this->model->promote_add($data);
    }
    /**
     * 检测用户名
     * @param  string  $field  用户名
     * @return integer         错误编号
     */
    public function checkUsername($username){
        return $this->model->checkField($username, 1);
    }

    /**
    *编辑管理员
    */
    // public function edit($data = array()){
    //     return $this->model->edit($data);
    // }
    /**
     * 检测邮箱
     * @param  string  $email  邮箱
     * @return integer         错误编号
     */
    public function checkEmail($email){
        return $this->model->checkField($email, 2);
    }
    /**
     * 检测手机
     * @param  string  $mobile  手机
     * @return integer         错误编号
     */
    public function checkMobile($mobile){
        return $this->model->checkField($mobile, 3);
    }

    public function verifyUser($uid, $password_in){
        return $this->model->verifyUser($uid,$password_in);
    }
     public function verify_er_User($uid, $password_in){
        return $this->model->verifyUser($uid,$password_in);
    }


    /**
     * 更新用户信息
     * @param int $uid 用户id
     * @return true 修改成功，false 修改失败
     * @author huajie <banhuajie@163.com>
     */
    public function admin_updateInfo($data){
        $return = $this->model->admin_updateInfo($data);
        return $return;
    }

}
