<?php

/**
 * Desc: 异步执行脚本
 * User: baagee
 * Date: 2018/12/4
 * Time: 下午5:48
 */

namespace BaAGee\AsyncTask;

/**
 * Class TaskScheduler
 * @package BaAGee\AsyncTask
 */
final class TaskScheduler
{
    /**
     * @var null
     */
    private static $self = null;

    /**
     * @return TaskScheduler
     */
    public static function getInstance()
    {
        if (self::$self === null || !(self::$self instanceof TaskScheduler)) {
            self::$self = new self();
        }
        return self::$self;
    }

    /**
     * TaskScheduler constructor.
     */
    private function __construct()
    {
    }

    /**
     *
     */
    private function __clone()
    {

    }

    /**
     * 执行任务类
     * @param string $taskClassName
     * @param array  $params
     * @return bool
     * @throws \Exception
     */
    public function runTask(string $taskClassName, $params = [])
    {
        //判断是不是继承了task base
        if (!(new $taskClassName() instanceof TaskBase)) {
            throw new \Exception('没有继承:' . TaskBase::class);
        }
        $file  = realpath(__DIR__ . DIRECTORY_SEPARATOR . 'run.php');
        $where = ' ';

        $newParams = [
            '_task_class_name_'   => $taskClassName,
            '_task_params_'       => json_encode($params),
            '_composer_autoload_' => $this->findComposerAutoloadFile()
        ];
        foreach ($newParams as $k => $v) {
            $where .= ' --' . $k . '=' . urlencode($v);
        }
        $command = PHP_BINARY . ' ' . $file . $where . ' &';
        // echo $command . PHP_EOL;
        $handle = popen($command, 'w');
        if ($handle == false) {
            return false;
        } else {
            pclose($handle);
            return true;
        }
    }

    /**
     * 寻找composer autoload.php 文件
     * @return string
     * @throws \Exception
     */
    protected function findComposerAutoloadFile()
    {
        $requireFiles = get_required_files();
        foreach ($requireFiles as $requireFile) {
            if (basename($requireFile) === 'autoload.php') {
                return $requireFile;
            }
        }
        throw new \Exception("找不到composer的autoload.php");
    }
}
