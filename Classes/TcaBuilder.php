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
use SpoonerWeb\TcaBuilder\Builder\ConcreteBuilder;
use SpoonerWeb\TcaBuilder\Builder\ConcretePaletteBuilder;
use SpoonerWeb\TcaBuilder\Helper\ArrayHelper;
use SpoonerWeb\TcaBuilder\Helper\PositionHelper;
use SpoonerWeb\TcaBuilder\Helper\StringHelper;

class TcaBuilder
{
    /**
     * @var ConcreteBuilder
     */
    protected $tcaBuilder;

    /**
     * @var ConcretePaletteBuilder
     */
    protected $paletteBuilder;

    public function __construct()
    {
        $this->tcaBuilder = new ConcreteBuilder();
        $this->paletteBuilder = new ConcretePaletteBuilder();
    }

    /**
     * Resets all fields
     *
     * @return TcaBuilder
     */
    public function reset(): TcaBuilder
    {
        $this->tcaBuilder->reset();

        return $this;
    }

    /**
     * Sets the table to configure
     *
     * @param string $table
     * @return TcaBuilder
     */
    public function setTable(string $table): TcaBuilder
    {
        $this->tcaBuilder->setTable($table);

        return $this;
    }

    /**
     * Sets the type to configure
     *
     * @param string $type
     * @return TcaBuilder
     */
    public function setType(string $type): TcaBuilder
    {
        $this->tcaBuilder->setType($type);

        return $this;
    }

    /**
     * Removes a given or selected type
     *
     * @param string $type
     * @return TcaBuilder
     */
    public function removeType(string $type = ''): TcaBuilder
    {
        $this->tcaBuilder->removeType($type);

        return $this;
    }

    /**
     * Sets a locallang file (beginning with 'EXT:') to be used
     * whenever using a label (label must begin with 'LANG:')
     *
     * @param string $localLangFile
     * @return TcaBuilder
     */
    public function useLocalLangFile(string $localLangFile)
    {
        $this->tcaBuilder->useLocalLangFile($localLangFile);

        return $this;
    }

    /**
     * Adds a field to TCA at the end or at specific position
     *
     * @param string $fieldName
     * @param string $position
     * @param string $altLabel
     * @param array $columnsOverrides
     * @return TcaBuilder
     */
    public function addField(string $fieldName, string $position = '', string $altLabel = '', array $columnsOverrides = []): TcaBuilder
    {
        if ($position === '') {
            $this->tcaBuilder->addField($fieldName, '', $altLabel, $columnsOverrides);

            return $this;
        }
        [,$field] = ArrayHelper::trimExplode(':', $position);
        $fieldWithoutLabel = '';
        if (PositionHelper::fieldHasLabel($field)) {
            $fieldWithoutLabel = StringHelper::removeLabelFromFieldName($field);
        }
        $doesFieldExist = $this->tcaBuilder->doesFieldExist($field) || ($fieldWithoutLabel && $this->tcaBuilder->doesFieldExist($fieldWithoutLabel));
        if ($doesFieldExist === false) {
            $this->tcaBuilder->addField($fieldName, '', $altLabel, $columnsOverrides);
        } else {
            $this->tcaBuilder->addField($fieldName, $position, $altLabel, $columnsOverrides);
        }

        return $this;
    }

    /**
     * Removes an existing field
     *
     * @param string $fieldName
     * @return TcaBuilder
     */
    public function removeField(string $fieldName): TcaBuilder
    {
        $this->tcaBuilder->removeField($fieldName);

        return $this;
    }

    /**
     * @param string $fieldName
     * @param string $newPosition
     * @param string $newLabel
     * @return TcaBuilder
     */
    public function moveField(string $fieldName, string $newPosition, string $newLabel = ''): TcaBuilder
    {
        if ($this->tcaBuilder->doesFieldExist($fieldName) && $this->tcaBuilder->doesFieldExist(ArrayHelper::trimExplode(':', $newPosition)[1])) {
            $this->tcaBuilder->removeField($fieldName);
            $this->tcaBuilder->addField($fieldName, $newPosition, $newLabel);
        }

        return $this;
    }

    /**
     * Adds a palette with given name at the end or at specific position
     *
     * @param string $paletteName
     * @param string $position
     * @param string $altLabel
     * @return TcaBuilder
     */
    public function addPalette(string $paletteName, string $position = '', string $altLabel = ''): TcaBuilder
    {
        $this->tcaBuilder->addPalette($paletteName, $position, $altLabel);

        return $this;
    }

    /**
     * Removes a palette by name
     *
     * @param string $paletteName
     * @return TcaBuilder
     */
    public function removePalette(string $paletteName): TcaBuilder
    {
        $this->tcaBuilder->removePalette($paletteName);

        return $this;
    }

    /**
     * @param string $paletteName
     * @param string $newPosition
     * @param string $newLabel
     * @return TcaBuilder
     */
    public function movePalette(string $paletteName, string $newPosition, string $newLabel = ''): TcaBuilder
    {
        if ($this->tcaBuilder->doesFieldExist(ArrayHelper::trimExplode(':', $newPosition)[1])) {
            $this->tcaBuilder->removePalette($paletteName);
            $this->tcaBuilder->addPalette($paletteName, $newPosition, $newLabel);
        }

        return $this;
    }

