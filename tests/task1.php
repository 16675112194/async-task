<?php
/**
 * Desc: 异步任务脚本
 * User: baagee
 * Date: 2018/12/11
 * Time: 下午4:32
 */

$path = dirname(__FILE__);

include_once $path . '/../vendor/autoload.php';

(new class extends \BaAGee\TaskBase
{
    protected $_receive_fields = [
        'name:',
        'age:'
    ];

    public function run()
    {
        // 做自己的业务逻辑处理
        $this->log(microtime(true));
        $this->log(__FILE__);
        sleep(mt_rand(2,5));
        $this->log(microtime(true));
    }

    private function log($str)
    {
        $this->trace($str);
    }
})->run();
