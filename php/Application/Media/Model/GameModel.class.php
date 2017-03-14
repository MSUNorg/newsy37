<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: huajie <banhuajie@163.com>
// +----------------------------------------------------------------------

namespace Media\Model;
use Think\Model;

/**
 * 文档基础模型
 */
class GameModel extends Model{

    

    /* 自动验证规则 */
    protected $_validate = array(
        array('game_name',  'require', '游戏名称不能为空',         self::MUST_VALIDATE,  'regex',  self::MODEL_BOTH),
        array('game_name',  '1,30',    '游戏名称不能超过30个字符', self::VALUE_VALIDATE, 'length', self::MODEL_BOTH),
        array('game_appid', 'require', '游戏APPID不能为空',        self::MUST_VALIDATE,  'regex',  self::MODEL_BOTH),

        array('discount',         '/^(?!0+(?:\.0+)?$)\d+(?:\.\d{1,2})?$/',           '游戏折扣输入不正确',             self::VALUE_VALIDATE,  'regex',  self::MODEL_BOTH),
        array('game_coin_ration', '/^[0-9]*$/',             '游戏比例必须是数字',             self::VALUE_VALIDATE,  'regex',  self::MODEL_BOTH),
        array('sort',             '/^[0-9]*$/',             '排序必须是数字',                 self::VALUE_VALIDATE,  'regex',  self::MODEL_BOTH),
        array('game_score',       '/^(\d(\.\d)?|10)$/',     '游戏评分输入格式不正确',         self::VALUE_VALIDATE,  'regex',  self::MODEL_BOTH),
        array('recommend_level',  '/^(\d(\.\d)?|10)$/',     '推荐指数输入格式不正确',         self::VALUE_VALIDATE,  'regex',  self::MODEL_BOTH),
         array('discount','0,4', '折扣输入错误',  self::VALUE_VALIDATE, 'length', self::MODEL_BOTH),
         // array('discount',array(1,2,3,4,5,6,7,8,9,10),'值的范围不正确！',2,'in'),
        array('discount',         '/^[1-9](\.\d+)?$/',           '代充折扣错误',             self::VALUE_VALIDATE,  'regex',  self::MODEL_BOTH),

    );

