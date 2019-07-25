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

class Doc
{
    public function markdown($data)
    {
        $content = '';
        $maxFiledLength = $data['meta']['maxFieldNameLength'];
        $tables = $data['tables'];

        foreach ($tables as $table) {
            $annotation = $table['Comment'];
            if (!empty(trim($annotation))) {
                $content .= sprintf("### %s %s\n", $table['Comment'], $table['Name']);
            } else {
                $content .= sprintf("### %s\n", $table['Name']);
            }
            $columns = $table['Columns'];
            $copyable = [];
            foreach ($columns as $column) {
                $field = $column['Field'];
                $type = ' ' . $column['Type'];
                $comment = $column['Comment'];

                // @TODO: IGNORE SHOULD BE CONFIGURABLE.
                $IGNORE = ['id', 'deleted_at', 'created_at', 'updated_at'];
                if (!in_array($field, $IGNORE)) {
                    $copyable[] = $field;
                }

                // @TODO: SCALE SHOULD BE CONFIGURABLE.
                $SCALE = 1.2;
                $type = str_pad($type, $maxFiledLength * $SCALE - strlen($field), '-', STR_PAD_LEFT);

                $fullField = sprintf('%s %s', $field, $type);

                if (!empty(trim($comment))) {
                    $content .= sprintf("* %s : %s\n", $fullField, $comment);
                } else {
                    $content .= sprintf("* %s\n", $fullField);
                }
            }
            $content .= sprintf("> %s\n", implode(', ', $copyable));
            $content .= "\n\n";
        }

        return $content;
    }
}
