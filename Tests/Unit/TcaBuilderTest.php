<?php
namespace SpoonerWeb\TcaBuilder\Unit\Tests;

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

use SpoonerWeb\TcaBuilder\TcaBuilder;

class TcaBuilderTest extends \Nimut\TestingFramework\TestCase\AbstractTestCase
{
    /**
     * @var \SpoonerWeb\TcaBuilder\TcaBuilder
     */
    protected $tcaBuilder;

    public function setUp()
    {
        $this->tcaBuilder = new TcaBuilder();
        $this->tcaBuilder->loadConfiguration('table', 'type');
    }

    public function tearDown()
    {
        unset($this->tcaBuilder);
    }

    /**
     * @test
     */
    public function classCanBeInstantiated()
    {
        self::assertInstanceOf(TcaBuilder::class, $this->tcaBuilder);
    }

    /**
     * @test
     */
    public function saveFieldsWithoutTableAndTypeReturnsNull()
    {
        $this->tcaBuilder
            ->reset()
            ->saveToTca();

        self::assertNull($GLOBALS['TCA']['table']['types']['type']['showitem']);
    }

    /**
     * @test
     */
    public function setTableAndTypeAndOneFieldReturnsFieldInTcaArray()
    {
        $table = 'myTable';
        $type = 'myType';
        $fieldLength = 10;
        $field = substr(
            str_shuffle(
                str_repeat(
                    $x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
                    ceil($fieldLength / strlen($x))
                )
            ),
            1,
            $fieldLength
        );
        $this->tcaBuilder
            ->setTable($table)
            ->setType($type)
            ->addField($field)
            ->saveToTca();

        self::assertEquals($field, $GLOBALS['TCA'][$table]['types'][$type]['showitem']);
    }

    /**
     * @test
     */
    public function loadByTableAndTypeAndManipulateFieldReturnsPreFilledButManipulatedStringList()
    {
        $this->tcaBuilder
            ->addField('firstField')
            ->addField('secondField')
            ->saveToTca();

        self::assertEquals('firstField,secondField', $GLOBALS['TCA']['table']['types']['type']['showitem']);

        $this->tcaBuilder
            ->setTable('table')
            ->setType('type')
            ->load()
            ->addField('thirdField')
            ->saveToTca();

        self::assertEquals('firstField,secondField,thirdField', $GLOBALS['TCA']['table']['types']['type']['showitem']);
    }

    /**
     * @test
     */
    public function useLocalLangFileReturnsChangedLabelString()
    {
        $this->tcaBuilder
            ->useLocalLangFile('EXT:my_ext/Resources/Private/Language/locallang.xlf')
            ->addField('newField', '', 'LANG:newField')
            ->saveToTca();

        self::assertEquals(
            'newField;LLL:EXT:my_ext/Resources/Private/Language/locallang.xlf:newField',
            $GLOBALS['TCA']['table']['types']['type']['showitem']
        );
    }

    /**
     * @test
     */
    public function removeTypeWithoutGivenOrSelectedTypeDoesNothing()
    {
        $this->tcaBuilder
            ->setTable('table')
            ->setType('type')
            ->addField('test')
            ->saveToTca();

        self::assertCount(1, $GLOBALS['TCA']['table']['types']);

        $this->tcaBuilder
            ->reset()
            ->setTable('table')
            ->removeType();

        self::assertCount(1, $GLOBALS['TCA']['table']['types']);
        self::assertNotEmpty($GLOBALS['TCA']['table']['types']['type']);
    }

    /**
     * @test
     */
    public function removeTypeWithSelectedTypeRemovesType()
    {
        $this->tcaBuilder
            ->setTable('table')
            ->setType('type')
            ->addField('test')
            ->saveToTca();

        self::assertCount(1, $GLOBALS['TCA']['table']['types']);

        $this->tcaBuilder
            ->reset()
            ->setTable('table')
            ->setType('type')
            ->removeType();

        self::assertCount(0, $GLOBALS['TCA']['table']['types']);
        self::assertEmpty($GLOBALS['TCA']['table']['types']['type']);
    }

    /**
     * @test
     */
    public function removeTypeWithGivenTypeRemovesType()
    {
        $this->tcaBuilder
            ->setTable('table')
            ->setType('type')
            ->addField('test')
            ->saveToTca();

        self::assertCount(1, $GLOBALS['TCA']['table']['types']);

        $this->tcaBuilder
            ->reset()
            ->setTable('table')
            ->removeType('type');

        self::assertCount(0, $GLOBALS['TCA']['table']['types']);
        self::assertEmpty($GLOBALS['TCA']['table']['types']['type']);
    }

