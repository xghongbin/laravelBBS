<?php

use Illuminate\Database\Seeder;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //  获取 Faker 实例
        $faker = app(Faker\Generator::class);

        //  头像假数据
        $avatars = [
            env('APP_URL').'/uploads/images/myAvatar-1.png',
            env('APP_URL').'/uploads/images/myAvatar-2.png',
            env('APP_URL').'/uploads/images/myAvatar-3.png',
            env('APP_URL').'/uploads/images/myAvatar-4.png',
            env('APP_URL').'/uploads/images/myAvatar-5.png',
            env('APP_URL').'/uploads/images/myAvatar-6.png'
        ];

        //  生成数据集合
        $users = factory(User::class)->times(10)->make()
                            ->each(function ($user, $index) use ($faker, $avatars){
                                //  从头像中随机取出一个
                                $user->avatar = $faker->randomElement($avatars);
                            } );

        //  让隐藏字段可见，并将数据集合转换为数组
        //  makeVisible () 可以显示 User 模型 $hidden 属性里指定隐藏的字段，此操作确保入库时数据库不会报错。
        $user_array = $users->makeVisible(['password','remember_token'])->toArray();

        //  插入数据库中
        User::insert($user_array);


        // 将 2 号用户指派为『管理员』
        $findSecondUser = User::find(2);
        // assignRole() --- 将给定的角色分配给模型
        $findSecondUser->assignRole('Maintainer');

        $findFirstUser = User::find(1);
        // 初始化用户角色，将 1 号用户指派为『站长』
        $findFirstUser->assignRole('Founder');

    }
}
