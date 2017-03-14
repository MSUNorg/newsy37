<?php
namespace Mobile\Controller;
use Think\Controller;

/**
* 首页
*/
class BaseController extends Controller {
	protected function _initialize(){
        /* 读取站点配置 */
        $config = api('Config/lists');
        C($config); //添加配置
    }
	public function __construct() {
		parent::__construct();	
		// 选择游戏
		$xzgame = $this->showlist(array('model'=>'Game','where'=>'game_status=1 ','order'=>'game_score desc'),6);
		$this->assign('xzgame',$xzgame);
		
		$user = D("User")->isLogin();
		if (!empty($user) && $user['status'] == 1) {
			$this->assign("user",$user);
		} else {
			$this->assign("user",null);
		}
	}
	
    public function lists($model,$p){
        $page = intval($p);
        $page = $page ? $page : 1; //默认显示第一页数据
		$fields = array($model['search_key'],$model['search_type']);
        //获取模型信息
        $model = M('Model')->getByName($model["model"]);
        $model || $this->error('模型不存在！');
        // 关键字搜索
        $map	=	array();
        $key	=	$model['search_key']?$model['search_key']:'title';
        if(isset($_REQUEST[$key])){
            $map[$key]	=	array('like','%'.$_GET[$key].'%');
            unset($_REQUEST[$key]);
        }
        // 条件搜索
        foreach($_REQUEST as $name=>$val){
            if(in_array($name,$fields)){
                $map[$name]	=	$val;
            }
        }
        $row    = empty($model['list_row']) ? 10 : $model['list_row'];
        //读取模型数据列表
        in_array('id', $fields) || array_push($fields, 'id');
        $name = parse_name(get_table_name($model['id']), true);
        $data = M($name)
        /* 查询指定字段，不指定则查询所有字段 */
        ->field(true)
        // 查询条件
        ->where($map)
        /* 默认通过id逆序排列 */
        ->order('id DESC')
        /* 数据分页 */
        ->page($page, $row)
        /* 执行查询 */
        ->select();
        /* 查询记录总数 */
        $count = M($name)->where($map)->count();
        //分页
        if($count > $row){
            $page = new \Think\Page($count, $row);
            $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
            $this->assign('_page', $page->show());
        }
		
		
        $this->assign('model', $model);
        $this->assign('list_data', $data);
        $this->meta_title = $model['title'].'列表';
	}
	
	public function search($model,$p){
        $page = intval($p);
        $page = $page ? $page : 1; //默认显示第一页数据
        // 关键字搜索
        $map	=	array();
        $fields = $key	=	$model['search_key']?$model['search_key']:'title';
        if(isset($model['search_value'])){
            $map[$key]	=	array('like','%'.$model['search_value'].'%');
			unset($model['search_value']);
        } 
		$join = "";
		$order = "id desc";
		$field = true;
        //获取模型信息
        $model = M('Model')->getByName($model["model"]);
        $model || $this->error('模型不存在！');
        // 条件搜索
        foreach($_REQUEST as $name=>$val){
            if(in_array($name,$fields)){
                $map[$name]	=	$val;
            }
        }
        $row    = empty($model['list_row']) ? 10 : $model['list_row'];
        //读取模型数据列表
        in_array('id', $fields) || array_push($fields, 'id');
        $name = parse_name(get_table_name($model['id']), true);
        $data = D($name)
        /* 查询指定字段，不指定则查询所有字段 */
        ->field($field)
		->join($join)
        // 查询条件
        ->where($map)
        /* 默认通过id逆序排列 */
        ->order($order)
        /* 数据分页 */
        ->page($page, $row)
        /* 执行查询 */
        ->select();
        /* 查询记录总数 */
        $count = D($name)->where($map)->count();
        //分页
        if($count > $row){
            $page = new \Think\Page($count, $row);
            $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
            $this->assign('_page', $page->show());
        }
		
        $this->assign('model', $model);        
        $this->meta_title = $model['title'].'列表';
		if ($count) {
			$total = intval(($count-1)/$row+1);
			return array('list'=>$data,'total'=>$total);
		} else 
			return false;
		
	}