    /**
     * @test
     */
    public function removeTypeWithGivenButUnusedTypeRemovesNothing()
    {
        $this->tcaBuilder
            ->setTable('table')
            ->setType('type')
            ->addField('test')
            ->saveToTca();

        self::assertCount(1, $GLOBALS['TCA']['table']['types']);

        $this->tcaBuilder
            ->reset()
            ->setTable('table')
            ->removeType('nonExistingType');

        self::assertCount(1, $GLOBALS['TCA']['table']['types']);
        self::assertNotEmpty($GLOBALS['TCA']['table']['types']['type']);
    }

    /**
     * @test
     */
    public function addNoFieldAndSaveDirectlyReturnsEmptyString()
    {
        $this->tcaBuilder
            ->saveToTca();

        self::assertEquals('', $GLOBALS['TCA']['table']['types']['type']['showitem']);
    }

    /**
     * @test
     */
    public function addFieldWithStringAddsField()
    {
        $this->tcaBuilder
            ->addField('newField')
            ->saveToTca();

        self::assertEquals('newField', $GLOBALS['TCA']['table']['types']['type']['showitem']);
    }

    /**
     * @test
     */
    public function addFieldWithStringAndAlternativeLabelAddsFieldWithLabel()
    {
        $this->tcaBuilder
            ->addField('newField', '', 'Label')
            ->saveToTca();

        self::assertEquals('newField;Label', $GLOBALS['TCA']['table']['types']['type']['showitem']);
    }

    /**
     * @test
     */
    public function addFieldWithStringAndColumnsOverridesReturnsConfigurationOfFieldAndColumnsOverrides()
    {
        $overridesConfiguration = ['config' => ['type' => 'input']];
        $this->tcaBuilder
            ->addField('newField', '', '', $overridesConfiguration)
            ->saveToTca();

        self::assertEquals('newField', $GLOBALS['TCA']['table']['types']['type']['showitem']);
        self::assertEquals($overridesConfiguration, $GLOBALS['TCA']['table']['types']['type']['columnsOverrides']['newField']);
    }

    /**
     * @test
     */
    public function addTwoFieldsWithStringsAddsTwoFields()
    {
        $this->tcaBuilder
            ->addField('newField')
            ->addField('newSecondField')
            ->saveToTca();

        self::assertEquals('newField,newSecondField', $GLOBALS['TCA']['table']['types']['type']['showitem']);
    }

    /**
     * @test
     */
    public function addTwoFieldsWithStringsAndOneWithPositionAddsTwoCorrectlySortedFields()
    {
        $this->tcaBuilder
            ->addField('newField')
            ->addField('newSecondField', 'before:newField')
            ->saveToTca();

        self::assertEquals('newSecondField,newField', $GLOBALS['TCA']['table']['types']['type']['showitem']);
    }

    /**
     * @test
     */
    public function addTwoFieldsWithStringsAndOneWithNonExistingPositionAddsTwoCorrectlySortedFields()
    {
        $this->tcaBuilder
            ->addField('newField')
            ->addField('newSecondField', 'before:nonExistingField')
            ->saveToTca();

        self::assertEquals('newField,newSecondField', $GLOBALS['TCA']['table']['types']['type']['showitem']);
    }

    /**
     * @test
     */
    public function addThreeFieldsWithStringsAndTwoWithPositionAddsThreeCorrectlySortedFields()
    {
        $this->tcaBuilder
            ->addField('newField')
            ->addField('newSecondField', 'before:newField')
            ->addField('newThirdField', 'before:newField')
            ->saveToTca();

        self::assertEquals('newSecondField,newThirdField,newField', $GLOBALS['TCA']['table']['types']['type']['showitem']);
    }

    /**
     * @test
     */
    public function removeFieldWithStringRemovesField()
    {
        $this->tcaBuilder
            ->addField('newField')
            ->removeField('newField')
            ->saveToTca();

        self::assertEquals('', $GLOBALS['TCA']['table']['types']['type']['showitem']);
    }

    /**
     * @test
     */
    public function removeFieldWithNonExistingStringRemovesNoField()
    {
        $this->tcaBuilder
            ->addField('newField')
            ->removeField('nonExistingField')
            ->saveToTca();

        self::assertEquals('newField', $GLOBALS['TCA']['table']['types']['type']['showitem']);
    }

