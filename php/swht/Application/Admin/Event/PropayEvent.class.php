<?php
namespace Admin\Event;
use Think\Controller;
/**
 * 后台事件控制器
 * @author zxc 
 */
class PropayEvent extends Controller {
    /*
    *自选
    */
    public function add1(){
      $promote_account=$_POST['account'];
      $amount= $_POST['amount'];
      if($this->isset_promote($promote_account)!==null){
        if($this->where_you($promote_account)==UID){
         if(!is_numeric($amount)||$amount<=0){
             $this->error("金额不正确！",U("propay"));
              return false;
            }            
            $add['order_number']=build_order_no();
            $add['amount']=$amount;
            $add['promote_id']=get_promote_id($promote_account);
            $add['promote_account']=$promote_account;
            $add['status']=0;
            $add['admin_id']=UID;
            $add['admin_name']=get_admin_nickname(UID);
            $add['create_time']=NOW_TIME;
            M("propay","tab_")->add($add);
            $this->success("插入成功",U('propay'));
        }else{
        $this->error("该推广员可能不属于你",U("propay"));  
        }
      }else{
        $this->error("账号不存在",U("propay"));
      }
    }

    /**
     * 内充管理---导入Excel
     * @author 顽皮蛋 <shf_l@163.com>
     */
    public function add2(){
        header("Content-Type:text/html;charset=utf-8");
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize   =     3145728 ;// 设置附件上传大小
        $upload->exts      =     array('xls', 'xlsx');// 设置附件上传类
        $upload->rootPath  =     './Uploads/'; // 设置附件上传目录
        $upload->savePath  =      'excel/'; // 设置附件上传目录
        // 上传文件
        $info   =   $upload->uploadOne($_FILES['excelData']);
        $filename = './Uploads/'.$info['savepath'].$info['savename'];
        $exts = $info['ext'];
        if(!$info) {// 上传错误提示错误信息
            $this->error($upload->getError());
          }else{// 上传成功
            $this->charge_import($filename, $exts);
        }
    }


    public function add3(){
         $promote_account=$_POST['pay_names'];
        $amount=$_POST['amount'];
        if(empty($promote_account)){$this->error("充值人员不能为空");}
        if($amount<=0){$this->error("金额不正确！");}            
        $namearr = explode("\n",$promote_account);
        for($i=0;$i<count($namearr);$i++){
            $user=get_user_pro_list(str_replace(array("\r\n", "\r", "\n"), "", $namearr[$i]));
            if(null!=$user){
              if($this->where_you(str_replace(array("\r\n", "\r", "\n"), "", $namearr[$i]))==UID){
                $add['promote_id']=$user['id'];
                $add['promote_account']=
                $add['order_number']=build_order_no();
                $add['amount']=$amount;
                $add['status']=0;
                $add['admin_id']=UID;
                $add['admin_name']=get_admin_nickname(UID);
                $add['create_time']=NOW_TIME;
                $prov=M("Propay","tab_")->add($add);
              }

            }
        }
        $this->success("提交成功",U("propay"));
    }



    //导入数据方法
    protected function charge_import($filename, $exts='xls'){
        //导入PHPExcel类库，因为PHPExcel没有用命名空间，只能inport导入
        //import("Org.Util.PHPExcel");
        vendor("PHPExcel.PHPExcel");
        //创建PHPExcel对象，注意，不能少了\
        $PHPExcel=new \PHPExcel();
        //如果excel文件后缀名为.xls，导入这个类
        if($exts == 'xls'){
            //import("Org.Util.PHPExcel.Reader.Excel5");
            $PHPReader=new \PHPExcel_Reader_Excel5();
        }else if($exts == 'xlsx'){
            //import("Org.Util.PHPExcel.Reader.Excel2007");
            $PHPReader=new \PHPExcel_Reader_Excel2007();
        }
        //载入文件
        $PHPExcel=$PHPReader->load($filename);
        //获取表中的第一个工作表，如果要获取第二个，把0改为1，依次类推
        $currentSheet=$PHPExcel->getSheet(0);
        //获取总列数
        $allColumn=$currentSheet->getHighestColumn();
        //获取总行数
        $allRow=$currentSheet->getHighestRow();
        //循环获取表中的数据，$currentRow表示当前行，从哪行开始读取数据，索引值从0开始
        for($currentRow=1;$currentRow<=$allRow;$currentRow++){
            //从哪列开始，A表示第一列
            for($currentColumn='A';$currentColumn<=$allColumn;$currentColumn++){
                //数据坐标
                $address=$currentColumn.$currentRow;
                //读取到的数据，保存到数组$arr中
                $data[$currentRow][$currentColumn]=$currentSheet->getCell($address)->getValue();
            }

        }
        $this->save_import($data);
    }

    //保存导入数据并返回错误信息
    public function save_import($data){ 
        unset($data[1]);
        $errorNum = 0;
        $succNum = 0;
        $errorList = array();//存储错误数据;
        foreach ($data as $k=>$v){
          $errorList[$errorNum]['A'] = $v['A'];
          $errorList[$errorNum]['B'] = $v['B'];
          $errorList[$errorNum]['C'] = $v['C'];
          if($v['C']<=0){//金额有问题
            $errorList[$errorNum]['D'] = '金额有问题';
            $errorNum++;
            continue;
          }
            if($this->isset_promote($v['A'])==null){//用户不存在
            $errorList[$errorNum]['D'] = '用户不存在';
            $errorNum++;
            continue;
          }
          if($this->where_you($v['A'])!==UID){//推广员不属于你
            $errorList[$errorNum]['D'] = '推广员不属于你';
            $errorNum++;
            continue;
          }
          
          $succNum++;
          $arr['promote_id'] = get_promote_id($v['A']);
          $arr['promote_account'] = $v['A'];
          $arr['admin_id']=UID;
          $arr['admin_name']=get_admin_nickname(UID);
          $arr['order_number']=build_order_no();
          $arr['status']=0;
          $arr['amount'] = (double)$v['C'];
          $arr['create_time'] = NOW_TIME;
          D('Propay')->add($arr);
        }
        $a = json_encode($errorList);
        $json = urlencode(json_encode($errorList));          
        $this->assign ( 'errorNum', $errorNum );
        $this->assign ( 'succNum', $succNum );
        $this->assign ( 'status', 1 );
        $this->assign ( 'json', $json);
        $this->success('成功：'.$succNum.';失败：'.$errorNum,U('propay'));
    }
    //判断输入是否存在推广员
    public function isset_promote($account){
       $map['account']=trim($account);
       $pro=M("Promote","tab_")->where($map)->find();
       return $pro;
     }
     //判断输入推广员是否属于当前管理员
    public function where_you($account){
     $map['account']=trim($account);
     $pro=M("Promote","tab_")->where($map)->find();
     return $pro['admin_id'];
     }
     


}