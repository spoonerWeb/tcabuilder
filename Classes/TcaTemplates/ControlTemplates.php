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

class ControlTemplates
{
    public const BASIC_TEMPLATE = [
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
        ],
    ];

    public const LANGUAGE_TEMPLATE = [
        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'translationSource' => 'l10n_source',
    ];

    public const VERSION_TEMPLATE = [
        'versioningWS' => true,
        'origUid' => 't3_origuid',
    ];

    public const SORTING_TEMPLATE = [
        'default_sortby' => 'ORDER BY sorting',
        'sortby' => 'sorting'
    ];
}