    /**
     * Returns full field name of palette
     *
     * @param string $paletteName
     * @return string
     */
    public function getPaletteString(string $paletteName): string
    {
        return $this->tcaBuilder->getPaletteFieldName($paletteName);
    }

    /**
     * Adds a div at the end or specific position
     *
     * @param string $divName
     * @param string $position
     * @return TcaBuilder
     */
    public function addDiv(string $divName, string $position = ''): TcaBuilder
    {
        $this->tcaBuilder->addDiv($divName, $position);

        return $this;
    }

    /**
     * Removes a div by either position (integer offset) or label
     *
     * @param $identifier
     * @return TcaBuilder
     */
    public function removeDiv($identifier): TcaBuilder
    {
        if (is_int($identifier)) {
            $this->tcaBuilder->removeDivByPosition((int)$identifier);
        } else {
            $this->tcaBuilder->removeDivByLabel($identifier);
        }

        return $this;
    }

    /**
     * Gets the complete string defined in types list by
     * position (int) or label (string)
     *
     * @param string|int $identifier
     * @return string
     */
    public function getDivString($identifier): string
    {
        if (is_int($identifier)) {
            $divString = $this->tcaBuilder->getDivByPosition((int)$identifier);
        } else {
            $divString = $this->tcaBuilder->getDivByLabel($identifier);
        }

        return $divString;
    }

    /**
     * Adds a custom override for a field
     *
     * @param string $fieldName
     * @param array $override
     * @return TcaBuilder
     */
    public function addOverride(string $fieldName, array $override): TcaBuilder
    {
        $this->tcaBuilder->addColumnsOverrides($fieldName, $override);

        return $this;
    }

    /**
     * Adds a new custom palette which can be added afterwards
     *
     * @param string $paletteId
     * @param array $showItems
     * @param string $label
     * @param string $position
     * @return TcaBuilder
     */
    public function addCustomPalette(string $paletteId, array $showItems, string $label = '', string $position = ''): TcaBuilder
    {
        $this->tcaBuilder->addCustomPalette($paletteId, $showItems, $label, $position);

        return $this;
    }

    /**
     * @param string $paletteId
     * @param string $field
     * @param string $position
     * @return TcaBuilder
     */
    public function addFieldToPalette(string $paletteId, string $field, string $position = ''): TcaBuilder
    {
        $this->saveToTca(false);
        $this->paletteBuilder->load($paletteId, $this->tcaBuilder->getTable());
        $this->paletteBuilder->addField($field, $position);
        $this->tcaBuilder->setFieldsForPalette($paletteId, $this->paletteBuilder->returnCurrentConfiguration());
        $this->paletteBuilder->saveToTca();
        $this->saveToTca(false);

        return $this;
    }

    /**
     * @param string $paletteId
     * @param string $field
     * @return TcaBuilder
     */
    public function removeFieldFromPalette(string $paletteId, string $field): TcaBuilder
    {
        $this->saveToTca(false);
        $this->paletteBuilder->load($paletteId, $this->tcaBuilder->getTable());
        $this->paletteBuilder->removeField($field);
        $this->tcaBuilder->setFieldsForPalette($paletteId, $this->paletteBuilder->returnCurrentConfiguration());
        $this->paletteBuilder->saveToTca();
        $this->saveToTca(false);

        return $this;
    }

    /**
     * @return TcaBuilder
     */
    public function initialize(): TcaBuilder
    {
        $this->tcaBuilder->initialize();

        return $this;
    }

    /**
     * @param string $paletteId
     * @return TcaBuilder
     */
    public function initializePalette(string $paletteId): TcaBuilder
    {
        $this->tcaBuilder->setFieldsForPalette($paletteId, []);

        return $this;
    }

    /**
     * @param string $type
     * @return TcaBuilder
     */
    public function copyFromType(string $type): TcaBuilder
    {
        $this->tcaBuilder->copyFromType($type);

        return $this;
    }

    /**
     * Loads the TCA fields from table and types
     *
     * @return TcaBuilder
     */
    public function load(): TcaBuilder
    {
        $this->tcaBuilder->load();

        return $this;
    }

    /**
     * Loads configuration of a type in a table
     *
     * @param string $table
     * @param string $type
     * @return TcaBuilder
     */
    public function loadConfiguration(string $table, string $type): TcaBuilder
    {
        $this->reset();
        $this->tcaBuilder->setTable($table);
        $this->tcaBuilder->setType($type);
        $this->tcaBuilder->load();

        return $this;
    }

    /**
     * Saves the configuration as TCA field list
     *
     * @param bool $resetAfterSave
     */
    public function saveToTca(bool $resetAfterSave = true): void
    {
        $this->tcaBuilder->save($resetAfterSave);
    }

    /**
     * Returns the built configuration as array to include directly
     *
     * @return array
     */
    public function returnAsArray(): array
    {
        return $this->tcaBuilder->returnAsArray();
    }
}
