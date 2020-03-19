<?php
/**
 * Desc:
 * User: baagee
 * Date: 2020/3/18
 * Time: 下午11:43
 */
include __DIR__ . '/../vendor/autoload.php';
include __DIR__ . '/TestTask.php';
$task = \BaAGee\AsyncTask\TaskScheduler::getInstance();
$res  = $task->runTask(\Test\TestTask::class, ['name' => '到的', 'age' => mt_rand(10, 19)]);
var_dump($res);
