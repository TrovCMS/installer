<?php

namespace App;

class Environment
{
    public static function isMac(): bool
    {
        return PHP_OS === 'Darwin';
    }

    public static function isWindows(): bool
    {
        return PHP_OS_FAMILY == 'Windows';
    }

    public static function isLinux(): bool
    {
        return PHP_OS_FAMILY == 'Linux';
    }
}
