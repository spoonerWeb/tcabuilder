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
     * Adds a field to TCA at the end or at specific position
     *
     * @param string $fieldName
     * @param string $position
     * @param string $altLabel
     * @return \SpoonerWeb\TcaBuilder\TcaBuilder
     */
    public function addField(string $fieldName, string $position = '', string $altLabel = ''): TcaBuilder
    {
        $this->tcaBuilder->addField($fieldName, $position, $altLabel);

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
     * @param $divName
     * @return \SpoonerWeb\TcaBuilder\TcaBuilder
     */
    public function removeDiv($divName): TcaBuilder
    {
        if (is_int($divName)) {
            $this->tcaBuilder->removeDivByPosition((int)$divName);
        } else {
            $this->tcaBuilder->removeDivByLabel($divName);
        }

        return $this;
    }

    public function addOverride(string $fieldName, array $override)
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
