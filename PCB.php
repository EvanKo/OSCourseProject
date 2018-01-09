<?php
/**
 * 进程
 */
 namespace OS;

class PCB
{
    public $process_name;
    public $n_time;  // 所需时间
    public $state;   // 状态
    public $r_time;  // 已运行时间
    public $n_mem;
    public $n_tape;
    public $run_at;  // 运行于
    public $level;   // 优先级

    
    // 构造函数
    public function __construct($process_name, $n_time, $n_mem, $n_tape, $level)
    {
        $this->process_name = $process_name;
        $this->n_time = $n_time;
        $this->level = $level;
        $this->n_mem = $n_mem;
        $this->n_tape = $n_tape;
        $this->state = 'W';
        $this->r_time = 0;
    }

    public function setRunAt($run_at)
    {
        $this->run_at = $run_at;
    }

    public function setState($state)
    {
        $this->state = $state;
    }
}
