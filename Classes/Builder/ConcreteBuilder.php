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

use TYPO3\CMS\Core\Utility\GeneralUtility;

class ConcreteBuilder implements \TYPO3\CMS\Core\SingletonInterface
{

    protected $table = '';

    protected $selectedType = '';

    protected $fields = [];

    public function reset()
    {
        $this->table = '';
        $this->selectedType = '';
        $this->fields = [];
    }

    public function setTable(string $table)
    {
        $this->table = $table;
    }

    public function setType(string $type)
    {
        $this->selectedType = $type;
    }

    public function addField(string $fieldName, string $position = '', string $altLabel = '')
    {
        if ($altLabel) {
            $fieldName .= ';' . $altLabel;
        }
        if ($position) {
            $this->addFieldToPosition($fieldName, $position);
        } else {
            $this->fields[] = $fieldName;
        }
    }

    public function removeField(string $fieldName)
    {
        foreach ($this->fields as $field) {
            if (strpos($field, $fieldName) === 0) {
                $this->removeStringInList($field);
            }
        }
    }

    public function addPalette(string $paletteName, string $position = '', string $altLabel = '')
    {
        $paletteNameArray[] = '--palette--';
        if ($altLabel) {
            $paletteNameArray[] = $altLabel;
        } else {
            $paletteNameArray[] = '';
        }
        $paletteNameArray[] = $paletteName;
        $fieldName = implode(';', $paletteNameArray);
        if ($position) {
            $this->addFieldToPosition($fieldName, $position);
        } else {
            $this->fields[] = $fieldName;
        }
    }

    public function removePalette(string $paletteName)
    {
        $allPalettes = array_filter($this->fields, [$this, 'beginsWithPalette']);
        foreach ($allPalettes as $palette) {
            if (strpos($palette, $paletteName) > 0) {
                $this->removeStringInList($palette);
            }
        }
    }

    public function addDiv(string $label, string $position = '')
    {
        $fieldName = '--div--;' . $label;
        if ($position) {
            $this->addFieldToPosition($fieldName, $position);
        } else {
            $this->fields[] = $fieldName;
        }
    }

    public function removeDivByLabel(string $label)
    {
        $divName = '--div--;' . $label;
        $this->removeStringInList($divName);
    }

    public function removeDivByPosition(int $position)
    {
        $allDivs = array_values(array_filter($this->fields, [$this, 'beginsWithDiv']));
        $this->removeStringInList($allDivs[$position]);
    }

    public function load(): ConcreteBuilder
    {
        $fields = $GLOBALS['TCA'][$this->table]['types'][$this->selectedType]['showitem'];

        $this->fields = GeneralUtility::trimExplode(',', $fields);

        return $this;
    }

    public function save()
    {
        if ($this->table === '' && $this->selectedType === '') {
            return;
        }

        $GLOBALS['TCA'][$this->table]['types'][$this->selectedType] = [
            'showitem' => implode(',', $this->fields)
        ];
    }

    protected function addFieldToPosition(string $fieldName, string $position)
    {
        [$direction, $fieldNameToSearch] = GeneralUtility::trimExplode(':', $position);
        $key = array_search($fieldNameToSearch, $this->fields, true);
        if ($key !== false) {
            switch ($direction) {
                case 'before':
                    array_splice($this->fields, $key, 0, $fieldName);
                    break;
                case 'after':
                    array_splice($this->fields, ++$key, 0, $fieldName);
                    break;
            }
        }
        $this->resetFieldKeys();
    }

    protected function resetFieldKeys()
    {
        $this->fields = array_values($this->fields);
    }

    protected function removeStringInList(string $fieldName)
    {
        array_splice($this->fields, array_search($fieldName, $this->fields, true), 1);
        $this->resetFieldKeys();
    }

    protected function beginsWithDiv($value)
    {
        return $this->beginsWith($value, '--div--;');
    }

    protected function beginsWithPalette($value)
    {
        return $this->beginsWith($value, '--palette--;');
    }

    protected function beginsWith($value, string $begin)
    {
        return strpos($value, $begin) === 0;
    }
}
