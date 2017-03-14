<?php
namespace App\Controller;
use User\Api\MemberApi;
use User\Api\UserApi;
use Org\UcpaasSDK\Ucpaas;

class ServerController extends BaseController{

    protected function _initialize(){
        /* 读取站点配置 */
        $config = api('Config/lists');
        C($config); //添加配置
    }

    /**
    *注册
    */
    public function register(){
        $request = json_decode(file_get_contents("php://input"));
        if(empty($request)){
            $data = array("status"=>"-1","return_status"=>"fail","return_msg"=>"数据不能为空");
            echo json_encode($data);-
            exit();
        }
        $user = A("User","Event");
        if(!$user->is_exist($request->account)){
            $data = array("status"=>"-2","return_status"=>"fail","return_msg"=>"账号已存在");
            echo json_encode($data);
            exit();
        }
        $data["account"]      = $request->account;                //登陆账户
        $data["password"]     = $request->password;               //登陆密码
        $data["nickname"]     = $request->nickname;                //昵称
        $data["sex"]     = $request->sex;             //性别
        $data["email"]     = $request->account;             //邮箱
        $data["register_way"] = 2;                                //注册方式
        $member = new MemberApi();
        $result = $member->register($request->account,$request->password,2,$request->sex);
        if($result > 0){
            $data = array("status"=>"1","return_status"=>"success","return_msg"=>"成功");
            echo json_encode($data);
        }
        else{
            $data = array("status"=>"0","return_status"=>"fail","return_msg"=>"失败");
            echo json_encode($data);
        }
    }
    //修改昵称
    public function set_nickname(){
        $request=json_decode(file_get_contents("php://input"));
        if(empty($request)){
            $data = array("status"=>"-1","return_msg"=>"数据不能为空");
            echo json_encode($data);
            exit();
        }   
        $map['id']=get_user_id($request->account);
        $data['nickname']=$request->nickname;
        $user=M("User","tab_")->where($map)->save($data);
        if($user){
            echo json_encode(array("return_code"=>'1','msg'=>"修改成功"));
        }else{
            echo json_encode(array("return_code"=>'-2','msg'=>"修改失败"));
        }
    }
    /**
    *所有游戏信息
    */
    public function get_game_list($game_name="",$type="",$limit=1){
        header("Content-type:text/html;charset=utf-8");
        $map = array();
        if(!empty($game_name)){
            $map['game_name'] = array("like","%$game_name%") ;
        }  
       
        switch ($type) {
            case 'tui':
                $map['recommend_status']=1;
                break;
            case 'new':
                $map['recommend_status']=3;
                break;
            case 'top':
                $map['recommend_status']=2;
                break;          
            default:
                $map['recommend_status']=array("egt",0);
                break;
        }
        $map['status']=1;
        // $this->wite_text(json_encode($map),dirname(__FILE__).'/a.txt');
        $model = array(
            'm_name' => 'game',
            'field'=>'tab_game.id,game_name,icon,game_size,game_type_id,dow_num,introduction,and_dow_address,recommend_status',
           /* 'join'=>'tab_game_info on tab_game.id=tab_game_info.id',*/
            'map'    => $map,
            'list_row'=>9,
            'icon'=>'icon',
            'order'  => 'id desc',
        );
        $data=M('game','tab_')
             ->field('tab_game.id,game_name,icon,game_size,game_type_id,dow_num,introduction,and_dow_address,recommend_status')
             ->where($map)
             ->order('id desc')
             ->page($limit,9)
             ->select();
/*        $user = A('User','Event');
        $list=$user->user_join($model,$limit);*/
            foreach ($data as $key => $value) {
                 $data[$key]['and_dow_address'] ='http://'.$_SERVER['HTTP_HOST']. $data[$key]['and_dow_address'];
                 $data[$key]['icon'] ='http://'.$_SERVER['HTTP_HOST']. get_cover($data[$key]['icon'],'path');
                      /*$list[$key]['category'] =get_cname($list[$key]['category']);*/
                     }
        echo json_encode(array("list"=>$data));
    }
    //写入文件
    public  function wite_text($txt,$name){

        $myfile = fopen($name, "w") or die("Unable to open file!");

        fwrite($myfile, $txt);

        fclose($myfile);

    }
     /**
    *所有礼包
    */
    public function get_gift_list($gift_name="",$recommend_status="",$game_id=0,$limit=1){
        $map = array();
        if(!empty($gift_name)){
            $map['tab_giftbag.giftbag_name'] = array("like","%$gift_name%");
        }
         if(!empty($recommend_status)){
            $map['tab_giftbag.recommend_status'] = $recommend_status;
        }
         if($game_id>0){
            $map['tab_giftbag.id'] = $game_id;
        }
        
        $model = array(
            'm_name' => 'giftbag',
            'field'=>'tab_giftbag.id as giftid,tab_giftbag.game_id,tab_giftbag.game_name,giftbag_name,icon,novice,tab_game.game_name,game_size,desribe',
            'join'=>'tab_game on tab_game.id=tab_giftbag.game_id',
            'map'    => $map,
            'list_row'=>10,
            'order'  => 'tab_giftbag.id desc',
        );
        $user = A('User','Event');
        $list=$user->user_join($model,$limit);

        foreach ($list as $key => $value) {
            $list[$key]['icon']="http://".$_SERVER["HTTP_HOST"].get_cover($value['icon'],'path');
            $len=explode(",",$value['novice']);
            $list[$key]['novice']=count($len);
        }
        echo json_encode(array("list"=>$list));
    }

