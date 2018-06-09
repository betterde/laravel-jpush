<?php

namespace Betterde\Laravel\Jpush\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

/**
 * Date: 2018/6/9
 * @author George
 * @package Betterde\Laravel\Jpush\Jobs
 */
class ScheduleGenerator implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * ScheduleGenerator constructor.
     */
    public function __construct()
    {

    }

    /**
     * 获取定时任务
     *
     * 获取定时任务的逻辑需要根据自己的实际业务逻辑进行查询，如果数据量大的话建议分块操作
     * 因定时任务不一定是准点触发，所以建议将HASH KEY 的值只取到分钟级别
     *
     * Date: 2018/6/2
     * @author George
     */
    public function handle()
    {
        $timestamp = strtotime('+1 day');
        $date = date('Y-m-d', $timestamp);
        DB::table('schedules')->select([
            'schedules.id',
            'users.id as user_id',
            'schedules.start_timestamp',
            'schedules.end_timestamp'
        ])->leftJoin('users', 'schedules.user_id', '=', 'users.id')
            ->whereDate('date', $date)
            ->where('users.attendance_remind', 1)
            ->orderBy('id')
            ->chunk(2000, function ($schedules) {
                foreach ($schedules as $schedule) {
                    Redis::connection('remind')->hset(config('jpush.remind.schedule.prefix') . '_' . substr($schedule->end_timestamp, 0, 16), $schedule->id, "user_{$schedule->user_id}");
                }
            });
        Log::info(date('Y-m-d H:i:s') . '已将待推送的任务添加到Hash表中');
    }
}
