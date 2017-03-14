<?php

namespace Admin\Controller;
use User\Api\UserApi as UserApi;

/**
 * 后台首页控制器
 * @author zxc
 */
class PropayController extends ThinkController {
	const model_name = 'propay';

    public function propaylist(){
    	if(isset($_REQUEST['promote_account'])){
    		$map['promote_account']=array('like','%'.$_REQUEST['promote_account'].'%');
    		unset($_REQUEST['promote_account']);
    	}
      if(isset($_REQUEST['time-start'])&&isset($_REQUEST['time-end'])){
            $map['create_time'] =array('BETWEEN',array(strtotime($_REQUEST['time-start']),strtotime($_REQUEST['time-end'])+24*60*60-1));
            unset($_REQUEST['time-start']);unset($_REQUEST['time-end']);
        }
        $map['admin_id']=UID;
        $maps['admin_id']=UID;
        $maps['status']=1;
        $total=M(self::model_name,'tab_')->where($maps)->sum('amount');
        $total=sprintf("%.2f",$total);
        $this->assign('total',$total);
    	parent::lists(self::model_name,$_GET["p"],$map);

    }
    public function propay(){    
        if(IS_POST){
            $type = $_REQUEST['type'];
            $Propay = A('Propay','Event');
            switch ($type) {
                case 1:
                    $Propay->add1();
                    break;
                case 2:
                    $Propay->add2();
                    break;
                case 3:
                    $Propay->add3();
                    break;
            }
        }   
        else{
            $this->display();
        }
    }

    public function apply() {
        $page = intval($p);
        $page = $page ? $page : 1; //默认显示第一页数据
        $row    = 10;
         if(isset($_REQUEST['com_name'])){
                $map['com_name']=array("like","%".$_REQUEST['com_name']."%");
            }
         if(isset($_REQUEST['time-start'])&&isset($_REQUEST['time-end'])){
            $map['create_time'] =array('BETWEEN',array(strtotime($_REQUEST['time-start']),strtotime($_REQUEST['time-end'])+24*60*60-1));
            unset($_REQUEST['time-start']);unset($_REQUEST['time-end']);
        }
        $map['com_id']=UID;
        $data = M("limit","tab_")
            // 查询条件
            ->where($map)
            /* 默认通过id逆序排列 */
            /* 数据分页 */
            ->page($page, $row)
            /* 执行查询 */
            ->select();
        /* 查询记录总数 */
        $count =M("limit","tab_")->where($map)->count();
         //分页
        if($count > $row){
            $page = new \Think\Page($count, $row);
            $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
            $this->assign('_page', $page->show());
        }
        $this->assign('list_data', $data);
        $this->display();
    }
    //申请额度
    public function apply_add(){
        if(IS_POST){
            $add['com_id']=UID;
            $add['com_name']=get_admin_nickname(UID);
            $add['s_limit']=$_POST['s_limit'];
            $add['status']=0; 
            $add['create_time']=time();
            M("limit","tab_")->add($add);
            $this->success("申请成功",U("apply"));
        }else{
             $this->display();            

        }
            
    }

    
    public function batch($ids){
        $list=M("Propay","tab_");
        //如果发放金额大于可发平台币
        $map['admin_id']=UID;
        $map['status']=1;
        $total=M(self::model_name,'tab_')->where($map)->sum('amount');
        $total=sprintf("%.2f",$total);      
        $maps['com_id']=UID;
        $com=M("comlimits","tab_")->where($maps)->find();
        if($total>$com['limits']||$total==$com['limits']){
          $this->error("平台币余额不足,请申请");
          return flase;
        }
        $map['id']=array("in",$ids);
        $map['status']=0;
        $pro=$list->where($map)->select();  
        for ($i=0; $i <count($pro) ; $i++) {
          $maps['id']=$pro[$i]['promote_id'];
          $user=M("Promote","tab_")->where($maps)->setInc("balance_coin",$pro[$i]['amount']);
          $list->where($map)->setField("status",1);
        }
        $this->success("充值成功",U("propaylist"));
    }

    public function delprovide($ids){
      $list=M("Propay","tab_");
      $map['id']=array("in",$ids);
      $map['status']=0;
      $delete=$list->where($map)->delete();
       if($delete){
            $this->success("批量删除成功！",U("propaylist"));
       }else{
       		 $this->error("批量删除失败！",U("propaylist"));
        }
    }
}
