<?php
// +----------------------------------------------------------------------
// | 徐州梦创信息科技有限公司—专业的游戏运营，推广解决方案.
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.vlcms.com  All rights reserved.
// +----------------------------------------------------------------------
// | Author: kefu@vlcms.com QQ：97471547
// +----------------------------------------------------------------------

namespace Admin\Model;
use Think\Model;

/**
 * 分类模型
 */
class SourceModel extends Model{

	/**
     * 构造函数
     * @param string $name 模型名称
     * @param string $tablePrefix 表前缀
     * @param mixed $connection 数据库连接信息
     */
    public function __construct($name = '', $tablePrefix = '', $connection = '') {
        /* 设置默认的表前缀 */
        $this->tablePrefix ='tab_game_';
        /* 执行构造方法 */
        parent::__construct($name, $tablePrefix, $connection);
    }

    /* 自动验证规则 */
    protected $_validate = array(
        array('game_id', 'require', '游戏名称不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('file_type', 'require', '游戏类型不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
    );
}
