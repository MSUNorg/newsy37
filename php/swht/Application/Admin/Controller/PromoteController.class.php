<?php
namespace Admin\Controller;
use User\Api\PromoteApi;
use User\Api\UserApi;
/**
 * 后台首页控制器
 * @author zxc
 */
class PromoteController extends ThinkController {

    const model_name = 'Promote';

    public function lists(){
        if(isset($_REQUEST['account'])){
            $map['account']=array('like','%'.$_REQUEST['account'].'%');
            unset($_REQUEST['account']);
        }
        $map['admin_id']=UID;
    	parent::lists(self::model_name,$_GET["p"],$map);
    }

    public function add($account=null,$password=null,$second_pwd=null,$real_name=null,$email=null,$mobile_phone=null,$bank_name=null,$bank_card=null,$admin_id=null,$status=null){
        if(IS_POST){
            $data=array('account'=>$account,'password'=>$password,'second_pwd'=>$second_pwd,'real_name'=>$real_name,'email'=>$email,'mobile_phone'=>$mobile_phone,'bank_name'=>$bank_name,'bank_card'=>$bank_card,'admin_id'=>$admin_id,'status'=>$status);
            $user = new PromoteApi();
            $res = $user->promote_add($data);
            if($res>0){
                $this->success("添加成功",U('lists'));
            }
            else{
                $this->error($res,U('lists'));
            }
        }
        else{
            $this->assign("UID",UID);
            $this->display();
        }
    }
    public function del($model = null, $ids=null){
        $model = M('Model')->getByName(self::model_name); 
        /*通过Model名称获取Model完整信息*/
        parent::del($model["id"],$ids);
    }
    //代充删除
    public function agent_del($model = null, $ids=null){
        $model = M('Model')->getByName('Agent'); 
        /*通过Model名称获取Model完整信息*/
        parent::del($model["id"],$ids);
    }
    public function edit($id=0){
		$id || $this->error('请选择要查看的用户！');
        $model = M('Model')->getByName(self::model_name); /*通过Model名称获取Model完整信息*/
        $data = array();
        if(IS_POST){
            $data = array(
                "id"         => $_POST['id'],
                "password"   => $_POST['password'],
                 "second_pwd"   => $_POST['second_pwd'],
                "status"     => $_POST['status'],
                "admin_id" => $_POST['admin']
            );
            $pwd = trim($_POST['password']);
             $second_pwd = trim($_POST['second_pwd']);
            $use=new UserApi();
            $data['password']=think_ucenter_md5($pwd,UC_AUTH_KEY);
            $data['second_pwd']=think_ucenter_md5($second_pwd,UC_AUTH_KEY);
            if(empty($pwd)){unset($data['password']);}
            if(empty($second_pwd)){unset($data['second_pwd']);}
            $res=M("promote","tab_")->where(array("id"=>$_POST['id']))->save($data);
            if($res !== false){
                $this->success('修改成功',U('lists'));
            }
            else{
                $this->error('修改失败');
            }
        }
        else{
            $model = D('Promote');
            $data = $model->find($id);
            $this->assign('data',$data);
            $this->display();
        }
    }

    /**
    *渠道注册列表
    */
    public function ch_reg_list(){
        if(isset($_REQUEST['game_name'])){
            if($_REQUEST['game_name']=='全部'){
                unset($_REQUEST['game_name']);
            }else{
                $map['game_name']=$_REQUEST['game_name'];
                unset($_REQUEST['game_name']);
            }
        }
        if(isset($_REQUEST['promote_name'])){
            if($_REQUEST['promote_name']=='全部'){
                unset($_REQUEST['promote_name']);
            }else if($_REQUEST['promote_name']=='自然注册'){
                $map['tab_user.promote_id']=array("elt",0);
                unset($_REQUEST['promote_name']);
            }else{
                $map['tab_user.promote_id']=get_promote_id($_REQUEST['promote_name']);
                unset($_REQUEST['promote_name']);
            }
        }else{
                $map['tab_user.promote_id']=array("in",get_pid());                
        }
        if(isset($_REQUEST['account'])){
            $map['tab_user.account']=array('like','%'.$_REQUEST['account'].'%');
            unset($_REQUEST['account']);
        }
        if(isset($_REQUEST['time-start'])&&isset($_REQUEST['time-end'])){
            $map['register_time']=array('BETWEEN',array(strtotime($_REQUEST['time-start']),strtotime($_REQUEST['time-end'])+24*60*60-1));
            unset($_REQUEST['time-start']);unset($_REQUEST['time_end']);
        }
        if(isset($_REQUEST['start'])&&isset($_REQUEST['end'])){
            $map['register_time']=array('BETWEEN',array(strtotime($_REQUEST['start']),strtotime($_REQUEST['end'])+24*60*60-1));
            unset($_REQUEST['start']);unset($_REQUEST['end']);
        }
        $model = array(
            'm_name' => 'User',
            'fields' => array('tab_user.account','tab_game.game_name','nickname','email','phone','promote_id'),
            'key'    => array('tab_user.account','tab_game.game_name'),
            'map'    => $map,
            'order'  => 'register_time desc',
            'title'  => '渠道注册',
            'template_list' =>'ch_reg_list',
        );
        $user = A('User','Event');
        $user->user_join($model,$_GET['p']);
    }

