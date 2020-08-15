# The TCA Builder

[![pipeline status](https://git.spooner.io/spooner-web/tcabuilder/badges/main/pipeline.svg)](https://git.spooner.io/spooner-web/tcabuilder/-/commits/main)
[![coverage report](https://git.spooner.io/spooner-web/tcabuilder/badges/main/coverage.svg)](https://git.spooner.io/spooner-web/tcabuilder/-/commits/main)

## What does it do?

With the TCA builder you have the possibility to create
or change the fields in type list in an easy and speaking way.

Times of semicolons within field names are gone.

For example:
1. Compose the field list for the form of a content element or any other record
1. Change the types of existing definitions

## Installation

`composer require spooner-web/tcabuilder`

## Usage

### General usage

1. Instantiate the `TcaBuilder` class
1. Set the table and type to configure (may also be a not existing type yet)
1. Use the methods to manipulate
1. Save to the TCA
1. Flush caches
1. See result

### Methods

#### Main methods

| Method name | Description | Parameters |
| ----------- | ----------- | ---------- |
| `setTable` | Sets the table to load configuration from | `string` $tableName |
| `setType` | Sets the type to load configuration from | `string` $typeName |
| `load` | Loads configuration if it's an existing type |  |
| `loadConfiguration` | Shorter method to run `setTable`, `setType` and `load` at once | `string` $tableName <br> `string` $typeName |
| `useLocalLangFile` | Set a locallang file (beginning with `EXT:`) to use in labels | `string` $localLangFile |
| `saveToTca` | Saves the manipulated configuration to TCA |  |
| `addField` | Adds a field to selected type | `string` $typeName <br> `string` $position (optional) <br> `string` $alternativeLabel (optional) |
| `addPalette` | Adds an existing palette to selected type | `string` $paletteName <br> `string` $position (optional)<br> `string` $alternativeLabel (optional) |
| `addDiv` | Adds a div (tab) to selected type | `string` $divName <br> `string` $label |
| `removeField` | Removes a field from selected type | `string` $fieldName |
| `removePalette` | Removes a palette from selected type | `string` $paletteName |
| `removeDiv` | Removes a div (tab) from selected type, either by position (index, beginning with 0) or by label | `string`&#124;`int` $positionOrLabel |
| `moveField` | Moves a field to a new position (alternatively with a new label) | `string` $fieldName <br> `string` $newPosition <br> `string` $newLabel (optional) |
| `movePalette` | Moves a palette to a new position (alternatively with a new label) | `string` $paletteName <br> `string` $newPosition <br> `string` $newLabel (optional) |
| `addOverride` | Adds a custom override of a field | `string` $fieldName <br> `array` $configuration |

#### Helper methods

| Method name | Description | Parameters | Returns |
| ----------- | ----------- | ---------- | ------- |
| `getPaletteString` | Finds the complete palette string which is used in list (for using it in position strings) | `string` $paletteName | `string` The complete palette string with `--palette--` and the possible label config |
| `getDivString` | Finds the complete div string which is used in list (for using it in position strings), either by position (index, beginning with 0) or by label | `string`&#124;`int` $positionOrLabel | `string` The complete palette string with `--div--` and the div's label |

## Examples

### Add an own content element

```php
$tcaBuilder = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\SpoonerWeb\TcaBuilder\TcaBuilder::class);
$tcaBuilder
    ->setTable('tt_content') // define table
    ->setType('test') // define type
    ->addDiv('General')
    ->addField('header', '', 'Top header!!')
    ->addField('bodytext')
    ->addField('subheader', 'after:header')
    ->addField('layout', 'before:header')
    ->addDiv('Extra')
    ->addPalette('access')
    ->addPalette('hidden', 'after:bodytext', 'Alternative label')
    ->saveToTca(); // save to TCA
```

### Change existing configurations

```php
$tcaBuilder = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\SpoonerWeb\TcaBuilder\TcaBuilder::class);
$tcaBuilder
    ->setTable('pages') // define table
    ->setType(\TYPO3\CMS\Frontend\Page\PageRepository::DOKTYPE_LINK) // define type
    ->load() // load definitions
    ->removeField('doktype')
    ->removePalette('external')
    ->removeDiv(1)
    ->addPalette('external', 'after:--palette--;;layout')
    ->saveToTca(); // save back to TCA
```

```php
$tcaBuilder = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\SpoonerWeb\TcaBuilder\TcaBuilder::class);
$tcaBuilder
    ->loadConfiguration('pages', \TYPO3\CMS\Frontend\Page\PageRepository::DOKTYPE_MOUNTPOINT)
    ->removeDiv('LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.tabs.metadata')
    ->movePalette('title', 'after:' . $tcaBuilder->getPaletteString('abstract'), 'New title')
    ->addOverride(
        'title',
        [
            'label' => 'New title',
            'config' => [
                'renderType' => 'inputLink'
            ]
        ]
    )
    ->saveToTca();
```
### Use language file

```php
$tcaBuilder = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\SpoonerWeb\TcaBuilder\TcaBuilder::class);
$tcaBuilder
    ->loadConfiguration('pages', \TYPO3\CMS\Frontend\Page\PageRepository::DOKTYPE_MOUNTPOINT)
    ->useLocalLangFile('EXT:my_extension/Resources/Private/Language/locallang.xlf')
    ->addField('new_field', '', 'LANG:new_field') // Used label: "LLL:EXT:my_extension/Resources/Private/Language/locallang.xlf:new_field"
    ->saveToTca();
```

### Do minimal changes

```php
$tcaBuilder = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\SpoonerWeb\TcaBuilder\TcaBuilder::class);
$tcaBuilder
    ->loadConfiguration('tt_content', 'textmedia')
    ->removePalette('headers')
    ->saveToTca();
```
