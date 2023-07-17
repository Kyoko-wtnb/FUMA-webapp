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

    public static function processCsvDataWithHeaders(string $file_path, string $header)
    {
        $cols = explode(":", $header);
        if (Storage::exists($file_path)) {
            $file = fopen(Storage::path($file_path), 'r');
            $all_rows = array();
            $head = fgetcsv($file, 0, "\t");
            $index = array();
            foreach ($cols as $c) {
                if (in_array($c, $head)) {
                    $index[] = array_search($c, $head);
                } else {
                    $index[] = -1;
                }
            }
            while ($row = fgetcsv($file, 0, "\t")) {
                $temp = [];
                foreach ($index as $i) {
                    if ($i == -1) {
                        $temp[] = "NA";
                    } else {
                        $temp[] = $row[$i];
                    }
                }
                $all_rows[] = $temp;
            }
            $json = (array('data' => $all_rows));

            return json_encode($json);
        } else {
            return json_encode(array('data' => []));
        }
    }

    public static function csv_file_to_array(string $file_path)
    {
        $data = array();
        $file = fopen($file_path, "r");
        $header = fgetcsv($file, 0, "\t");

        while ($row = fgetcsv($file, 0, "\t")) {
            array_push($data, $row);
        }

        fclose($file);
        return array("header" => $header, "data" => $data);
    }

    /**
     * The following function (result of chatgpt) with some modifications could be used as a drop in replacement
     * for the dt.py code and thus to replace the docker container.
     */
    public static function dt($filedir, $filename, $draw, $headers, $sort_col, $sort_dir, $start, $length, $search = null)
    {
        if ($search) {
            $search = strtolower($search);
        }

        $cols = explode(":", $headers);
        $fin = self::read_csv($filedir, $filename)["data"];
        $header = self::read_csv($filedir, $filename)["header"];
        $hind = array_map(function ($x) use ($header) {
            return array_search($x, $header);
        }, $cols);
        $fin = array_map(function ($row) use ($hind) {
            return array_intersect_key($row, array_flip($hind));
        }, $fin);

        $total = count($fin);
        $filt = $total;

        if ($search) {
            $n = array();
            foreach ($hind as $i) {
                $tmp = array_filter($fin, function ($row) use ($i, $search) {
                    return (strpos(strtolower($row[$i]), $search) !== false);
                });
                $n = array_merge($n, array_keys($tmp));
            }
            $n = array_unique($n);
            $fin = array_intersect_key($fin, array_flip($n));
            $filt = count($fin);
        }

        if ($filt > 0) {
            usort($fin, function ($a, $b) use ($sort_col, $sort_dir) {
                $cmp = strcmp($a[$sort_col], $b[$sort_col]);
                return ($sort_dir == 'asc') ? $cmp : -$cmp;
            });
            $fin = array_slice($fin, $start, $length);
        } else {
            $fin = array();
        }

        $out = array(
            "draw" => $draw,
            "recordsTotal" => $total,
            "recordsFiltered" => $filt,
            "data" => $fin
        );
        return json_encode($out);
    }

    public static function summary_table_in_json(string $file_path)
    {
        if (Storage::exists($file_path)) {
            $lines = file(Storage::path($file_path));
            $out = [];
            foreach ($lines as $l) {
                $l = preg_split("/\t/", chop($l));
                $out[] = [$l[0], $l[1]];
            }
            return json_encode($out);
        } else {
            return json_encode(['error' => 'No summary table found.']);
        }
    }

    public static function summary_table_in_html(string $file_path)
    {
        if (Storage::exists($file_path)) {
            $table = '<table class="table table-bordered" style="width:auto;margin-right:auto; margin-left:auto; text-align: right;"><tbody>';
            $lines = file(Storage::path($file_path));
            foreach ($lines as $l) {
                $line = preg_split("/[\t]/", chop($l));
                $table .= "<tr><td>" . $line[0] . "</td><td>" . $line[1] . "</td></tr>";
            }
            $table .= "</tbody></table>";
            return $table;
        } else {
            return '<p>No summary table found.</p>';
        }
    }

    public static function parse_ini_file(string $file_path)
    {
        if (Storage::exists($file_path)) {
            $params = parse_ini_string(Storage::get($file_path), false, INI_SCANNER_RAW);
            $out = [];
            foreach ($params as $key => $value) {
                $out[] = [$key, $value];
            }
            return json_encode($out);
        } else {
            return json_encode(['error' => 'No params.config file found.']);
        }
    }
}
