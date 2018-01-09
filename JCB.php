<?php
/**
 * 作业
 */
namespace OS;

class JCB
{
    public $job_name;   // 名字
    public $arrive_at;  // 到达时间
    public $n_time;     // 估计运行时间
    public $n_mem;      // 内存需要
    public $n_tape;     // 磁带机需要
    public $level;      // 优先级
    public $run_at;     // 运行于
    public $end_at;     // 结束于

    public $state;

    // 构造函数
    public function __construct($job_name, $arrive_at, $n_time, $n_mem, $n_tape, $level)
    {
        $this->job_name = $job_name;
        $this->arrive_at = $arrive_at;
        $this->n_time = $n_time;
        $this->n_mem = $n_mem;
        $this->n_tape = $n_tape;
        $this->level = $level;

        $this->state = 'W';
    }

    public function setRunAt($run_at)
    {
        $this->run_at = $run_at;
    }

    public function setEndAt($end_at)
    {
        $this->end_at = $end_at;
    }
    public function setState($state)
    {
        $this->state = $state;
    }
}