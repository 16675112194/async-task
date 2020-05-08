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
        SIGHUP => 'exitSignalHandler',
        SIGINT => 'exitSignalHandler',
        SIGQUIT => 'exitSignalHandler',
        SIGUSR1 => 'exitSignalHandler',
        SIGUSR2 => 'exitSignalHandler',
    ];
    foreach ($exitSignal as $signal => $handler) {
        pcntl_signal($signal, $handler);
    }
}

function decr($lockFile)
{
    \BaAGee\AsyncTask\TaskCounter::setLockFile($lockFile);
    $resource = \BaAGee\AsyncTask\TaskCounter::getLockResource();
    $lock = \BaAGee\AsyncTask\TaskCounter::getLock($resource, true);
    if ($lock) {
        $val = $val1 = \BaAGee\AsyncTask\TaskScheduler::currentTaskNumber(true);
        $val2 = \BaAGee\AsyncTask\TaskScheduler::currentTaskNumber(false);
        if ($val1 != $val2) {
            $val = $val2;
        }
        \BaAGee\AsyncTask\TaskCounter::setValue($val - 1);
        \BaAGee\AsyncTask\TaskCounter::unLock($resource);
    }
    \BaAGee\AsyncTask\TaskCounter::closeLock($resource);
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

require $receiveParams['_composer_autoload_'];

$taskParamsArr = unserialize($receiveParams['_task_params_']) ?? [];
$__lock_file__nscghwerufhpefjiwgywe7_ = $receiveParams['_lock_file_'];

try {
    /**
     * @var $task BaAGee\AsyncTask\TaskBase
     */
    $task = new $receiveParams['_task_class_name_']();
} catch (Throwable $e) {
    echo 'New TaskError: ' . $e->getMessage() . PHP_EOL;
    echo 'Trace: ' . $e->getTraceAsString() . PHP_EOL;
    decr($__lock_file__nscghwerufhpefjiwgywe7_);
    exit(0);
}

register_shutdown_function(function () use ($__lock_file__nscghwerufhpefjiwgywe7_, $task) {
    decr($__lock_file__nscghwerufhpefjiwgywe7_);
    $task->shutdownFunc();
});

$task->run($taskParamsArr);

exit(0);
