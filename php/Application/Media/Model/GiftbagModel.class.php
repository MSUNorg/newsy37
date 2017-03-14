<?php
namespace Media\Model;
use Think\Model;

/**
 * 文档基础模型
 */
class GiftbagModel extends Model{

    
    /* 自动验证规则 */
    protected $_validate = array(
        array('giftbag_name', 'require', '礼包名称不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('giftbag_name', '1,30', '礼包名称不能超过30个字符', self::VALUE_VALIDATE, 'length', self::MODEL_BOTH),
        array('giftbag_type', 'require', '请选择礼包类型', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('start_time', 'require', '开始时间不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('end_time', 'require', '结束时间不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
    );

    /* 自动完成规则 */
    protected $_auto = array(
        array('create_time', 'getCreateTime', self::MODEL_BOTH,'callback'),
        array('area_num', 0, self::MODEL_BOTH),
        array('start_time', 'strtotime', self::MODEL_BOTH, 'function'),
        array('end_time', 'strtotime', self::MODEL_BOTH, 'function'),
        //array('game_score', 0, self::MODEL_BOTH),
    );

    //protected $this->$tablePrefix = 'tab_'; 
    /**
     * 构造函数
     * @param string $name 模型名称
     * @param string $tablePrefix 表前缀
     * @param mixed $connection 数据库连接信息
     */
    public function __construct($name = '', $tablePrefix = '', $connection = '') {
        /* 设置默认的表前缀 */
        $this->tablePrefix ='tab_';
        /* 执行构造方法 */
        parent::__construct($name, $tablePrefix, $connection);
    }

    
    

    /**
     * 创建时间不写则取当前时间
     * @return int 时间戳
     * @author huajie <banhuajie@163.com>
     */
    protected function getCreateTime(){
        $create_time    =   I('post.create_time');
        return $create_time?strtotime($create_time):NOW_TIME;
    }


    
    /**
     * 生成不重复的name标识
     * @author huajie <banhuajie@163.com>
     */
    private function generateName(){
        $str = 'abcdefghijklmnopqrstuvwxyz0123456789';	//源字符串
        $min = 10;
        $max = 39;
        $name = false;
        while (true){
            $length = rand($min, $max);	//生成的标识长度
            $name = substr(str_shuffle(substr($str,0,26)), 0, 1);	//第一个字母
            $name .= substr(str_shuffle($str), 0, $length);
            //检查是否已存在
            $res = $this->getFieldByName($name, 'id');
            if(!$res){
                break;
            }
        }
        return $name;
    }

    /**
    *礼包列表
    */
    public function gift_list_limt($category, $order = '`id` DESC', $status = 1, $field = true,$limit=10){
        $map = $this->listMap($category, $status);
        return $this->field('tab_giftbag.*,tab_game.icon,tab_game.cover')
                    ->join("LEFT JOIN tab_game ON tab_giftbag.game_id = tab_game.id")
                    ->where($map)
                    ->order($order)
                    ->limit($limit)
                    ->select();
    }
    //全部礼包
    public function gift_list_limt_clone($category, $order = '`id` DESC', $status = 1, $field = true,$limit=40){
     /*   if($category != 0){$map = $this->listMap($category, $status);}*/
        $where['tab_giftbag.status']=$status;
        if(!is_null(I('category')) AND I('category') != 0)$where['game_type_id']=$category;
        if(I('nameid') != NULL AND I('nameid') !=-1)$where['short']=I('nameid');
        if(!is_null(I('id')) AND I('id')!= 0) $where['tab_game.id']=I('id');

    /*   echo  $map['tab_game.id']=I('nameid');*/
         $data=$this->field('tab_giftbag.*,tab_game.icon,tab_game.cover')
                    ->join("LEFT JOIN tab_game ON tab_giftbag.game_id = tab_game.id")
                    ->where($where)
                    ->order($order)
                    ->limit($limit)
                    ->select();
                   /* print_r($data);*/
      /*  echo $this->getLastSql();*/
        return $data;           
    }

    /**
    *推荐礼包信息
    */
    public function gift_recommend_limt($recommend_status, $order = '`id` DESC', $status = 1, $field = true,$limit=10){
        $map['status'] = $status;
        $map['tab_giftbag.recommend_status'] = $recommend_status;
        return $this->field('tab_giftbag.*,tab_game.icon,tab_game.cover')
                    ->join("LEFT JOIN tab_game ON tab_giftbag.game_id = tab_game.id")
                    ->where($map)
                    ->order($order)
                    ->limit($limit)
                    ->select();          
    }
    /**
     * 设置where查询条件
     * @param  number  $category 游戏分类ID
     * @param  number  $pos      推荐位
     * @param  integer $status   状态
     * @return array             查询条件
     */
    private function listMap($category, $status = 1){
        /* 设置状态 */
        $map = array('status' => $status);

        /* 设置分类 */
        if(!is_null($category)){
            if(is_numeric($category)){
                $map['game_type_id'] = $category;
            } else {
                $map['game_type_id'] = array('in', str2arr($category));
            }
        }
        return $map;
    }

//===================
    /**
     * 礼包详情
     * @author 卜昭鹤 <2016.6.29>
     * @param  $data['receive']       已领取礼包数
     * @param  $data['count_novice']  礼包总数量
     * @return array                   
     */ 
    public function gift_detail() 
    {    
        $data=$this->field('a.*,b.icon')
                   ->table('tab_giftbag a')
                   ->join("LEFT JOIN tab_game b ON a.game_id = b.id")
                   ->where(array('a.id'=>I('id')))
                   ->order('a.create_time desc')
                   ->find();
        //计算礼包总数及剩余百分比
        if(!empty($data['novice']))     
            $data['novice']=count(explode(",",$data['novice'])); 
        else
            $data['novice']=0;
        $data['receive']=$this->table('tab_gift_record')->where(array('gift_id'=>$data['id']))->count();
        $data['count_novice']=$data['novice']+ $data['receive'];
        $data['%']=(int)$data['%']=($data['novice']/$data['count_novice'])*100;
        return $data;           
    }

    /**
     * 礼包横幅
     * @author 卜昭鹤 <2016.6.27>
     * @param  GIFT [常量]  礼包中心宣传位标识,于后台广告管理及礼包控制器中配置
     * @return array  
     */ 
    public function gift_banner() 
    {  
       $data = $this->field('a.title,a.url,a.data')
                    ->table('tab_adv a')
                    ->join("LEFT JOIN tab_adv_pos b ON a.pos_id = b.id")
                    ->order('b.title')
                    ->limit(7)
                    ->where(array('a.status'=>1,'b.name'=>GIFT))
                    ->select();
        return $data;            
    } 

     /**
     * 我的礼包
     * @author 卜昭鹤 <2016.6.27>
     * @return  array  
     */ 
    public function my_gift($id) 
    {    
       return $this->field('a.*,b.icon')
                   ->table('tab_gift_record a')
                   ->join("LEFT JOIN tab_game b ON a.game_id = b.id")
                   ->where(array('a.user_id'=>$id))
                   ->order('a.create_time desc')
                   ->limit(20)
                   ->select();        
    }

    /**
     * 大家都在抢
     * @author 卜昭鹤 <2016.6.27>
     * @return  array  
     */   
    public function everybody() 
    {    
          $data = $this->field('a.gift_id,a.gift_name,a.game_name,a.user_account account,b.icon')
                       ->table('tab_gift_record a')
                       ->join("LEFT JOIN tab_game b ON a.game_id = b.id")
                       ->order('a.create_time desc')
                       ->limit('30')
                       ->select();
          foreach($data as $k=>$q) {
                $accs = substr($q['account'],0,2);
                $acce = substr($q['account'],-2);
                $data[$k]['account']=$accs."****".$acce;
          }
          return $data;
    }
}