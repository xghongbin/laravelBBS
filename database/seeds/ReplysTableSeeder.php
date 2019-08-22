<?php

use Illuminate\Database\Seeder;
use App\Models\Reply;

use App\Models\User;
use App\Models\Topic;

class ReplysTableSeeder extends Seeder
{
    public function run()
    {

        //  随机取用户ID
        $user_ids = User::all()->pluck('id')->toArray();

        //  随机话题ID
        $topic_ids = Topic::all()->pluck('id')->toArray();

        //  获取 Faker 实例
        $faker = app(Faker\Generator::class);


        $replys = factory(Reply::class)
            ->times(50)
            ->make()
            ->each(function ($reply, $index) use ($user_ids, $topic_ids, $faker) {
                    //  从用户ID数组中随机取出一个并赋值
                    $reply->user_id = $faker->randomElement($user_ids);

                    //  话题ID
                    $reply->topic_id = $faker->randomElement($topic_ids);
            });

        //  数据集合转换数组，并入库
        Reply::insert($replys->toArray());
    }

}

