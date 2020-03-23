<?php
/**
 * Desc:
 * User: baagee
 * Date: 2020/3/19
 * Time: 上午11:04
 */

namespace BaAGee\AsyncTask\Test;

use BaAGee\AsyncTask\TaskBase;

class TestTask extends TaskBase
{
    public function run($params = [])
    {
        sleep($params['age']);
        file_put_contents(getcwd() . "/var/tmp/" . $params['name'], var_export($params, true), LOCK_EX);
        sleep(100);
        echo json_encode($params, JSON_UNESCAPED_UNICODE) . PHP_EOL;
        // throw new \Exception("c出错啦");
    }
}
