<?php
/**
 * Created by PhpStorm.
 * User: fanzhaogui
 * Date: 2020/4/26
 * Time: 16:56
 */

namespace App\Task;


use App\Model\CompanyVisit;
use EasySwoole\Task\AbstractInterface\TaskInterface;

class LogTask implements TaskInterface
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
        echo "templete task run : \n";
        var_dump($this->data);
        try {
            // 数据处理
            $data = $this->checkData();
            $model = new CompanyVisit($data);
            $res = $model->save();
        } catch (\Throwable $e) {
            echo "save fault";
            return false;
        }
        //只有同步调用才能返回数据
        return "return : " . $res;
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
        error_log("taskId:{$taskId} - workderIndex:{$workerIndex} - message : {$throwable->getMessage()} \n", 3, 'task_exception.log');
        return true;
    }


    protected function checkData()
    {
        $fields = [
            'sub_site_url', 'request_header','request_param','ip',
            'company_id', 'search_world','brower_type','platform',
            'from_parent_domain',
        ];

        if (!$this->data['company_id']) {
            throw new \InvalidArgumentException('参数错误：compay_id 不存在');
        }

        $data = [];
        foreach ($fields as $field) {
            $data[$field] = $this->data[$field] ?? '';
        }
        return $data;
    }
}