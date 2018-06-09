<?php

namespace Betterde\Laravel\Jpush\Facades;

use JPush\PushPayload;
use JPush\ReportPayload;
use JPush\DevicePayload;
use JPush\SchedulePayload;
use Illuminate\Support\Facades\Facade;

/**
 * Push Facade
 *
 * Date: 2018/6/2
 * @author George
 * @method static DevicePayload device()
 * @method static PushPayload push()
 * @method static ReportPayload report()
 * @method static SchedulePayload schedule()
 * @method static string getAuthStr()
 * @method static string getRetryTimes()
 * @method static string getLogFile()
 * @package App\Facades
 */
class Push extends Facade
{
	/**
	 * 获取组件的注册名称
	 *
	 * Date: 2018/6/2
	 * @author George
	 * @return string
	 */
	protected static function getFacadeAccessor()
	{
		return 'push';
	}
}
