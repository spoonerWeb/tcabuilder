<?php
namespace SpoonerWeb\TcaBuilder\Tests\Unit;

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
use PHPUnit\Framework\TestCase;
use SpoonerWeb\TcaBuilder\TcaBuilder;
use SpoonerWeb\TcaBuilder\TcaCreator;
use SpoonerWeb\TcaBuilder\TcaTemplates\ColumnsTemplates;
use SpoonerWeb\TcaBuilder\TcaTemplates\ControlTemplates;

class TcaCreatorTest extends TestCase
{
    /**
     * @test
     */
    public function instanceCanBeCalled(): void
    {
        self::assertTrue(class_exists(TcaCreator::class));
    }

    /**
     * @test
     */
    public function getControlConfigurationOnlyWithTitleAndLabelReturnsFullControlConfigurationAsArray(): void
    {
        $configuration = TcaCreator::getControlConfiguration(
            'title',
            'label'
        );

        self::assertEquals(
            array_merge(
                [
                    'title' => 'title',
                    'label' => 'label',
                ],
                ControlTemplates::BASIC_TEMPLATE,
                ControlTemplates::LANGUAGE_TEMPLATE,
                ControlTemplates::VERSION_TEMPLATE,
                ControlTemplates::SORTING_TEMPLATE
            ),
            $configuration
        );
    }

    /**
     * @test
     */
    public function getControlConfigurationWithoutAllAdditionsReturnsControlConfigurationAsArray(): void
    {
        $configuration = TcaCreator::getControlConfiguration(
            'title',
            'label',
            [],
            false,
            false,
            false
        );

        self::assertEquals(
            array_merge(
                [
                    'title' => 'title',
                    'label' => 'label',
                ],
                ControlTemplates::BASIC_TEMPLATE
            ),
            $configuration
        );
    }

    /**
     * @test
     */
    public function getControlConfigurationWithLanguageFieldsReturnsControlConfigurationAsArray(): void
    {
        $configuration = TcaCreator::getControlConfiguration(
            'title',
            'label',
            [],
            true,
            false,
            false
        );

        self::assertEquals(
            array_merge(
                [
                    'title' => 'title',
                    'label' => 'label',
                ],
                ControlTemplates::BASIC_TEMPLATE,
                ControlTemplates::LANGUAGE_TEMPLATE
            ),
            $configuration
        );
    }

    /**
     * @test
     */
    public function getControlConfigurationWithVersionFieldsReturnsControlConfigurationAsArray(): void
    {
        $configuration = TcaCreator::getControlConfiguration(
            'title',
            'label',
            [],
            false,
            true,
            false
        );

        self::assertEquals(
            array_merge(
                [
                    'title' => 'title',
                    'label' => 'label',
                ],
                ControlTemplates::BASIC_TEMPLATE,
                ControlTemplates::VERSION_TEMPLATE
            ),
            $configuration
        );
    }

    /**
     * @test
     */
    public function getControlConfigurationWithSortingFieldsReturnsControlConfigurationAsArray(): void
    {
        $configuration = TcaCreator::getControlConfiguration(
            'title',
            'label',
            [],
            false,
            false,
            true
        );

        self::assertEquals(
            array_merge(
                [
                    'title' => 'title',
                    'label' => 'label',
                ],
                ControlTemplates::BASIC_TEMPLATE,
                ControlTemplates::SORTING_TEMPLATE
            ),
            $configuration
        );
    }

    /**
     * @test
     */
    public function getControlConfigurationWithOverridingFieldReturnsChangedControlConfigurationAsArray(): void
    {
        $configuration = TcaCreator::getControlConfiguration(
            'title',
            'label',
            [
                'iconfile' => 'test.png',
                'label' => 'New label',
            ],
            false,
            false,
            false
        );

        self::assertEquals(
            array_merge(
                [
                    'title' => 'title',
                    'label' => 'New label',
                    'iconfile' => 'test.png',
                ],
                ControlTemplates::BASIC_TEMPLATE
            ),
            $configuration
        );
    }

    /**
     * @test
     */
    public function getColumnsConfigurationWithCompleteControlConfigurationReturnsFullColumnsArray(): void
    {
        $columns = TcaCreator::getColumnsConfiguration(
            TcaCreator::getControlConfiguration('title', 'label'),
            'tx_table'
        );

        self::assertEquals(
            [
                'hidden' => ColumnsTemplates::DISABLED_TEMPLATE,
                'sys_language_uid' => ColumnsTemplates::LANGUAGE_FIELD_TEMPLATE,
                'l10n_parent' => ColumnsTemplates::getLanguageParentColumnWithReplacedTableName('tx_table'),
                'l10n_diffsource' => ColumnsTemplates::LANGUAGE_DIFFSOURCE_FIELD_TEMPLATE,
                'l10n_source' => ColumnsTemplates::LANGUAGE_SOURCE_FIELD_TEMPLATE,
            ],
            $columns
        );
    }

    /**
     * @test
     */
    public function getColumnsConfigurationWithCompleteControlConfigurationAndAdditionalColumnReturnsFullColumnsArray(): void
    {
        $columns = TcaCreator::getColumnsConfiguration(
            TcaCreator::getControlConfiguration('title', 'label'),
            'tx_table',
            [
                'title' => [
                    'label' => 'Title',
                    'config' => [
                        'type' => 'input',
                    ],
                ],
            ]
        );

        self::assertEquals(
            [
                'hidden' => ColumnsTemplates::DISABLED_TEMPLATE,
                'sys_language_uid' => ColumnsTemplates::LANGUAGE_FIELD_TEMPLATE,
                'l10n_parent' => ColumnsTemplates::getLanguageParentColumnWithReplacedTableName('tx_table'),
                'l10n_diffsource' => ColumnsTemplates::LANGUAGE_DIFFSOURCE_FIELD_TEMPLATE,
                'l10n_source' => ColumnsTemplates::LANGUAGE_SOURCE_FIELD_TEMPLATE,
                'title' => [
                    'label' => 'Title',
                    'config' => [
                        'type' => 'input',
                    ],
                ],
            ],
            $columns
        );
    }

    /**
     * @test
     */
    public function buildColumnsConfigurationReplacesTableInLanguageParentField(): void
    {
        $tableName = 'tx_table';
        $columns = TcaCreator::getColumnsConfiguration(
            TcaCreator::getControlConfiguration(
                'title',
                'label',
                [],
                true,
                false,
                false
            ),
            'tx_table'
        );

        self::assertEquals(
            $tableName,
            $columns['l10n_parent']['config']['foreign_table']
        );
    }

    /**
     * @test
     */
    public function buildTypesConfigurationReturnsTcaBuilderInstance(): void
    {
        self::assertEquals(
            new TcaBuilder(),
            TcaCreator::buildTypesConfiguration()
        );
    }
}
