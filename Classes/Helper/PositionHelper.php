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

use SpoonerWeb\TcaBuilder\Builder\ConcreteBuilder;

class PositionHelper
{
    public static function addFieldToPosition(array &$fields, string $fieldName, string $position = ''): void
    {
        if ($position === '') {
            $fields[] = $fieldName;
            return;
        }

        [$direction,] = ArrayHelper::trimExplode(':', $position);
        $fieldNameToSearch = str_replace($direction . ':', '', $position);
        $key = array_search($fieldNameToSearch, $fields, true);
        if ($key === false) {
            $fieldNameToSearchWithoutLabel = StringHelper::removeLabelFromFieldName($fieldNameToSearch);
            $shortenedFields = [];
            foreach ($fields as $key => $field) {
                $shortenedFields[$key] = StringHelper::removeLabelFromFieldName($field);
            }
            $key = array_search($fieldNameToSearchWithoutLabel, $shortenedFields, true);
        }
        if ($key !== false) {
            switch ($direction) {
                case 'before':
                    array_splice($fields, $key, 0, $fieldName);
                    break;
                case 'replace':
                    array_splice($fields, $key, 1, $fieldName);
                    break;
                case 'after':
                    array_splice($fields, ++$key, 0, $fieldName);
                    break;
                default:
            }
        }

        ArrayHelper::resetKeys($fields);
    }

    public static function fieldHasLabel(string $field): bool
    {
        $countingSemicolons = count_chars($field, 1)[ord(';')] ?? 0;

        return $countingSemicolons === 1 && ArrayHelper::trimExplode(';', $field)[0] !== ConcreteBuilder::DIV_MARKER;
    }
}
