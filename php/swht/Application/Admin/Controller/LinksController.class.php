<?php

namespace Admin\Controller;
use User\Api\UserApi as UserApi;
/**
 * 后台首页控制器
 * @author zxc
 */
class LinksController extends ThinkController {
	const model_name = 'Links';
	public function lists(){
		if(isset($_REQUEST['title'])){
			$map['title']=array('like','%'.$_REQUEST['title'].'%');
			unset($_REQUEST['title']);
		}
		parent::lists(self::model_name,$_GET['p'],$map);
	}
	public function add(){
		if(IS_POST){
			$Model  =   D('Links');
            // 获取模型的字段信息
            $Model  =   $this->checkAttr($Model,$model['id']);
			$data=$Model->create();
			if($data){
                $Model->add($data);
                $this->success('添加'.$model['title'].'成功！', U('lists?model='.$model['name']));
            } else {
                $this->error($Model->getError());
            }
        } else {
            $this->display('add');
        }
	}
	public function del($model = null, $ids=null)
    {
        $model = M('Model')->getByName(self::model_name); /*通过Model名称获取Model完整信息*/
        parent::del($model["id"],$ids);
    }
    public function edit($id=0){
        if(!isset($_REQUEST['id'])||$_REQUEST['id']==null){$this->error('请选择要编辑的用户！');}
        $model = M('Model')->getByName(self::model_name); /*通过Model名称获取Model完整信息*/
        //获取模型信息
        $model = M('Model')->find($model['id']);
        $model || $this->error('模型不存在！');
        if(IS_POST){
            $Model  =   D(parse_name(get_table_name($model['id']),1));
            // 获取模型的字段信息
            $Model  =   $this->checkAttr($Model,$model['id']);
            $data = $Model->create();
            if($data){
                $Links=M('Links','tab_')->where(array('id'=>$id))->save($data);
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
}