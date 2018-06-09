<?php

namespace Betterde\Laravel\Jpush\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Betterde\Laravel\Jpush\Facades\Push;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

/**
 * Date: 2018/6/9
 * @author George
 * @package Betterde\Laravel\Jpush\Jobs
 */
class ScheduleHandler implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * 定义通知标题
     *
     * @var string
     * Date: 2018/6/9
     * @author George
     */
    protected $title;

    /**
     * 定义通知内容
     *
     * @var string
     * Date: 2018/6/9
     * @author George
     */
    protected $alert;

    /**
     * 定义通知扩展字段
     *
     * @var array
     * Date: 2018/6/9
     * @author George
     */
    protected $extras;

    /**
     * SchedulePush constructor.
     * @param string $title
     * @param string $alert
     * @param array $extras
     */
    public function __construct(string $title, string $alert, array $extras = [])
    {
        $this->title = $title;
        $this->alert = $alert;
        $this->extras = $extras;
    }

    /**
     * 获取定时任务
     *
     * Date: 2018/6/2
     * @author George
     */
    public function handle()
    {
        $key = date('Y-m-d H:i', time() + config('jpush.remind.advance'));
        $schedules = Redis::connection(config('jpush.remind.database'))->hvals(config('jpush.remind.schedule.prefix') . '_' . $key);
        $keys = Redis::connection(config('jpush.remind.database'))->hkeys(config('jpush.remind.schedule.prefix') . '_' . $key);

        $this->send($schedules, $this->title, $this->alert, $this->extras);

        // 通知发送完毕后，删除通知
        Redis::connection(config('jpush.remind.database'))->hdel($key, $keys);
    }

    /**
     * Date: 2018/6/2
     * @author George
     * @param array $alias
     * @param string $title
     * @param string $alert
     * @param array $extras
     */
    private function send(array $alias, $title = '', $alert = '', $extras = [])
    {
        // 定义IOS通知样式
        $ios_notification = config('jpush.ios');
        $ios_notification['extras'] = $extras;

        $ios_alert = [
            "title" => $title,
            "body" => $alert,
        ];

        // 定义Android通知样式
        $android_notification = config('jpush.android');
        $android_notification['title'] = $title;
        $android_notification['extras'] = $extras;

        try {
            $response = Push::push()->setPlatform('all')
                ->addAlias($alias)
                ->iosNotification($ios_alert, $ios_notification)
                ->androidNotification($alert, $android_notification)
                ->setNotificationAlert($alert)
                ->options(['apns_production' => config('jpush.apns_production')])
                ->send();
            Log::info(json_encode($response));
        } catch (Exception $exception) {
            Log::error($exception);
        }
    }
}
