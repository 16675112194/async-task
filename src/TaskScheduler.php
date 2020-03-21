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
     * 繁忙错误码
     */
    public const SYSTEM_BUSY = 10010;
    /**
     * 任务执行脚本
     */
    protected const TASK_RUN_SCRIPT = __DIR__ . DIRECTORY_SEPARATOR . 'task_run.php';

    /**
     * @var bool
     */
    protected static $isInit = false;
    /**
     * @var string
     */
    protected static $lockFile = '';
    /**
     * @var string composer autoload.php文件
     */
    protected static $composerAutoloadFile = '';
    /**
     * @var int
     */
    protected static $maxTaskCount = 50;

    /**
     * @var null
     */
    private static $self = null;

    /**
     * @param string $lockFile
     * @param int    $maxTaskCount
     * @return bool
     */
    public static function init(string $lockFile, int $maxTaskCount = 50)
    {
        if (self::$isInit === false) {
            self::$maxTaskCount = $maxTaskCount;
            TaskCounter::setLockFile($lockFile);
            self::$lockFile = realpath($lockFile);
            self::$isInit   = true;
            return true;
        }
        return false;
    }

    /**
     * @throws \Exception
     */
    protected static function checkInit()
    {
        if (self::$isInit == false) {
            throw new \Exception(__CLASS__ . ' not initialized');
        }
    }


    /**
     * @return TaskScheduler|null
     * @throws \Exception
     */
    public static function getInstance()
    {
        self::checkInit();
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
     * @param string $taskClassName 任务类名
     * @param array  $params        参数
     * @param bool   $blocking      是否阻塞 如果是true 会等待获得锁
     *                              false不等待获得锁，直接执行失败返回false
     * @return bool|false|string
     * @throws \Exception
     */
    public function runTask(string $taskClassName, array $params = [], bool $blocking = true)
    {
        self::checkInit();
        $resource = TaskCounter::getLockResource();
        $getLock  = TaskCounter::getLock($resource, $blocking);
        $ret      = false;
        if ($getLock) {//get lock
            try {
                if (!empty($params)) {
                    $this->checkParams($params);
                }
                //判断是不是继承了task base
                if (!is_subclass_of($taskClassName, TaskBase::class)) {
                    throw new \Exception('没有继承:' . TaskBase::class);
                }

                $val = self::getCurrentTaskNumber();
                if ($val >= self::$maxTaskCount) {
                    throw new \Exception("系统繁忙，请稍后再试", self::SYSTEM_BUSY);
                }
                $newParams = [
                    '_task_class_name_'   => $taskClassName,
                    '_task_params_'       => json_encode($params),
                    '_composer_autoload_' => $this->findComposerAutoloadFile(),
                    '_lock_file_'         => self::$lockFile,
                ];

                $command = new Command(PHP_BINDIR . DIRECTORY_SEPARATOR . 'php ' . self::TASK_RUN_SCRIPT);
                foreach ($newParams as $k => $v) {
                    $where = '--' . $k . '=' . urlencode($v);
                    $command->append($where);
                }
                $command->deamon();
                // echo $command . PHP_EOL;

                $ret = $command->popen('w');
                if ($ret == true) {
                    // 成功 加1
                    TaskCounter::setValue($val + 1);
                }
                TaskCounter::unLock($resource);
            } catch (\Exception $e) {
                TaskCounter::unLock($resource);
                TaskCounter::closeLock($resource);
                throw $e;
            }
        } else {
            //没有获得锁
            // echo 'not get lock' . PHP_EOL;
        }
        TaskCounter::closeLock($resource);
        return $ret;
    }

    /**
     * @return int
     */
    protected static function getCurrentTaskNumber()
    {
        return TaskCounter::getValue();
    }


    /**
     * 获取当前任务数
     * @param bool $fast true:通过计数器获取
     *                   false:使用ps -ef统计 速度略慢
     * @return int
     */
    public static function currentTaskNumber(bool $fast = true)
    {
        if ($fast) {
            return self::getCurrentTaskNumber();
        } else {
            $command = new Command('ps -ef');
            $command->pipe(new Command('grep ' . self::TASK_RUN_SCRIPT))
                ->pipe(new Command('grep -v grep'))
                ->pipe(new Command('grep -v "ps -ef"'))
                ->pipe(new Command('wc -l'));

            $ret = $command->popen('r');
            return intval(trim($ret));
        }
    }

    /**
     * 寻找composer autoload.php 文件
     * @return string
     * @throws \Exception
     */
    protected function findComposerAutoloadFile()
    {
        if (empty(self::$composerAutoloadFile)) {
            $requireFiles = get_required_files();
            foreach ($requireFiles as $requireFile) {
                if (basename($requireFile) === 'autoload.php') {
                    self::$composerAutoloadFile = $requireFile;
                    return $requireFile;
                }
            }
            throw new \Exception("找不到composer的autoload.php");
        } else {
            return self::$composerAutoloadFile;
        }
    }
}