    /* 自动完成规则 */
    protected $_auto = array(
        array('create_time',       'getCreateTime',         self::MODEL_INSERT,  'callback'),
        array('recommend_level',   0,                       self::MODEL_INSERT),
        array('game_score',        0,                       self::MODEL_INSERT),
        array('discount',          0,                       self::MODEL_INSERT),
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
     * 获取详情页数据
     * @param  integer $id 文档ID
     * @return array       详细数据
     */
    public function detail($id){
        /* 获取基础数据 */
        $info = $this->field(true)->find($id);
        if(!(is_array($info) || 1 !== $info['status'])){
            $this->error = '游戏被禁用或已删除！';
            return false;
        }
        /* 获取模型数据 */
        $logic  = new SetLogic();
        $detail = $logic->detail($id); //获取指定ID的数据
        if(!$detail){
            $this->error = $logic->getError();
            return false;
        }
        $info = array_merge( $detail,$info);

        return $info;
    }

    /**
     * 新增或更新一个游戏
     * @param array  $data 手动传入的数据
     * @return boolean fasle 失败 ， int  成功 返回完整的数据
     * @author 王贺 
     */
    public function update($data = null){
        /* 获取数据对象 */
        $data = $this->token(false)->create($data);
        if(empty($data)){
            return false;
        }

        /* 添加或新增基础内容 */
        if(empty($data['id'])){ //新增数据
            $id = $this->add(); //添加基础内容
            if(!$id){
                $this->error = '新增基础内容出错！';
                return false;
            }
        } else { //更新数据
            $status = $this->save(); //更新基础内容
            if(false === $status){
                $this->error = '更新基础内容出错！';
                return false;
            }
        }
         // 添加或新增扩展内容
        $logic = $this->logic('Set');
        $logic->checkModelAttr(5);
        if(!$logic->update($id)){
            if(isset($id)){ //新增失败，删除基础数据
                $this->delete($id);
            }
            $this->error = $logic->getError();
            return false;
        }
        return $data;
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
     * 获取扩展模型对象
     * @param  integer $model 模型编号
     * @return object         模型对象
     */
    private function logic($model){
        $name  = $model;//parse_name(get_document_model($model, 'name'), 1);
        $class = is_file(MODULE_PATH . 'Logic/' . $name . 'Logic' . EXT) ? $name : 'Base';
        $class = MODULE_NAME . '\\Logic\\' . $class . 'Logic';
        return new $class($name);
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

    private function game_list($category, $order = '`id` DESC', $status = 1, $field = true){

    }

    /**
    *游戏列表
    */
    public function game_list_limt($category, $order = '`id` DESC', $status = 1, $field = true,$limit=10){
        $map = $this->listMap($category, $status);
        if(I('themeid')) $map['category']=I('themeid');
        return $this->field($field)->where($map)->order($order)->limit($limit)->select();
    }


    /**
    *推荐游戏信息
    */
    public function game_recommend_limt($recommend_status, $order = '`id` DESC', $status = 1, $field = true,$limit=10){
        $map['status'] = $status;
        $map['recommend_status'] = $recommend_status;
        $data=$this->field($field)->where($map)->order($order)->limit($limit)->select();
        return $data;
    }   

    public function game_recommend_limts($recommend_status, $order = '`id` DESC', $status = 1, $field = true,$limit=12){

        $map['status'] = $status;
        $map['recommend_status'] = $recommend_status;
        $data=$this->field($field)->where($map)->order()->limit($limit)->select();
        return $data;
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

//=======================
    
    /**
     * AJAX动态获取游戏展示
     * @author 卜昭鹤 <2016.6.28>
     * @return array  
     */ 
    public function ajax_game()
    {
        $where=array('game_type_id'=>$_POST['type']);
        $data=$this->limit('20')->where($where)->select(); 

        foreach ($data as $k=> $v) {
            $html='<li><div class="game-center-icon">';    
            $html.="<a target='_blank' href='{:U('Game/f1?id=".$v['id'].")>";
            $html.="<img src='".$v['icon']."' alt='".$v['game_name']."'></a></div>";
            $html.='<div class="game-center-msg"><div class="game-center-msg-m1">'.$v['game_name'].'</div>
            <div class="game-center-msg-m2">'.$v['game_type'].'&nbsp;|&nbsp;'.$v['dow_num'].'次下载</div>';
            $html.='</li>';
            echo $html;
        }

    }

    /**
     * 游戏详情
     * @author 卜昭鹤 <2016.6.28>
     * @return array  
     */

    public function game_detail()
    {
        $data = $this->field(TRUE)->where(array('id'=>I('id')))->find($id);
/*        $data['and_dow_address']="http://".$_SERVER['HTTP_HOST'].$data['and_dow_address'];*/
        return $data;
    } 
    
    /**
     * 游戏搜索
     * @author 卜昭鹤 <2016.6.28>
     * @return array  
     */
    public function search_game()
    {     
        $where['game_name']=array('LIKE','%'.I('keyword').'%');
        $data['list']=$this->field()->where($where)->select();
        $data['count']=$this->where($where)->count();
        $data['kw']=I('keyword');
        return $data;
      /*  echo $this->getLastSql(); */       
    }    

    public function game_list_limt2($category, $order = '`id` DESC', $status = 1, $field = true,$limit=21){
        $where['status']=$status;
        if(isset($category) AND $category != 0) $where['game_type_id']=$category;
        if(I('themeid') != NULL AND I('themeid') != '0')$where['category']=I('themeid');
        $page=I('p')?I('p'):1;
        $count      = $this->where($where)->count();// 查询满足要求的总记录数
        $Pages       = new \Think\Page($count,$limit);// 实例化分页类 传入总记录数和每页显示的记录数
        $data['show']       = $Pages->show();// 分页显示输出*/
        $data['list']=$this->field($field)->where($where)->order($order)->page($page,$limit)->select();
/*        echo $this->getLastSql();*/
        return $data;
    }
    //换一组
    public function game_change($recommend_status=1,$order = '`sort` DESC', $status = 1, $field = true,$limit=0){
        $map['status'] = $status;
        $map['recommend_status'] = $recommend_status;
        $data=$this->field($field)->where($map)->order('sort desc')->page($limit,4)->select();
        return $data;


    }
}