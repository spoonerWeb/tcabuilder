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
    public static function removeStringInList(array &$fields, string $fieldName): void
    {
        array_splice(
            $fields,
            self::findFieldVariantInList($fieldName, $fields),
            1
        );

        ArrayHelper::resetKeys($fields);
    }

    public static function stringStartsWith(string $string, string $startsWith): bool
    {
        return strpos($string, $startsWith) === 0;
    }

    public static function removeLabelFromFieldName(string $fieldName): string
    {
        $fieldNameWithoutLabel = '';
        if (PositionHelper::fieldHasLabel($fieldName)) {
            [$fieldNameWithoutLabel,] = ArrayHelper::trimExplode(';', $fieldName);
        }

        return $fieldNameWithoutLabel ?: $fieldName;
    }

    public static function findFieldVariantInList(string $fieldName, array $fields): ?int
    {
        $pattern = '/' . preg_quote($fieldName, '/') . '(;\w?)?/';
        $matches = preg_grep($pattern, $fields);

        return array_key_first($matches);
    }
}
