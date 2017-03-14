<?php
use JPush\src\JPush;
use JPush\src\core\JPushException;
namespace Admin\Controller;
/**
 * 后台首页控制器
 * @author zxc <zuojiazi@vip.qq.com>
 */
class PushController extends ThinkController {
    const model_name = 'push';

   public function pushsetlsit()
   {	
   	parent::lists(self::model_name,$_GET['p'],$map);

   }


   public function add()
   {
   	if(IS_POST){
         $push  =   D("push");
         $data=$push->create();
         $map['app_key']=$data['app_key'];
         $map['master_secret']=$data['master_secret'];
         $find_push=$push->where($map)->find();
         if(null!==$find_push){
             $this->error('该应用已经添加过！', U("pushsetlsit"));
             return false;
         }
         if($data){
             $push->add($data);
             $this->success('添加成功！', U("pushsetlsit"));
          } else {
                $this->error($push->getError());
          }
   	  }else{
        $this->meta_title = '新增推送设置';
   	$this->display();
   	}

   }
   public function edit($id)
   {
      $push  =   D("push");
   	if(IS_POST){
      $data=$push->create();
      if($data){
         $push->save($data);
         $this->success('修改成功！', U("pushsetlsit"));
      }else{
         $this->error($push->getError());
      }
   	}else{
      $map['id']=$id;
      $edit=$push->where($id)->find();
      $this->assign("data",$edit);
        $this->meta_title = '编辑推送设置';

   	$this->display();
   	}

   }
 
   public function del($model = null, $ids=null)
    {
        $model = M('Model')->getByName(self::model_name); /*通过Model名称获取Model完整信息*/
        parent::del($model["id"],$ids);
    }

   public function pushlist()
   {
   	parent::lists("PushNotice",$_GET['p'],$map);

   }

   public function add_list()
   {
   	if(IS_POST){
         $push  =   M("PushNotice","tab_");
         $data=$push->create();
         if($data){
            if(empty($data['push_id'])){
               $this->error("应用名不能为空",U("pushlist"));
            }
            if(strlen($data['push_time'])>10){
               $data['push_time']=strtotime($data['push_time']);
            }
            $data['push_name']=get_push_name($data['push_id']);
            $map['id']=$data['push_id'];
            $pus=D("push")->where($map)->find();
            if($pus['status']==0){$this->error("该应用已关闭",U("pushlist"));return false;}
            $data['app_key']=$pus['app_key'];
            $data['master_secret']=$pus['master_secret'];

            $this->jpush($data);
             $push->add($data);
             $this->success('添加成功！', U("pushlist"));
         }else{
             $this->error($push->getError());
         }
   	}else{
        $this->meta_title = '新增发送通知';

   	$this->display();

   	}

   }

   public function edit_list()
   {
   	if(IS_POST){
   		
   	}else{

   	$this->display();

   	}

   }
   public function del_list($model = null, $ids=null)
   {
  	     $model = M('Model')->getByName("PushNotice"); /*通过Model名称获取Model完整信息*/
        parent::del($model["id"],$ids);
   }

    public function set_status()
    {
        parent::set_status(self::model_name);
    }

    //推送
    public function jpush($data){
        Vendor("Jpush.src.JPush");
        $client = new \JPush($data['app_key'], $data['master_secret']);
        $str="";
        switch ($data['push_object']) {
           case 0:
           $str="all";
              break;
           case 1:
          $str="ios";
              break;
           case 2:
            $str="android";
              break;
            case 3:
              $str="all";
              break;
           default:
            $str="all";
              break;
        }
        switch ($data['push_time_type']) {
           case 0:
             $result = $client->push()
              ->setPlatform($str)
              ->addAllAudience()
              ->setNotificationAlert($data['content'])
              ->send();
             // echo  json_encode($result) . $br;
              break;
           case 1:
               $payload = $client->push()
                ->setPlatform($str)
                ->addAllAudience()
                ->setNotificationAlert($data['content'])
                ->build();                
   // 创建一个2016-12-22 13:45:00触发的定时任务
   $response = $client->schedule()->createSingleSchedule("定时任务", $payload, array("time"=>date("Y-m-d H:i:s",$data['push_time'])));
              break;
        }

    }
}