    public function get_gift_limit($limit=1){
         $model = array(
            'm_name' => 'giftbag',
            'field'=>'tab_giftbag.id as gid,giftbag_name,icon,novice,giftbag_type',
            'join'=>'tab_game on tab_game.id=tab_giftbag.game_id',
            'map'    => $map,
            'list_row'=>10,
            'order'  => 'tab_giftbag.id desc',
        );
        $user = A('User','Event');
        $list=$user->user_join($model,$limit);
        //var_dump($list);exit;
        foreach ($list as $key => $value) {
            $list[$key]['icon']="http://".$_SERVER["HTTP_HOST"].get_cover($value['icon'],'path');
            $len=explode(",",$value['novice']);
            $list[$key]['novice']=count($len);
            switch ($value['giftbag_type']) {
                case 1:
                    $data['1'][$key] = $value;
                    break;
                case 2:
                    $data['2'][$key] = $value;
                    break;
                case 3:
                    $data['3'][$key] = $value;
                    break;
                case 4:
                    $data['4'][$key] = $value;
                    break;
            }
        }
        echo json_encode(array("data"=>$data));
    }

    //领取礼包
     public function receive_gift($mid=0,$giftid=0){
        if($mid==0 or $giftid==0){
            echo json_encode(array('status'=>-1,'return_msg'=>'数据不能为空'));
            exit();
        }
        $list=M('record','tab_gift_');
        $is=$list->where(array('user_id'=>$mid,'gift_id'=>$giftid))->find();
        if(!empty($is)) {   
                // $map['user_id']=$mid;
                // $map['gift_id']=$giftid;
                // $info=$list->where($map)->find();
                // if($info){
                //     $data=$info['novice'];
                //     echo  json_encode(array('status'=>'1','info'=>'no','data'=>$data));
                // }
            //$novice = empty($is['novice'])?"异常":$is['novice'];
            //$d = array('status'=>'1','info'=>'no','data'=>$is['novice']);
            echo  json_encode(array('status'=>'2','info'=>'receive','data'=>$is['novice']));
        }
        else{           
            $bag=M('giftbag','tab_');               
            //$giftid= $giftid;
            $ji=$bag->where(array("id"=>$giftid))->field("novice")->find();
            if(empty($ji['novice'])){
                echo json_encode(array('status'=>'3','info'=>'none'));
            }
            else
            {
                $at=explode(",",$ji['novice']);
                $gameid=$bag->where(array("id"=>$giftid))->field('giftbag_name,game_id')->find();
                $add['game_id']=$gameid['game_id'];
                $add['game_name']=get_game_name($gameid['game_id']);
                $add['gift_id']=$giftid;
                $add['gift_name']=$gameid['giftbag_name'];
                $add['status']=1;
                $add['novice']=$at[0];
                $add['user_id'] =$mid;
                $add['create_time']=time();
                $list->add($add);
                $new=$at;
                if(in_array($new[0],$new)){
                    $sd=array_search($new[0],$new);
                    unset($new[$sd]);
                }
                $act['novice']=implode(",", $new);
                $bag->where("id=".$giftid)->save($act);
                echo  json_encode(array('status'=>'1','info'=>'ok','data'=>$at[0]));
            }   
        } 
    }

