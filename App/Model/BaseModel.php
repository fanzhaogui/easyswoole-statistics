<?php
/**
 * Created by PhpStorm.
 * User: fanzhaogui
 * Date: 2020/4/26
 * Time: 17:51
 */

namespace App\Model;


use EasySwoole\EasySwoole\Config;
use EasySwoole\ORM\AbstractModel;

class BaseModel extends AbstractModel
{
    /**
     * 分表前缀
     *
     * @var string $tableNamePrefix
     */
    protected static $tableNamePrefix;

    /**
     * 获取当前的表名
     *
     * @author: fanzhaogui
     * @param int $compayId
     * @return string
     */
    public static function getCurrentTableName($compayId)
    {
        $num = Config::getInstance()->getConf('MYSQL_LIMIT_NUM');
        return static::$tableNamePrefix . (($compayId & $num) + 1);
    }

    /**
     * 设置当前的表名
     *
     * @author: fanzhaogui
     * @param int $compayId
     * @return $this
     */
    public function setTableName($tableName)
    {
        // $this->tableName = $tableName;
        return $this->tableName($tableName);
    }
}