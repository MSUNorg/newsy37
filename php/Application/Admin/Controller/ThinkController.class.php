<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: zxc <zuojiazi@vip.qq.com> <http://www.msun.com>
// +----------------------------------------------------------------------

namespace Admin\Controller;

/**
 * 模型数据管理控制器
 * @author zxc <zuojiazi@vip.qq.com>
 */
class ThinkController extends AdminController {

    /**
     * 显示指定模型列表数据
     * @param  String $model 模型标识
     * @author zxc <zuojiazi@vip.qq.com>
     */
    public function lists($model = null, $p = 0,$extend_map = array()){
        $model || $this->error('模型名标识必须！');
        $page = intval($p);
        $page = $page ? $page : 1; //默认显示第一页数据

        //获取模型信息
        
        $model = M('Model')->getByName($model);
        $model || $this->error('模型不存在！');
        //解析列表规则
        $fields = array();
        $grids  = preg_split('/[;\r\n]+/s', trim($model['list_grid']));
        foreach ($grids as &$value) {
        	if(trim($value) === ''){
        		continue;
        	}
            // 字段:标题:链接
            $val      = explode(':', $value);
            // 支持多个字段显示
            $field   = explode(',', $val[0]);
            $value    = array('field' => $field, 'title' => $val[1]);
            if(isset($val[2])){
                // 链接信息
                $value['href']	=	$val[2];
                // 搜索链接信息中的字段信息
                preg_replace_callback('/\[([a-z_]+)\]/', function($match) use(&$fields){$fields[]=$match[1];}, $value['href']);
            }
            if(strpos($val[1],'|')){
                // 显示格式定义
                list($value['title'],$value['format'])    =   explode('|',$val[1]);
            }
            foreach($field as $val){
                $array	=	explode('|',$val);
                $fields[] = $array[0];
            }
        }
        // 过滤重复字段信息
        $fields =   array_unique($fields);
        // 关键字搜索
        $map	=	$extend_map;
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

        //读取模型数据列表
        if($model['extend']){
            $name   = get_table_name($model['id']);
            $parent = get_table_name($model['extend']);
            $fix    = C("DB_PREFIX");

            $key = array_search('id', $fields);
            if(false === $key){
                array_push($fields, "{$fix}{$parent}.id as id");
            } else {
                $fields[$key] = "{$fix}{$parent}.id as id";
            }

            /* 查询记录数 */
            $count = D($parent)->join("INNER JOIN {$fix}{$name} ON {$fix}{$parent}.id = {$fix}{$name}.id")->where($map)->count();

            // 查询数据
            $data   = D($parent)
                ->join("INNER JOIN {$fix}{$name} ON {$fix}{$parent}.id = {$fix}{$name}.id")
                /* 查询指定字段，不指定则查询所有字段 */
                ->field(empty($fields) ? true : $fields)
                // 查询条件
                ->where($map)
                /* 默认通过id逆序排列 */
                ->order("{$fix}{$parent}.id DESC")
                /* 数据分页 */
                ->page($page, $row)
                /* 执行查询 */
                ->select();

        } else {
            if($model['need_pk']){
                in_array('id', $fields) || array_push($fields, 'id');
            }
            $name = parse_name(get_table_name($model['id']), true);
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
        }

        //分页
        if($count > $row){
            $page = new \Think\Page($count, $row);
            $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
            $this->assign('_page', $page->show());
        }
        // var_dump($model);exit;
        $data   =   $this->parseDocumentList($data,$model['id']);
        $this->assign('model', $model);
        $this->assign('list_grids', $grids);
        $this->assign('list_data', $data);
        $this->meta_title = $model['title'].'列表';
        $this->display($model['template_list']);
    }

