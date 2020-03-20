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
            if (!is_file($lockFile)) {
                fclose(fopen($lockFile, 'w'));
            }
            self::$maxTaskCount = $maxTaskCount;
            self::$lockFile     = realpath($lockFile);
            self::$isInit       = true;
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
     * @param string $taskClassName
     * @param array  $params
     * @return bool
     * @throws \Exception
     */
    public function runTask(string $taskClassName, array $params = [])
    {
        self::checkInit();
        list($getLock, $fh) = $this->getLock();
        if ($getLock) {
            if (!empty($params)) {
                $this->checkParams($params);
            }
            //判断是不是继承了task base
            if (!is_subclass_of($taskClassName, TaskBase::class)) {
                throw new \Exception('没有继承:' . TaskBase::class);
            }

            $val = self::realCurrentTaskNumber();
            if ($val >= self::$maxTaskCount) {
                throw new \Exception("系统繁忙，请稍后再试", self::SYSTEM_BUSY);
            }

            $newParams = [
                '_task_class_name_'   => $taskClassName,
                '_task_params_'       => json_encode($params),
                '_composer_autoload_' => $this->findComposerAutoloadFile(),
            ];

            $command = new Command(PHP_BINDIR . DIRECTORY_SEPARATOR . 'php ' . self::TASK_RUN_SCRIPT);
            foreach ($newParams as $k => $v) {
                $where = '--' . $k . '=' . urlencode($v);
                $command->append($where);
            }
            $command->deamon();
            // echo $command . PHP_EOL;

            $ret = $command->popen('w');
            $this->releaseLock($fh);
        } else {
            $ret = false;
        }
        $this->closeLock($fh);
        return $ret;
    }

    /**
     * @param resource $fh
     */
    protected function closeLock($fh)
    {
        fclose($fh);
    }

    /**
     * @return array
     */
    protected function getLock()
    {
        $fh = fopen(self::$lockFile, 'w+');
        return [flock($fh, LOCK_EX), $fh];
    }

    /**
     * @param resource $fh
     */
    protected function releaseLock($fh)
    {
        flock($fh, LOCK_UN);
    }

    /**
     * @return int
     */
    protected static function realCurrentTaskNumber()
    {
        // $s1      = microtime(true);
        $command = new Command('ps -ef');
        $command->pipe(new Command('grep ' . self::TASK_RUN_SCRIPT))
            ->pipe(new Command('grep -v grep'))
            ->pipe(new Command('wc -l'));

        $ret = $command->popen('r');
        // $s2  = microtime(true);
        // var_dump(($s2 - $s1) * 1000);
        return intval(trim($ret));
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
