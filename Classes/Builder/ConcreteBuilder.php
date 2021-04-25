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
    public const TYPES_KEYWORD = 'types';
    public const SHOWITEM_KEYWORD = 'showitem';
    public const PALETTES_KEYWORD = 'palettes';
    public const DIV_MARKER = '--div--';
    public const PALETTE_MARKER = '--palette--';

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
            unset($GLOBALS['TCA'][$this->table][self::TYPES_KEYWORD][$type]);
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
        $paletteNameArray[] = self::PALETTE_MARKER;
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
        $fieldName = self::DIV_MARKER . ';' . $this->getLabel($label);
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
        if (!$this->doesFieldExist(self::DIV_MARKER . ';' . $this->getLabel($label))) {
            return '';
        }

        return self::DIV_MARKER . ';' . $this->getLabel($label);
    }

    public function addColumnsOverrides(string $fieldName, array $override)
    {
        $this->columnsOverrides[$fieldName] = $override;
    }

    public function addCustomPalette(string $paletteId, array $showItems, string $label = '', string $position = '')
    {
        $this->customPalettes[$paletteId] = [
            'label' => $label,
            self::SHOWITEM_KEYWORD => implode(',', $showItems)
        ];

        if ($position !== '') {
            $this->addPalette($paletteId, $position);
        }
    }

    public function setFieldsForPalette(string $paletteId, array $fields)
    {
        $this->customPalettes[$paletteId][self::SHOWITEM_KEYWORD] = implode(',', $fields);
    }

    public function load()
    {
        $loadedFields = $GLOBALS['TCA'][$this->table][self::TYPES_KEYWORD][$this->selectedType][self::SHOWITEM_KEYWORD];

        $this->fields = GeneralUtility::trimExplode(',', $loadedFields);
        $this->columnsOverrides = $GLOBALS['TCA'][$this->table][self::TYPES_KEYWORD][$this->selectedType]['columnsOverrides'] ?? null;
    }

    public function save(bool $resetAfterSave = true)
    {
        if ($this->table === '' && $this->selectedType === '') {
            return;
        }

        $fieldsToSave = array_values(array_filter($this->fields));
        $GLOBALS['TCA'][$this->table][self::TYPES_KEYWORD][$this->selectedType][self::SHOWITEM_KEYWORD] = count($fieldsToSave) === 1 ? $fieldsToSave[0] : implode(',', $fieldsToSave);

        if ($this->columnsOverrides !== [] || $this->initializeOverrides) {
            $GLOBALS['TCA'][$this->table][self::TYPES_KEYWORD][$this->selectedType]['columnsOverrides'] = $this->columnsOverrides;
        }

        foreach ($this->customPalettes as $customPaletteId => $customPaletteConfiguration) {
            $GLOBALS['TCA'][$this->table][self::PALETTES_KEYWORD][$customPaletteId] = $customPaletteConfiguration;
        }

        if ($resetAfterSave) {
            $this->reset();
        }
    }

    public function returnAsArray(): array
    {
        $fieldsToSave = array_values(array_filter($this->fields));
        $typeConfiguration[self::SHOWITEM_KEYWORD] = count($fieldsToSave) === 1 ? $fieldsToSave[0] : implode(',', $fieldsToSave);

        if ($this->columnsOverrides !== [] || $this->initializeOverrides) {
            $typeConfiguration['columnsOverrides'] = $this->columnsOverrides;
        }

        return $typeConfiguration;
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
        return $this->beginsWith($value, self::DIV_MARKER . ';');
    }

    protected function beginsWithPalette($value): bool
    {
        return $this->beginsWith($value, self::PALETTE_MARKER . ';');
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
