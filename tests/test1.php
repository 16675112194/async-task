<?php
/**
 * Desc: 使用实例
 * User: baagee
 * Date: 2018/12/11
 * Time: 下午4:21
 */

$path = dirname(__FILE__);

include_once $path . '/../vendor/autoload.php';
try {
    $res = \BaAGee\ProcessTask::addTask('task1', $path . '/task1.php');
    $res = \BaAGee\ProcessTask::run('task1', ['name' => '小明', 'age' => '20'], 2);
    var_dump($res);
} catch (Throwable $e) {
    die("Error: " . $e->getMessage());
}
/*
[work@gz-development01 test]$ ../php/bin/php ./tests/test1.php
Task name [task1] Process [2] pid [92997] : Start time=2018-12-12 07:06:15
Task name [task1] Process [2] pid [92997] : Receive data:{"name":"小明","age":"20","_task_name":"task1","_process_id":"2"}
Task name [task1] Process [1] pid [92995] : Start time=2018-12-12 07:06:15
Task name [task1] Process [2] pid [92997] : 1544598375.2569
Task name [task1] Process [2] pid [92997] : /Users/baagee/PhpstormProjects/github/process-task/tests/task1.php
Task name [task1] Process [1] pid [92995] : Receive data:{"name":"小明","age":"20","_task_name":"task1","_process_id":"1"}
Task name [task1] Process [1] pid [92995] : 1544598375.2569
Task name [task1] Process [1] pid [92995] : /Users/baagee/PhpstormProjects/github/process-task/tests/task1.php
Task name [task1] Process [1] pid [92995] : 1544598377.2588
Task name [task1] Process [1] pid [92995] : Over time=2018-12-12 07:06:17 used=2.002030
Task name [task1] Process [2] pid [92997] : 1544598380.2578
Task name [task1] Process [2] pid [92997] : Over time=2018-12-12 07:06:20 used=5.001051
*/
