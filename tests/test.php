<?php
/**
 * Desc:
 * User: baagee
 * Date: 2020/3/18
 * Time: 下午11:43
 */
include __DIR__ . '/../vendor/autoload.php';

\BaAGee\AsyncTask\TaskScheduler::init('./lock', 30);
$task = \BaAGee\AsyncTask\TaskScheduler::getInstance();

for ($i = 1; $i <= 55; $i++) {
    try {
        $res = $task->runTask(
            \BaAGee\AsyncTask\Test\TestTask::class,
            [
                'name' => 'task_' . $i, 'age' => mt_rand(10, 19)
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