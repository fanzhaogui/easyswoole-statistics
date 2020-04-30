<?php
/**
 * Created by PhpStorm.
 * User: fanzhaogui
 * Date: 2020/4/26
 * Time: 17:44
 */

namespace App\Model;


/**
 * 访问
 *
 * @package App\Model
 */
class CompanyVisit extends BaseModel
{
    protected static $tableNamePrefix = 'company_visits';

    // 都是非必选的，默认值看文档下面说明
    // protected $autoTimeStamp = true;
    // protected $createTime = 'create_at';
    // protected $updateTime = 'update_at';


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
        $this->setTableName($tableName);
        self::create($data);
        return $this->save();
    }
}