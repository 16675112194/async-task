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
     * 验证参数是不是标量
     * @param $params
     * @throws \Exception
     */
    protected function checkParams($params)
    {
        foreach ($params as $k => $v) {
            if (is_string($v) || is_bool($v) || is_integer($v) || is_float($v)) {

            } else {
                throw new \Exception('参数不合法，只允许标量数据类型');
            }
        }
    }

    /**
     * 执行任务类
     * @param string $taskClassName
     * @param array  $params
     * @return bool
     * @throws \Exception
     */
    public function runTask(string $taskClassName, array $params = [])
    {
        if (!empty($params)) {
            $this->checkParams($params);
        }
        //判断是不是继承了task base
        if (!is_subclass_of($taskClassName, TaskBase::class)) {
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
        $command = PHP_BINDIR . DIRECTORY_SEPARATOR . 'php ' . $file . $where . ' &';
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