    /**
     * @test
     */
    public function moveFieldWithStringAndPositionReturnsFieldListInCorrectOrder()
    {
        $this->tcaBuilder
            ->addField('newField')
            ->addField('newSecondField')
            ->addField('newThirdField')
            ->moveField('newThirdField', 'after:newField')
            ->saveToTca();

        self::assertEquals('newField,newThirdField,newSecondField', $GLOBALS['TCA']['table']['types']['type']['showitem']);
    }

    /**
     * @test
     */
    public function replaceFieldWithStringAndPositionReturnsFieldListInCorrectOrder()
    {
        $this->tcaBuilder
            ->addField('newField')
            ->addField('newSecondField')
            ->addField('newThirdField')
            ->moveField('newThirdField', 'replace:newField')
            ->saveToTca();

        self::assertEquals('newThirdField,newSecondField', $GLOBALS['TCA']['table']['types']['type']['showitem']);
    }

    /**
     * @test
     */
    public function moveFieldWithStringAndPositionAndLabelReturnsFieldListInCorrectOrder()
    {
        $this->tcaBuilder
            ->addField('newField')
            ->addField('newSecondField')
            ->addField('newThirdField')
            ->moveField('newThirdField', 'before:newSecondField', 'newLabel')
            ->saveToTca();

        self::assertEquals('newField,newThirdField;newLabel,newSecondField', $GLOBALS['TCA']['table']['types']['type']['showitem']);
    }

    /**
     * @test
     */
    public function moveFieldWithStringAndNonExistingPositionReturnsFieldListInOriginalOrder()
    {
        $this->tcaBuilder
            ->addField('newField')
            ->addField('newSecondField')
            ->addField('newThirdField')
            ->moveField('newThirdField', 'before:nonExistingField', 'newLabel')
            ->saveToTca();

        self::assertEquals('newField,newSecondField,newThirdField', $GLOBALS['TCA']['table']['types']['type']['showitem']);
    }

    /**
     * @test
     */
    public function addPaletteWithStringReturnsPaletteString()
    {
        $this->tcaBuilder
            ->addPalette('newPalette')
            ->saveToTca();

        self::assertEquals('--palette--;;newPalette', $GLOBALS['TCA']['table']['types']['type']['showitem']);
    }

    /**
     * @test
     */
    public function addPaletteWithStringAndAlternativeLabelReturnsPaletteStringWithLabel()
    {
        $this->tcaBuilder
            ->addPalette('newPalette', '', 'newLabel')
            ->saveToTca();

        self::assertEquals('--palette--;newLabel;newPalette', $GLOBALS['TCA']['table']['types']['type']['showitem']);
    }

    /**
     * @test
     */
    public function addPaletteWithLllStringAndAlternativeLabelReturnsPaletteStringWithLabel()
    {
        $this->tcaBuilder
            ->addPalette('newPalette', '', 'LLL:EXT:myext/Resources/Private/Language/locallang.xlf:newLabel')
            ->saveToTca();

        self::assertEquals('--palette--;LLL:EXT:myext/Resources/Private/Language/locallang.xlf:newLabel;newPalette', $GLOBALS['TCA']['table']['types']['type']['showitem']);
    }

    /**
     * @test
     */
    public function addTwoPalettesWithStringsReturnsPaletteString()
    {
        $this->tcaBuilder
            ->addPalette('newPalette')
            ->addPalette('newSecondPalette')
            ->saveToTca();

        self::assertEquals('--palette--;;newPalette,--palette--;;newSecondPalette', $GLOBALS['TCA']['table']['types']['type']['showitem']);
    }

    /**
     * @test
     */
    public function addThreePalettesWithStringsAndOneWithExactPositionStringReturnsPaletteString()
    {
        $this->tcaBuilder
            ->addPalette('newPalette')
            ->addPalette('newSecondPalette')
            ->addPalette('newThirdPalette', 'before:--palette--;;newSecondPalette')
            ->saveToTca();

        self::assertEquals(
            '--palette--;;newPalette,--palette--;;newThirdPalette,--palette--;;newSecondPalette',
            $GLOBALS['TCA']['table']['types']['type']['showitem']
        );
    }

    /**
     * @test
     */
    public function addThreePalettesWithStringsAndMoveOneWithExactPositionStringReturnsPaletteString()
    {
        $this->tcaBuilder
            ->addPalette('newPalette')
            ->addPalette('newSecondPalette')
            ->addPalette('newThirdPalette', 'replace:--palette--;;newSecondPalette')
            ->saveToTca();

        self::assertEquals(
            '--palette--;;newPalette,--palette--;;newThirdPalette',
            $GLOBALS['TCA']['table']['types']['type']['showitem']
        );
    }

