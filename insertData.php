<?php
/**
 * 该文件用于输入作业数据 and 全局数据配置
 */
require __DIR__.'/JCB.php';
require __DIR__.'/PCB.php';

$total_mem = 100; // 单位KB
$tape_num = 4;    // 磁带机数量
$JCB_List = [];
// [params]               job_name arrive_at n_time n_mem n_tape level
$JCB_List[] = new \OS\JCB('JOB1', '10:00',      25,   15,   2,   3);
$JCB_List[] = new \OS\JCB('JOB2', '10:20',      30,   60,   1,   1);
$JCB_List[] = new \OS\JCB('JOB3', '10:30',      10,   50,   3,   4);
$JCB_List[] = new \OS\JCB('JOB4', '10:35',      20,   10,   2,   2);
$JCB_List[] = new \OS\JCB('JOB5', '10:40',      15,   30,   2,   5);

$JCB_List_count = count($JCB_List);
$JCB_WList= [];
$PCB_List = [];
$PCB_Done = [];

$time = '10:00'; // 设置开始时间

// var_dump($JCB_List);
