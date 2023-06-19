<?php

namespace App\CustomClasses\DockerApi;

use Illuminate\Support\Str;

class DockerNamesBuilder
{
    public static function imageName(string $stackName, string $imageName, string $delimiter = '-'): string
    {
        return $stackName . $delimiter . $imageName;
    }

    public static function containerName($id, string $prefix = 'job', string $sufix = '', string $delimiter = '-'): string
    {
        $uuid = Str::uuid();
        return $prefix . $delimiter . $id . $delimiter . $uuid . ($sufix === '' ? '' : $delimiter . $sufix);
    }
}
