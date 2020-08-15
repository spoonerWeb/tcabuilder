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

    protected $customOverrides = [];

    protected $locallangFile = '';

    public function reset()
    {
        $this->table = '';
        $this->selectedType = '';
        $this->fields = [];
        $this->customOverrides = [];
        $this->locallangFile = '';
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
            $fieldName .= ';' . $this->getLabel($altLabel);
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
            $paletteNameArray[] = $this->getLabel($altLabel);
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
        $this->removeStringInList($this->getPaletteFieldName($paletteName));
    }

    public function getPaletteFieldName(string $paletteName): string
    {
        $allPalettes = array_filter($this->fields, [$this, 'beginsWithPalette']);
        foreach ($allPalettes as $palette) {
            if (strpos($palette, $paletteName) > 0) {
                return $palette;
            }
        }

        return '';
    }

    public function addDiv(string $label, string $position = '')
    {
        $fieldName = '--div--;' . $this->getLabel($label);
        if ($position) {
            $this->addFieldToPosition($fieldName, $position);
        } else {
            $this->fields[] = $fieldName;
        }
    }

    public function removeDivByLabel(string $label)
    {
        $this->removeStringInList($this->getDivByLabel($label));
    }

    public function removeDivByPosition(int $position)
    {
        $this->removeStringInList($this->getDivByPosition($position));
    }

    public function getDivByPosition(int $position): string
    {
        $allDivs = array_values(array_filter($this->fields, [$this, 'beginsWithDiv']));

        return $allDivs[$position] ?? '';
    }

    public function getDivByLabel(string $label): string
    {
        if (!$this->doesFieldExist('--div--;' . $this->getLabel($label))) {
            return '';
        }

        return '--div--;' . $this->getLabel($label);
    }

    public function addCustomOverride(string $fieldName, array $override)
    {
        $this->customOverrides[$fieldName] = $override;
    }

    public function load()
    {
        $fields = $GLOBALS['TCA'][$this->table]['types'][$this->selectedType]['showitem'];

        $this->fields = GeneralUtility::trimExplode(',', $fields);
        $this->customOverrides = $GLOBALS['TCA'][$this->table]['types'][$this->selectedType]['customOverrides'] ?? null;
    }

    public function save()
    {
        if ($this->table === '' && $this->selectedType === '') {
            return;
        }

        $fields = array_values(array_filter($this->fields));
        $GLOBALS['TCA'][$this->table]['types'][$this->selectedType]['showitem'] = count($fields) === 1 ? $fields[0] : implode(',', $fields);

        if ($this->customOverrides) {
            $GLOBALS['TCA'][$this->table]['types'][$this->selectedType]['customOverrides'] = $this->customOverrides;
        }

        $this->reset();
    }

    public function useLocalLangFile(string $filePath)
    {
        $this->locallangFile = $filePath;
    }

    public function doesFieldExist(string $fieldName)
    {
        return array_search($fieldName, $this->fields, true);
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

    protected function beginsWithDiv($value): bool
    {
        return $this->beginsWith($value, '--div--;');
    }

    protected function beginsWithPalette($value): bool
    {
        return $this->beginsWith($value, '--palette--;');
    }

    protected function beginsWith($value, string $begin): bool
    {
        return strpos($value, $begin) === 0;
    }

    protected function getLabel(string $label): string
    {
        if ($this->locallangFile !== '' && strpos($label, 'LANG:') === 0) {
            return str_replace('LANG:', 'LLL:' . $this->locallangFile . ':', $label);
        }

        return $label;
    }
}