    //用户已领取礼包
    public function receive_gift_list($user_id=0)
    {
        $where['user_id']=$user_id;
        $model=M('gift_record','tab_');
        
    }
    //礼包详情 Buzhaohe <61673158@qq.com>
    public function get_gift_details()
    {   
        $request=json_decode(base64_decode(file_get_contents("php://input")),true);
        if(empty($request)){
            $data = array("status"=>"-1","return_msg"=>"数据不能为空");
            echo json_encode($data);
            exit();
        }
        $where['tab_giftbag.id']=$request['gift_id'];
        $model = M("giftbag","tab_");
        $data = array();
        $data = $model
              ->field('tab_giftbag.*,tab_game.icon')
              ->join('tab_game ON tab_giftbag.game_id = tab_game.id','LEFT')
              ->where($where)
              ->find();

        if($data)
        {  
           $data['icon']="http://".$_SERVER['HTTP_HOST'].get_cover($data['icon'],"path");  
           $len=explode(",",$data['novice']);
           $data['novice']=count($len);
           $data['start_time']= date("Y-m-d ",$data['start_time']); 
           $data['end_time']= date("Y-m-d ",$data['end_time']); 
           echo base64_encode(json_encode(array("status"=>1,"msg"=>$data)));
        }
        else echo base64_encode(json_encode(array("status"=>-1,"list"=>"参数错误")));

    }

