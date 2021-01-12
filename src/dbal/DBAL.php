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

    public function __construct($host, $db, $user, $pass)
    {
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
            throw new \PDOException($e->getMessage(), (int)$e->getCode());
        }
    }

    public function tables(array $only = [])
    {
        $statement = self::$pdo->query('SHOW TABLE STATUS');
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);

        $tables = array_map(function ($item) use ($only) {
            $payload = [
                'Name' => $item['Name'],
                'Engine' => $item['Engine'],
                'Comment' => $item['Comment'],
            ];
            if (!empty($only)) {
                if (in_array($item['Name'], $only)) {
                    return $payload;
                }
            } else {
                return $payload;
            }
        }, $result);

        $tables = array_filter($tables);
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
                    $fieldType = $item['Type'];
                    $fieldComment = $item['Comment'];
                    if (strpos($fieldType, 'enum') !== false) {
                        $fieldComment .= $this->str_replace_first('enum', '', $fieldType);
                        $fieldType = 'enum';
                    }

                    $length = strlen($field . $fieldType);
                    if ($length >= $maxFieldNameLength) {
                        $maxFieldNameLength = $length;
                    }

                    return [
                        'Field' => $field,
                        'Type' => $fieldType,
                        'Comment' => $fieldComment,
                    ];
                }, $result),
            ];
        }

        return [
            'meta' => [
                'maxFieldNameLength' => $maxFieldNameLength,
            ],
            'tables' => array_reverse($columns),
        ];
    }

    private function str_replace_first($search, $replace, $subject)
    {
        if ($search == '') {
            return $subject;
        }

        $position = strpos($subject, $search);

        if ($position !== false) {
            return substr_replace($subject, $replace, $position, strlen($search));
        }

        return $subject;
    }
}
