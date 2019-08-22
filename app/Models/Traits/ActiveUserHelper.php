<?php
/**
 * Created by PhpStorm.
 * User: huanghongbin
 * Date: 2019-08-15
 * Time: 09:57
 */
namespace App\Models\Traits;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Arr;
use App\Models\Topic;
use Illuminate\Support\Facades\DB;

trait ActiveUserHelper
{

    //  用于存放临时用户数据
    protected $users = [];

    //  配置信息
    protected $topic_weight = 4;//  话题权重
    protected $reply_weight = 1;//  回复权重
    protected $pass_days    = 7;//  多少天内发表过内容
    protected $user_number  = 6;//  取出多少用户

    //  缓存相关配置
    protected $cache_key    = 'larabbs_active_users';
    protected $cache_expire_in_seconds = 65 * 60;


    /**
     * 提供该方法用于控制器获取缓存中的"活跃用户"
     *
     * 尝试从缓存中取出cache_key对应的数据，
     * 如果能取到，便直接返回数据
     * 否则运行匿名函数中的代码来取出活跃用户数据，返回的同时做缓存操作
     */
    public function getActiveUsers()
    {
            return Cache::remember($this->cache_key,$this->cache_expire_in_seconds,function (){
                return $this->calculateActiveUsers();
            });
    }

    /**
     * 提供该方法用于 app\Console\Commands\CalculateActiveUser.php 中 handle 调用
     *
     * 取得活跃用户列表
     * 并加以缓存
     */
    public function calculateAndCacheActiveUsers()
    {
        $active_users = $this->calculateActiveUsers();
        $this->cacheActiveUsers($active_users);
    }


    private function calculateActiveUsers()
    {
        $this->calculateTopicScore();
        $this->calculateReplyScore();

        //  数组按照得分排序
        $users = Arr::sort($this->users,function ($user){
            return $user['score'];
        });

        //  需要倒序，高分靠前，第二个参数为保持数据的 KEY 不变
        $users = array_reverse($users, true);

        //  只获取需要的数量
        $users = array_slice($users, 0, $this->user_number, true);

        //  新建一个空的集合
        $active_users = collect();

        foreach ($users as $user_id => $user) {
            //  找寻是否可以找到用户
            $user = $this->find($user_id);

            if($user) {
                //  将此用户实体放入集合的末尾
                $active_users->push($user);
            }
        }

        return $active_users;
    }

    /**
     * 从话题数据表里取出限定时间范围($pass_days)内,有发表过话题的用户
     * 并且同时取出用户此段时间内发布话题的数量
     *
     * DB::raw  创建原生表达式
     * Carbon::now() 根据当前时间创建 Carbon 实例
     * Carbon::now()->subDays 当前实例减去指定数量的天数，返回当前实例
     */
    private function calculateTopicScore()
    {
        $topic_users = Topic::query()->select(DB::raw('user_id, count(*) as topic_count'))
            ->where('created_at', '>=', Carbon::now()->subDays($this->pass_days))
            ->groupBy('user_id')
            ->get();

        //  根据话题数量计算得分
        foreach ($topic_users as $value) {
            $this->users[$value->user_id]['score'] = $value->topic_count * $this->topic_weight;
        }
    }


    /**
     * 从回复数据表里取出限定时间范围($pass_days)内，有发表过回复的用户
     * 并且同时取出用户此段时间内发布的数量
     *
     *
     */
    private function calculateReplyScore()
    {
        $reply_users = Topic::query()->select(DB::raw('user_id, count(*) as reply_count'))
            ->where('created_at', '>=', Carbon::now()->subDays($this->pass_days))
            ->groupBy('user_id')
            ->get();

        //  根据回复数量计算得分
        foreach ($reply_users as $value) {
            $reply_score = $value->reply_count * $this->reply_weight;

            if (isset($this->users[$value->user_id])) {
                $this->users[$value->user_id]['score'] += $reply_score;
            }
            else {
                $this->users[$value->user_id]['score'] = $reply_score;
            }
        }
    }


    /**
     * 将数据放入缓存中
     */
    private function cacheActiveUsers($active_users)
    {
        Cache::put($this->cache_key, $active_users, $this->cache_expire_in_seconds);
    }

}