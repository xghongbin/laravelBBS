<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailTrait;
use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;// 接口类，User 使用 implements 实现接口
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Traits\HasRoles;// 使用 Laravel-permission


/**
 *
 *  implements 用于表示「实现」接口，可实现多个接口 --- 实现 MustVerifyEmailContract 接口
 *  extends 表示「继承」类，且只能单继承
 *
 *  Trait:
 *        php从以前到现在一直都是单继承的语言，无法同时从两个基类中继承属性和方法，为了解决这个问题，php 出了 Trait 特性
 *
 *  PHP 为了规避多继承造成的继承关系混乱，所以采用了 Trait
 *  通过在类中使用 use 关键字，声明要组合的Trait名称，Trait不能实例化 --- 组合 MustVerifyEmailTrait 这个类
 */
class User extends Authenticatable implements MustVerifyEmailContract
{
    use  MustVerifyEmailTrait;

    use HasRoles;

    //  使用自定义的"计算活跃用户算法"
    use Traits\ActiveUserHelper;

    //  使用自定义的最后登录时间
    use Traits\LastActiveAtHelper;

    use Notifiable {
        notify as protected laravelNotify;
    }

    public function notify($instance)
    {
        // 如果要通知的人是当前用户，就不必通知了！
        if ($this->id == Auth::id()) {
            return;
        }

        // 只有数据库类型通知才需提醒，直接发送 Email 或者其他的都 Pass
        if (method_exists($instance, 'toDatabase')) {
            $this->increment('notification_count');
        }

        $this->laravelNotify($instance);
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','introduction','avatar',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $guarded = ['geetest_challenge', 'geetest_validate', 'geetest_seccode'];

    public function topics()
    {
        return $this->hasMany(Topic::class);
    }

    /**
     * 是否作者本人，用于 app/Policies/TopicPolicy.php的调用
     * 话题是否本人的判断即可使用
     * @param $model
     * @return bool
     */
    public function isAuthorOf($model)
    {
        return $this->id == $model->user_id;
    }

    //  一个用户可以拥有多条评论
    public function replies(){
        return $this->hasMany(Reply::class);
    }

    //  清除未读消息数标记
    public function markAsRead()
    {
        $this->notification_count = 0;
        $this->save();
        $this->unreadNotifications->markAsRead();
    }

    //  Eloquent 修改器
    public function setPasswordAttribute($value)
    {
        if(strlen($value) != 32){
            $value = md5($value);
        }
        $this->attributes['password'] = $value;
    }

    public function setAvatarAttribute($path)
    {
        // 如果不是 `http` 子串开头，那就是从后台上传的，需要补全 URL
        if ( ! starts_with($path, 'http')) {

            // 拼接完整的 URL
            $path = config('app.url') . "/uploads/images/avatars/$path";
        }

        $this->attributes['avatar'] = $path;
    }

}
