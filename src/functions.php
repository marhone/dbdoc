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
            $start = microtime(true);
            loadenv();

            $host = env('DB_HOST', '');
            $db = env('DB_DATABASE', '');
            $user = env('DB_USERNAME', '');
            $pass = env('DB_PASSWORD', '');

            $docLineScale = env('DOC_LINE_SCALE', 1.2);
            $docFieldIgnore = explode(',', env('DOC_FIELD_IGNORE', ''));
            $filename = env('DOC_NAME', 'dictionary.md');

            $database = (new DBAL($host, $db, $user, $pass))->tables();
            (new Doc($filename))
                ->markdown(
                    $database,
                    $docLineScale,
                    $docFieldIgnore
                )
                ->save();

            $spent = round(microtime(true) - $start, 3);
            $memory = round(memory_get_usage() / (1024 * 1024), 3);

            $tableCount = count($database['tables']);

            $message = <<<DOC
Using data source: {$db}@{$host}
Found {$tableCount} table(s)

[{$filename}] saved in {$spent} seconds, {$memory} MB memory used.

DOC;
            echo $message;
        };
    }
}

if (!function_exists('Dbdoc\loadenv')) {
    function loadenv()
    {
        $dotenv = new Dotenv(getcwd(), '.env');
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
