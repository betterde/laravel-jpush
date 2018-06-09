# Laravel-Jpush

Jpush SDK for Laravel framework

[![Latest Stable Version](https://poser.pugx.org/betterde/laravel-jpush/v/stable)](https://packagist.org/packages/betterde/laravel-jpush)
[![Total Downloads](https://poser.pugx.org/betterde/laravel-jpush/downloads)](https://packagist.org/packages/betterde/laravel-jpush)
[![Latest Unstable Version](https://poser.pugx.org/betterde/laravel-jpush/v/unstable)](https://packagist.org/packages/betterde/laravel-jpush)
[![License](https://poser.pugx.org/betterde/laravel-jpush/license)](https://packagist.org/packages/betterde/laravel-jpush)

## Table of Contents

- <a href="#introduction">Introduction</a>
    - <a href="#installation">Installation</a>
    - <a href="#config">Config</a>
    - <a href="#usage">Usage</a>
    - <a href="#schedule">Schedule</a>


## Introduction

### Installation
```terminal
composer require betterde/laravel-jpusher
```

### Config

```terminal
php artisan vendor:publish --tag=jpush-config
```

```php
<?php

return [
    'key' => env('JPUSH_APP_KEY'),
    'secret' => env('JPUSH_MASTER_SECRET'),
    'path' => storage_path('logs/push.log'),
    'apns_production' => env('JPUSH_APNS_ENV'),
    'retry_time' => 3,
    'ios' => [
        'sound' => 'default', //表示通知提示声音，默认填充为空字符串
        'badge' => '+1', //表示应用角标，把角标数字改为指定的数字；为 0 表示清除，支持 '+1','-1' 这样的字符串，表示在原有的 badge 基础上进行增减，默认填充为 '+1'
        'content-available' => true, //表示推送唤醒，仅接受 true 表示为 Background Remote Notification，若不填默认表示普通的 Remote Notification
        "mutable-content" => true, //表示通知扩展, 仅接受 true 表示支持 iOS10 的 UNNotificationServiceExtension, 若不填默认表示普通的 Remote Notification
        'category' => 'attendance', //IOS8才支持。设置 APNs payload 中的 'category' 字段值
        'extras' => []
    ],
    'android' => [
        'title' => '', //表示通知标题，会替换通知里原来展示 App 名称的地方
        'builder_id' => 2, //表示通知栏样式 ID
        "priority" => 0, // 表示通知栏展示优先级，默认为 0，范围为 -2～2 ，其他值将会被忽略而采用默认值
        'extras' => [] //表示扩展字段，接受一个数组，自定义 Key/value 信息以供业务使用
    ],
    // 定时通知配置
    'remind' => [
        'database' => 'remind',
        'schedule' => [
            'prefix' => 'schedule',
        ],
        // 提前提醒时间，单位：秒
        'advance' => 300
    ]
];
```

### Usage

```php
use Betterde\Laravel\Jpush\Facades\Push;

class ClassName {
   Push::device();
   Push::push();
   Push::report();
   Push::schedule();
}
```

### Schedule

Copy jobs class to project jobs

```terminal
php artisan vendor:publish --tag=jpush-jobs
```

Now you can modify jobs `handle` function logic. Then you can modify `App\Console\Kernel` Class `schedule` function like this:

```php

use App\Jobs\ScheduleHandler;
use App\Jobs\ScheduleGenerator;

protected function schedule(Schedule $schedule)
{
    $schedule->job(new ScheduleGenerator)->dailyAt('23:00');
    $schedule->job(new ScheduleHandler('Remind Title', 'Alert content', []))->everyMinute();
}

```
