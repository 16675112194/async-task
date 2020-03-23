<?php
if (extension_loaded('pcntl')) {
    function exitSignalHandler($signo)
    {
        switch ($signo) {
            case SIGTERM:
            case SIGHUP:
            case SIGINT:
            case SIGUSR1:
            case SIGUSR2:
            case SIGQUIT:
                echo 'Receive signal: ' . $signo . ' then exit!' . PHP_EOL;
                exit(0);
        }
    }

    if (function_exists('\pcntl_async_signals')) {
        \pcntl_async_signals(true);
    } else {
        declare(ticks=1);
    }
    $exitSignal = [
        SIGTERM => 'exitSignalHandler',
        SIGHUP  => 'exitSignalHandler',
        SIGINT  => 'exitSignalHandler',
        SIGQUIT => 'exitSignalHandler',
        SIGUSR1 => 'exitSignalHandler',
        SIGUSR2 => 'exitSignalHandler',
    ];
    foreach ($exitSignal as $signal => $handler) {
        pcntl_signal($signal, $handler);
    }
}

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
        $val  = $val1 = \BaAGee\AsyncTask\TaskScheduler::currentTaskNumber(true);
        $val2 = \BaAGee\AsyncTask\TaskScheduler::currentTaskNumber(false);
        if ($val1 != $val2) {
            $val = $val2;
        }
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
