<?php

/**
 * Worlds | world flag definitions
 */

namespace surva\worlds\utils;

class FileUtils
{
    /**
     * Copy a world directory recursively
     *
     * @param  string  $from
     * @param  string  $to
     *
     * @return bool
     */
    public static function copyRecursive(string $from, string $to): bool
    {
        if (!is_dir($from)) {
            return false;
        }

        if (is_dir($to)) {
            return false;
        }

        mkdir($to);

        foreach (scandir($from) as $obj) {
            if ($obj === "." or $obj === "..") {
                continue;
            }

            $fromObj = $from . "/" . $obj;
            $toObj   = $to . "/" . $obj;

            is_dir($fromObj) ? self::copyRecursive($fromObj, $toObj) : @copy($fromObj, $toObj);
        }

        return true;
    }

    /**
     * Delete a world directory recursively
     *
     * @param  string  $dir
     *
     * @return bool
     */
    public static function deleteRecursive(string $dir): bool
    {
        if (!is_dir($dir)) {
            return false;
        }

        foreach (scandir($dir) as $obj) {
            if ($obj === "." or $obj === "..") {
                continue;
            }

            $dirObj = $dir . "/" . $obj;

            is_dir($dirObj) ? self::deleteRecursive($dirObj) : unlink($dirObj);
        }

        rmdir($dir);

        return true;
    }
}
