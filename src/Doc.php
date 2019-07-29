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
    protected $content = '';
    protected $filename;

    public function __construct($filename)
    {
        $this->filename = $filename;
    }

    public function markdown($data, $lineScale, $ignore)
    {
        $maxFiledLength = $data['meta']['maxFieldNameLength'];
        $tables = $data['tables'];

        foreach ($tables as $table) {
            $annotation = $table['Comment'];
            if (!empty(trim($annotation))) {
                $this->content .= sprintf("### %s %s\n", $table['Comment'], $table['Name']);
            } else {
                $this->content .= sprintf("### %s\n", $table['Name']);
            }
            $columns = $table['Columns'];
            $copyable = [];
            foreach ($columns as $column) {
                $field = $column['Field'];
                $type = ' ' . $column['Type'];
                $comment = $column['Comment'];

                if (!in_array($field, $ignore)) {
                    $copyable[] = sprintf("'%s'", $field);
                }

                $type = str_pad($type, $maxFiledLength * $lineScale - strlen($field), '-', STR_PAD_LEFT);

                $fullField = sprintf('%s %s', $field, $type);

                if (!empty(trim($comment))) {
                    $this->content .= sprintf("* %s : %s\n", $fullField, $comment);
                } else {
                    $this->content .= sprintf("* %s\n", $fullField);
                }
            }
            $this->content .= sprintf("> %s\n", implode(', ', $copyable));
            $this->content .= "\n\n";
        }

        return $this;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function save()
    {
        file_put_contents($this->filename, $this->content);
    }
}
