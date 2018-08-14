<?php

namespace Org\Util;

//程序运行时间类
class Timer {

    private $StartTime = 0; //程序运行开始时间
    private $StopTime = 0; //程序运行结束时间
    private $TimeSpent = 0; //程序运行花费时间

    function start() {
        //程序运行开始
        $this->StartTime = microtime(true);
    }

    function stop() {
        //程序运行结束
        $this->StopTime = microtime(true);

        return $this->spent(); // 结束时直接返回
    }

    function spent() {
        //程序运行花费的时间
        $this->TimeSpent = $this->StopTime - $this->StartTime;
        return number_format($this->TimeSpent * 1000, 4); //返回获取到的程序运行时间差 . '毫秒'
    }

}
