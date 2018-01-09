<?php
/**
 * 入口文件
 */
require __DIR__.'/insertData.php';

main();

function main()
{
    
    while (check()) {
        JCBrunning();
        PCBSort();
        PCBrunning();
        printf_data();
        timeLine();
        sleep(1);
    }
}

function JCBrunning()
{
    global $JCB_WList;
    global $JCB_List;
    global $PCB_List;
    global $total_mem;
    global $tape_num;
    global $time;
    global $JCB_List_count;
    // var_dump(count($JCB_List));
    // for($i=0;$i<count($JCB_List);$i++) {
    //     var_dump($JCB_List[$i]->job_name);
    // }die();
    // var_dump($JCB_List);
   
    for ($i=0; $i<$JCB_List_count; $i++) {
        // 如果到了作业到达时间, 加入作业等待序列
        if ($JCB_List[$i]->arrive_at == $time) {
            $JCB_WList[] = $JCB_List[$i];
            unset($JCB_List[$i]); // 从原队列里删除
        } else {
            continue;
        }
    }
    $count = count($JCB_WList);
    if (!$count) {
        return;
    }
    JCBSort();
    for ($i=0; $i<$count; $i++) {
        // 如果该作业状态为'W'
        if ($JCB_WList[$i]->state == 'W') {
            // 判断资源需求
            if ($JCB_WList[$i]->n_mem <= $total_mem && $JCB_WList[$i]->n_tape <= $tape_num) {
                $data = $JCB_WList[$i];
                // 调入内存
                $PCB_List[] = new \OS\PCB($data->job_name, $data->n_time, $data->n_mem, $data->n_tape, $data->level);
                // 计算剩余资源
                $total_mem = $total_mem - $data->n_mem;
                $tape_num = $tape_num - $data->n_tape;
                // 设作业为状态R
                $JCB_WList[$i]->setState('R');
                $JCB_WList[$i]->setRunAt($time);
                continue;
            } else {
                // $Waiting_flag = true;
                break;
            }
        } else {
            continue;
        }
    }
}

function PCBrunning()
{
    global $PCB_List;
    global $JCB_WList;
    global $PCB_Done;
    global $total_mem;
    global $tape_num;
    global $time;
    if (!count($PCB_List)) {
        return;
    }
    // if ($PCB_List[0]->r_time == $PCB_List[0]->n_time) {
    /* 进程已完成 */
    if ($PCB_List[0]->n_time == 0) {
        $PCB_List[0]->setState('F');
        // 出队
        $Finish_process = array_shift($PCB_List);
        // 释放资源
        $total_mem += $Finish_process->n_mem;
        $tape_num += $Finish_process->n_tape;
        // 保存到进程完成队列
        $PCB_Done[] = $Finish_process;
        // TODO 设置作业也完成
        for ($i=0; $i<count($JCB_WList); $i++) {
            if ($JCB_WList[$i]->job_name == $Finish_process->process_name) {
                $JCB_WList[$i]->setEndAt($time);
                $JCB_WList[$i]->setState('F');
            } else {
                continue;
            }
        }
        echo "进程：".$Finish_process->process_name." 已完成!\n";
        JCBrunning();
        PCBSort();
        // if ($PCB_List[0]->run_at == null && $PCB_List[0]) {
        //     $PCB_List[0]->setRunAt($time);
        // }
    }
    /* 进程未完成或被抢占 */
    if (count($PCB_List)) {
        // 选择队首进程运行 设置进程状态
        if ($PCB_List[0]->state == 'W') {
            $PCB_List[0]->setState('R');
        }
        if ($PCB_List[0]->run_at == null) {
            $PCB_List[0]->setRunAt($time);
        }
    
        $PCB_List[0]->n_time--;
        $PCB_List[0]->r_time++;

        // 设置其他进程状态为‘W’
        for ($i=1; $i<count($PCB_List); $i++) {
            $PCB_List[$i]->setState('W');
        }
    }
}

