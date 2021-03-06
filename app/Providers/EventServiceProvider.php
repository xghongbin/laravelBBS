<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;


class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        //  注册了 Registered （注册成功后）事件的监听器
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        //  对邮箱认证事件 Verified 进行监听，监听器是 EmailVerified
        \Illuminate\Auth\Events\Verified::class => [
            \App\Listeners\EmailVerified::class,
        ],
        //  对事件重置密码 ResetsPassword 进行监听，监听器是 PasswordRest
        \Illuminate\Auth\Events\PasswordReset::class => [
            \App\Listeners\RestPassword::class,
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