	public function pagelists($model,$p){
        $page = intval($p);
        $page = $page ? $page : 1; //默认显示第一页数据	
 
		if (empty($model['where']))			
			$map = " ";
		else $map = $model['where'];
		
		if (empty($model['order']))			
			$mapo = " id DESC ";
		else $mapo = $model['order'];
        //获取模型信息
        $model = M('Model')->getByName($model["model"]);
        $model || $this->error('模型不存在！');
        $row    = empty($model['list_row']) ? 10 : $model['list_row'];
        //读取模型数据列表
        $name = parse_name(get_table_name($model['id']), true);
        $data = M($name)
        /* 查询指定字段，不指定则查询所有字段 */
        ->field(true)
        // 查询条件
        ->where($map)
        /* 默认通过id逆序排列 */
        ->order($mapo)
        /* 数据分页 */
        ->page($page, $row)
        /* 执行查询 */
        ->select();
        /* 查询记录总数 */
        $count = M($name)->where($map)->count();
        //分页
        if($count > $row){
            $page = new \Think\Page($count, $row);
            $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
            $this->assign('_page', $page->show());
        }
		
        $this->assign('model', $model);
        $this->assign('plist_data', $data);
        $this->meta_title = $model['title'].'列表';
	}
	
	public function plists($model,$p){
        $page = intval($p);
        $page = $page ? $page : 1; //默认显示第一页数据	
		$map=array();
		$map[] = " 1 ";
		$tablename = C('DB_PREFIX').strtolower($model["model"]);
		$m = M($name)->query("SHOW COLUMNS FROM ".$tablename);
		foreach($m as $n) {
			$fields[]=$tablename.'.'.$n['Field'];
		}
		// 条件搜索
        foreach($_REQUEST as $name=>$val){
            if(in_array($tablename.'.'.$name,$fields)){
                $map[$tablename.'.'.$name]	=	$val;
            }
        }

		if (empty($model['order']))			
			$mapo = $tablename.".id DESC ";
		else $mapo = $tablename.'.'.$model['order'];
		if (empty($model['join']))			
			$mapj = null;
		else {
			$mapj = $model['join'];
			$mapd=$model['direction'];
		}
		if (!empty($model['field'])) {
			$f = explode(',',$model['field']);
			foreach($f as $i) {
				$fields[]=$i;
			}
		}
        //获取模型信息
        $model = M('Model')->getByName($model["model"]);
        $model || $this->error('模型不存在！');
        $row    = empty($model['list_row']) ? 10 : $model['list_row'];
        //读取模型数据列表
        $name = parse_name(get_table_name($model['id']), true);
		if (empty($mapj)) {
			$data = M($name)
			/* 查询指定字段，不指定则查询所有字段 */
			->field(true)
			// 查询条件
			->where($map)
			/* 默认通过id逆序排列 */
			->order($mapo)
			/* 数据分页 */
			->page($page, $row)
			/* 执行查询 */
			->select();
		
		} else {
			$data = M($name)
			->field($fields)
			->join($mapj,$mapd)
			->where($map)
			->order($mapo)
			->page($page, $row)
			->select();
		}
			/* 查询记录总数 */
        $count = M($name)->where($map)->count();
        //分页
        if($count > $row){
            $page = new \Think\Page($count, $row);
            $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
            $this->assign('_page', $page->show());
        }
		
        $this->assign('model', $model);
        $this->assign('plist_data', $data);
        $this->meta_title = $model['title'].'列表';
	}
	
	public function getlists($model) {
		if($model['field']) $field = $model['field'];
		else $field = true;
		$mo = D($model['model']);
		$list = $mo->field($field) ->join($model['joins'])->where($model['where'])
		->order($model['order'])->limit($model['limit'])
		->page($model['page'])->select();
		$count = $mo->where($model['where'])->count();
		$totalpage = intval(($count-1)/$model['limit']+1);	
		return array('list'=>$list,'total'=>$totalpage);
	}
	
	public function showlist($model,$num=10) {
		if ($num==-1) $num="";
		if($model['field']) $field = $model['field'];
		else $field = true;
		$mo = D($model['model']);
		$list = $mo->field($field) ->join($model['joins'])
		->where($model['where'])->order($model['order'])
		->limit($num)->select();
		return $list;
	}
	
	public function detail($model) {
		$mo = D($model['model']);
		$data = $mo->where($model['dwhere'])->order($model['dorder'])->find();
		return $data;
	}
}