    //我的礼包
    public function my_gift($user_id=0,$limit=1){
        if($user_id==0){
           echo json_encode(array("status"=>-2,"msg"=>"参数错误"));
           exit();
        }
        $map['user_id']=$user_id;
        $model = array(
            'm_name' => 'gift_record',
            'field'=>'tab_gift_record.game_name,gift_name,tab_gift_record.novice as record_novice,icon,tab_giftbag.desribe,tab_giftbag.novice,gift_id',
            'join'=>'tab_game on tab_gift_record.game_id=tab_game.id',
            'joins'=>'tab_giftbag on tab_gift_record.gift_id=tab_giftbag.id',
            'map'    => $map,
            'list_row'=>10,
        );
        $user = A('User','Event');
        $gift=$user->user_join($model,$limit);
         if($gift){
            foreach ($gift as $key => $value) {
                $gift[$key]['icon']="http://".$_SERVER['HTTP_HOST'].get_cover($value['icon'],"path");
                 $len=explode(",",$value['novice']);
                 $gift[$key]['novice']=count($len);
            }
           echo json_encode(array("status"=>1,"msg"=>$gift));
         }else{
            echo json_encode(array("status"=>-1,"msg"=>"你还没有领取礼包"));
         }

    }
    //游戏详情
    public function get_game_details($game_id=0)
    {
          if($game!==0){
            $map['tab_game.id'] = $game_id;
            $model = array(
            'm_name' => 'game',//tab_game.gift 已添加
            'field'=>'tab_game.id,game_name,version,icon,screenshot,game_type_id,game_size,dow_num,and_dow_address,introduction,recommend_status,tab_opentype.open_name',
               'join'=>'tab_opentype on tab_opentype.id=tab_game.category',
            'map'    => $map,
            'list_row'=>9,
            'icon'=>'icon',
            'order'  => 'id desc',
        );
            $user = A('User','Event');
            $list=$user->user_joins($model);
           //  $sql="SELECT a.*
           //      FROM tab_giftbag a
           // LEFT JOIN tab_opentype ON tab_opentype.id=tab_game.category
           //     WHERE a.id = %d ";
/*            $limit=$limit*9;     
            $sql.="ORDER BY a.giftbag_type DESC LIMIT $limit,12";*/
            // $Model = new \Think\Model();
            // $data=$Model->query($sql,$game_id);

            foreach ($list as $key => $value) {
                $list[$key]['screenshot']=$this->screenshots($list[$key]['screenshot']);
                $list[$key]['and_dow_address']="http://".$_SERVER['HTTP_HOST'].$value['and_dow_address'];
            }
             echo json_encode(array("list"=>$list));
         }else{
             echo json_encode(array("status"=>-1,"list"=>"参数错误"));
         }   
    }
    /**
    *游戏截图
    */
    protected function screenshots($str){
        $data = explode(',', $str);
        $screenshots = array();
        foreach ($data as $key => $value) {
            $screenshots[$key] = 'http://'.$_SERVER['HTTP_HOST']. get_cover($value,'path');
        }
        return $screenshots;
    }
    //游戏分类
    public function game_category($type_id="",$type="",$limit = 1,$cate=""){
        /*$map['game_type'] = $type;*/
        $data=M("game_type","tab_")->field("id,type_name,icon")->select();
        $page = intval($limit);
        $page = $page ? $page : 1; //默认显示第一页数据
        if(!empty($type_id)){
        $map['game_type_id']=$type_id;
        }
        if(!empty($type)){
        switch ($type) {
            case 'tui':
                $map['recommend_status']=1;
                break;
            case 'new':
                $map['recommend_status']=3;
                break;
            case 'top':
                $map['recommend_status']=2;
                break;          
            default:
                $map['recommend_status']=array("in","1,2,3,0");
                break;
        }         
        }
        
        $row    =10;
        if($cate != 'cate'){
        $list = M("game","tab_")//tab_game.gift
            ->field("tab_game.id,tab_game.game_name,tab_game.game_type_id,tab_game.category,tab_game.icon,tab_game.cover,tab_game.game_size,tab_game.discount,and_dow_address,dow_num,game_score,recommend_status,introduction,tab_rebate.money,tab_rebate.ratio,tab_opentype.open_name")
            ->join("tab_game_type on tab_game.game_type_id=tab_game_type.id")
            ->join("tab_rebate on tab_rebate.game_id = tab_game.id","LEFT")
            ->join("tab_opentype on tab_opentype.id=tab_game.category","LEFT")        
            // 查询条件
            ->where($map)
            /* 数据分页 */
            ->page($page,$row)
            /* 执行查询 */
            ->select();

            foreach ($list as $key => $value) {
                 $list[$key]['and_dow_address'] ='http://'.$_SERVER['HTTP_HOST']. $list[$key]['and_dow_address'];
                 $list[$key]['icon'] ='http://'.$_SERVER['HTTP_HOST']. get_cover($list[$key]['tab_game.icon'],'path');
                 $list[$key]['cover'] ='http://'.$_SERVER['HTTP_HOST']. get_cover($list[$key]['cover'],'path');
                 $list[$key]['ratio']=(float)$list[$key]['ratio'];
                      /*$list[$key]['category'] =get_cname($list[$key]['category']);*/
                     }
            }         
            foreach ($data as $key => $value) {
                $data[$key]['icon'] ='http://'.$_SERVER['HTTP_HOST']. get_cover($data[$key]['icon'],'path');
             }
        echo json_encode(array("data"=>$data,'list'=>$list));

    }
    //下载次数累加
    public function updata_down($game_id){
        $map['id']=$game_id;
        $game=M("game","tab_")->where($map)->setInc("dow_num",1);
        if($game){
            echo json_encode(array("status"=>1,"msg"=>"成功"));
        }else{
            echo json_encode(array("status"=>-1,"msg"=>"失败"));
        }
    }

//下载排行榜sssss
    public function get_down()
    {
        //$data=M("game_type","tab_")->field("id,type_name")->select();
         $class=M("game_type","tab_")->field("id,icon,cover")->select();
             foreach ($class as $key => $value) {
                $class[$key]['icon']="http://".$_SERVER['HTTP_HOST'].get_cover($value['icon'],"path");
                $class[$key]['cover']="http://".$_SERVER['HTTP_HOST'].get_cover($value['cover'],"path");
         }  
/*        $model = array(
            'm_name' => 'game',
            'field'=>'tab_game.id,game_name,icon,game_size,game_type_id,dow_num,and_dow_address,introduction',
            'join'=>'tab_game_info on tab_game.id=tab_game_info.id',
            'map'    => $map,         
            'icon'=>'icon',
            'group'=>'game_type_id,game_name',
            'order'  => 'game_type_id,dow_num desc',
        );
        $user = A('User','Event');
        $list=$user->user_joins($model,$limit);*/
         $sql="SELECT a.id,a.game_name,a.icon game_icon,a.game_size,a.game_type_id,a.dow_num,a.and_dow_address,a.introduction
                FROM tab_game AS a
           LEFT JOIN tab_game AS b ON a.game_type_id = b.game_type_id AND a.id < b.id
            GROUP BY a.id,a.game_type_id
              HAVING COUNT(b.id) < 3
            ORDER BY a.id DESC";

        $Model = new \Think\Model();
        $list=$Model->query($sql); 

        $_gameTypeArr=array();  
        foreach ($class as $k => $v) {
            $_gameTypeArr[$v['id']] = $v;
            $_gameTypeArr[$v['id']]['gamelist'] = array();
        }
        foreach ($list as $lk=>$lv){ 
            $lv['game_icon'] ='http://'.$_SERVER['HTTP_HOST']. get_cover($lv['game_icon'],'path');
            array_push($_gameTypeArr[$lv['game_type_id']]['gamelist'], $lv);

        }
                echo json_encode($_gameTypeArr);

    }

