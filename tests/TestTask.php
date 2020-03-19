<?php
/**
 * Desc:
 * User: baagee
 * Date: 2020/3/19
 * Time: 上午11:04
 */

namespace Test;
include __DIR__ . '/../vendor/autoload.php';

use BaAGee\AsyncTask\TaskBase;

class TestTask extends TaskBase
{
    protected $paramKeys = [
        'name:', 'age:'
    ];

    protected function main($params)
    {
        file_put_contents("test_task", json_encode($params));
    }
}

$test = new TestTask();
$test->run();
//运行时间
$time = ($test->getEndTime() - $test->getStartTime()) * 1000;
//...
