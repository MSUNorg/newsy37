<?php
namespace Admin\Controller;
use User\Api\PromoteApi;
use User\Api\UserApi;
/**
 * 后台首页控制器
 * @author zxc <zuojiazi@vip.qq.com>
 */
class PromoteController extends ThinkController {

    const model_name = 'Promote';

    public function lists(){
        if(isset($_REQUEST['account'])){
            $map['account']=array('like','%'.$_REQUEST['account'].'%');
            unset($_REQUEST['account']);
        }
        if(isset($_REQUEST['admin'])){
            $map['admin_id']=get_admin_id($_REQUEST['admin']);
            unset($_REQUEST['admin']);
        }
        if(isset($_REQUEST['parent_id'])){
            if($_REQUEST['parent_id']>0){
                $map['parent_id']=array('gt',0);
            }else{
                $map['parent_id']=0;
            }
            unset($_REQUEST['parent_id']);
        }
    	parent::lists(self::model_name,$_GET["p"],$map);
    }

    public function add($account=null,$password=null,$second_pwd=null,$real_name=null,$email=null,$mobile_phone=null,$bank_name=null,$bank_card=null,$admin=null,$status=null){
        if(IS_POST){
            $data=array('account'=>$account,'password'=>$password,'second_pwd'=>$second_pwd,'real_name'=>$real_name,'email'=>$email,'mobile_phone'=>$mobile_phone,'bank_name'=>$bank_name,'bank_card'=>$bank_card,'admin_id'=>$admin,'status'=>$status);
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
            $this->meta_title ='新增推广员信息';
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
                "mark1"     => $_POST['mark1'],
                "mark2"     => $_POST['mark2'],
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
            $data['bank_area']=explode(',',$data['bank_area']);
            $this->assign('data',$data);
            $this->display();
        }
    }
    //设置状态
    public function set_status($model='Promote'){
        if(isset($_REQUEST['model'])){
            $model=$_REQUEST['model'];
            unset($_REQUEST['model']);
        }
        parent::set_status($model);
    }
    //设置对账状态yyh
    public function set_check_status($model='Promote'){
        if(isset($_REQUEST['model'])){
            $model=$_REQUEST['model'];
            unset($_REQUEST['model']);
        }
        parent::set_status($model);
    }
    /**
    *渠道注册列表
    */
    public function ch_reg_list(){
        if(isset($_REQUEST['game_name'])){
            if($_REQUEST['game_name']=='全部'){
                unset($_REQUEST['game_name']);
            }else{
                $map['fgame_name']=$_REQUEST['game_name'];
                unset($_REQUEST['game_name']);
            }
        }
        $map['tab_user.promote_id'] = array("neq",0);
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
        }
        if(isset($_REQUEST['is_check'])&&$_REQUEST['is_check']!="全部"){
            $map['is_check']=check_status($_REQUEST['is_check']);
            unset($_REQUEST['is_check']);
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
            'fields' => array('id','tab_user.account','tab_user.fgame_name','nickname','email','phone','promote_id','register_way','register_ip','promote_account','parent_name','is_check'),
            'key'    => array('tab_user.account','tab_game.fgame_name'),
            'map'    => $map,
            'order'  => 'id desc',
            'title'  => '渠道注册',
            'template_list' =>'ch_reg_list',
        );
        $user = A('User','Event');
        $user->user_join__($model,$_GET['p']);
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
                $map['promote_id']=array("lte",0);
                
