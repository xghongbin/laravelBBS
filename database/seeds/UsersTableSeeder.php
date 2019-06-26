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
            'https://iocaffcdn.phphub.org/uploads/images/201710/14/1/s5ehp11z6s.png',
            'https://iocaffcdn.phphub.org/uploads/images/201710/14/1/Lhd1SHqu86.png',
            'https://iocaffcdn.phphub.org/uploads/images/201710/14/1/LOnMrqbHJn.png',
            'https://iocaffcdn.phphub.org/uploads/images/201710/14/1/xAuDMxteQy.png',
            'https://iocaffcdn.phphub.org/uploads/images/201710/14/1/ZqM7iaP4CR.png',
            'https://iocaffcdn.phphub.org/uploads/images/201710/14/1/NDnzMutoxX.png',
        ];

        //  生成数据集合
        $users = factory(User::class)->times(10)->make()
                            ->each(function ($user, $index) use ($faker, $avatars){
                                //  从头像中随机取出一个
                                $user->avatar = $faker->randomElement($avatars);
                            } );

        //  让隐藏字段可见，并将数据集合转换为数组
        //  可以显示 User 模型 $hidden 属性里指定隐藏的字段，此操作确保入库时数据库不会报错。
        $user_array = $users->makeVisible(['password','remember_token'])->toArray();

        //  插入数据库中
        User::insert($user_array);


    }
}
