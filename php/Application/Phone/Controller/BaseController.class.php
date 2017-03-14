<?php
namespace Phone\Controller;
use Think\Controller;
/** 
* 父类控制器 
* lwx 
*/
class BaseController extends Controller {   
    /* 空操作，用于输出404页面 */
	public function _empty(){
		$this->redirect('Index/index');
	}
    
	protected function _initialize(){
        /* 读取站点配置 */
        $config = api('WebConfig/lists');
        C($config); //添加配置
		
		$up = D("User");
		$user = $up->isLogin();
		if($user){
			$this->assign('user',$user);
		}
    }
	
	public function __construct() {
		parent::__construct();
		
		//最新游戏
		$game['model']="Game";
		$game['where']="g.game_status=1 and g.recommend_status=3";
		$game['order']="g.sort desc,g.id desc";
		$newsgame=$this->showlist($game,10);
		//推荐游戏
		$game['field']="g.*,gi.introduction";
		$game['where']="g.game_status=1 and g.recommend_status=1";
		$game['join']="__GAME_INFO__ as gi on(gi.id= g.id)";
		$game['joinnum']="left";
		$recommend=$this->showlist($game,10);
		if ($recommend)
		foreach($recommend as $k=> $r) {
			$recommend[$k]['introduction']=mb_strcut(strip_tags($r['introduction']),0,50,'utf-8');
		}
		$this->assign("newsgame",$newsgame);
		$this->assign("recommend",$recommend);
        
		//logo
		$img=M("Adpic")->where("mark='logo'")->find();
		$logo=get_cover($img['cover'],'path');
		$this->assign("logo",$logo);
		
	}
	
	// 搜索
	public function search($model,$p){
        // 关键字搜索
        $map	=	array();
		$sk = $model['search_key']?$model['search_key']:'title';
		$sn = explode(",",$model['search_isnum']);
        $fields = $key	= explode(",",$sk);	
        if(isset($model['search_value'])){
			foreach($key as $k) {
				if (in_array($k,$sn)) {
					$str = $model['search_value'];
					eval("\$result = get_".$k."_code(\"".$str."\",'like');");
					if ($result)
						$map[$k] = "$result";
				} else if(strpos($k,'_id') && is_numeric($model['search_value'])){
					$map[$k]   =   array('like',''.$model['search_value'].'');
				} else {
					$map[$k]	=	array('like','%'.$model['search_value'].'%');
				}
			}
			$map['_logic']=$model['search_logic'];
			unset($model['search_value']);
        }
		
		if (isset($model['where'])) {
			$where['_complex']=$map;
			$where['_string']=$model['where'];
			$model['where']=$where;
		} else {
			$model['where']['_complex']=$map;
		}	
		$data = $this->getpdatas($model,$p,true);
		if ($data['count']) {
			return $data;
		} else 
			return false;
		
	}

	// 列表显示
	public function plists($model,$p){
		$data = $this->getpdatas($model,$p,true);
        $this->assign('plist_data', $data['list']);		
	}
	
	public function getlists($model) {
		$data = $this->getdatas($model,true);
		return array('list'=>$data['list'],'total'=>$data['total']);
	}
	
	public function getlist($model) {
		$data = $this->getdatas($model,true);
		return array('list'=>$data['list'],'count'=>$data['count']);
	}
	
	// 多条数据
	public function showlist($model,$num=10) {
		if ($num==-1) $model['limit']=""; else $model['limit']=$num;		
		return $this->getdatas($model);
	}
	
	// 详情数据
	public function pdetail($model) {
		$model['limit']=1;
		$list = $this->getdatas($model);
		return $list[0];
	}
	
	// 列表数据处理
	private function getpdatas($model,$p,$flag=false) {
		$page = intval($p);
        $page = $page ? $page : 1; //默认显示第一页数据	
		$mod = $model;
		//获取模型信息
        $model = M('Model')->getByName($model["model"]);
        $model || $this->error('模型不存在！');
		
		if (isset($mod['limit']))
			$row=$mod['limit'];
        else 
			$row=$mod['limit']    = empty($model['list_row']) ? 10 : $model['list_row'];
        //读取模型数据列表
        $name = parse_name(get_table_name($model['id']), true);
        $mod['model']=$name;		
		$mod['page']=$page;
		$data = $mod = $this->getdatas($mod,$flag);
		if($flag) {
			$count = $mod['count'];
			//分页
			if($count > $row){
				$page = new \Think\Page($count, $row);
				$page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
				$this->assign('_page', $page->show());
			}		
			$this->assign('model', $model);
			$this->meta_title = $model['title'].'列表';
		}
		return $data;		
	}
	
	// 数据查询
	private function getdatas($model,$flag=false) {
		if(isset($model['model'])) $name = $model['model']; else return null;
		if (isset($model['join'])) {
			$join = $model['join'];
			$joinnum = isset($model['joinnum'])? $model['joinnum'] : 'INNER';
		} else {
			$join=$joinnum="";
		}
		if (isset($model['join1'])) {
			$join1 = $model['join1'];
			$joinnum1 = isset($model['joinnum1'])? $model['joinnum1'] : 'INNER';
		} else {
			$join1=$joinnum1="";
		}
		if (isset($model['join2'])) {
			$join2 = $model['join2'];
			$joinnum2 = isset($model['joinnum2'])? $model['joinnum2'] : 'INNER';
		} else {
			$join2=$joinnum2="";
		}
		$m = substr($name,0,1);
		$field = isset($model['field'])?$model['field']:"*";
		$prefix = isset($model['prefix'])?$model['prefix']:"";
		$table = isset($model['table'])?$model['table']:"__".strtoupper($name)."__ as ".strtolower($m)." ";
		$order = isset($model['order'])?$model['order']:" ".strtolower($m).".id DESC ";
		$where = isset($model['where'])?$model['where']:"";
		$group = isset($model['group'])?$model['group']:"";
		$limit = isset($model['limit'])?$model['limit']:"";
		$page = isset($model['page'])?$model['page']:"";
		if ($prefix)
			$mo = M($name,$prefix);
		else 
			$mo = D($name);
		if ($page) {
			$list = $mo->field($field)->table($table)
			->join($join,$joinnum)
			->join($join1,$joinnum1)
			->join($join2,$joinnum2)
			->where($where)
			->group($group)
			->order($order)
			->limit($limit)
			->page($page)->select();
		} else {
			$list = $mo->field($field)->table($table)
			->join($join,$joinnum)
			->join($join1,$joinnum1)
			->join($join2,$joinnum2)
			->where($where)
			->group($group)
			->order($order)
			->limit($limit)
			->select();			
		}
		if ($flag) {
			$count = $mo->table($table)->join($join,$joinnum)
			->join($join1,$joinnum1)
			->join($join2,$joinnum2)
			->where($where)->count();
			$totalpage = intval(($count-1)/$model['limit']+1);
			return array('list'=>$list,'count'=>$count,'total'=>$totalpage);
		}
		return $list;
	}
	
	
}