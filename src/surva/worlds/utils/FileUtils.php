<?php

/**
 * Worlds | world flag definitions
 */

namespace surva\worlds\utils;

use Exception;

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

        try {
            mkdir($to);
        } catch (Exception $e) {
            return false;
        }

        $success = true;

        foreach (scandir($from) as $obj) {
            if ($obj === "." or $obj === "..") {
                continue;
            }

            $fromObj = $from . "/" . $obj;
            $toObj   = $to . "/" . $obj;

            if (is_dir($fromObj)) {
                $success = self::copyRecursive($fromObj, $toObj);

                continue;
            }

            try {
                copy($fromObj, $toObj);
            } catch (Exception $e) {
                $success = false;
            }
        }

        return $success;
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

        $success = true;

        foreach (scandir($dir) as $obj) {
            if ($obj === "." or $obj === "..") {
                continue;
            }

            $dirObj = $dir . "/" . $obj;

            if (is_dir($dirObj)) {
                $success = self::deleteRecursive($dirObj);

                continue;
            }

            try {
                unlink($dirObj);
            } catch (Exception $e) {
                $success = false;
            }
        }

        try {
            rmdir($dir);
        } catch (Exception $e) {
            $success = false;
        }

        return $success;
    }
}
