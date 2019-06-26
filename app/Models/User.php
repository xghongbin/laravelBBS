<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailTrait;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;

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
    use Notifiable, MustVerifyEmailTrait;

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
}
