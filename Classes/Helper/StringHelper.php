<?php
namespace SpoonerWeb\TcaBuilder\Helper;

/*
 * This file is part of a TYPO3 extension.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

class StringHelper
{
    public static function removeStringInList(array &$fields, string $fieldName)
    {
        array_splice(
            $fields,
            array_search($fieldName, $fields, true),
            1
        );

        ArrayHelper::resetKeys($fields);
    }

    public static function stringStartsWith(string $string, string $startsWith): bool
    {
        if (function_exists('str_starts_with')) {
            return str_starts_with($string, $startsWith);
        }

        return strpos($string, $startsWith) === 0;
    }
}