    //开测表
    public function open_test_game($limit=0)
    {
        $map['category']=array("gt",0);
         $model = array(
            'm_name' => 'game',//category
            'field'=>'tab_game.id,game_name,icon,game_size,game_type_id,and_dow_address,FROM_UNIXTIME(tab_game.create_time,"%m-%d %h:%i") as time,introduction',
            'map'    => $map, 
           /* 'join'=>'tab_game_info on tab_game_info.id=tab_game.id',*/
            'icon'=>'icon',
            'group'=>'game_type_id,game_name',
        );
        $user = A('User','Event');
        $list=$user->user_join($model,$limit);
        foreach ($list as $key => $value) {            
            $list[$key]['category']=get_cname($value['category']);
           $list[$key]['and_dow_address']="http://".$_SERVER['HTTP_HOST'].$value['and_dow_address'];
        }
        echo json_encode(array("list"=>$list));
    }

//开服表
    public function open_server_game($limit=0)
    {
         $model = array(
            'm_name' => 'game',//category
            'field'=>'tab_game.id,tab_game.game_name,tab_server.server_name,tab_game.icon,game_size,game_type_id,and_dow_address,FROM_UNIXTIME(tab_server.create_time,"%m-%d %h:%i") as time,introduction',
            'map'    => $map, 
            'join'=>'tab_server on tab_server.game_id=tab_game.id',
            /*'joins'=>'tab_game_info on tab_game.id=tab_game_info.id',*/
            'icon'=>'icon',
            'group'=>'game_type_id,tab_game.game_name',
            'order'  => 'tab_server.create_time desc',
        );
        $user = A('User','Event');
        $list=$user->user_join($model,$limit);
        /*foreach ($list as $key => $value) {            
            $list[$key]['category']=get_cname($value['category']);
        }*/
        echo json_encode(array("list"=>$list));
    }


     //轮换图片获取
    public function rotation_img(){
        $model = M("adv","tab_");
        $data  = $model->field("data,url")->where("pos_id=3")->order("create_time desc")->limit(4)->select();
        foreach ($data as $key => $value) {
            $data[$key]['data'] = 'http://'.$_SERVER['HTTP_HOST'].get_cover($value['data'],'path');
            
        }
        echo json_encode(array("data"=>$data)) ;
    }

    //礼包推荐 BuZhaoHe <616731538@qq.com> 2016.6.17
    public function gift_recommendation()
    {
        //根据礼包推荐状态分类，每组取3条数据
        $sql="SELECT a.id gift_id,a.recommend_status,a.giftbag_name,a.game_id,a.novice,c.icon,c.game_name
                FROM tab_giftbag AS a
           LEFT JOIN tab_giftbag AS b ON a.recommend_status = b.recommend_status AND a.id < b.id
           LEFT JOIN tab_game AS c ON a.game_id=c.id
               WHERE a.recommend_status != 0
            GROUP BY a.id,a.recommend_status
              HAVING COUNT(b.id) < 3
            ORDER BY a.recommend_status DESC";

        $Model = new \Think\Model();
        $data=$Model->query($sql); 

        if($data)
        {    
              #根据安卓开发人员需求，将结果以特定形式的三维数组返回
              foreach ($data as $k => $v) {
                 $data[$k]['icon'] ='http://'.$_SERVER['HTTP_HOST']. get_cover($data[$k]['icon'],'path');
                 $len=explode(",",$data[$k]['novice']);
                 $data[$k]['novice']=count($len);

                if($list[$v['giftbag_name']] == '')
                {
                    $list[$v['recommend_status']]['giftbag_name']=$v['recommend_status'];
                }
                    $list[$v['recommend_status']]['gamelist'][]=$data[$k];
           }
          echo json_encode(array("status"=>1,"msg"=>$list));
        } 
        else echo json_encode(array("status"=>-1,"msg"=>"参数有误"));

    } 

