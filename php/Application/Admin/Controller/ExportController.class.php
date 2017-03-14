<?php
namespace admin\Controller;
use Think\Controller;
class ExportController extends Controller
{
	public function exportExcel($expTitle,$expCellName,$expTableData){
        $xlsTitle = iconv('utf-8', 'gb2312', $expTitle);//文件名称  
        $fileName = session('user_auth.username').date('_YmdHis');//or $xlsTitle 文件名称可根据自己情况设定
        $cellNum = count($expCellName);
        $dataNum = count($expTableData);
        Vendor("PHPExcel.PHPExcel");
        $objPHPExcel = new \PHPExcel();
        $cellName = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ');
        $objPHPExcel->getActiveSheet(0)->mergeCells('A1:'.$cellName[$cellNum-1].'1');//合并单元格
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', $expTitle);  
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('A1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        for($i=0;$i<$cellNum;$i++){
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cellName[$i].'2', $expCellName[$i][1]); 
        } 
        for($i=0;$i<$dataNum;$i++){
          for($j=0;$j<$cellNum;$j++){
            $objPHPExcel->getActiveSheet(0)->setCellValue($cellName[$j].($i+3), $expTableData[$i][$expCellName[$j][0]]);
          }             
        }  
        header('pragma:public');
        header('Content-type:application/vnd.ms-excel;charset=utf-8;name="'.$xlsTitle.'.xls"');
        header("Content-Disposition:attachment;filename=$fileName.xls");//attachment新窗口打印inline本窗口打印
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');  
        $objWriter->save('php://output'); 
        exit;   
    }

	//导出Excel
     function expUser($id){
     	switch ($id) {
          case 1:
            $xlsName  = "代充记录";
            $xlsCell  = array(
                    array('id','编号'),
                    array('user_account','账号'),
                    array('game_name','游戏名称'), 
                    array('amount','充值金额'),
                    array('real_amount','实扣金额'),
                    array('zhekou','折扣比例'),
                    array('pay_status','支付状态'),
                    array('create_time','充值时间'),  
                    array('promote_account','推广员账号'),    
            ); 
            if(isset($_REQUEST['user_account'])){
            $map['user_account']=array('like','%'.$_REQUEST['user_account'].'%');
            }
            if(isset($_REQUEST['game_name'])){
                if($_REQUEST['game_name']=='全部'){
                    unset($_REQUEST['game_name']);
                }else{
                    $map['game_name']=$_REQUEST['game_name'];
                    unset($_REQUEST['game_name']);
                }
            }    
            if(isset($_REQUEST['time-start'])&&isset($_REQUEST['time-end'])){
                $map['create_time']=array('BETWEEN',array(strtotime($_REQUEST['time-start']),strtotime($_REQUEST['time-end'])+24*60*60-1));
            }
            if(isset($_REQUEST['start'])&&isset($_REQUEST['end'])){
                $map['create_time']=array('BETWEEN',array(strtotime($_REQUEST['start']),strtotime($_REQUEST['end'])+24*60*60-1));
            }      
           $xlsData=M('agent','tab_')
           ->field("id,user_account,game_name,amount,real_amount,zhekou,pay_status,FROM_UNIXTIME(create_time,'%Y-%m-%d') as create_time,promote_account")
             ->where($map) 
             ->order("create_time")
             ->select(); 
        break; 
             case 2:
                $xlsName  = "渠道充值";
                $xlsCell  = array(
                    array('id','编号'),
                    array('pay_order_number','订单号'),
                    array('user_account','账号'),
                    array('game_name','游戏名称'),
                    array('pay_amount','充值金额'),
                    array('pay_way','充值方式(0平台币;1支付宝;2微信)'),
                    array('pay_time','充值时间'),  
                    array('spend_ip','充值IP'),  
                    array('promote_account','所属渠道'), 
                    array('is_check','是否对账(1参与;2不参与;3参与(已对账);4不参与(已对账))'),   
                );
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
            $map['tab_spend.pay_status'] = 1;
            $xlsData=M('Spend','tab_')
            ->field('tab_spend.*,DATE_FORMAT( FROM_UNIXTIME(pay_time),"%Y-%m-%d %H:%i:%s") AS pay_time')
            ->where($map) 
            ->select();
            break; 
            case 3:
                $xlsName  = "渠道注册";
                $xlsCell  = array(
                    array('id','编号'),
                    array('account','账号'),
                    array('fgame_name','注册游戏'),
                    array('register_ip','注册IP'),
                    array('promote_account','所属渠道'),
                    array('parent_name','上线渠道'),
                    array('register_time','注册时间'),  
                    array('is_check','是否对账(1参与;2不参与;3参与(已对账);4不参与(已对账))'),
                );
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
            $xlsData= M('User','tab_')
            ->field('tab_user.*,tab_user_play.game_id,tab_user_play.game_name,DATE_FORMAT( FROM_UNIXTIME(register_time),"%Y-%m-%d %H:%i:%s") AS register_time')
            ->join('tab_user_play ON tab_user.id = tab_user_play.user_id','LEFT')
            // 查询条件
            ->where($map)
            /* 默认通过id逆序排列 */
            ->group('tab_user.id')
            /* 执行查询 */
            ->select();
            break;
             case 4:
                $xlsName  = "渠道对账";
                $xlsCell  = array(
                    array('bill_number','订单编号'),
                    array('bill_time','对账时间'),
                    array('game_name','游戏名称'),
                    array('total_money','充值总额'),
                    array('total_number','注册人数'),
                    array('promote_account','推广员账号'),
                    array('settlement_status','结算状态(0未结算;1已结算)'),
                    // array('pay_time','充值时间'),  
                );
                $map['status'] = 1;
                // 条件搜索
                foreach($_REQUEST as $name=>$val){
                    switch ($name) {
                        case 'id':break;
                        case 'group':break;
                        case 'timestart':
                            $map['bill_start_time'] = array('egt',strtotime($val));
                            break;
                        case 'timeend':
                            $map['bill_end_time'] = array('elt',strtotime($val)+24*60*60-1);
                            break;
                        default :
                            if ($val == '全部') {$map[$name]=array('like','%%');}
                            else
                                $map[$name]=array('like','%'.$val.'%');
                            break;
                    }
                }
                $xlsData=M('bill','tab_')
                ->where($map) 
                ->select(); 
            break;
            case 5:
                $xlsName  = "渠道结算";
                $xlsCell  = array(
                    array('id','编号'),
                    array('game_name','游戏名称'),
                    array('promote_account','推广员账号'),
                    array('total_money','充值总额'),
                    array('total_number','注册人数'),
                    array('pattern','合作模式(0CPS;1CPA)'), 
                    array('ratio','分成比例'),
                    array('money','注册单价'),
                    array('sum_money','结算金额'),
                    array('create_time','结算时间'),
                );
                foreach($_REQUEST as $name=>$val){
                    switch ($name) {
                        case 'group':break;
                        case 'id':break;
                        case 'timestart':
                            $map['create_time'] = array('egt',strtotime($val));
                            break;
                        case 'timeend':
                            $map['create_time'] = array('elt',strtotime($val)+24*60*60-1);
                            break;
                        case 'withdraw_status':
                            $map['withdraw_status'] = $val;break;
                        default :
                            if ($val == '全部') {$map[$name]=array('like','%%');}
                            else
                                $map[$name]=array('like','%'.$val.'%');
                            break;
                    }
                }
                $xlsData=M('Settlement','tab_')
                    ->field('tab_settlement.*,DATE_FORMAT( FROM_UNIXTIME(create_time),"%Y-%m-%d %H:%i:%s") AS create_time')
                    ->where($map)
                    ->select();     
            break;
            case 6:
                $xlsName  = "渠道提现";
                $xlsCell  = array(
                    array('id','编号'),
                    array('promote_account','推广员账号'),
                    array('sum_money','提现金额'),
                    array('settlement_number','提现单号'),
                    array('create_time','提现时间'),   
                    array('status','提现状态(-1未申请;0申请中;1已通过;2已驳回)'),   
                );
                foreach($_REQUEST as $name=>$val){
                    switch ($name) {
                        case 'group':break;
                        case 'id':break;
                        case 'timestart':
                            $map['create_time'] = array('egt',strtotime($val));
                            break;
                        case 'timeend':
                            $map['create_time'] = array('elt',strtotime($val)+24*60*60-1);
                            break;
                        case 'withdraw_status':
                            $map['withdraw_status'] = $val;break;
                        default :
                            if ($val == '全部') {$map[$name]=array('like','%%');}
                            else
                                $map[$name]=array('like','%'.$val.'%');
                            break;
                    }
                }
                $xlsData=M('withdraw','tab_')
                    ->field('tab_withdraw.*,DATE_FORMAT( FROM_UNIXTIME(create_time),"%Y-%m-%d %H:%i:%s") AS create_time')
                    ->where($map)
                    ->select();
            break;
            case 7:
                $xlsName  = "游戏消费记录";
                $xlsCell  = array(
                    array('id','编号'),
                    array('pay_order_number','订单号'),
                    array('user_account','用户帐号'),
                    array('game_name','游戏名称'),
                    array('pay_amount','充值金额'),
                    array('pay_time','充值时间'),    
                    array('pay_way','充值方式(0平台币;1支付宝;2微信)'),
                    array('pay_status','充值状态(0未支付;1成功)'),
                );
                if(isset($_REQUEST['user_account'])){
                $map['user_account']=array('like','%'.$_REQUEST['user_account'].'%');
                }
                if(isset($_REQUEST['pay_way'])){
                    $map['pay_way']=$_REQUEST['pay_way'];
                }
                if(isset($_REQUEST['pay_status'])){
                    $map['pay_status']=$_REQUEST['pay_status'];
                }
                if(isset($_REQUEST['game_name'])){
                    if($_REQUEST['game_name']=='全部'){
                        unset($_REQUEST['game_name']);
                    }else{
                        $map['game_name']=$_REQUEST['game_name'];
                        unset($_REQUEST['game_name']);
                    }
                }
                if(isset($_REQUEST['time-start']) && isset($_REQUEST['time-end'])){
                    $map['pay_time'] =array('BETWEEN',array(strtotime($_REQUEST['time-start']),strtotime($_REQUEST['time-end'])+24*60*60-1));
                }
                if(isset($_REQUEST['start']) && isset($_REQUEST['end'])){
                    $map['pay_time'] = array('BETWEEN',array(strtotime($_REQUEST['start']),strtotime($_REQUEST['end'])+24*60*60-1));;
                }
                $xlsData=M('Spend','tab_')
                ->field("id,pay_order_number,user_account,game_name,pay_amount,FROM_UNIXTIME(pay_time,'%Y-%m-%d') as pay_time,pay_way,pay_status")
                ->where($map) 
                ->order("pay_time")
                ->select(); 
            break;
            case 8:
                $xlsName  = "平台币充值记录";
                $xlsCell  = array(
                    array('id','编号'),
                    array('pay_order_number','订单号'),
                    array('user_nickname','用户昵称'),
                    array('pay_amount','支付金额'),
                    array('promote_account','所属渠道'),
                    array('create_time','充值时间'),    
                    array('pay_way','充值方式(1支付宝;2微信)'),
                    array('pay_status','充值状态(0失败;1成功)'),
                    array('pay_source','支付来源(1:PC;2:SDK;3APP)'),
                );
                if(isset($_REQUEST['user_nickname'])){
                $map['user_nickname']=array('like','%'.$_REQUEST['user_nickname'].'%');
                }
                if(isset($_REQUEST['pay_way'])){
                    $map['pay_way']=$_REQUEST['pay_way'];
                }
                if(isset($_REQUEST['pay_status'])){
                    $map['pay_status']=$_REQUEST['pay_status'];
                }
                if(!isset($_REQUEST['promote_id'])){

                }else if(isset($_REQUEST['promote_id']) && $_REQUEST['promote_id']==0){
                    $map['promote_id']=array('elt',0);
                }elseif(isset($_REQUEST['promote_name'])&&$_REQUEST['promote_id']==-1){
                    $map['promote_id']=get_promote_id($_REQUEST['promote_name']);
                }else{
                    $map['promote_id']=$_REQUEST['promote_id'];
                }
                if(isset($_REQUEST['time-start']) && isset($_REQUEST['time-end'])){
                    $map['create_time'] =array('BETWEEN',array(strtotime($_REQUEST['time-start']),strtotime($_REQUEST['time-end'])+24*60*60-1));
                }
                if(isset($_REQUEST['start']) && isset($_REQUEST['end'])){
                    $map['create_time'] = array('BETWEEN',array(strtotime($_REQUEST['start']),strtotime($_REQUEST['end'])+24*60*60-1));;
                }
                $xlsData=M('Deposit','tab_')
                ->field("id,pay_order_number,user_nickname,pay_amount,promote_account,FROM_UNIXTIME(create_time,'%Y-%m-%d') as create_time,pay_way,pay_status,pay_source")
                ->where($map) 
                ->order("create_time")
                ->select(); 
            break;
            case 9:
                $xlsName  = "平台币发放记录";
                $xlsCell  = array(
                    array('id','编号'),
                    array('pay_order_number','订单号'),
                    array('user_nickname','用户昵称'),
                    array('game_name','游戏名称'),
                    array('amount','金额'),
                    array('create_time','充值时间'),    
                    array('status','状态(0未充值;1已充值)'),
                    array('op_account','操作人'),
                );
                if(isset($_REQUEST['user_nickname'])){
                $map['user_nickname']=array('like','%'.$_REQUEST['user_nickname'].'%');
                }
                if(isset($_REQUEST['time-start']) && isset($_REQUEST['time-end'])){
                    $map['create_time'] =array('BETWEEN',array(strtotime($_REQUEST['time-start']),strtotime($_REQUEST['time-end'])+24*60*60-1));
                }
                if(isset($_REQUEST['start']) && isset($_REQUEST['end'])){
                    $map['create_time'] = array('BETWEEN',array(strtotime($_REQUEST['start']),strtotime($_REQUEST['end'])+24*60*60-1));;
                }
                $xlsData=M('Provide','tab_')
                ->field("id,pay_order_number,user_nickname,game_name,amount,FROM_UNIXTIME(create_time,'%Y-%m-%d') as create_time,status,op_account")
                ->where($map) 
                ->order("create_time")
                ->select(); 
            break;
            case 10:
                $xlsName  = "平台币使用记录";
                $xlsCell  = array(
                    array('id','编号'),
                    array('pay_order_number','订单号'),
                    array('user_nickname','用户昵称'),
                    array('game_name','游戏'),
                    array('pay_amount','金额'),
                    array('props_name','游戏道具'),
                    array('pay_time','充值时间'),    
                    array('pay_status','状态(0下单未支付;1成功)'),
                );
                if(isset($_REQUEST['user_nickname'])){
                $map['user_nickname']=array('like','%'.$_REQUEST['user_nickname'].'%');
                }
                if(isset($_REQUEST['game_name'])){
                    if($_REQUEST['game_name']=='全部'){
                    }else{
                        $map['game_name']=$_REQUEST['game_name'];
                    }
                }
                if(isset($_REQUEST['time-start']) && isset($_REQUEST['time-end'])){
                    $map['pay_time'] =array('BETWEEN',array(strtotime($_REQUEST['time-start']),strtotime($_REQUEST['time-end'])+24*60*60-1));
                }
                if(isset($_REQUEST['start']) && isset($_REQUEST['end'])){
                    $map['pay_time'] = array('BETWEEN',array(strtotime($_REQUEST['start']),strtotime($_REQUEST['end'])+24*60*60-1));;
                }
                if(!isset($_REQUEST['promote_id'])){

                }else if(isset($_REQUEST['promote_id']) && $_REQUEST['promote_id']==0){
                    $map['promote_id']=array('elt',0);
                    unset($_REQUEST['promote_id']);
                    unset($_REQUEST['promote_name']);
                }elseif(isset($_REQUEST['promote_name'])&&$_REQUEST['promote_id']==-1){
                    $map['promote_id']=get_promote_id($_REQUEST['promote_name']);
                }else{
                    $map['promote_id']=$_REQUEST['promote_id'];
                    unset($_REQUEST['promote_id']);
                    unset($_REQUEST['promote_name']);
                }
                if(isset($_REQUEST['game_name'])){
                    if($_REQUEST['game_name']=='全部'){
                        unset($_REQUEST['game_name']);
                    }else{
                        $map['game_name']=$_REQUEST['game_name'];
                    }
                    unset($_REQUEST['game_name']);
                }
                $xlsData=M('Bind_spend','tab_')
                ->field("id,pay_order_number,user_nickname,game_name,pay_amount,props_name,FROM_UNIXTIME(pay_time,'%Y-%m-%d') as pay_time,pay_status")
                ->where($map) 
                ->order("pay_time")
                ->select(); 
            break;
            case 11:
                $xlsName  = "礼包领取记录";
                $xlsCell  = array(
                    array('id','编号'),
                    array('game_name','游戏名称'),
                    array('gift_name','礼包名称'),
                    array('user_account','领取用户'),
                    array('novice','激活码'),    
                    array('create_time','领取时间'),
                );
                if(isset($_REQUEST['game_name'])){
                $map['game_name']=array('like','%'.$_REQUEST['game_name'].'%');
                }
                $xlsData=M('gift_record','tab_')
                ->field("id,game_name,gift_name,user_account,novice,FROM_UNIXTIME(create_time,'%Y-%m-%d') as create_time")
                ->where($map) 
                ->order("create_time")
                ->select(); 
            break;
          case 12:
                $xlsName  = "平台用户";
                $xlsCell  = array(
                    array('id','用户id'),
                    array('account','用户账号'),
                    array('balance','平台币余额'),
                    array('register_way','注册方式(0:WEB;1:SDK;2:APP)'),
                    array('register_time','注册时间'),
                );
                if(isset($_REQUEST['account'])){
                    $map['tab_user.account'] = array('like','%'.$_REQUEST['account'].'%');
                }
                if(isset($_REQUEST['game_id'])){
                    $map['tab_game.id'] = $_REQUEST['game_id'];
                }
                if(isset($_REQUEST['register_way'])){
                    $map['register_way'] = $_REQUEST['register_way'];
                }
                if(isset($_REQUEST['time-start']) && isset($_REQUEST['time-end'])){
                    $map['register_time'] =array('BETWEEN',array(strtotime($_REQUEST['time-start']),strtotime($_REQUEST['time-end'])+24*60*60-1));
                }
                if(isset($_REQUEST['start']) && isset($_REQUEST['end'])){
                    $map['register_time'] = array('BETWEEN',array(strtotime($_REQUEST['start']),strtotime($_REQUEST['end'])+24*60*60-1));;
                }
                $xlsData=M('User','tab_')
                ->field("id,account,balance,register_time,register_way,FROM_UNIXTIME(register_time,'%Y-%m-%d') as register_time")
                ->where($map) 
                ->order("register_time")
                ->select(); 
            break;
            case 13:
                $xlsName  = "代充额度";
                $xlsCell  = array(
                    array('id','编号'),
                    array('account','渠道账号'),
                    array('pay_limit','代充上限'),
                    array('set_pay_time','更新时间'),
                );
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
            $map['pay_limit']=array('gt','0');
                $xlsData=M('Promote','tab_')
                ->field("id,account,pay_limit,FROM_UNIXTIME(set_pay_time,'%Y-%m-%d') as set_pay_time")
                ->where($map) 
                ->order("set_pay_time")
                ->select(); 
            break;
             case 14:
                $xlsName  = "游戏返利";
                $xlsCell  = array(
                    array('id','编号'),
                    array('pay_order_number','订单号'),
                    array('user_id','用户名'),
                    array('game_name','游戏名称'),
                    array('pay_amount','充值金额'),
                    array('ratio','返利比例'),
                    array('ratio_amount','返利金额'),
                    array('promote_name','所属推广员'),
                    array('create_time','添加时间'),
                );
            if(isset($_REQUEST['game_name'])){
                if($_REQUEST['game_name']=='全部'){
                    unset($_REQUEST['game_name']);
                }else if($_REQUEST['game_name']=='自然注册'){
                    $map['id']=array("elt",0);
                    unset($_REQUEST['game_name']);
                }else{
                    $map['id']=get_game_id($_REQUEST['game_name']);
                    unset($_REQUEST['game_name']);
                }
            }
            
                $xlsData=M('RebateList','tab_')
                ->field("id,pay_order_number,user_id,user_name,game_name,pay_amount,ratio,ratio_amount,promote_name,FROM_UNIXTIME(create_time,'%Y-%m-%d %H:%i:%s') as create_time")
                ->where($map) 
                ->order("create_time")
                ->select(); 
            break;
          
     	}
     	   $this->exportExcel($xlsName,$xlsCell,$xlsData);

     }
	
}