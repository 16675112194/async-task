<?php
/**
 * Desc:
 * User: baagee
 * Date: 2020/3/18
 * Time: 下午11:43
 */
include __DIR__ . '/../vendor/autoload.php';

\BaAGee\AsyncTask\TaskScheduler::init('./var/lock', 40);
$task = \BaAGee\AsyncTask\TaskScheduler::getInstance();

for ($i = 1; $i <= 111; $i++) {
    try {
        $res = $task->runTask(
            \BaAGee\AsyncTask\Test\TestTask::class,
            [
                'name'   => 'task_' . $i,
                'age'    => mt_rand(10, 19),
                'string' => "hello 哈哈",
                'int'    => mt_rand(100000, 99999999),
                'float'  => 3.1498765467689801,
                'bool1'  => true,
                'bool2'  => false,
                // 不允许的参数类型
                // 'array'  => ['sdfgs', 2354],
                // 'object' => new stdClass()
            ]
        );
        echo $i . ($res ? ' success' : ' failed') . PHP_EOL;
    } catch (Exception $e) {
        echo $i . ' failed' . PHP_EOL;
        if ($e->getCode() == \BaAGee\AsyncTask\TaskScheduler::SYSTEM_BUSY) {
            $res = false;
            $i--;
        }
    }
}
