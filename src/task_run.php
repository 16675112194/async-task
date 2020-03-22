<?php
declare(ticks=1);
//开始时间
$__task_start_time_dshji67876ygrr_79r76rf453q3sh9 = time();
//定义接受的参数
$paramKeys = [
    '_task_class_name_:',
    '_task_params_:',
    '_composer_autoload_:',
    '_lock_file_:',
    '_task_timeout_:',
];
// 接受变量并解码
$__receive_params_sdfgs767_897y78r456fgh_dfsyt76567g_ = getopt('', $paramKeys);
array_walk($__receive_params_sdfgs767_897y78r456fgh_dfsyt76567g_, function (&$v) {
    $v = base64_decode($v);
});

// 超时时间
$__task_timeout_sdhg8768YH768t7yuhv_hgre655sdgs868t = $__receive_params_sdfgs767_897y78r456fgh_dfsyt76567g_['_task_timeout_'] ?? 3600 * 3;
// 任务类
$__task_class_name_sdhg8768YH768t7yuhv_hgre655sdgs868t = $__receive_params_sdfgs767_897y78r456fgh_dfsyt76567g_['_task_class_name_'];
// 参数
$__task_params_sdfgsd789h_sdgs7tgssdfew_ = json_decode($__receive_params_sdfgs767_897y78r456fgh_dfsyt76567g_['_task_params_'], true) ?? [];
// composer autoload
$__composer_autoload_file_iuhu_878ghytygh_sdfd_sdg_ = $__receive_params_sdfgs767_897y78r456fgh_dfsyt76567g_['_composer_autoload_'];
// 计数器锁文件
$__lock_file__nscghwerufhpefjiwgywe7_56ytfggyutr6sdf435fty = $__receive_params_sdfgs767_897y78r456fgh_dfsyt76567g_['_lock_file_'];


// 判断是否运行超过了时间
register_tick_function(function () use (
    $__task_class_name_sdhg8768YH768t7yuhv_hgre655sdgs868t,
    $__task_start_time_dshji67876ygrr_79r76rf453q3sh9,
    $__task_timeout_sdhg8768YH768t7yuhv_hgre655sdgs868t,
    $__task_params_sdfgsd789h_sdgs7tgssdfew_
) {
    if (time() - $__task_start_time_dshji67876ygrr_79r76rf453q3sh9 > $__task_timeout_sdhg8768YH768t7yuhv_hgre655sdgs868t) {
        //超时 主动退出
        echo sprintf("【Timeout exit!】 task:%s params:%s" . PHP_EOL,
            $__task_class_name_sdhg8768YH768t7yuhv_hgre655sdgs868t,
            json_encode($__task_params_sdfgsd789h_sdgs7tgssdfew_, JSON_UNESCAPED_UNICODE)
        );
        exit(0);
    }
});
//注册任务关闭逻辑
register_shutdown_function(function () use ($__lock_file__nscghwerufhpefjiwgywe7_56ytfggyutr6sdf435fty) {
    \BaAGee\AsyncTask\TaskCounter::setLockFile($__lock_file__nscghwerufhpefjiwgywe7_56ytfggyutr6sdf435fty);
    $resource = \BaAGee\AsyncTask\TaskCounter::getLockResource();
    $lock     = \BaAGee\AsyncTask\TaskCounter::getLock($resource, true);
    if ($lock) {
        $val  = $val1 = \BaAGee\AsyncTask\TaskScheduler::currentTaskNumber(true);
        $val2 = \BaAGee\AsyncTask\TaskScheduler::currentTaskNumber(false);
        if ($val1 != $val2) {
            //以2为准
            $val = $val2;
        }
        \BaAGee\AsyncTask\TaskCounter::setValue($val - 1);
        \BaAGee\AsyncTask\TaskCounter::unLock($resource);
    }
    \BaAGee\AsyncTask\TaskCounter::closeLock($resource);
});

require $__composer_autoload_file_iuhu_878ghytygh_sdfd_sdg_;

/**
 * @var $task BaAGee\AsyncTask\TaskBase
 */
$task = new $__task_class_name_sdhg8768YH768t7yuhv_hgre655sdgs868t();
$task->run($__task_params_sdfgsd789h_sdgs7tgssdfew_);

exit(0);
