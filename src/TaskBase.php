<?php

/**
 * Desc: 异步任务父类
 * User: baagee
 * Date: 2018/12/4
 * Time: 下午5:51
 */

namespace BaAGee;
/**
 * Class TaskBase
 * @package BaAGee
 */
abstract class TaskBase
{
    /**
     * @var array
     */
    protected $_receive_fields = [];
    /**
     * @var array
     */
    protected $_receive_data = [];
    /**
     * @var int|mixed
     */
    protected $_start_time = 0;

    /**
     * TaskBase constructor.
     */
    public function __construct()
    {
        $this->_start_time   = microtime(true);
        $this->_receive_data = getopt('', array_unique(array_merge($this->_receive_fields, ['_process_id:', '_task_name:'])));
        $this->trace('Start time=' . date('Y-m-d H:i:s'));
        $this->trace('Receive data:' . json_encode($this->_receive_data, JSON_UNESCAPED_UNICODE));
    }

    /**
     * @return mixed
     */
    abstract public function run();

    /**
     *
     */
    public function __destruct()
    {
        $this->trace(sprintf('Over time=%s used=%f', date('Y-m-d H:i:s'), microtime(true) - $this->_start_time));
    }

    /**
     * @param $msg
     */
    protected function trace($msg)
    {
        // todo 写入log
//        $msg = sprintf('Task name [%s] Process [%d] : %s', $this->_receive_data['_task_name'], $this->_receive_data['_process_id'], $msg);
//        echo $msg . PHP_EOL;
    }
}