<?php

$paramKeys = [
    '_task_class_name_:',
    '_task_params_:',
    '_composer_autoload_:',
    '_lock_file_:',
];

$receiveParams = getopt('', $paramKeys);
array_walk($receiveParams, function (&$v) {
    $v = base64_decode($v);
});

$__lock_file__nscghwerufhpefjiwgywe7_ = $receiveParams['_lock_file_'];
register_shutdown_function(function () use ($__lock_file__nscghwerufhpefjiwgywe7_) {
    \BaAGee\AsyncTask\TaskCounter::setLockFile($__lock_file__nscghwerufhpefjiwgywe7_);
    $resource = \BaAGee\AsyncTask\TaskCounter::getLockResource();
    $lock     = \BaAGee\AsyncTask\TaskCounter::getLock($resource, true);
    if ($lock) {
        $val = \BaAGee\AsyncTask\TaskCounter::getValue();
        \BaAGee\AsyncTask\TaskCounter::setValue($val - 1);
        \BaAGee\AsyncTask\TaskCounter::unLock($resource);
    }
    \BaAGee\AsyncTask\TaskCounter::closeLock($resource);
});

require $receiveParams['_composer_autoload_'];

$taskParamsArr = json_decode($receiveParams['_task_params_'], true) ?? [];
/**
 * @var $task BaAGee\AsyncTask\TaskBase
 */
$task = new $receiveParams['_task_class_name_']();
$task->run($taskParamsArr);
exit(0);
