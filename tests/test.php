<?php
/**
 * Desc:
 * User: baagee
 * Date: 2020/3/18
 * Time: 下午11:43
 */
include __DIR__ . '/../vendor/autoload.php';

// 初始化 最大同时运行任务20个
\BaAGee\AsyncTask\TaskScheduler::init('./var/lock', 20);
// 获取对象
$task = \BaAGee\AsyncTask\TaskScheduler::getInstance();

//循环创建40个任务
for ($i = 1; $i <= 40; $i++) {
    try {
        $res = $task->runTask(
            \BaAGee\AsyncTask\Test\TestTask::class,
            [
                'name'   => 'task_' . $i,
                'age'    => mt_rand(2, 4),
                'string' => "hello 哈哈",
                'int'    => mt_rand(100000, 99999999),
                'float'  => 3.1498765467689801,
                'bool1'  => true,
                'bool2'  => false,
                // 不允许的参数类型
                // 'array'  => ['sdfgs', 2354],
                // 'object' => new stdClass()
            ], false
        );
        echo $i . ($res ? ' success' : ' failed') . PHP_EOL;
        // die;
    } catch (Exception $e) {
        echo $i . ' failed' . PHP_EOL;
        if ($e->getCode() == \BaAGee\AsyncTask\TaskScheduler::SYSTEM_BUSY) {
            $res = false;
            $i--;
            usleep(500000);
        }
    }
}

