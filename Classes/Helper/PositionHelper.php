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

use TYPO3\CMS\Core\Utility\GeneralUtility;

class PositionHelper
{
    public static function addFieldToPosition(array &$fields, string $fieldName, string $position = '')
    {
        if ($position === '') {
            $fields[] = $fieldName;
            return;
        }

        [$direction,] = GeneralUtility::trimExplode(':', $position);
        $fieldNameToSearch = str_replace($direction . ':', '', $position);
        $key = array_search($fieldNameToSearch, $fields, true);
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
            }
        }

        ArrayHelper::resetKeys($fields);
    }
}