    /**
     * @test
     */
    public function addThreePalettesWithStringsAndOneWithExactPositionLllStringReturnsPaletteString()
    {
        $this->tcaBuilder
            ->addPalette('newPalette')
            ->addPalette('newSecondPalette', '', 'LLL:EXT:myext/Resources/Private/Language/locallang.xlf:newLabel')
            ->addPalette('newThirdPalette', 'before:--palette--;LLL:EXT:myext/Resources/Private/Language/locallang.xlf:newLabel;newSecondPalette')
            ->saveToTca();

        self::assertEquals(
            '--palette--;;newPalette,--palette--;;newThirdPalette,--palette--;LLL:EXT:myext/Resources/Private/Language/locallang.xlf:newLabel;newSecondPalette',
            $GLOBALS['TCA']['table']['types']['type']['showitem']
        );
    }

    /**
     * @test
     */
    public function addThreePalettesWithStringsAndOneWithPositionStringUsingFunctionReturnsPaletteString()
    {
        $this->tcaBuilder
            ->addPalette('newPalette')
            ->addPalette('newSecondPalette')
            ->addPalette('newThirdPalette', 'before:' . $this->tcaBuilder->getPaletteString('newSecondPalette'))
            ->saveToTca();

        self::assertEquals(
            '--palette--;;newPalette,--palette--;;newThirdPalette,--palette--;;newSecondPalette',
            $GLOBALS['TCA']['table']['types']['type']['showitem']
        );
    }

    /**
     * @test
     */
    public function addThreePalettesWithStringsAndOneWithPositionLllStringUsingFunctionReturnsPaletteString()
    {
        $this->tcaBuilder
            ->addPalette('newPalette')
            ->addPalette('newSecondPalette', '', 'LLL:EXT:myext/Resources/Private/Language/locallang.xlf:newLabel')
            ->addPalette('newThirdPalette', 'before:' . $this->tcaBuilder->getPaletteString('newSecondPalette'))
            ->saveToTca();

        self::assertEquals(
            '--palette--;;newPalette,--palette--;;newThirdPalette,--palette--;LLL:EXT:myext/Resources/Private/Language/locallang.xlf:newLabel;newSecondPalette',
            $GLOBALS['TCA']['table']['types']['type']['showitem']
        );
    }

    /**
     * @test
     */
    public function addThreePalettesWithStringsAndOneWithLabelAndOneWithPositionStringUsingFunctionReturnsPaletteString()
    {
        $this->tcaBuilder
            ->addPalette('newPalette')
            ->addPalette('newSecondPalette', '', 'newLabel')
            ->addPalette('newThirdPalette', 'before:' . $this->tcaBuilder->getPaletteString('newSecondPalette'))
            ->saveToTca();

        self::assertEquals(
            '--palette--;;newPalette,--palette--;;newThirdPalette,--palette--;newLabel;newSecondPalette',
            $GLOBALS['TCA']['table']['types']['type']['showitem']
        );
    }

    /**
     * @test
     */
    public function addTwoPalettesAndRemoveOneReturnsStringWithOnePalette()
    {
        $this->tcaBuilder
            ->addPalette('newPalette')
            ->addPalette('newSecondPalette')
            ->removePalette('newPalette')
            ->saveToTca();

        self::assertEquals(
            '--palette--;;newSecondPalette',
            $GLOBALS['TCA']['table']['types']['type']['showitem']
        );
    }

    /**
     * @test
     */
    public function addTwoPalettesAndRemoveNonExistingOneReturnsStringWithTwoPalettes()
    {
        $this->tcaBuilder
            ->addPalette('newPalette')
            ->addPalette('newSecondPalette')
            ->removePalette('newThirdPalette')
            ->saveToTca();

        self::assertEquals(
            '--palette--;;newPalette,--palette--;;newSecondPalette',
            $GLOBALS['TCA']['table']['types']['type']['showitem']
        );
    }

    /**
     * @test
     */
    public function moveOnePaletteWithExistingPositionReturnsPalettesInCorrectOrder()
    {
        $this->tcaBuilder
            ->addPalette('newPalette')
            ->addPalette('newSecondPalette')
            ->addPalette('newThirdPalette')
            ->movePalette('newSecondPalette', 'after:--palette--;;newThirdPalette')
            ->saveToTca();

        self::assertEquals(
            '--palette--;;newPalette,--palette--;;newThirdPalette,--palette--;;newSecondPalette',
            $GLOBALS['TCA']['table']['types']['type']['showitem']
        );
    }