function JCBSort()
{
    global $JCB_WList;
    global $time;
    // 选择排序法 按已到达的作业需要运行时间从小到大排序
    for ($i = 0; $i<count($JCB_WList); $i++) {
        if ($JCB_WList[$i]->state == 'W') {
            for ($j=$i+1; $j<count($JCB_WList); $j++) {
                if ($JCB_WList[$i]->n_time > $JCB_WList[$i+1]->n_time) {
                    $JCB_WList = swap($JCB_WList, $i, $j);
                } else {
                    continue;
                }
            }
        } else {
            continue;
        }
    }
}

function PCBSort()
{
    global $PCB_List;
    // 选择排序法 按进程优先级排序
    for ($i=0; $i<count($PCB_List); $i++) {
        for ($j=$i+1; $j<count($PCB_List); $j++) {
            if ($PCB_List[$i]->level > $PCB_List[$i+1]->level) {
                $PCB_List = swap($PCB_List, $i, $j);
            } else {
                continue;
            }
        }
    }
}
// 检查作业完成
function check()
{
    // global $JCB_WList;
    // $F_num = 0;
    // for ($i = 0; $i<count($JCB_WList); $i++) {
    //     if ($JCB_WList[$i]->state == 'F') {
    //         $F_num++;
    //     }
    // }
    // if ($F_num == count($JCB_WList)) {
    //     return false;
    // } else {
    //     return true;
    // }
    global $JCB_WList;
    global $JCB_List_count;
    global $PCB_Done;
    $wNum = count($JCB_WList); // 作业等待和完成数
    $pNum = count($PCB_Done); // 进程运行数

    if ($JCB_List_count == $wNum && $JCB_List_count == $pNum) {
        echo "\n全部完成！\n";
        caculate();
        return false;
    } else {
        return true;
    }
}
// 输出
function printf_data()
{
    global $PCB_List;
    global $JCB_WList;
    global $PCB_Done;
    global $total_mem;
    global $tape_num;
    global $time;
    echo "当前时间：$time\n";
    echo "剩余内存：$total_mem\t剩余磁带机：$tape_num\n";
    echo "作业名称 到达时间 估计运行时间 需要内存 需要磁带机 优先级 状态  运行于    结束于\n";
    foreach ($JCB_WList as $job) {
        echo $job->job_name."\t   ".$job->arrive_at."\t   ";
        echo $job->n_time."\t   ".$job->n_mem."\t   ";
        echo $job->n_tape."\t   ".$job->level."\t   ";
        echo $job->state."\t";
        echo $job->run_at."\t  ".$job->end_at;
        echo "\n";
    }
    echo "进程名称 所需时间 已运行时间 运行于 优先级 状态\n";
    foreach ($PCB_List as $process) {
        if ($process->run_at == null) {
            $run_at = '等待';
        } else {
            $run_at = $process->run_at;
        }
        echo $process->process_name."\t   ".$process->n_time."\t     ";
        echo $process->r_time."\t     ".$run_at."\t";
        echo $process->level."   ".$process->state;
        echo "\n";
    }
    echo "\n";
}
// 交换函数
function swap($array, $key1, $key2)
{
    $temp = $array[$key1];
    $array[$key1] = $array[$key2];
    $array[$key2] = $temp;
    return $array;
}
// 时间线增加
function timeLine()
{
    global $time;
    $timestamp = strtotime($time)+60;
    $time = date('H:i', $timestamp);
}
// 计算作业平均周转时间
function caculate()
{
    global $JCB_WList;
    echo "\n";
    $minute = 0;
    for ($i=0; $i<count($JCB_WList); $i++) {
        $time1 = strtotime($JCB_WList[$i]->arrive_at);
        $time2 = strtotime($JCB_WList[$i]->end_at);
        $minute += ($time2-$time1)%86400/60;
    }
    $T = $minute/count($JCB_WList);
    echo "作业平均周转时间:$T\n";
}