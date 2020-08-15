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
use TYPO3\CMS\Core\Utility\GeneralUtility;

class TcaBuilder implements \TYPO3\CMS\Core\SingletonInterface
{

    /**
     * @var \SpoonerWeb\TcaBuilder\Builder\ConcreteBuilder
     */
    protected $tcaBuilder;

    public function __construct()
    {
        $this->tcaBuilder = GeneralUtility::makeInstance(ConcreteBuilder::class);
    }

    /**
     * Resets all fields
     *
     * @return \SpoonerWeb\TcaBuilder\TcaBuilder
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
     * @return \SpoonerWeb\TcaBuilder\TcaBuilder
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
     * @return \SpoonerWeb\TcaBuilder\TcaBuilder
     */
    public function setType(string $type): TcaBuilder
    {
        $this->tcaBuilder->setType($type);

        return $this;
    }

    /**
     * Sets a locallang file (beginning with 'EXT:') to be used
     * whenever using a label (label must begin with 'LANG:')
     *
     * @param string $localLangFile
     * @return \SpoonerWeb\TcaBuilder\TcaBuilder
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
     * @return \SpoonerWeb\TcaBuilder\TcaBuilder
     */
    public function addField(string $fieldName, string $position = '', string $altLabel = ''): TcaBuilder
    {
        if ($position && !$this->tcaBuilder->doesFieldExist(GeneralUtility::trimExplode(':', $position)[1])) {
            $this->tcaBuilder->addField($fieldName, '', $altLabel);
        } else {
            $this->tcaBuilder->addField($fieldName, $position, $altLabel);
        }

        return $this;
    }

    /**
     * Removes an existing field
     *
     * @param string $fieldName
     * @return \SpoonerWeb\TcaBuilder\TcaBuilder
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
     * @return \SpoonerWeb\TcaBuilder\TcaBuilder
     */
    public function moveField(string $fieldName, string $newPosition, string $newLabel = ''): TcaBuilder
    {
        if ($this->tcaBuilder->doesFieldExist($fieldName) && $this->tcaBuilder->doesFieldExist(GeneralUtility::trimExplode(':', $newPosition)[1])) {
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
     * @return \SpoonerWeb\TcaBuilder\TcaBuilder
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
     * @return \SpoonerWeb\TcaBuilder\TcaBuilder
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
     * @return \SpoonerWeb\TcaBuilder\TcaBuilder
     */
    public function movePalette(string $paletteName, string $newPosition, string $newLabel = ''): TcaBuilder
    {
        if ($this->tcaBuilder->doesFieldExist(GeneralUtility::trimExplode(':', $newPosition)[1])) {
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
     * @param string $altLabel
     * @return \SpoonerWeb\TcaBuilder\TcaBuilder
     */
    public function addDiv(string $divName, string $position = '', string $altLabel = ''): TcaBuilder
    {
        $this->tcaBuilder->addDiv($divName, $position, $altLabel);

        return $this;
    }

    /**
     * Removes a div by either position (integer offset) or label
     *
     * @param $identifier
     * @return \SpoonerWeb\TcaBuilder\TcaBuilder
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
     * @return \SpoonerWeb\TcaBuilder\TcaBuilder
     */
    public function addOverride(string $fieldName, array $override): TcaBuilder
    {
        $this->tcaBuilder->addCustomOverride($fieldName, $override);

        return $this;
    }

    /**
     * Loads the TCA fields from table and types
     *
     * @return \SpoonerWeb\TcaBuilder\TcaBuilder
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
     * @return \SpoonerWeb\TcaBuilder\TcaBuilder
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
     * @return void
     */
    public function saveToTca()
    {
        $this->tcaBuilder->save();
    }
}
