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
BaAGee\ProcessTask Process [1] Ready to start. /home/work/php/bin/php /home/work/test/tests/task1.php --name=小明 --age=20 --_task_name=task1 --_process_id=1 &
BaAGee\ProcessTask Process [2] Ready to start. /home/work/php/bin/php /home/work/test/tests/task1.php --name=小明 --age=20 --_task_name=task1 --_process_id=2 &
[work@gz-sems-development01 test]$ Task name [task1] Process [1] : Start time=2018-12-11 17:30:56
Task name [task1] Process [1] : Receive data:{"name":"小明","age":"20","_task_name":"task1","_process_id":"1"}
Task name [task1] Process [1] : 1544520656.9348
Task name [task1] Process [1] : /home/work/test/tests/task1.php
Task name [task1] Process [2] : Start time=2018-12-11 17:30:56
Task name [task1] Process [2] : Receive data:{"name":"小明","age":"20","_task_name":"task1","_process_id":"2"}
Task name [task1] Process [2] : 1544520656.9376
Task name [task1] Process [2] : /home/work/test/tests/task1.php
Task name [task1] Process [2] : 1544520658.9377
Task name [task1] Process [2] : Over time=2018-12-11 17:30:58 used=2.000228
Task name [task1] Process [1] : 1544520661.9349
Task name [task1] Process [1] : Over time=2018-12-11 17:31:01 used=5.000236

*/