    /**
    *渠道充值
    */
    public function spend_list(){
        if(isset($_REQUEST['game_name'])){
            if($_REQUEST['game_name']=='全部'){
                unset($_REQUEST['game_name']);
            }else{
                $map['game_name']=$_REQUEST['game_name'];
                unset($_REQUEST['game_name']);
            }
        }
        if(isset($_REQUEST['promote_name'])){
            if($_REQUEST['promote_name']=='全部'){
                unset($_REQUEST['promote_name']);
            }else if($_REQUEST['promote_name']=='自然注册'){
                $map['promote_id']=array("elt",0);
                
                unset($_REQUEST['promote_name']);
            }else{
                $map['promote_id']=get_promote_id($_REQUEST['promote_name']);
                unset($_REQUEST['promote_name']);
            }
        }else{
            $map['promote_id']=array("in",get_pid());            
        }
        
        if(isset($_REQUEST['pay_way'])){
            $map['pay_way']=$_REQUEST['pay_way'];
            unset($_REQUEST['pay_way']);
        }
        
        if(isset($_REQUEST['user_account'])){
            $map['user_account']=array('like','%'.$_REQUEST['user_account'].'%');
            unset($_REQUEST['user_account']);
        }
        if(isset($_REQUEST['promote_name'])){
            $map['promote_account']=$_REQUEST['promote_name'];
            unset($_REQUEST['user_account']);
        }
        if(isset($_REQUEST['time-start'])&&isset($_REQUEST['time-end'])){
            $map['pay_time']=array('BETWEEN',array(strtotime($_REQUEST['time-start']),strtotime($_REQUEST['time-end'])+24*60*60-1));
            unset($_REQUEST['time-start']);unset($_REQUEST['time_end']);
        }
        if(isset($_REQUEST['start'])&&isset($_REQUEST['end'])){
            $map['pay_time']=array('BETWEEN',array(strtotime($_REQUEST['start']),strtotime($_REQUEST['end'])+24*60*60-1));
            unset($_REQUEST['start']);unset($_REQUEST['end']);
        }
        $model = array(
            'm_name' => 'Spend',
            'map'    => $map,
            'order'  => 'id desc',
            'title'  => '渠道充值',
            'template_list' =>'spend_list',
        );

        $map1=$map;
        $map1['pay_status']=1;
        $total=M('Spend','tab_')->where($map1)->sum('pay_amount');
        $total=sprintf("%.2f",$total);
        $this->assign('total',$total);
        $user = A('Spend','Event');
        $user->spend_list($model,$_GET['p']);
    }

    /**
    *代充记录
    */
    public function agent_list(){
        if(isset($_REQUEST['user_account'])){
            $map['user_account']=array('like','%'.$_REQUEST['user_account'].'%');
            unset($_REQUEST['user_account']);
        }
        if(isset($_REQUEST['pay_status'])){
            $map['pay_status']=$_REQUEST['pay_status'];
            unset($_REQUEST['pay_status']);
        }
        if(isset($_REQUEST['promote_name'])){
            if($_REQUEST['promote_name']=='全部'){
                unset($_REQUEST['promote_name']);
            }else if($_REQUEST['promote_name']=='自然注册'){
                $map['promote_id']=array("elt",0);
                unset($_REQUEST['promote_name']);
            }else{
                $map['promote_id']=get_promote_id($_REQUEST['promote_name']);
                unset($_REQUEST['promote_name']);
            }
        }else{
            $map['promote_id']=array("in",get_pid());            
        }
        if(isset($_REQUEST['time-start'])&&isset($_REQUEST['time-end'])){
            $map['create_time']=array('BETWEEN',array(strtotime($_REQUEST['time-start']),strtotime($_REQUEST['time-end'])+24*60*60-1));
            unset($_REQUEST['time-start']);unset($_REQUEST['time_end']);
        }
        if(isset($_REQUEST['start'])&&isset($_REQUEST['end'])){
            $map['create_time']=array('BETWEEN',array(strtotime($_REQUEST['start']),strtotime($_REQUEST['end'])+24*60*60-1));
            unset($_REQUEST['start']);unset($_REQUEST['end']);
        }
        if(isset($_REQUEST['game_name'])){
            if($_REQUEST['game_name']=='全部'){
                unset($_REQUEST['game_name']);
            }else{
                $map['game_name']=$_REQUEST['game_name'];
                unset($_REQUEST['game_name']);
            }
        }
        $map1=$map;
        $map1['pay_status']=1;
        $total=M('Agent','tab_')->where($map1)->sum('amount');
        $total=sprintf("%.2f",$total);
        $this->assign('total',$total);
        parent::lists('Agent',$_GET["p"],$map);
    }
    
}
