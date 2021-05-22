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

use SpoonerWeb\TcaBuilder\Builder\ConcreteBuilder;
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

        self::assertNull($GLOBALS['TCA']['table'][ConcreteBuilder::TYPES_KEYWORD]['type'][ConcreteBuilder::SHOWITEM_KEYWORD]);
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

        self::assertEquals($field, $GLOBALS['TCA'][$table][ConcreteBuilder::TYPES_KEYWORD][$type][ConcreteBuilder::SHOWITEM_KEYWORD]);
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

        self::assertEquals('firstField,secondField', $GLOBALS['TCA']['table'][ConcreteBuilder::TYPES_KEYWORD]['type'][ConcreteBuilder::SHOWITEM_KEYWORD]);

        $this->tcaBuilder
            ->setTable('table')
            ->setType('type')
            ->load()
            ->addField('thirdField')
            ->saveToTca();

        self::assertEquals('firstField,secondField,thirdField', $GLOBALS['TCA']['table'][ConcreteBuilder::TYPES_KEYWORD]['type'][ConcreteBuilder::SHOWITEM_KEYWORD]);
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
            $GLOBALS['TCA']['table'][ConcreteBuilder::TYPES_KEYWORD]['type'][ConcreteBuilder::SHOWITEM_KEYWORD]
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

        self::assertCount(1, $GLOBALS['TCA']['table'][ConcreteBuilder::TYPES_KEYWORD]);

        $this->tcaBuilder
            ->reset()
            ->setTable('table')
            ->removeType();

        self::assertCount(1, $GLOBALS['TCA']['table'][ConcreteBuilder::TYPES_KEYWORD]);
        self::assertNotEmpty($GLOBALS['TCA']['table'][ConcreteBuilder::TYPES_KEYWORD]['type']);
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

        self::assertCount(1, $GLOBALS['TCA']['table'][ConcreteBuilder::TYPES_KEYWORD]);

        $this->tcaBuilder
            ->reset()
            ->setTable('table')
            ->setType('type')
            ->removeType();

        self::assertCount(0, $GLOBALS['TCA']['table'][ConcreteBuilder::TYPES_KEYWORD]);
        self::assertEmpty($GLOBALS['TCA']['table'][ConcreteBuilder::TYPES_KEYWORD]['type']);
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

        self::assertCount(1, $GLOBALS['TCA']['table'][ConcreteBuilder::TYPES_KEYWORD]);

        $this->tcaBuilder
            ->reset()
            ->setTable('table')
            ->removeType('type');

        self::assertCount(0, $GLOBALS['TCA']['table'][ConcreteBuilder::TYPES_KEYWORD]);
        self::assertEmpty($GLOBALS['TCA']['table'][ConcreteBuilder::TYPES_KEYWORD]['type']);
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

        self::assertCount(1, $GLOBALS['TCA']['table'][ConcreteBuilder::TYPES_KEYWORD]);

        $this->tcaBuilder
            ->reset()
            ->setTable('table')
            ->removeType('nonExistingType');

        self::assertCount(1, $GLOBALS['TCA']['table'][ConcreteBuilder::TYPES_KEYWORD]);
        self::assertNotEmpty($GLOBALS['TCA']['table'][ConcreteBuilder::TYPES_KEYWORD]['type']);
    }

    /**
     * @test
     */
    public function addNoFieldAndSaveDirectlyReturnsEmptyString()
    {
        $this->tcaBuilder
            ->saveToTca();

        self::assertEquals('', $GLOBALS['TCA']['table'][ConcreteBuilder::TYPES_KEYWORD]['type'][ConcreteBuilder::SHOWITEM_KEYWORD]);
    }

    /**
     * @test
     */
    public function addFieldWithStringAddsField()
    {
        $this->tcaBuilder
            ->addField('newField')
            ->saveToTca();

        self::assertEquals('newField', $GLOBALS['TCA']['table'][ConcreteBuilder::TYPES_KEYWORD]['type'][ConcreteBuilder::SHOWITEM_KEYWORD]);
    }

    /**
     * @test
     */
    public function addFieldWithStringAndAlternativeLabelAddsFieldWithLabel()
    {
        $this->tcaBuilder
            ->addField('newField', '', 'Label')
            ->saveToTca();

        self::assertEquals('newField;Label', $GLOBALS['TCA']['table'][ConcreteBuilder::TYPES_KEYWORD]['type'][ConcreteBuilder::SHOWITEM_KEYWORD]);
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

        self::assertEquals('newField', $GLOBALS['TCA']['table'][ConcreteBuilder::TYPES_KEYWORD]['type'][ConcreteBuilder::SHOWITEM_KEYWORD]);
        self::assertEquals($overridesConfiguration, $GLOBALS['TCA']['table'][ConcreteBuilder::TYPES_KEYWORD]['type']['columnsOverrides']['newField']);
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

        self::assertEquals('newField,newSecondField', $GLOBALS['TCA']['table'][ConcreteBuilder::TYPES_KEYWORD]['type'][ConcreteBuilder::SHOWITEM_KEYWORD]);
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

        self::assertEquals('newSecondField,newField', $GLOBALS['TCA']['table'][ConcreteBuilder::TYPES_KEYWORD]['type'][ConcreteBuilder::SHOWITEM_KEYWORD]);
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

        self::assertEquals('newField,newSecondField', $GLOBALS['TCA']['table'][ConcreteBuilder::TYPES_KEYWORD]['type'][ConcreteBuilder::SHOWITEM_KEYWORD]);
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

        self::assertEquals('newSecondField,newThirdField,newField', $GLOBALS['TCA']['table'][ConcreteBuilder::TYPES_KEYWORD]['type'][ConcreteBuilder::SHOWITEM_KEYWORD]);
    }

    /**
     * @test
     */
    public function addTwoFieldsWithAltLabelReturnsCorrectFieldsWithAltLabels()
    {
        $this->tcaBuilder
            ->addField('newField')
            ->addField('newSecondField', 'after:newField', 'Second Field')
            ->addField('newThirdField', 'after:newSecondField', 'Third Field')
            ->saveToTca();

        self::assertEquals(
            'newField,newSecondField;Second Field,newThirdField;Third Field',
            $GLOBALS['TCA']['table'][ConcreteBuilder::TYPES_KEYWORD]['type'][ConcreteBuilder::SHOWITEM_KEYWORD]
        );
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

        self::assertEquals('', $GLOBALS['TCA']['table'][ConcreteBuilder::TYPES_KEYWORD]['type'][ConcreteBuilder::SHOWITEM_KEYWORD]);
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

        self::assertEquals('newField', $GLOBALS['TCA']['table'][ConcreteBuilder::TYPES_KEYWORD]['type'][ConcreteBuilder::SHOWITEM_KEYWORD]);
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

        self::assertEquals('newField,newThirdField,newSecondField', $GLOBALS['TCA']['table'][ConcreteBuilder::TYPES_KEYWORD]['type'][ConcreteBuilder::SHOWITEM_KEYWORD]);
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

        self::assertEquals('newThirdField,newSecondField', $GLOBALS['TCA']['table'][ConcreteBuilder::TYPES_KEYWORD]['type'][ConcreteBuilder::SHOWITEM_KEYWORD]);
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

        self::assertEquals('newField,newThirdField;newLabel,newSecondField', $GLOBALS['TCA']['table'][ConcreteBuilder::TYPES_KEYWORD]['type'][ConcreteBuilder::SHOWITEM_KEYWORD]);
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

        self::assertEquals('newField,newSecondField,newThirdField', $GLOBALS['TCA']['table'][ConcreteBuilder::TYPES_KEYWORD]['type'][ConcreteBuilder::SHOWITEM_KEYWORD]);
    }

    /**
     * @test
     */
    public function addPaletteWithStringReturnsPaletteString()
    {
        $this->tcaBuilder
            ->addPalette('newPalette')
            ->saveToTca();

        self::assertEquals(ConcreteBuilder::PALETTE_MARKER . ';;newPalette', $GLOBALS['TCA']['table'][ConcreteBuilder::TYPES_KEYWORD]['type'][ConcreteBuilder::SHOWITEM_KEYWORD]);
    }

    /**
     * @test
     */
    public function addPaletteWithStringAndAlternativeLabelReturnsPaletteStringWithLabel()
    {
        $this->tcaBuilder
            ->addPalette('newPalette', '', 'newLabel')
            ->saveToTca();

        self::assertEquals(ConcreteBuilder::PALETTE_MARKER . ';newLabel;newPalette', $GLOBALS['TCA']['table'][ConcreteBuilder::TYPES_KEYWORD]['type'][ConcreteBuilder::SHOWITEM_KEYWORD]);
    }

    /**
     * @test
     */
    public function addPaletteWithLllStringAndAlternativeLabelReturnsPaletteStringWithLabel()
    {
        $this->tcaBuilder
            ->addPalette('newPalette', '', 'LLL:EXT:myext/Resources/Private/Language/locallang.xlf:newLabel')
            ->saveToTca();

        self::assertEquals(ConcreteBuilder::PALETTE_MARKER . ';LLL:EXT:myext/Resources/Private/Language/locallang.xlf:newLabel;newPalette', $GLOBALS['TCA']['table'][ConcreteBuilder::TYPES_KEYWORD]['type'][ConcreteBuilder::SHOWITEM_KEYWORD]);
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

        self::assertEquals(ConcreteBuilder::PALETTE_MARKER . ';;newPalette,' . ConcreteBuilder::PALETTE_MARKER . ';;newSecondPalette', $GLOBALS['TCA']['table'][ConcreteBuilder::TYPES_KEYWORD]['type'][ConcreteBuilder::SHOWITEM_KEYWORD]);
    }

    /**
     * @test
     */
    public function addThreePalettesWithStringsAndOneWithExactPositionStringReturnsPaletteString()
    {
        $this->tcaBuilder
            ->addPalette('newPalette')
            ->addPalette('newSecondPalette')
            ->addPalette('newThirdPalette', 'before:' . ConcreteBuilder::PALETTE_MARKER . ';;newSecondPalette')
            ->saveToTca();

        self::assertEquals(
            ConcreteBuilder::PALETTE_MARKER . ';;newPalette,' . ConcreteBuilder::PALETTE_MARKER . ';;newThirdPalette,' . ConcreteBuilder::PALETTE_MARKER . ';;newSecondPalette',
            $GLOBALS['TCA']['table'][ConcreteBuilder::TYPES_KEYWORD]['type'][ConcreteBuilder::SHOWITEM_KEYWORD]
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
            ->addPalette('newThirdPalette', 'replace:' . ConcreteBuilder::PALETTE_MARKER . ';;newSecondPalette')
            ->saveToTca();

        self::assertEquals(
            ConcreteBuilder::PALETTE_MARKER . ';;newPalette,' . ConcreteBuilder::PALETTE_MARKER . ';;newThirdPalette',
            $GLOBALS['TCA']['table'][ConcreteBuilder::TYPES_KEYWORD]['type'][ConcreteBuilder::SHOWITEM_KEYWORD]
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
            ->addPalette('newThirdPalette', 'before:' . ConcreteBuilder::PALETTE_MARKER . ';LLL:EXT:myext/Resources/Private/Language/locallang.xlf:newLabel;newSecondPalette')
            ->saveToTca();

        self::assertEquals(
            ConcreteBuilder::PALETTE_MARKER . ';;newPalette,' . ConcreteBuilder::PALETTE_MARKER . ';;newThirdPalette,' . ConcreteBuilder::PALETTE_MARKER . ';LLL:EXT:myext/Resources/Private/Language/locallang.xlf:newLabel;newSecondPalette',
            $GLOBALS['TCA']['table'][ConcreteBuilder::TYPES_KEYWORD]['type'][ConcreteBuilder::SHOWITEM_KEYWORD]
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
            ConcreteBuilder::PALETTE_MARKER . ';;newPalette,' . ConcreteBuilder::PALETTE_MARKER . ';;newThirdPalette,' . ConcreteBuilder::PALETTE_MARKER . ';;newSecondPalette',
            $GLOBALS['TCA']['table'][ConcreteBuilder::TYPES_KEYWORD]['type'][ConcreteBuilder::SHOWITEM_KEYWORD]
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
            ConcreteBuilder::PALETTE_MARKER . ';;newPalette,' . ConcreteBuilder::PALETTE_MARKER . ';;newThirdPalette,' . ConcreteBuilder::PALETTE_MARKER . ';LLL:EXT:myext/Resources/Private/Language/locallang.xlf:newLabel;newSecondPalette',
            $GLOBALS['TCA']['table'][ConcreteBuilder::TYPES_KEYWORD]['type'][ConcreteBuilder::SHOWITEM_KEYWORD]
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
            ConcreteBuilder::PALETTE_MARKER . ';;newPalette,' . ConcreteBuilder::PALETTE_MARKER . ';;newThirdPalette,' . ConcreteBuilder::PALETTE_MARKER . ';newLabel;newSecondPalette',
            $GLOBALS['TCA']['table'][ConcreteBuilder::TYPES_KEYWORD]['type'][ConcreteBuilder::SHOWITEM_KEYWORD]
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
            ConcreteBuilder::PALETTE_MARKER . ';;newSecondPalette',
            $GLOBALS['TCA']['table'][ConcreteBuilder::TYPES_KEYWORD]['type'][ConcreteBuilder::SHOWITEM_KEYWORD]
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
            ConcreteBuilder::PALETTE_MARKER . ';;newPalette,' . ConcreteBuilder::PALETTE_MARKER . ';;newSecondPalette',
            $GLOBALS['TCA']['table'][ConcreteBuilder::TYPES_KEYWORD]['type'][ConcreteBuilder::SHOWITEM_KEYWORD]
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
            ->movePalette('newSecondPalette', 'after:' . ConcreteBuilder::PALETTE_MARKER . ';;newThirdPalette')
            ->saveToTca();

        self::assertEquals(
            ConcreteBuilder::PALETTE_MARKER . ';;newPalette,' . ConcreteBuilder::PALETTE_MARKER . ';;newThirdPalette,' . ConcreteBuilder::PALETTE_MARKER . ';;newSecondPalette',
            $GLOBALS['TCA']['table'][ConcreteBuilder::TYPES_KEYWORD]['type'][ConcreteBuilder::SHOWITEM_KEYWORD]
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
            ->movePalette('newSecondPalette', 'after:' . ConcreteBuilder::PALETTE_MARKER . ';;nonExistingPalette')
            ->saveToTca();

        self::assertEquals(
            ConcreteBuilder::PALETTE_MARKER . ';;newPalette,' . ConcreteBuilder::PALETTE_MARKER . ';;newSecondPalette,' . ConcreteBuilder::PALETTE_MARKER . ';;newThirdPalette',
            $GLOBALS['TCA']['table'][ConcreteBuilder::TYPES_KEYWORD]['type'][ConcreteBuilder::SHOWITEM_KEYWORD]
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
            ConcreteBuilder::DIV_MARKER . ';newDiv',
            $GLOBALS['TCA']['table'][ConcreteBuilder::TYPES_KEYWORD]['type'][ConcreteBuilder::SHOWITEM_KEYWORD]
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
            ConcreteBuilder::DIV_MARKER . ';positionDiv,' . ConcreteBuilder::DIV_MARKER . ';newDiv,' . ConcreteBuilder::DIV_MARKER . ';secondDiv',
            $GLOBALS['TCA']['table'][ConcreteBuilder::TYPES_KEYWORD]['type'][ConcreteBuilder::SHOWITEM_KEYWORD]
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
            ConcreteBuilder::DIV_MARKER . ';positionDiv,' . ConcreteBuilder::DIV_MARKER . ';secondDiv',
            $GLOBALS['TCA']['table'][ConcreteBuilder::TYPES_KEYWORD]['type'][ConcreteBuilder::SHOWITEM_KEYWORD]
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
            ConcreteBuilder::DIV_MARKER . ';newSecondDiv',
            $GLOBALS['TCA']['table'][ConcreteBuilder::TYPES_KEYWORD]['type'][ConcreteBuilder::SHOWITEM_KEYWORD]
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
            ConcreteBuilder::DIV_MARKER . ';newSecondDiv',
            $GLOBALS['TCA']['table'][ConcreteBuilder::TYPES_KEYWORD]['type'][ConcreteBuilder::SHOWITEM_KEYWORD]
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
            ConcreteBuilder::DIV_MARKER . ';newDiv,' . ConcreteBuilder::DIV_MARKER . ';newSecondDiv',
            $GLOBALS['TCA']['table'][ConcreteBuilder::TYPES_KEYWORD]['type'][ConcreteBuilder::SHOWITEM_KEYWORD]
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
            ConcreteBuilder::DIV_MARKER . ';newDiv,' . ConcreteBuilder::DIV_MARKER . ';newSecondDiv',
            $GLOBALS['TCA']['table'][ConcreteBuilder::TYPES_KEYWORD]['type'][ConcreteBuilder::SHOWITEM_KEYWORD]
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
            ConcreteBuilder::DIV_MARKER . ';newDiv',
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
            ConcreteBuilder::DIV_MARKER . ';newSecondDiv',
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

        self::assertEquals([$field => $config], $GLOBALS['TCA']['table'][ConcreteBuilder::TYPES_KEYWORD]['type']['columnsOverrides']);
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
            ['label' => 'customPaletteLabel', ConcreteBuilder::SHOWITEM_KEYWORD => 'header,bodytext,hidden'],
            $GLOBALS['TCA']['table'][ConcreteBuilder::PALETTES_KEYWORD]['custom']
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
            ['label' => 'customPaletteLabel', ConcreteBuilder::SHOWITEM_KEYWORD => 'header,bodytext,hidden'],
            $GLOBALS['TCA']['table'][ConcreteBuilder::PALETTES_KEYWORD]['custom']
        );

        self::assertEquals(
            'field1,' . ConcreteBuilder::PALETTE_MARKER . ';;custom,field2',
            $GLOBALS['TCA']['table'][ConcreteBuilder::TYPES_KEYWORD]['type'][ConcreteBuilder::SHOWITEM_KEYWORD]
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
            $GLOBALS['TCA']['table'][ConcreteBuilder::TYPES_KEYWORD]['type'][ConcreteBuilder::SHOWITEM_KEYWORD]
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
            $GLOBALS['TCA']['table'][ConcreteBuilder::PALETTES_KEYWORD]['custom'][ConcreteBuilder::SHOWITEM_KEYWORD]
        );
        self::assertEquals(
            ConcreteBuilder::PALETTE_MARKER . ';;custom',
            $GLOBALS['TCA']['table'][ConcreteBuilder::TYPES_KEYWORD]['type'][ConcreteBuilder::SHOWITEM_KEYWORD]
        );
    }

    /**
     * @test
     */
    public function addFieldWithPositionToExistingPaletteReturnsConfigurationWithUpdatedPaletteString()
    {
        $this->tcaBuilder
            ->addCustomPalette('custom', ['field1', 'field2'])
            ->addPalette('custom')
            ->addFieldToPalette('custom', 'field3', 'after:field1')
            ->addFieldToPalette('custom', 'field5', 'replace:field2')
            ->saveToTca();

        self::assertEquals(
            'field1,field3,field5',
            $GLOBALS['TCA']['table'][ConcreteBuilder::PALETTES_KEYWORD]['custom'][ConcreteBuilder::SHOWITEM_KEYWORD]
        );
        self::assertEquals(
            ConcreteBuilder::PALETTE_MARKER . ';;custom',
            $GLOBALS['TCA']['table'][ConcreteBuilder::TYPES_KEYWORD]['type'][ConcreteBuilder::SHOWITEM_KEYWORD]
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
            $GLOBALS['TCA']['table'][ConcreteBuilder::PALETTES_KEYWORD]['custom'][ConcreteBuilder::SHOWITEM_KEYWORD]
        );
        self::assertEquals(
            ConcreteBuilder::PALETTE_MARKER . ';;custom',
            $GLOBALS['TCA']['table'][ConcreteBuilder::TYPES_KEYWORD]['type'][ConcreteBuilder::SHOWITEM_KEYWORD]
        );
    }

    /**
     * @test
     */
    public function initializeTypeWithEmptyListReturnsEmptyTypeListAndEmptyOverrides()
    {
        $this->tcaBuilder
            ->addField('field1')
            ->addField('field2', '', '', ['config' => 'input'])
            ->initialize()
            ->saveToTca();

        self::assertEquals(
            '',
            $GLOBALS['TCA']['table'][ConcreteBuilder::TYPES_KEYWORD]['type'][ConcreteBuilder::SHOWITEM_KEYWORD]
        );
        self::assertEquals(
            [],
            $GLOBALS['TCA']['table'][ConcreteBuilder::TYPES_KEYWORD]['type']['columnsOverrides']
        );
    }

    /**
     * @test
     */
    public function initializeTypeWithEmptyListAndThenNewFieldsReturnsTypeListWithNewAddedFields()
    {
        $this->tcaBuilder
            ->addField('field1')
            ->addField('field2', '', '', ['config' => 'input'])
            ->initialize()
            ->addField('field3')
            ->addField('field5', '', '', ['config' => 'input'])
            ->saveToTca();

        self::assertEquals(
            'field3,field5',
            $GLOBALS['TCA']['table'][ConcreteBuilder::TYPES_KEYWORD]['type'][ConcreteBuilder::SHOWITEM_KEYWORD]
        );
        self::assertEquals(
            ['field5' => ['config' => 'input']],
            $GLOBALS['TCA']['table'][ConcreteBuilder::TYPES_KEYWORD]['type']['columnsOverrides']
        );
    }

    /**
     * @test
     */
    public function initializePaletteWithEmptyListReturnsEmptyPaletteList()
    {
        $this->tcaBuilder
            ->addCustomPalette('palette', ['field1', 'field2'])
            ->addPalette('palette')
            ->initializePalette('palette')
            ->saveToTca();

        self::assertEquals(
            '',
            $GLOBALS['TCA']['table'][ConcreteBuilder::PALETTES_KEYWORD]['palette'][ConcreteBuilder::SHOWITEM_KEYWORD]
        );
        self::assertEquals(
            ConcreteBuilder::PALETTE_MARKER . ';;palette',
            $GLOBALS['TCA']['table'][ConcreteBuilder::TYPES_KEYWORD]['type'][ConcreteBuilder::SHOWITEM_KEYWORD]
        );
    }

    /**
     * @test
     */
    public function copyFromExistingTypeToNewTypeReturnsSameList()
    {
        $this->tcaBuilder
            ->addField('field3')
            ->addField('field5')
            ->saveToTca();

        $this->tcaBuilder
            ->loadConfiguration('table', 'newType')
            ->copyFromType('type')
            ->saveToTca();

        self::assertEquals(
            $GLOBALS['TCA']['table'][ConcreteBuilder::TYPES_KEYWORD]['type'],
            $GLOBALS['TCA']['table'][ConcreteBuilder::TYPES_KEYWORD]['newType']
        );
    }

    /**
     * @test
     */
    public function copyFromExistingTypeAndAddingNewFieldReturnsChangedList()
    {
        $this->tcaBuilder
            ->addField('field3')
            ->addField('field5')
            ->saveToTca();

        $this->tcaBuilder
            ->loadConfiguration('table', 'newType')
            ->copyFromType('type')
            ->addField('field4', 'before:field5')
            ->saveToTca();

        self::assertEquals(
            'field3,field4,field5',
            $GLOBALS['TCA']['table'][ConcreteBuilder::TYPES_KEYWORD]['newType'][ConcreteBuilder::SHOWITEM_KEYWORD]
        );
    }

    /**
     * @test
     */
    public function copyFromExistingTypeAndRemovingFieldReturnsChangedList()
    {
        $this->tcaBuilder
            ->addField('field3')
            ->addField('field5')
            ->saveToTca();

        $this->tcaBuilder
            ->loadConfiguration('table', 'newType')
            ->copyFromType('type')
            ->removeField('field3')
            ->saveToTca();

        self::assertEquals(
            'field5',
            $GLOBALS['TCA']['table'][ConcreteBuilder::TYPES_KEYWORD]['newType'][ConcreteBuilder::SHOWITEM_KEYWORD]
        );
    }

    /**
     * @test
     */
    public function returnFromArrayReturnsTypeConfigurationAsArray()
    {
        $override = [
            'config' => [
                'label' => 'Test'
            ]
        ];
        $configuration = $this->tcaBuilder
            ->addField('field1')
            ->addField('field2')
            ->addOverride('field1', $override)
            ->returnAsArray();

        self::assertEquals(
            [
                ConcreteBuilder::SHOWITEM_KEYWORD => 'field1,field2',
                'columnsOverrides' => [
                    'field1' => $override
                ]
            ],
            $configuration
        );
    }

    /**
     * @test
     */
    public function copyFromTextTypeAndMoveHeaderFieldAndUseAltLabelReturnsCorrectFieldsOrderAndLabels()
    {
        $GLOBALS['TCA']['table'][ConcreteBuilder::TYPES_KEYWORD]['text'][ConcreteBuilder::SHOWITEM_KEYWORD] = '--palette--;;general,--palette--;headers,bodytext';

        $this->tcaBuilder
            ->copyFromType('text')
            ->removePalette('headers')
            ->addField(
                'header',
                'after:' . $this->tcaBuilder->getPaletteString('general'),
                'New Header'
            )
            ->addField(
                'subheader',
                'after:header;New Header',
                'New Subheader'
            )
            ->saveToTca();

        self::assertEquals(
            '--palette--;;general,header;New Header,subheader;New Subheader,bodytext',
            $GLOBALS['TCA']['table'][ConcreteBuilder::TYPES_KEYWORD]['type'][ConcreteBuilder::SHOWITEM_KEYWORD]
        );
    }
}
