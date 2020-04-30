<?php
/**
 * Created by PhpStorm.
 * User: fanzhaogui
 * Date: 2020/4/26
 * Time: 16:56
 */

namespace App\Task;


use App\Model\CopyLog;
use EasySwoole\Task\AbstractInterface\TaskInterface;

class CopyTask implements TaskInterface
{
    protected $data;

    /**
     * 通过构造函数，传入数据，获取该任务的数据
     *
     * @param $data
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * 执行本次任务
     *
     * @author: fanzhaogui
     * @param int $taskId
     * @param int $workerIndex
     */
    function run(int $taskId, int $workerIndex)
    {
        echo "templete CopyTask run : \n";
         var_dump($this->data);
        // 数据处理
        $data = $this->checkData();
        var_dump($data);
        $model = new CopyLog();
        $tableName = $model::getCurrentTableName($data['company_id']);
        echo "INSERT table name : " . $tableName . "\n";
        $model->setTableName($tableName)->data($data);
        try {
            $rst = $model->save();
            if (!$rst) {
                echo $model->lastQueryResult()->getLastError();
            }
        } catch (\Throwable $e) {
            echo __FILE__ . "save fault \n";

            return false;
        }

        //只有同步调用才能返回数据
        return "return : " . $rst;
    }

    /**
     * 出现异常时处理
     * @param \Throwable $throwable
     * @param int $taskId
     * @param int $workerIndex
     * @return bool
     */
    function onException(\Throwable $throwable, int $taskId, int $workerIndex)
    {
        error_log(date('Y-m-d H:i:s') . ": taskId:{$taskId} - workderIndex:{$workerIndex} - message : {$throwable->getMessage()} \n", 3, 'task_exception.log');
        return true;
    }

    protected function checkData()
    {
        $fields = [
            'url', 'company_id', 'search_word','copy_content','source_engine',
            'source_ip', 'source_area','sys','browser',
            'source_url',
        ];
        if (!isset($this->data['company_id']) || $this->data['company_id'] == false) {
            throw new \InvalidArgumentException('参数错误：compay_id 不存在');
        }

        $data = [];
        foreach ($fields as $field) {
            $data[$field] = $this->data[$field] ?? '';
        }
        return $data;
    }
}