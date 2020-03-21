<?php
/**
 * Desc: 任务计数器
 * User: baagee
 * Date: 2020/3/20
 * Time: 下午7:08
 */

namespace BaAGee\AsyncTask;

/**
 * Class TaskCounter
 * @package BaAGee\AsyncTask
 */
final class TaskCounter
{
    public static function setLockFile($file)
    {
        if (!is_file($file)) {
            fclose(fopen($file, 'w'));
        }
        self::$lockFile = realpath($file);
    }

    /**
     * @var string 计数器文件
     */
    protected static $lockFile = __DIR__ . DIRECTORY_SEPARATOR . 'lock';

    /**
     * @var null
     */
    protected static $lockResource = null;

    /**
     * 获取锁资源
     * @return false|resource|null
     */
    public static function getLockResource()
    {
        if (is_null(self::$lockResource)) {
            self::$lockResource = fopen(self::$lockFile, 'r+');
        }
        return self::$lockResource;
    }

    /**
     * 获得锁
     * @param      $lockResource
     * @param bool $blocking
     * @return bool
     */
    public static function getLock($lockResource, bool $blocking = true)
    {
        if ($blocking) {
            // 阻塞
            return flock($lockResource, LOCK_EX);
        } else {
            // 不阻塞
            return flock($lockResource, LOCK_EX | LOCK_NB);
        }
    }

    /**
     * 释放锁
     * @param $lockResource
     */
    public static function unLock($lockResource)
    {
        flock($lockResource, LOCK_UN);
    }

    /**
     * 关闭锁资源
     * @param $lockResource
     */
    public static function closeLock($lockResource)
    {
        fclose($lockResource);
        self::$lockResource = null;
    }

    /**
     * 获得当前任务数
     * @return int
     */
    public static function getValue()
    {
        $val = file_get_contents(self::$lockFile);
        $val = intval($val);
        $val = $val <= 0 ? 0 : $val;
        return $val;
    }

    /**
     * 设置值
     * @param int $value 新值
     */
    public static function setValue(int $value)
    {
        file_put_contents(self::$lockFile, $value);
    }
}
