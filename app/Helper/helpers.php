<?php

if (! function_exists('scripts_path')) {
    /**
     * Get the path to the scripts directory.
     * If an argument is passed it is appended to the path.
     *
     * @param  string  $path
     * @return string
     */
    function scripts_path($path = '')
    {
        return app()->basePath().DIRECTORY_SEPARATOR.'scripts'.($path ? DIRECTORY_SEPARATOR.$path : $path);
    }
}