                unset($_REQUEST['promote_name']);
            }else{
                $map['promote_id']=get_promote_id($_REQUEST['promote_name']);
                unset($_REQUEST['promote_name']);
            }
        }else{
            $map['promote_id']=array("gt",0);
        }
        
        if(isset($_REQUEST['pay_way'])){
            $map['pay_way']=$_REQUEST['pay_way'];
            unset($_REQUEST['pay_way']);
        }
        if(isset($_REQUEST['is_check'])&&$_REQUEST['is_check']!="全部"){
            $map['is_check']=check_status($_REQUEST['is_check']);
            unset($_REQUEST['is_check']);
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
        $map['promote_id'] = array("neq",0);
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
                unset($_REQUEST['promote_id']);
            }
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
    /**
    *代充额度
    */
    public function pay_limit(){
        if(isset($_REQUEST['account'])){
            $map['account']=array('like','%'.$_REQUEST['account'].'%');
            unset($_REQUEST['account']);
        }
        if(isset($_REQUEST['promote_name'])){
            if($_REQUEST['promote_name']=='全部'){
                unset($_REQUEST['promote_name']);
            }else if($_REQUEST['promote_name']=='自然注册'){
                $map['id']=array("elt",0);
                unset($_REQUEST['promote_name']);
            }else{
                $map['id']=get_promote_id($_REQUEST['promote_name']);
                unset($_REQUEST['promote_name']);
            }
        }
        $row=10;
        $map['pay_limit']=array('gt','0');
        $page = intval($_GET['p']);
        $page = $page ? $page : 1; //默认显示第一页数据
        $model=D('Promote');
        $data=$model
        ->field('id,account,pay_limit,set_pay_time')
        ->where($map)
        ->page($page, 10)
        ->select();
        $count=$model
        ->field('id,account,pay_limit')
        ->where($map)
        ->count();
        if($count > $row){
            $page = new \Think\Page($count, $row);
            $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
            $this->assign('_page', $page->show());
        }
        $this->assign('list_data', $data);
        $this->meta_title ='代充额度';
        $this->display();
    }
    public function pay_limit_add()
    {
        $limit=D("Promote");
        if(IS_POST){
            if(trim($_REQUEST['promote_id'])==''){
            $this->error("请选择管理员推广员");
            }
            if(trim($_REQUEST['limits'])==''){
            $this->error("请输入代充额度");
            }
            if(trim($_REQUEST['limits'])==0){
            $this->error("代充额度不能低于0");
            }
            $data['id']=$_REQUEST['promote_id'];
            $data['pay_limit']=$_REQUEST['limits'];
            $find=$limit->where(array("id"=>$data['id']))->find();
            if($find['pay_limit']!=0&&$find['set_pay_time']!=null){
            $this->error("已经设置过该推广员",U('pay_limit'));
            }else{
             $limit->where(array("id"=>$data['id']))->setField('pay_limit',trim($_REQUEST['limits']));
             $limit->where(array("id"=>$data['id']))->setField('set_pay_time',time());
             $this->success("添加成功！",U('pay_limit'));
            }
        }else{
            $this->meta_title ='新增代充额度';
            $this->display();
        }
    }
    public function pay_limit_del()
    {
        $limit=D("Promote");
        if(empty($_REQUEST['ids'])){
            $this->error('请选择要操作的数据');
        }
        if(isset($_REQUEST['ids'])){
            $id=$_REQUEST['ids'];
        }
         $limit
         ->where(array("id"=>$id))
         ->setField('pay_limit','0');
         $this->success("删除成功！",U('pay_limit'));
    }
    public function pay_limit_edit()
    {
        $limit=D("Promote");
        if(IS_POST){
            if(trim($_REQUEST['promote_id'])==''){
            $this->error("请选择管理员推广员");
            }
            if(trim($_REQUEST['limits'])==''){
            $this->error("请输入代充额度");
            }
            $data['id']=$_REQUEST['promote_id'];
             $edit=$limit->where(array("id"=>$data['id']))->setField('pay_limit',trim($_REQUEST['limits']));
             $limit->where(array("id"=>$data['id']))->setField('set_pay_time',time());
             if($edit==0){
                $this->error('数据未更改');
             }else{
                $this->success("编辑成功！",U('pay_limit'));
            }
        }else{
            $edit_data=$limit
            ->where(array('id'=>$_REQUEST['ids']))
            ->find();
            $this->assign('edit_data',$edit_data);
            $this->meta_title ='编辑代充额度';
            $this->display();
        }
    }
    
}
