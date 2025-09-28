<?php

namespace App\Helpers;

use App\Exceptions\ConfigFileNotFoundException;

class Config
{
    public static function getFileContents(string $fileName)
    {
        $path = realpath(__DIR__ . "/../configs/$fileName.php");

        if (!file_exists($path)) {
            throw new ConfigFileNotFoundException();
        }

        return require $path;
    }

    public static function getConfig(string $fileName, string $key = null)
    {
        $fileContents = self::getFileContents($fileName);

        if (!$key) {
            return $fileContents;
        }

        return $fileContents[$key] ?? null;
    }
}
