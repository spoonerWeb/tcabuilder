<?php
namespace SpoonerWeb\TcaBuilder\Builder;

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

use SpoonerWeb\TcaBuilder\Helper\PositionHelper;
use SpoonerWeb\TcaBuilder\Helper\StringHelper;

class ConcretePaletteBuilder implements \TYPO3\CMS\Core\SingletonInterface
{
    protected $paletteId = '';

    protected $table = '';

    protected $paletteFields = [];

    public function load(string $paletteId, string $table)
    {
        $this->reset();
        $this->paletteId = $paletteId;
        $this->table = $table;
        $this->paletteFields = explode(',', $GLOBALS['TCA'][$table]['palettes'][$paletteId]['showitem']);
    }

    public function reset()
    {
        $this->paletteId = '';
        $this->table = '';
        $this->paletteFields = [];
    }

    public function saveToTca()
    {
        $GLOBALS['TCA'][$this->table]['palettes'][$this->paletteId]['showitem'] = implode(
            ',',
            $this->paletteFields
        );
        $this->reset();
    }

    public function addField(string $fieldName, string $position = '')
    {
        PositionHelper::addFieldToPosition($this->paletteFields, $fieldName, $position);
    }

    public function removeField(string $fieldName)
    {
        StringHelper::removeStringInList($this->paletteFields, $fieldName);
    }

    public function returnCurrentConfiguration(): array
    {
        return $this->paletteFields;
    }
}
