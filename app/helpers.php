<?php
use Illuminate\Support\Facades\Route;

function route_class()
{
    return str_replace('.', '-', Route::currentRouteName());
}

function mkdirs($dir, $mode = 0777)
{
    if (is_dir($dir) || @mkdir($dir, $mode)) return TRUE;
    if (!mkdirs(dirname($dir), $mode)) return FALSE;

    return @mkdir($dir, $mode);
}

function category_nav_active($category_id)
{
    return active_class((if_route('categories.show') && if_route_param('category', $category_id)));
}