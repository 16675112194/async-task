<?php

/**
 * Desc: 异步任务父类
 * User: baagee
 * Date: 2018/12/4
 * Time: 下午5:51
 */

namespace BaAGee\AsyncTask;
/**
 * Class TaskBase
 * @package BaAGee\AsyncTask
 */
abstract class TaskBase
{
    protected $paramKeys     = [];
    protected $receiveParams = [];
    protected $startTime     = 0.0;
    protected $endTime       = 0.0;

    public function __construct()
    {
        $this->startTime     = microtime(true);
        $this->receiveParams = getopt('', $this->paramKeys);
        array_walk($this->receiveParams, function (&$v, $k) {
            $v = urldecode($v);
        });
    }

    /**
     * @return float
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * @return float
     */
    public function getEndTime()
    {
        return $this->endTime;
    }

    abstract protected function main($params);

    final public function run()
    {
        try {
            return $this->main($this->receiveParams);
        } catch (\Exception $e) {
            throw $e;
        } finally {
            $this->endTime = microtime(true);
        }
    }
}
