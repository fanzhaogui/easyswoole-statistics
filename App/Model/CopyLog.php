<?php
/**
 * Created by PhpStorm.
 * User: fanzhaogui
 * Date: 2020/4/26
 * Time: 17:44
 */

namespace App\Model;


/**
 * 复制
 * @package App\Model
 */
class CopyLog extends BaseModel
{
    protected static $tableNamePrefix = 'copy_logs';


    /**
     * 保存一条数据
     *
     * @author: fanzhaogui
     * @param $data
     * @return bool|int
     */
    public function one($data)
    {
        $tableName = self::getCurrentTableName($data['company_id']);
        echo "INSERT table name : " . $tableName . "\n";
        $this->setTableName($tableName);
        self::create($data);
        $res = $this->save();
        var_dump($res);
        if (!$res) {
            // 打印最后执行的sql语句
            var_dump($this->lastQuery()->getLastQuery());
        }
        return $res;
    }
}