<?php

class FileHelper
{
    public static function createFile($name, $address)
    {
        touch($address . '/' . $name);
    }

    public static function createDirectory($name, $address)
    {
        $path = $address . '/' . $name;
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
    }

    public static function editFile($name, $address, $content)
    {
        $path = $address . '/' . $name;
        if (is_file($path)) {
            file_put_contents($path, $content);
        }
    }

    public static function moveFile($from, $to)
    {
        if (!is_file($from)) {
            return;
        }
        if (!file_exists(dirname($to))) {
            mkdir(dirname($to), 0777, true);
        }
        rename($from, $to);
    }

    public static function copyFile($from, $to)
    {
        if (!is_file($from)) {
            return;
        }
        if (!file_exists(dirname($to))) {
            mkdir(dirname($to), 0777, true);
        }
        copy($from, $to);
    }

    public static function moveDirectory($from, $to)
    {
        if (is_dir($from)) {
            mkdir(dirname($to), 0777, true);
            rename($from, $to);
        }
    }

    public static function custom_copy($src, $dst)
    {

        // open the source directory
        $dir = opendir($src);

        // Make the destination directory if not exist
        @mkdir($dst);

        // Loop through the files in source directory
        while ($file = readdir($dir)) {

            if (($file != '.') && ($file != '..')) {
                if (is_dir($src . '/' . $file)) {

                    // Recursively calling custom copy function
                    // for sub directory 
                    self::custom_copy($src . '/' . $file, $dst . '/' . $file);
                } else {
                    copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }

        closedir($dir);
    }

    public static function copyDirectory($from, $to)
    {
        if (is_dir($from)) {
            mkdir(dirname($to), 0777, true);
            self::custom_copy($from, $to);
        }
    }

    public static function deleteFile($name, $address)
    {
        $path = $address . '/' . $name;
        if (is_file($path)) {
            unlink($path);
        }
    }

    public static function deleteDirectory($address)
    {
        if (is_dir($address)) {
            $objects = scandir($address);
            foreach ($objects as $object) {
                if ($object != '.' && $object != '..') {
                    if (is_dir($address . '/' . $object) && !is_link($address . '/' . $object))
                        rmdir($address . '/' . $object);
                    else
                        unlink($address . '/' . $object);
                }
            }
            rmdir($address);
        }
    }

    public static function getFile($name, $address)
    {
        $path = $address . '/' . $name;
        return file_exists($path) ? file_get_contents($path) : '';
    }

    public static function listDirectory($address)
    {
        $files = scandir($address);
        $pos = array_search('.', $files);
        if ($pos !== false) {
            unset($files[$pos]);
            $files = array_values($files);
        }
        usort($files, function ($a, $b) {
            $a_is_dir = is_dir($a);
            $b_is_dir = is_dir($b);
            if ($a_is_dir == $b_is_dir) {
                return strcmp($a, $b);
            }

            return $a_is_dir ? -1 : 1;
        });

        return $files;
    }

    public static function searchFile($name, $address)
    {
        $result = [];
        foreach (scandir($address) as $path) {
            if (in_array($path, ['.', '..'])) {
                continue;
            }
            $path = $address . '/' . $path;
            if (!is_file($path)) {
                $result = array_merge($result, self::searchFile($name, $path));
                continue;
            }
            if (basename($path) == $name) {
                $result[] = $path;
            }
        }

        return $result;
    }

    public static function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
