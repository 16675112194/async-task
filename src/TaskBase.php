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
    abstract public function run($params = []);

    // TODO 正常或者意外停止时做的处理 比如kill命令时执行的操作
    public function shutdownFunc()
    {

    }
}
