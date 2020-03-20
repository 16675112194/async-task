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
    $res = $task->runTask(
        \BaAGee\AsyncTask\Test\TestTask::class,
        [
            'name' => '到的', 'age' => mt_rand(10, 19)
        ]
    );
    var_dump($i . ($res ? ' success' : ' failed'));
}