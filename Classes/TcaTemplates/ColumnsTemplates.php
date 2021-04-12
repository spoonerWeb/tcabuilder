<?php
namespace SpoonerWeb\TcaBuilder\TcaTemplates;

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

class ColumnsTemplates
{
    public const DISABLED_TEMPLATE = [
        'exclude' => true,
        'label' => 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.hidden',
        'config' => [
            'type' => 'check',
            'default' => 0
        ]
    ];

    public const LANGUAGE_FIELD_TEMPLATE = [
        'exclude' => true,
        'label' => 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.language',
        'config' => [
            'type' => 'select',
            'renderType' => 'selectSingle',
            'special' => 'languages',
            'items' => [
                [
                    'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.allLanguages',
                    -1,
                    'flags-multiple'
                ],
            ],
            'default' => 0,
        ]
    ];

    public const LANGUAGE_PARENT_FIELD_TEMPLATE = [
        'displayCond' => 'FIELD:sys_language_uid:>:0',
        'exclude' => true,
        'label' => 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.l18n_parent',
        'config' => [
            'type' => 'select',
            'renderType' => 'selectSingle',
            'items' => [
                ['', 0],
            ],
            'foreign_table' => '###TCABUILDER_TABLE###',
            'foreign_table_where' => 'AND ###TCABUILDER_TABLE###.pid=###CURRENT_PID### AND ###TCABUILDER_TABLE###.sys_language_uid IN (-1,0)',
            'default' => 0,
        ]
    ];

    public const LANGUAGE_DIFFSOURCE_FIELD_TEMPLATE = [
        'config' => [
            'type' => 'passthrough',
            'default' => '',
        ]
    ];

    public const LANGUAGE_SOURCE_FIELD_TEMPLATE = [
        'config' => [
            'type' => 'passthrough',
            'default' => 0,
        ]
    ];

    public static function getLanguageParentColumnWithReplacedTableName(string $tableName): array
    {
        $config = self::LANGUAGE_PARENT_FIELD_TEMPLATE;
        foreach (['foreign_table', 'foreign_table_where'] as $field) {
            $config['config'][$field] = str_replace(
                '###TCABUILDER_TABLE###',
                $tableName,
                $config['config'][$field]
            );
        }

        return $config;
    }
}
