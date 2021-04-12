<?php
namespace SpoonerWeb\TcaBuilder;

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

use SpoonerWeb\TcaBuilder\TcaTemplates\ColumnsTemplates;
use SpoonerWeb\TcaBuilder\TcaTemplates\ControlTemplates;

class TcaCreator
{
    public static function getControlConfiguration(
        string $title,
        string $label,
        array $additionalConfiguration = [],
        $addLanguageFields = true,
        $addVersioningFields = true,
        $defaultSorting = true
    ): array {
        $configuration['title'] = $title;
        $configuration['label'] = $label;

        $configuration = array_merge(
            $configuration,
            ControlTemplates::BASIC_TEMPLATE
        );

        if ($addLanguageFields) {
            $configuration = array_merge(
                $configuration,
                ControlTemplates::LANGUAGE_TEMPLATE
            );
        }

        if ($addVersioningFields) {
            $configuration = array_merge(
                $configuration,
                ControlTemplates::VERSION_TEMPLATE
            );
        }

        if ($defaultSorting) {
            $configuration = array_merge(
                $configuration,
                ControlTemplates::SORTING_TEMPLATE
            );
        }

        if (!empty($additionalConfiguration)) {
            $configuration = array_merge($configuration, $additionalConfiguration);
        }

        return $configuration;
    }

    public static function getColumnsConfiguration(
        array $controlConfiguration,
        string $tableName,
        array $additionalColumns = []
    ): array {
        $columns = [];
        if ($controlConfiguration['enablecolumns']['disabled']) {
            $columns[$controlConfiguration['enablecolumns']['disabled']] = ColumnsTemplates::DISABLED_TEMPLATE;
        }

        if ($controlConfiguration['languageField']) {
            $columns[$controlConfiguration['languageField']] = ColumnsTemplates::LANGUAGE_FIELD_TEMPLATE;
        }

        if ($controlConfiguration['transOrigPointerField']) {
            $columns[$controlConfiguration['transOrigPointerField']] = ColumnsTemplates::getLanguageParentColumnWithReplacedTableName($tableName);
        }

        if ($controlConfiguration['transOrigDiffSourceField']) {
            $columns[$controlConfiguration['transOrigDiffSourceField']] = ColumnsTemplates::LANGUAGE_DIFFSOURCE_FIELD_TEMPLATE;
        }

        if ($controlConfiguration['translationSource']) {
            $columns[$controlConfiguration['translationSource']] = ColumnsTemplates::LANGUAGE_SOURCE_FIELD_TEMPLATE;
        }

        if ($additionalColumns) {
            $columns = array_merge($columns, $additionalColumns);
        }

        return $columns;
    }

    public static function buildTypesConfiguration(): TcaBuilder
    {
        $tcaBuilder = new TcaBuilder();

        return $tcaBuilder->reset();
    }
}
