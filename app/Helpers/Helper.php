<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;

class Helper
{
    public static function scripts_path($path = '')
    {
        return app()->basePath() . DIRECTORY_SEPARATOR . 'scripts' . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }

    public static function getFilesContents($filedir, $fileNames)
    {
        $result = array();

        foreach ($fileNames as $fileName) {
            $inputString = Storage::get($filedir . $fileName);

            // Trim any empty lines at the beginning or end of the input string.
            $inputString = trim($inputString, "\n");

            // Split the input string into an array of records using the newline character ("\n") as the delimiter.
            $records = explode("\n", $inputString);

            if (empty($records)) {
                // Handle case where no records were found
                // ...
            }

            // Split the first record into an array of headers.
            $headers = explode("\t", $records[0]);

            if (empty($headers)) {
                // Handle case where no headers were found
                // ...
            }

            // Iterate over the rest of the records and split them into arrays using the same delimiter.
            $data = array();
            for ($i = 1; $i < count($records); $i++) {
                $recordValues = explode("\t", $records[$i]);
                $record = array();
                for ($j = 0; $j < count($headers); $j++) {
                    $record[$headers[$j]] = $recordValues[$j];
                }
                $data[] = $record;
            }

            $result[$fileName] = $data;
        }
        return $result;
    }


    /**
     * This is a private function called "my_glob" that takes two parameters: $filedir (string) and $pattern (string).
     * It lists all the filenames in the given directory using the "Storage::files" method from the Laravel framework.
     * It then filters the resulting array of filenames to only include those that match a specific pattern using the "preg_grep" function.
     * The pattern is specified as a regular expression, so it can match filenames with a specific format, such as "filename.extension".
     * Finally, the function returns an array of the filenames that match the pattern.
     */
    public static function my_glob($filedir, $pattern)
    {
        // list all filenames in given path
        $allFiles = Storage::files($filedir);

        // filter the ones that match the filename.* 
        $matchingFiles = preg_grep($pattern, $allFiles);
        $matchingFiles = array_values($matchingFiles);
        // return the matching filenames
        return $matchingFiles;
    }
}
