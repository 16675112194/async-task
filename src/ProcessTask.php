<?php

/**
 * Desc: 异步执行脚本
 * User: baagee
 * Date: 2018/12/4
 * Time: 下午5:48
 */

namespace BaAGee;

/**
 * Class ProcessTask
 * @package BaAGee
 */
class ProcessTask
{
    /**
     * 保存任务列表
     * @var array
     */
    private static $tasks = [];

    /**
     * @param string $task_name
     * @param string $task_file
     * @return bool
     * @throws \Exception
     */
    public static function addTask(string $task_name, string $task_file)
    {
        if (is_file($task_file)) {
            self::$tasks[$task_name] = realpath($task_file);
            return true;
        } else {
            throw new \Exception($task_file . ' 文件不存在');
        }
    }

    /**
     * ProcessTask constructor.
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
     * 执行异步任务 返回array('total'=>'总的进程数（$process_number）','success'=>'成功开启的进程数')
     * @param string $task_name      任务名称
     * @param array  $data           任务需要的数据
     * @param int    $process_number 进程数量
     * @return array ['total'=>'总的进程数（$process_number）','success'=>'成功开启的进程数']
     * @throws \Exception
     */
    public static function run(string $task_name, array $data = [], int $process_number = 1)
    {
        $script_path        = self::getTask($task_name);
        $data['_task_name'] = $task_name;
        $success            = 0;
        for ($i = 1; $i <= $process_number; $i++) {
            $data['_process_id'] = $i;
            $where               = '';
            foreach ($data as $k => $v) {
                if (!is_array($v) && !is_object($v)) {
                    $where .= ' --' . $k . '=' . $v;
                } else {
                    throw new \Exception('参数不合法，不能是数组或者对象');
                }
            }
            $command = PHP_BINARY . ' ' . $script_path . $where . ' &';
            $handle  = popen($command, 'w');
            if ($handle == false) {
                break;
            } else {
                pclose($handle);
                $success++;
//                echo sprintf(__CLASS__ . ' Process [%d] Ready to start. %s' . PHP_EOL, $i, $command);
            }
        }
        return [
            'total'   => $process_number,
            'success' => $success
        ];
    }

    /**
     * @param string $task_name
     * @return mixed
     * @throws \Exception
     */
    public static function getTask(string $task_name)
    {
        if (array_key_exists($task_name, self::$tasks)) {
            return self::$tasks[$task_name];
        } else {
            throw new \Exception($task_name . ' 不存在');
        }
    }
}