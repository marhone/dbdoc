<?php

/*
 * This file is part of dbdoc.
 *
 * (c) 2017-2019 marhone
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dbdoc\dbal;

use PDO;

class DBAL
{
    private static $pdo = null;

    public function __construct()
    {
        // @TODO: DATA SOURCE SHOULD BE CONFIGURABLE.
        $host = '127.0.0.1';
        $db = 'danmarks';
        $user = 'root';
        $pass = 'shell123';
        $charset = 'utf8mb4';

        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        try {
            if (is_null(self::$pdo)) {
                self::$pdo = new PDO($dsn, $user, $pass, $options);
            }
        } catch (\PDOException $e) {
            throw new \PDOException($e->getMessage(), (int) $e->getCode());
        }
    }

    public function tables()
    {
        $statement = self::$pdo->query('SHOW TABLE STATUS');
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);

        $tables = array_map(function ($item) {
            return [
                'Name' => $item['Name'],
                'Engine' => $item['Engine'],
                'Comment' => $item['Comment'],
            ];
        }, $result);

        $columns = [];
        $maxFieldNameLength = 0;
        foreach ($tables as $table) {
            $statement = self::$pdo->query("SHOW FULL COLUMNS FROM {$table['Name']}");
            // Fetch our result.
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);
            $columns[] = [
                'Name' => $table['Name'],
                'Engine' => $table['Engine'],
                'Comment' => $table['Comment'],
                'Columns' => array_map(function ($item) use (&$maxFieldNameLength) {
                    $field = $item['Field'];
                    $length = strlen($field . $item['Type']);
                    if ($length >= $maxFieldNameLength) {
                        $maxFieldNameLength = $length;
                    }

                    return [
                        'Field' => $field,
                        'Type' => $item['Type'],
                        'Comment' => $item['Comment'],
                    ];
                }, $result),
            ];
        }

        return [
            'meta' => [
                'maxFieldNameLength' => $maxFieldNameLength,
            ],
            'tables' => $columns,
        ];
    }
}
