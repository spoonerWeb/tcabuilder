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
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ConcreteBuilder implements \TYPO3\CMS\Core\SingletonInterface
{
    protected $table = '';

    protected $selectedType = '';

    protected $fields = [];

    protected $columnsOverrides = [];

    protected $initializeOverrides = false;

    protected $customPalettes = [];

    protected $locallangFile = '';

    public function reset()
    {
        $this->table = '';
        $this->selectedType = '';
        $this->fields = [];
        $this->columnsOverrides = [];
        $this->customPalettes = [];
        $this->locallangFile = '';
    }

    public function initialize()
    {
        $this->fields = [];
        $this->columnsOverrides = [];
        $this->initializeOverrides = true;
    }

    public function setTable(string $table)
    {
        $this->table = $table;
    }

    public function getTable(): string
    {
        return $this->table;
    }

    public function setType(string $type)
    {
        $this->selectedType = $type;
    }

    public function removeType(string $type = '')
    {
        if ($type === '') {
            $type = $this->selectedType;
        }

        if ($type) {
            unset($GLOBALS['TCA'][$this->table]['types'][$type]);
        }
    }

    public function copyFromType(string $type)
    {
        $saveCurrentType = $this->selectedType;
        $this->setType($type);
        $this->load();
        $this->setType($saveCurrentType);
    }

    public function addField(string $fieldName, string $position = '', string $altLabel = '', array $columnsOverrides = [])
    {
        if ($altLabel !== '') {
            $fieldName .= ';' . $this->getLabel($altLabel);
        }

        PositionHelper::addFieldToPosition($this->fields, $fieldName, $position);

        if ($columnsOverrides !== []) {
            $this->addColumnsOverrides($fieldName, $columnsOverrides);
        }
    }

    public function removeField(string $fieldName)
    {
        foreach ($this->fields as $field) {
            if (StringHelper::stringStartsWith($field, $fieldName)) {
                StringHelper::removeStringInList($this->fields, $field);
            }
        }
    }

    public function addPalette(string $paletteName, string $position = '', string $altLabel = '')
    {
        $paletteNameArray[] = '--palette--';
        $paletteNameArray[] = $altLabel !== '' ? $this->getLabel($altLabel) : '';
        $paletteNameArray[] = $paletteName;
        $fieldName = implode(';', $paletteNameArray);
        PositionHelper::addFieldToPosition($this->fields, $fieldName, $position);
    }

    public function removePalette(string $paletteName)
    {
        StringHelper::removeStringInList($this->fields, $this->getPaletteFieldName($paletteName));
    }

    public function getPaletteFieldName(string $paletteName): string
    {
        $allPalettes = array_filter($this->fields, function ($value): bool {
            return $this->beginsWithPalette($value);
        });
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
        PositionHelper::addFieldToPosition($this->fields, $fieldName, $position);
    }

    public function removeDivByLabel(string $label)
    {
        StringHelper::removeStringInList($this->fields, $this->getDivByLabel($label));
    }

    public function removeDivByPosition(int $position)
    {
        StringHelper::removeStringInList($this->fields, $this->getDivByPosition($position));
    }

    public function getDivByPosition(int $position): string
    {
        $allDivs = array_values(array_filter($this->fields, function ($value): bool {
            return $this->beginsWithDiv($value);
        }));

        return $allDivs[$position] ?? '';
    }

    public function getDivByLabel(string $label): string
    {
        if (!$this->doesFieldExist('--div--;' . $this->getLabel($label))) {
            return '';
        }

        return '--div--;' . $this->getLabel($label);
    }

    public function addColumnsOverrides(string $fieldName, array $override)
    {
        $this->columnsOverrides[$fieldName] = $override;
    }

    public function addCustomPalette(string $paletteId, array $showItems, string $label = '', string $position = '')
    {
        $this->customPalettes[$paletteId] = [
            'label' => $label,
            'showitem' => implode(',', $showItems)
        ];

        if ($position !== '') {
            $this->addPalette($paletteId, $position);
        }
    }

    public function setFieldsForPalette(string $paletteId, array $fields)
    {
        $this->customPalettes[$paletteId]['showitem'] = implode(',', $fields);
    }

    public function load()
    {
        $fields = $GLOBALS['TCA'][$this->table]['types'][$this->selectedType]['showitem'];

        $this->fields = GeneralUtility::trimExplode(',', $fields);
        $this->columnsOverrides = $GLOBALS['TCA'][$this->table]['types'][$this->selectedType]['columnsOverrides'] ?? null;
    }

    public function save(bool $resetAfterSave = true)
    {
        if ($this->table === '' && $this->selectedType === '') {
            return;
        }

        $fields = array_values(array_filter($this->fields));
        $GLOBALS['TCA'][$this->table]['types'][$this->selectedType]['showitem'] = count($fields) === 1 ? $fields[0] : implode(',', $fields);

        if ($this->columnsOverrides !== [] || $this->initializeOverrides) {
            $GLOBALS['TCA'][$this->table]['types'][$this->selectedType]['columnsOverrides'] = $this->columnsOverrides;
        }

        foreach ($this->customPalettes as $customPaletteId => $customPaletteConfiguration) {
            $GLOBALS['TCA'][$this->table]['palettes'][$customPaletteId] = $customPaletteConfiguration;
        }

        if ($resetAfterSave) {
            $this->reset();
        }
    }

    public function useLocalLangFile(string $filePath)
    {
        $this->locallangFile = $filePath;
    }

    public function doesFieldExist(string $fieldName)
    {
        return array_search($fieldName, $this->fields, true);
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
        return StringHelper::stringStartsWith($value, $begin);
    }

    protected function getLabel(string $label): string
    {
        if ($this->locallangFile !== '' && StringHelper::stringStartsWith($label, 'LANG:')) {
            return str_replace('LANG:', 'LLL:' . $this->locallangFile . ':', $label);
        }

        return $label;
    }
}
