<?php

namespace App\Helpers;

class Helper
{
    public static function scripts_path($path = '')
    {
        return app()->basePath() . DIRECTORY_SEPARATOR . 'scripts' . ($path ? DIRECTORY_SEPARATOR . $path : $path);

    }
}