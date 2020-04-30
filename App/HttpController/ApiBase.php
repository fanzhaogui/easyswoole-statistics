<?php
/**
 * Created by PhpStorm.
 * User: fanzhaogui
 * Date: 2020/4/26
 * Time: 14:46
 */

namespace App\HttpController;


use EasySwoole\EasySwoole\Config;
use EasySwoole\EasySwoole\Core;
use EasySwoole\EasySwoole\Trigger;
use EasySwoole\Http\Message\Status;
use http\Exception\InvalidArgumentException;
use Lcobucci\JWT\Parser;

abstract class ApiBase extends BaseController
{
    /**
     * 标识落地页所属
     * @var int $company_id
     */
    protected $company_id;

    /**
     * jwt检测并获取响应的数据
     *
     * @author: fanzhaogui
     * @return bool
     */
    protected function CheckjwtAuth()
    {
        $request = $this->request();
        $jwt = $request->getHeader('token')[0] ?? '';

        $jwt = $request->getQueryParam('sign_id');

        try {
            $parse = new Parser();
            $token = $parse->parse((string)$jwt);
            // 公司ID
            $this->company_id = intval($token->getClaim('uid'));
            echo $this->company_id;
            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }


    /**
     * 获取搜索引擎
     * @param $url 传入地址
     * @return string 搜索引擎名称
     */
    protected function getSearchEngine($url)
    {
        // $url = 'https://www.sogou.com/link?url=LrcXQwOk1jD1bwyOf7aoRyhY5M6lguB2JSn2Aun2iKESriNt6Vf6kZw5sD8aRjGpJLzWk1lVsw1cI8rlFgkSPa&wd=&eqid=fa1dc9c900004d82000000065e1e79d9';
        // $url = '';
        // 解析url
        $p_url = parse_url($url);




        // 得到域名
        $domian = $p_url['host'] ?? '未获取到';

        /**
         * [
         *  [
         *    域名标识符,
         *    搜索引擎名称
         *  ],
         *  ...
         * ]
         */
        $search_engines = [
            [
                'baidu.com',
                '百度'
            ],
            [
                'so.com',
                '360'
            ],
            [
                'sogou.com',
                '搜狗',
            ],
            [
                'google.com',
                'Google'
            ],
            [
                'bing.com',
                '必应'
            ],
        ];

        foreach ($search_engines as $value) {
            // $value[0] 搜索引擎标识符
            // $value[1] 搜索引擎名称
            if (strpos($url, $value[0])) {
                return $value[1];
            }
        }

        // TODO: 将未知的搜索引擎写入到日志
        return '';
    }


    protected function getIpArea($ip, $format=false)
    {
        // 拼接完整路径
        $ipdb_path = EASYSWOOLE_ROOT . '/extend/ipdb/ipip.ipdb';

        // 读取ip库
        $city = new \ipip\db\City($ipdb_path);

        // 解析对应ip地址的位置
        /**
         * array find(ip, language);
         */
        $ip_info = $city->find($ip, 'CN');

        // 如果外部不传入处理函数则自动进行拼接
        $format or $format = function($ips) {
            return implode(':', $ips);
        };

        // 执行回调函数获得结果
        return $format($ip_info);
    }


    protected function parsedNotSucess()
    {
        $this->writeJson(Status::CODE_UNAUTHORIZED, '', '无法解析');
    }

    protected function actionNotFound(?string $action): void
    {
        $this->writeJson(Status::CODE_NOT_FOUND);
    }

    public function onRequest(?string $action): ?bool
    {
        if (!parent::onRequest($action)) {
            return false;
        }
        return true;
    }

    /**
     * 请求异常时，统一返回给用户的响应
     *
     * @param \Throwable $throwable
     */
    protected function onException(\Throwable $throwable): void
    {
        if ($throwable instanceof InvalidArgumentException) {
            $msg = $throwable->getMessage();
            $this->writeJson(400, null, "{$msg}");
        } else {
            if (Core::getInstance()->isDev()) {
                $this->writeJson(500, null, $throwable->getMessage());
            } else {
                Trigger::getInstance()->throwable($throwable);
                $this->writeJson(500, null, '系统内部错误，请稍后重试');
            }
        }
    }
}