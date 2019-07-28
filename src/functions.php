<?php

/*
 * This file is part of dbdoc.
 *
 * (c) 2017-2019 marhone
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dbdoc;

use Dbdoc\dbal\DBAL;
use Dotenv\Dotenv;

if (!function_exists('Dbdoc\bin')) {
    function bin()
    {
        return function () {
            // load env.
            loadenv();

            $host = env('DB_HOST', '');
            $db = env('DB_DATABASE', '');
            $user = env('DB_USERNAME', '');
            $pass = env('DB_PASSWORD', '');

            $docLineScale = env('DOC_LINE_SCALE', 1.2);
            $docFieldIgnore = explode(',', env('DOC_FIELD_IGNORE', ''));
            $filename = env('DOC_NAME', 'dictionary.md');

            (new Doc($filename))
                ->markdown(
                    (new DBAL($host, $db, $user, $pass))->tables(),
                    $docLineScale,
                    $docFieldIgnore
                )
                ->save();
        };
    }
}


if (!function_exists('Dbdoc\loadenv')) {
    function loadenv()
    {
        $dotenv = Dotenv::create(getcwd(), '.env');
        $dotenv->load();
    }
}

if (!function_exists('Dbdoc\env')) {
    function env($key, $default = null)
    {
        $value = getenv($key);
        if ($value === false) {
            return $default;
        }
        return $value;
    }
}