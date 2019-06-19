<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    //白名单属性，告诉程序那些字段是支持修改的
    protected $fillable = [
        'name','description'
    ];
}
