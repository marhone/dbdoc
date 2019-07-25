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

if (!function_exists('Dbdoc\bin')) {
    function bin()
    {
        return function () {
            $content = (new Doc())->markdown((new DBAL())->tables());

            // @todo: OUTPUT SHOULD BE CONFIGURABLE.
            $OUTPUT = 'dict.md';
            file_put_contents($OUTPUT, $content);
        };
    }
}
