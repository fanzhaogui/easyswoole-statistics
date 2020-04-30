<?php
/**
 * Created by PhpStorm.
 * User: fanzhaogui
 * Date: 2020/4/26
 * Time: 15:04
 */

namespace App\HttpController\Api;


use App\HttpController\ApiBase;
use App\Model\CopyLog;
use App\Task\CopyTask;
use App\Task\LogTask;
use EasySwoole\EasySwoole\Task\TaskManager;

/**
 * 数据收集
 *
 * @package App\HttpController\Api
 */
class Collection extends ApiBase
{
    /**
     * copy 复制统计
     * @author: fanzhaogui
     * @date 2020-04-26
     *
     * @url /Api/Collection/copy
     */
    public function copy()
    {
        /* 数据库字段
            'url|当前url'
            'search_word|搜索词'
            'copy_content|复制内容'
            'copy_type|复制类型'
            'source_engine|来源搜索引擎'
            'source_ip|来源ip'
            'source_area|来源地域'
            'sys|操作系统'
            'browser|浏览器类型'
            'source_url|来源url'
            'user_id|用户ID'
            'domain_id|域名ID'
            'created_at|'
            'updated_at|'
         * */
        /* 访问链接
            http://wxc/api/copy.png
            ?sign_id=75a89b229cc98b1e9649bffe00c1c3ca
            &type=2
            &url=http%3A//localhost/test.html
            &ref=http%3A//localhost/
            &c=1123123
            &time=599
            &v=1578033452914-0
        */

        if (!$this->CheckjwtAuth()) {
            return $this->parsedNotSucess();
        }

        // 其他参数的获取
        $request       = $this->request();
        $request_param = $request->getRequestParam();
        $get_param     = $request->getQueryParams();
        $post_param    = $request->getParsedBody();
        // 该方法用于获取以非form-data或x-www-form-urlenceded编码格式POST提交的原始数据，相当于PHP中的$HTTP_RAW_POST_DATA。
        $body_content = $request->getBody()->__toString();
        $raw_array    = json_decode($body_content, true);
        $header       = $request->getHeaders();
        $server       = $request->getServerParams();
        $cookie       = $request->getCookieParams();
        $company_id   = $this->company_id;
        $ip           = $this->clientRealIP();

        //$rst = compact('request_param', 'get_param', 'post_param', 'body_content', 'raw_array', 'header', 'server', 'cookie',
        //    'company_id', 'ip');

        $param = $request->getQueryParams();

        /**
         * 提取数据
         */
        $data = [];

        $data['company_id'] = $this->company_id;

        // 获取当前页面的url
        $data['url'] = $param['url'];

        // 获取来源url
        $data['source_url'] = $param['ref'] ?? '';

        // 获取搜索词
        // 如果不是搜索引擎则没有搜索词
        $data['search_word'] = '';

        // 获取来源搜索引擎
        $data['source_engine'] = $this->getSearchEngine($param['ref']);

        // 获取复制内容
        $data['copy_content'] = $param['c'] ?? '';

        // 获取复制类型
        $data['copy_type'] = $param['type'] ?? 0;

        // 获取来源ip
        $data['source_ip'] = ip2long($this->clientRealIP());

        // 获取来源地址
        $data['source_area'] = $this->getIpArea($this->clientRealIP());

        // 获取操作系统
        $browse      = new \Browser($header['user-agent'][0] ?? ''); // 初始化获取浏览器信息获取类
        $data['sys'] = $browse->getPlatform();

        // 获取用户浏览器类型
        $data['browser'] = $browse->getBrowser();


//        $model = new CopyLog();
//        $rs = $model->one($data);
//        if (!$rs) {
//            return $this->writeJson(302, $model->lastQueryResult()->getLastError());
//        }
//        return $this->writeJson(200, [$rs]);

        // TODO 异步处理
        $task = TaskManager::getInstance();
        echo "异步 \n";
        $task->async(new CopyTask($data));
//        echo "同步 \n";
//        $data = $task->sync(new CopyTask($data));

        return $this->writeJson(200, [$data]);
    }


    /**
     * visit 访问统计
     * @author: fanzhaogui
     * @date 2020-04-26
     *
     * @url /Api/Collection/visit
     */
    public function visit()
    {
        if (!$this->CheckjwtAuth()) {
            return $this->parsedNotSucess();
        }

        // 其他参数的获取
        $request       = $this->request();
        $request_param = $request->getRequestParam();
        $get_param     = $request->getQueryParams();
        $post_param    = $request->getParsedBody();
        // 该方法用于获取以非form-data或x-www-form-urlenceded编码格式POST提交的原始数据，相当于PHP中的$HTTP_RAW_POST_DATA。
        $body_content = $request->getBody()->__toString();
        $raw_array    = json_decode($body_content, true);
        $header       = $request->getHeaders();
        $server       = $request->getServerParams();
        $cookie       = $request->getCookieParams();
        $company_id   = $this->company_id;
        $ip           = $this->clientRealIP();

        $rst = compact('request_param', 'get_param', 'post_param', 'body_content', 'raw_array', 'header', 'server', 'cookie',
            'company_id', 'ip');

        $data  = [];
        $param = $request->getQueryParams();

        $data['from_parent_domain'] = $param['ref']; // 来源地址
        $data['sub_site_url']       = $param['url']; // 落地页url
        $data['company_id']         = $this->company_id; // 工时ID
        $data['request_header']     = json_encode($request->getHeaders(), JSON_UNESCAPED_UNICODE); // 请求头信息
        $data['request_param']      = $request->getQueryParams(); // 请求参数
        $data['ip']                 = ip2long($this->clientRealIP()); // ip
        $data['search_world']       = ''; // 搜索词
        $browse                     = new \Browser($header['user-agent'][0] ?? ''); // 初始化获取浏览器信息获取类
        $data['brower_type']        = $browse->getBrowser(); // 浏览器类型
        $data['platform']           = $browse->getPlatform(); // 获取平台
        $data['view_tool']          = ''; // 屏幕大小

        // TODO 异步处理
        $task = TaskManager::getInstance();
        echo "异步 \n";
        $task->async(new LogTask($data));
        echo "同步 \n";
        $data = $task->sync(new LogTask($data));

        return $this->writeJson(200, [$rst, $data]);
    }
}