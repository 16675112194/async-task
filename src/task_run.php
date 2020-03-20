<?php

$paramKeys = [
    '_task_class_name_:',
    '_task_params_:',
    '_composer_autoload_:',
];

$receiveParams = getopt('', $paramKeys);
array_walk($receiveParams, function (&$v) {
    $v = urldecode($v);
});
require $receiveParams['_composer_autoload_'];

$taskParamsArr = json_decode($receiveParams['_task_params_'], true) ?? [];
/**
 * @var $task BaAGee\AsyncTask\TaskBase
 */
try {
    $task = new $receiveParams['_task_class_name_']();
    $task->run($taskParamsArr);
} catch (\Exception $e) {
    error_log(sprintf("Task error:%s file:%s line:%d", $e->getMessage(), $e->getFile(), $e->getLine()));
}