    /**
     * @test
     */
    public function moveOnePaletteWithNonExistingPositionReturnsPalettesInCorrectOrder()
    {
        $this->tcaBuilder
            ->addPalette('newPalette')
            ->addPalette('newSecondPalette')
            ->addPalette('newThirdPalette')
            ->movePalette('newSecondPalette', 'after:--palette--;;nonExistingPalette')
            ->saveToTca();

        self::assertEquals(
            '--palette--;;newPalette,--palette--;;newSecondPalette,--palette--;;newThirdPalette',
            $GLOBALS['TCA']['table']['types']['type']['showitem']
        );
    }

    /**
     * @test
     */
    public function addDivWithLabelReturnsListWithDiv()
    {
        $this->tcaBuilder
            ->addDiv('newDiv')
            ->saveToTca();

        self::assertEquals(
            '--div--;newDiv',
            $GLOBALS['TCA']['table']['types']['type']['showitem']
        );
    }

    /**
     * @test
     */
    public function addTwoDivsWithLabelAndPositionReturnsListWithTwoDivs()
    {
        $this->tcaBuilder
            ->addDiv('newDiv')
            ->addDiv('secondDiv')
            ->addDiv('positionDiv', 'before:' . $this->tcaBuilder->getDivString(0))
            ->saveToTca();

        self::assertEquals(
            '--div--;positionDiv,--div--;newDiv,--div--;secondDiv',
            $GLOBALS['TCA']['table']['types']['type']['showitem']
        );
    }

    /**
     * @test
     */
    public function addTwoDivsWithLabelAndReplaceOnePositionReturnsListWithTwoDivs()
    {
        $this->tcaBuilder
            ->addDiv('newDiv')
            ->addDiv('secondDiv')
            ->addDiv('positionDiv', 'replace:' . $this->tcaBuilder->getDivString(0))
            ->saveToTca();

        self::assertEquals(
            '--div--;positionDiv,--div--;secondDiv',
            $GLOBALS['TCA']['table']['types']['type']['showitem']
        );
    }

    /**
     * @test
     */
    public function addTwoDivsWithLabelAndReturnsOneExistingDivByLabelReturnsOneDiv()
    {
        $this->tcaBuilder
            ->addDiv('newDiv')
            ->addDiv('newSecondDiv')
            ->removeDiv('newDiv')
            ->saveToTca();

        self::assertEquals(
            '--div--;newSecondDiv',
            $GLOBALS['TCA']['table']['types']['type']['showitem']
        );
    }

    /**
     * @test
     */
    public function addTwoDivsWithLabelAndReturnsOneExistingDivByPositionReturnsOneDiv()
    {
        $this->tcaBuilder
            ->addDiv('newDiv')
            ->addDiv('newSecondDiv')
            ->removeDiv(0)
            ->saveToTca();

        self::assertEquals(
            '--div--;newSecondDiv',
            $GLOBALS['TCA']['table']['types']['type']['showitem']
        );
    }

    /**
     * @test
     */
    public function addTwoDivsWithLabelAndReturnsOneNonExistingDivByLabelReturnsTwoDivs()
    {
        $this->tcaBuilder
            ->addDiv('newDiv')
            ->addDiv('newSecondDiv')
            ->removeDiv('nonExistingDiv')
            ->saveToTca();

        self::assertEquals(
            '--div--;newDiv,--div--;newSecondDiv',
            $GLOBALS['TCA']['table']['types']['type']['showitem']
        );
    }

    /**
     * @test
     */
    public function addTwoDivsWithLabelAndReturnsOneNonExistingDivByPositionReturnsTwoDivs()
    {
        $this->tcaBuilder
            ->addDiv('newDiv')
            ->addDiv('newSecondDiv')
            ->removeDiv(4)
            ->saveToTca();

        self::assertEquals(
            '--div--;newDiv,--div--;newSecondDiv',
            $GLOBALS['TCA']['table']['types']['type']['showitem']
        );
    }

    /**
     * @test
     */
    public function getDivStringByLabelReturnsCorrectDivString()
    {
        $this->tcaBuilder
            ->addDiv('newDiv')
            ->addDiv('newSecondDiv');

        self::assertEquals(
            '--div--;newDiv',
            $this->tcaBuilder->getDivString('newDiv')
        );
    }