    //关于我们 BuZhaoHe <616731538@qq.com> 
    public function about_us(){
        $list=array(
            'qq'=>C('APP_QQ'),
            'weixin'=>C('APP_WEIXIN'),
            'qq_group'=>C('APP_QQ_GROUP'),
            'network'=>C('APP_NETWORK'),
            'icon'=>C('APP_ICON'),
            'version'=>C('APP_VERSION'),
            'app_download'=>C('APP_DOWNLOAD'),
            'app_name'=>C('APP_NAME'),
            'app_welcome'=>C('APP_SET_COVER'),
            'about_ico'=>C('ABOUT_ICO'),
            );
        $list['app_welcome']='http://'.$_SERVER['HTTP_HOST']. get_cover($list['app_welcome'],'path');
        $list['about_ico']='http://'.$_SERVER['HTTP_HOST']. get_cover($list['about_ico'],'path');
        if($list)echo json_encode(array("status"=>1,"msg"=>$list));
        else echo json_encode(array("status"=>-1,"msg"=>"参数有误"));

    }
    //所有游戏,礼包概况统计展示 BuZhaoHe 616731538@qq.com
    public function game_gift_listAll($game_name="",$limit=0){
/*        
        $sql="SELECT count(a.id) gift_count,a.game_id,a.giftbag_type,a.game_name,b.icon
                FROM tab_giftbag AS a
           LEFT JOIN tab_game AS b ON a.game_id=b.id WHERE 1";
*/
        $where['a.game_name']=array('LIKE','%'.$game_name.'%');
        $where['a.status']=1;

        $Model=M('giftbag','tab_');
        $data=$Model->alias('a')
                    ->field('count(a.id) gift_count,a.game_name,a.game_id,b.icon')
                    ->where($where)
                    ->group('a.game_id')
                    ->limit($limit*10,10)
                    ->join('tab_game b ON b.id = a.game_id')
                    ->select();
        if($data)
        {    
              foreach ($data as $k => $v) {
                 $data[$k]['icon'] ='http://'.$_SERVER['HTTP_HOST']. get_cover($data[$k]['icon'],'path');
              }
          echo json_encode(array("status"=>1,"msg"=>$data));
        } 
        else echo json_encode(array("status"=>-1,"msg"=>"没有数据"));

    }
    //单个游戏所属所有礼包
    public function game_gift_list($game_id="",$limit=0){

        $sql="SELECT a.id gift_id,a.game_id,a.giftbag_type,a.game_name,a.giftbag_name,a.desribe,a.novice,b.icon,b.game_size
                FROM tab_giftbag a
           LEFT JOIN tab_game b ON a.game_id = b.id
               WHERE a.status = 1 AND a.game_id = %d ";
        $limit=$limit*12;     
        $sql.="ORDER BY a.giftbag_type DESC LIMIT %d,12";
        $Model = new \Think\Model();
        $data=$Model->query($sql,$game_id,$limit);
        if($data)
        {    
              foreach ($data as $k => $v) {
                 $data[$k]['icon'] ='http://'.$_SERVER['HTTP_HOST']. get_cover($data[$k]['icon'],'path');
                 $len=explode(",",$data[$k]['novice']);
                 $data[$k]['novice']=count($len);
              }
            $game=array(
                'game_id'=>$data[0]['game_id'],
                'game_name'=>$data[0]['game_name'],
                'gift_count'=>count($data),
                'game_size'=>$data[0]['game_size'],
                'game_icon'=>$data[0]['icon'],
            );
          echo json_encode(array("status"=>1,"game"=>$game,"msg"=>$data));
        } 
        else echo json_encode(array("status"=>-1,"msg"=>"没有数据"));

    }



}