#!/usr/bin/env php
<?php
/*
 * This file is part of dbdoc.
 *
 * (c) 2017-2019 marhone
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
// came with this script.
if (!function_exists('Dbdoc\bin')) {
    // <<<
    if (is_file(__DIR__ . '/../vendor/autoload.php')) {
        require __DIR__ . '/../vendor/autoload.php';
    } elseif (is_file(__DIR__ . '/../../../autoload.php')) {
        require __DIR__ . '/../../../autoload.php';
    } else {
        echo 'dbdoc dependencies not found, be sure to run `composer install`.' . PHP_EOL;
        echo 'See https://getcomposer.org to get Composer.' . PHP_EOL;
        exit(1);
    }
    // >>>
}
//
// Keep this PHP 5.3 code around for a while in case someone is using a globally
if (version_compare(PHP_VERSION, '5.3.6', '<')) {
    $trace = debug_backtrace();
} elseif (version_compare(PHP_VERSION, '5.4.0', '<')) {
    $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
} else {
    $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1);
}

// And go!
call_user_func(Dbdoc\bin());
