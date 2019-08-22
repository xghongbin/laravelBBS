<?php

namespace App\Listeners;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Password;

class RestPassword
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  PasswordReset  $event
     * @return void
     */
    public function handle(PasswordReset $event)
    {
        // 使用事件监听重写用户密码，并闪存信息
        // property_exists() --- 检查对象或类是否具有该属性
        $response = property_exists(Password::class, 'PASSWORD_RESET') ?
            Password::PASSWORD_RESET : '您的密码已被重置！';
        session()->flash('success',trans($response));

    }
}
