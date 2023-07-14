<?php

namespace App\CustomClasses;

use Illuminate\Support\Facades\Storage;
use ZipArchive;

class myFile
{
    public static function fileValidationAndStore($file, $file_name, $filedir): void
    {
        $acceptable_text_mime_types = array(
            "text/plain",
            "application/octet-stream"
        );

        $acceptable_zip_mime_types = array(
            "application/zip",
            "application/x-zip",
            "application/x-zip-compressed"
        );

        $acceptable_gzip_mime_types = array(
            "application/x-gzip",
            "application/gzip"
        );

        $type = $file->getClientMimeType();

        if (in_array($type, $acceptable_text_mime_types)) {
            Storage::put($filedir . '/' . $file_name, file_get_contents($file));

        } else if (in_array($type, $acceptable_zip_mime_types)) {
            Storage::put($filedir . '/' . 'temp', file_get_contents($file));

            $zip = new ZipArchive;
            $zip->open(Storage::path($filedir . '/temp'));


            $zf = $zip->getNameIndex(0);
            $zip->extractTo(Storage::path($filedir));
            $zip->close();

            Storage::move($filedir . '/' . $zf, $filedir . '/' . $file_name);
            Storage::delete($filedir . '/temp');
        } else if (in_array($type, $acceptable_gzip_mime_types)) {
            Storage::put($filedir . '/' . 'temp', file_get_contents($file));

            $buffer_size = 4096; // The number of bytes that needs to be read at a specific time, 4KB here
            $file = gzopen(Storage::path($filedir . '/temp'), 'rb'); //Opening the file in binary mode
            $out_file = fopen(Storage::path($filedir . '/' . $file_name), 'wb');
            // Keep repeating until the end of the input file
            while (!gzeof($file)) {
                fwrite($out_file, gzread($file, $buffer_size)); //Read buffer-size bytes.
            }
            fclose($out_file); //Close the files once they are done with
            gzclose($file);

            Storage::delete($filedir . '/temp');
        }
        return;
    }
}
