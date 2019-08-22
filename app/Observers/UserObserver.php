<?php

namespace App\Observers;

use App\Models\User;

// creating, created, updating, updated, saving,
// saved,  deleting, deleted, restoring, restored

class UserObserver
{
    public function creating(User $user)
    {
        if(empty($user->avatar)){
            $avatars = [
                getenv('APP_URL').'/uploads/images/myAvatar-1.png',
                getenv('APP_URL').'/uploads/images/myAvatar-2.png',
                getenv('APP_URL').'/uploads/images/myAvatar-3.png',
                getenv('APP_URL').'/uploads/images/myAvatar-4.png',
                getenv('APP_URL').'/uploads/images/myAvatar-5.png',
                getenv('APP_URL').'/uploads/images/myAvatar-6.png'
            ];
            $user->avatar = $avatars[array_rand($avatars,1)];
        }
    }

    public function updating(User $user)
    {
        //
    }
}