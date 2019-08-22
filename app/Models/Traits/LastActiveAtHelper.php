<?php
/**
 * Created by PhpStorm.
 * User: huanghongbin
 * Date: 2019-08-16
 * Time: 12:24
 */
namespace App\Models\Traits;

use Carbon\Carbon;
use Illuminate\Support\Facades\Redis;

trait LastActiveAtHelper
{
    //缓存相关
    protected $hash_prefix = 'larabbs_last_actived_at_';

    protected $field_prefix = 'user_';


    /**
     * User Model 中 use LastActiveAtHelper
     * app/Http/Kernel.php 中 protected $middlewareGroups 调用 （记录用户最后活跃时间）
     * 中间件 app/Http/Middleware/RecordLastActivedTime.php 调用
     */
    public function recordLastActiveAt()
    {
        // 获取今天的日期
        $date   =   Carbon::now()->toDateString();

        //  Redis 哈希表的命名，如 “larabbs_last_actived_at_2019-08-16”
        $hash   =   $this->hash_prefix.$date;

        //  字段名称 如"user_28"
        $field  =   $this->field_prefix.$this->id;

        //dd(Redis::hGetAll($hash));

        // 当前时间，如：2019-08-16 12:30:15
        $now = Carbon::now()->toDateTimeString();

        Redis::hSet($hash,$field,$now);
    }


    /**
     * 将Redis 中的 “用户最后登录时间” 同步到数据库中
     * 新建 Artisan 命令供计划任务调用
     *
     */
    public function syncUserActiveAt()
    {
        //  获取昨天的日期：格式：2019-08-15
        $yesterday_date = Carbon::yesterday()->toDateString();

        // Redis 哈希表的命名，如：larabbs_last_actived_at_2019-08-15
        $hash   =   $this->hash_prefix.$yesterday_date;

        // 从 Redis 中获取所有哈希表里的数据
        $dates = Redis::hGetAll($hash);

        foreach($dates as $user_id => $actived_at){
            $user_id = str_replace($this->field_prefix,'',$user_id);

            if($user = $this->find($user_id)){
                $user->last_actived_at = $actived_at;
                $user->save();
            }
        }

        // 以数据库为中心的存储，既已同步，即可删除
        Redis::del($hash);
    }

    public function getLastActivedAtAttribute($value)
    {
        // 获取今天的日期
        $date = Carbon::now()->toDateString();

        // Redis 哈希表的命名，如：larabbs_last_actived_at_2019-08-16
        $hash = $this->hash_prefix . $date;

        // 字段名称，如：user_1
        $field = $this->field_prefix . $this->id;

        // 三元运算符，优先选择 Redis 的数据，否则使用数据库中
        $datetime = Redis::hGet($hash, $field) ? : $value;

        // 如果存在的话，返回时间对应的 Carbon 实体
        if ($datetime) {
            return new Carbon($datetime);
        } else {
            // 否则使用用户注册时间
            return $this->created_at;
        }
    }
}