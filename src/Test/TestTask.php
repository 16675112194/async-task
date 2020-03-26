<?php
/**
 * Desc:
 * User: baagee
 * Date: 2020/3/19
 * Time: 上午11:04
 */

namespace BaAGee\AsyncTask\Test;

use BaAGee\AsyncTask\TaskBase;

/**
 * Class TestTask
 * @package BaAGee\AsyncTask\Test
 */
class TestTask extends TaskBase
{
    public function __construct()
    {
        // throw new \Exception("sdfas");
    }

    /**
     * @param array $params
     */
    public function run($params = [])
    {
        sleep($params['age']);
        file_put_contents(getcwd() . "/var/tmp/" . $params['name'], var_export($params, true), LOCK_EX);
        // sleep(100);
        echo json_encode($params, JSON_UNESCAPED_UNICODE) . PHP_EOL;
        throw new \Exception("c出错啦");
    }


    /*
     * 正常或者意外停止时做的处理 比如kill命令时执行的操作
     */
    public function shutdownFunc()
    {
        echo __METHOD__ . PHP_EOL;
        // throw new \Exception("eeee rrror");
    }
}