    public function del($model = null, $ids=null){
        $model = M('Model')->find($model);
        $model || $this->error('模型不存在！');

        $ids = array_unique((array)I('ids',0));

        if ( empty($ids) ) {
            $this->error('请选择要操作的数据!');
        }

        $Model = D(get_table_name($model['id']));
        $map = array('id' => array('in', $ids) );
        if($Model->where($map)->delete()){
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }

    /**
     * 设置一条或者多条数据的状态
     * @author huajie <banhuajie@163.com>
     */
    public function setStatus($model='Document'){
        return parent::setStatus($model);
    }
    
    public function edit($model = null, $id = 0){
        //获取模型信息
        $model = D('Model')->find($model);
        $model || $this->error('模型不存在！');

        if(IS_POST){
            $Model  =   D(parse_name(get_table_name($model['id']),1));
            // 获取模型的字段信息
            $Model  =   $this->checkAttr($Model,$model['id']);
            if($Model->create() && $Model->save()){
                $this->success('保存'.$model['title'].'成功！', U('lists?model='.$model['name']));
            } else {
                $this->error($Model->getError());
            }
        } else {
            $fields     = get_model_attribute($model['id']);

            //获取数据
            $data       = D(get_table_name($model['id']))->find($id);
            $data || $this->error('数据不存在！');

            $this->assign('model', $model);
            $this->assign('fields', $fields);
            $this->assign('data', $data);
            $this->meta_title = '编辑'.$model['title'];
            $this->display($model['template_edit']?$model['template_edit']:'');
        }
    }

    public function add($model = null){
        //获取模型信息
        $model = M('Model')->where(array('status' => 1))->find($model);
        $model || $this->error('模型不存在！');
        if(IS_POST){
            $Model  =   D(parse_name(get_table_name($model['id']),1));
            // 获取模型的字段信息
            $Model  =   $this->checkAttr($Model,$model['id']);
            if($Model->create() && $Model->add()){
                $this->success('添加'.$model['title'].'成功！', U('lists?model='.$model['name']));
            } else {
                $this->error($Model->getError());
            }
        } else {
            $fields = get_model_attribute($model['id']);
            $this->assign('model', $model);
            $this->assign('fields', $fields);
            $this->meta_title = '新增'.$model['title'];
            $this->display($model['template_add']?$model['template_add']:'');
        }
    }

    public function remove($model = null,$model1=null, $ids=null,$prefix='tab_'){
        $model = M('Model')->find($model);
        $model || $this->error('模型不存在！');

        $ids = array_unique((array)I('request.ids',null));

        if ( empty($ids) ) {
            $this->error('请选择要操作的数据!');
        }

        $Model = M(get_table_name($model['id']),$prefix);
        $map = array('id' => array('in', $ids) );
        if($Model->where($map)->delete()){
            $info = D($model1,'Logic');
            $info->deletes($ids);
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }

    /**
     * 设置一条或者多条数据的状态
     */
    public function set_status($Model=CONTROLLER_NAME){
        $ids      = I('request.ids');
        $status   = I('request.status');
        $msg_type = I('request.msg_type',1);
        $field    = I('request.field','status');
        if(empty($ids)){
            $this->error('请选择要操作的数据');
        }
        $msg = array(
                0=>array('success'=>'推荐成功','error'=>'推荐失败'),
                1=>array('success'=>'启用成功','error'=>'启用失败'),
                2=>array('success'=>'开启成功','error'=>'开启失败'),
                3=>array('success'=>'关闭成功','error'=>'关闭失败'),
                4=>array('success'=>'删除成功','error'=>'删除失败'),
                5=>array('success'=>'审核成功','error'=>'审核失败'),
                6=>array('success'=>'驳回成功','error'=>'驳回失败'),
                7=>array('success'=>'修复成功','error'=>'修复失败'),
                8=>array('success'=>'操作成功','error'=>'操作失败'),
                9=>array('success'=>'取消成功','error'=>'取消失败'),
               10=>array('success'=>'拉黑成功','error'=>'拉黑失败'),
        );

        $map['id'] = array('in',$ids);
        switch ($status){
            case -1 :
                $this->delete($Model, $map, $msg[$msg_type], $field);
                break;
            case 0  :
                $this->forbid($Model, $map, $msg[$msg_type], $field);
                break;
            case 1  :
                $this->resume($Model, $map, $msg[$msg_type], $field);
                break;
            case 2  :
                $this->reject($Model, $map, $msg[$msg_type],$field);
                break;
            default :
                $this->error('参数错误');
                break;
        }
    }

    protected function checkAttr($Model,$model_id){
        $fields     =   get_model_attribute($model_id,false);
        $validate   =   $auto   =   array();
        foreach($fields as $key=>$attr){
            if($attr['is_must']){// 必填字段
                $validate[]  =  array($attr['name'],'require',$attr['title'].'必须!');
            }
            // 自动验证规则
            if(!empty($attr['validate_rule'])) {
                $validate[]  =  array($attr['name'],$attr['validate_rule'],$attr['error_info']?$attr['error_info']:$attr['title'].'验证错误',0,$attr['validate_type'],$attr['validate_time']);
            }
            // 自动完成规则
            if(!empty($attr['auto_rule'])) {
                $auto[]  =  array($attr['name'],$attr['auto_rule'],$attr['auto_time'],$attr['auto_type']);
            }elseif('checkbox'==$attr['type']){ // 多选型
                $auto[] =   array($attr['name'],'arr2str',3,'function');
            }elseif('date' == $attr['type']){ // 日期型
                $auto[] =   array($attr['name'],'strtotime',3,'function');
            }elseif('datetime' == $attr['type']){ // 时间型
                $auto[] =   array($attr['name'],'strtotime',3,'function');
            }
        }
        return $Model->validate($validate)->auto($auto);
    }
}