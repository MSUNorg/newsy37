<?php
namespace Media\Controller;
use Think\Controller;
/** 
* 父类控制器 
* lwx 
*/
class BaseController extends Controller {   

    protected function _initialize(){
        /* 读取站点配置 */
        $config = api('Config/lists');
        C($config); //添加配置
        $this->assign('newsgame',D('Game')->game_recommend_limt(3));
        $this->assign('recommend',D('Game')->game_recommend_limt(2));
    }

    /* 空操作，用于输出404页面 */
	public function _empty(){
		$this->redirect('Index/index');
	}
    
	public function __construct() {
		parent::__construct();
        $Game = D('Game');
	}
    
    public function qrcode($url='pc.vlcms.com',$level=3,$size=4){

		Vendor('phpqrcode.phpqrcode');

		$errorCorrectionLevel =intval($level) ;//容错级别 

		$matrixPointSize = intval($size);//生成图片大小 

		$url = base64_decode($url);

		//生成二维码图片 

		$object = new \QRcode();

		echo $object->png($url, false, $errorCorrectionLevel, $matrixPointSize, 2);   

	}

	/**
     * 显示指定模型列表数据
     * @param  String $model 模型标识
     * @author zxc <zuojiazi@vip.qq.com>
     */
    public function lists($model = null, $p = 0,$extend = array()){
        $page = intval($p);
        $page = $page ? $page : 1; //默认显示第一页数据

        // 过滤重复字段信息
        $fields =   array_unique($fields);
        // 关键字搜索
        $map	=	$extend['map'];
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
        $row    = empty($model['list_row']) ? 15 : $model['list_row'];

        if($model['need_pk']){
            in_array('id', $fields) || array_push($fields, 'id');
        }
        
        $name = $model['name'];
        $data = D($name)
            /* 查询指定字段，不指定则查询所有字段 */
            ->field(empty($fields) ? true : $fields)
            // 查询条件
            ->where($map)
            /* 默认通过id逆序排列 */
            ->order(empty($map['order'])?"id desc":$map['order'])
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
        //$data   =   $this->parseDocumentList($data,$model['id']);
        $this->assign('model', $model);
        $this->assign('list_grids', $grids);
        $this->assign('list_data', $data);
        $this->meta_title = $model['title'].'列表';
        $this->display($model['template_list']);
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

}