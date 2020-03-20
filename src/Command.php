<?php
/**
 * Desc:
 * User: baagee
 * Date: 2020/3/19
 * Time: 下午11:42
 */

namespace BaAGee\AsyncTask;

/**
 * Class Command
 * @package BaAGee\AsyncTask
 */
final class Command
{
    /**
     * @var string
     */
    protected $command = '';

    /**
     * Command constructor.
     * @param $cmd
     */
    public function __construct($cmd)
    {
        $this->command = $cmd;
    }

    /**
     * @param Command $cmd
     * @return $this
     */
    public function pipe(Command $cmd)
    {
        $this->command .= '|' . $cmd;
        return $this;
    }

    /**
     * 后台运行
     */
    public function deamon()
    {
        $this->command .= ' &';
        return $this;
    }

    /**
     * @return string
     */
    public function getCommand(): string
    {
        return $this->command;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getCommand();
    }

    /**
     * @param $str
     * @return $this
     */
    public function append($str)
    {
        $this->command .= ' ' . $str;
        return $this;
    }

    /**
     * @param string $mode
     * @return bool|false|string
     */
    public function popen($mode = 'w')
    {
        $handle = popen($this->getCommand(), $mode);
        if ($handle == false) {
            return false;
        } else {
            if ($mode == 'r') {
                $ret = '';
                do {
                    $bytes = fread($handle, 1024);
                    $ret   .= $bytes;
                } while ($bytes != false);
            } else {
                $ret = true;
            }
            pclose($handle);
            return $ret;
        }
    }
}
