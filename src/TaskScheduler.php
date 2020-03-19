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
     * 执行任务
     * @param string $taskClassName 任务类
     * @param array  $params        参数
     * @return bool
     * @throws \ReflectionException
     */
    public function runTask($taskClassName, $params = [])
    {
        //判断是不是继承了task base
        if (!(new $taskClassName() instanceof TaskBase)) {
            throw new \Exception('没有继承:' . TaskBase::class);
        }
        $refClass = new \ReflectionClass($taskClassName);
        $file     = $refClass->getFileName();
        $where    = ' ';
        foreach ($params as $k => $v) {
            if (!is_array($v) && !is_object($v)) {
                $where .= ' --' . $k . '=' . urlencode($v);
            } else {
                throw new \Exception('参数不合法，不能是数组或者对象');
            }
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
}