    /**
     * @test
     */
    public function getDivStringByPositionReturnsCorrectDivString()
    {
        $this->tcaBuilder
            ->addDiv('newDiv')
            ->addDiv('newSecondDiv');

        self::assertEquals(
            '--div--;newSecondDiv',
            $this->tcaBuilder->getDivString(1)
        );
    }

    /**
     * @test
     */
    public function getDivStringByNonExistingLabelReturnsEmptyString()
    {
        $this->tcaBuilder
            ->addDiv('newDiv')
            ->addDiv('newSecondDiv');

        self::assertEquals(
            '',
            $this->tcaBuilder->getDivString('nonExistingDiv')
        );
    }

    /**
     * @test
     */
    public function getDivStringByNonExistingPositionReturnsEmptyString()
    {
        $this->tcaBuilder
            ->addDiv('newDiv')
            ->addDiv('newSecondDiv');

        self::assertEquals(
            '',
            $this->tcaBuilder->getDivString(5)
        );
    }

    /**
     * @test
     */
    public function addOverridesReturnsGivenConfigurationInColumnsOverrides()
    {
        $field = 'field';
        $config = [
            'label' => 'Test',
            'config' => [
                'type' => 'check'
            ]
        ];
        $this->tcaBuilder
            ->addOverride(
                $field,
                $config
            )
            ->saveToTca();

        self::assertEquals([$field => $config], $GLOBALS['TCA']['table']['types']['type']['columnsOverrides']);
    }

    /**
     * @test
     */
    public function addCustomPaletteReturnsCustomPaletteInPalettesConfiguration()
    {
        $this->tcaBuilder->addCustomPalette(
            'custom',
            [
                'header',
                'bodytext',
                'hidden'
            ],
            'customPaletteLabel'
        )->saveToTca();

        self::assertEquals(
            ['label' => 'customPaletteLabel', 'showitem' => 'header,bodytext,hidden'],
            $GLOBALS['TCA']['table']['palettes']['custom']
        );
    }

    /**
     * @test
     */
    public function addCustomPaletteWithPositionReturnsCustomPaletteInPalettesConfigurationAndPaletteInType()
    {
        $this->tcaBuilder
            ->addField('field1')
            ->addField('field2')
            ->addCustomPalette(
                'custom',
                [
                    'header',
                    'bodytext',
                    'hidden'
                ],
                'customPaletteLabel',
                'before:field2'
            )
            ->saveToTca();

        self::assertEquals(
            ['label' => 'customPaletteLabel', 'showitem' => 'header,bodytext,hidden'],
            $GLOBALS['TCA']['table']['palettes']['custom']
        );

        self::assertEquals(
            'field1,--palette--;;custom,field2',
            $GLOBALS['TCA']['table']['types']['type']['showitem']
        );
    }

    /**
     * @test
     */
    public function addFieldBeforeFirstFieldReturnsCorrectPosition()
    {
        $this->tcaBuilder
            ->addField('field1')
            ->addField('field2', 'before:field1')
            ->saveToTca();

        self::assertEquals(
            'field2,field1',
            $GLOBALS['TCA']['table']['types']['type']['showitem']
        );
    }

    /**
     * @test
     */
    public function addFieldToExistingPaletteReturnsConfigurationWithUpdatedPaletteString()
    {
        $this->tcaBuilder
            ->addCustomPalette('custom', ['field1', 'field2'])
            ->addPalette('custom')
            ->addFieldToPalette('custom', 'field3')
            ->saveToTca();

        self::assertEquals(
            'field1,field2,field3',
            $GLOBALS['TCA']['table']['palettes']['custom']['showitem']
        );
        self::assertEquals(
            '--palette--;;custom',
            $GLOBALS['TCA']['table']['types']['type']['showitem']
        );
    }
    /**
     * @test
     */
    public function removeFieldOfExistingPaletteReturnsConfigurationWithUpdatedPaletteString()
    {
        $this->tcaBuilder
            ->addCustomPalette('custom', ['field1', 'field2'])
            ->addPalette('custom')
            ->removeFieldFromPalette('custom', 'field1')
            ->saveToTca();

        self::assertEquals(
            'field2',
            $GLOBALS['TCA']['table']['palettes']['custom']['showitem']
        );
        self::assertEquals(
            '--palette--;;custom',
            $GLOBALS['TCA']['table']['types']['type']['showitem']
        );
    }
}
