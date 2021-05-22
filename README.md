# The TCA Builder

[![pipeline status](https://git.spooner.io/spooner-web/tcabuilder/badges/main/pipeline.svg)](https://git.spooner.io/spooner-web/tcabuilder/-/commits/main)
[![coverage report](https://git.spooner.io/spooner-web/tcabuilder/badges/main/coverage.svg)](https://git.spooner.io/spooner-web/tcabuilder/-/commits/main)
[![Mutation MSI](https://git.spooner.io/spooner-web/tcabuilder/-/jobs/artifacts/main/raw/badges/mutation_msi.svg?job=Mutation+Badges)](https://git.spooner.io/spooner-web/tcabuilder/-/commits/main)
[![Mutation MCC](https://git.spooner.io/spooner-web/tcabuilder/-/jobs/artifacts/main/raw/badges/mutation_mcc.svg?job=Mutation+Badges)](https://git.spooner.io/spooner-web/tcabuilder/-/commits/main)
[![Mutation CCMSI](https://git.spooner.io/spooner-web/tcabuilder/-/jobs/artifacts/main/raw/badges/mutation_ccmsi.svg?job=Mutation+Badges)](https://git.spooner.io/spooner-web/tcabuilder/-/commits/main)
[![Latest Version](https://git.spooner.io/spooner-web/tcabuilder/-/jobs/artifacts/main/raw/badges/latestVersion.svg?job=Create+Badge)](https://git.spooner.io/spooner-web/tcabuilder/-/tags)

## What does it do?

With the TCA builder you have the possibility to create
or change the fields in type list in an easy and speaking way.

Times of semicolons within field names are gone.

For example:
1. Compose the field list for the form of a content element or any other record
1. Change the types of existing definitions

### Introducing TcaCreator in v1.6.0

With the TCA creator you have the possibility to create TCA forms
from scratch and do not need to think about the configuration of the default fields
like the `ctrl` section or the default fields in the `columns` section.

More to see in the [Examples Section](#tcacreator)

## Installation

### Installation via composer

`composer require spooner-web/tcabuilder`

> :warning: **In a composer install, the package won't be in typo3conf/ext folder but in vendor**

### Installation via classic mode

1. Head to https://extensions.typo3.org/extension/tcabuilder
1. Download the ZIP file
1. Upload it to your TYPO3 instance using non-composer mode

### Installation via Extension Manager

1. Update your extension list
1. Search for "tcabuilder"
1. Install

### Composer fails checking out this package

As composer is not able to detect the ZIP archive of a self-hosted GitLab instance,
there may occur problems when deploying or building a project with this package.

To fix this issue, you need to add the GitLab built-in packagist API into the `repositories` section
of your project composer.json:

```json
{
	"type": "composer",
	"url": "https://git.spooner.io/api/v4/group/8/-/packages/composer/"
}
```

## Usage

### General usage

Recommendation is to use the `TcaBuilder` in the php files of your `Configuration/TCA/Overrides/` folder of your extension.

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
| `saveToTca` | Saves the manipulated configuration to TCA | `bool` $resetAfterSave(true) |
| `returnAsArray` | Instead of saving the configuration it returns it directly as array |  |

#### Manipulating types

| Method name | Description | Parameters |
| ----------- | ----------- | ---------- |
| `addField` | Adds a field to selected type | `string` $typeName <br> `string` [$position (optional)](#positioning) <br> `string` $alternativeLabel (optional) |
| `removeField` | Removes a field from selected type | `string` $fieldName |
| `moveField` | Moves a field to a new position (alternatively with a new label) | `string` $fieldName <br> `string` [$newPosition](#positioning)<br> `string` $newLabel (optional) |
| `addPalette` | Adds an existing palette to selected type | `string` $paletteName <br> `string` [$position (optional)](#positioning)<br> `string` $alternativeLabel (optional) |
| `removePalette` | Removes a palette from selected type | `string` $paletteName |
| `movePalette` | Moves a palette to a new position (alternatively with a new label) | `string` $paletteName <br> `string` $newPosition <br> `string` $newLabel (optional) |
| `addDiv` | Adds a div (tab) to selected type | `string` $divName <br> `string` $label |
| `removeDiv` | Removes a div (tab) from selected type, either by position (index, beginning with 0) or by label | `string`&#124;`int` $positionOrLabel |
| `addOverride` | Adds a custom override of a field | `string` $fieldName <br> `array` $configuration |
| `initialize` | Initializes the type with an empty list |  |

#### Manipulating palettes

| Method name | Description | Parameters |
| ----------- | ----------- | ---------- |
| `addCustomPalette` | Creates a new palette (and optionally inserts it directly to given position) | `string` $paletteId <br> `array` $fields <br> `string` $label (optional) <br> `string` [$position (optional)](#positioning) |
| `addFieldToPalette` | Adds a field to a palette | `string` $paletteId <br> `string` $field <br> `string` [$position (optional)](#positioning) |
| `removeFieldFromPalette` | Removes a field from a palette | `string` $paletteId <br> `string` $field |
| `initializePalette` | Initializes the palette with an empty list | `string` $paletteId  |

#### Helper methods

| Method name | Description | Parameters | Returns |
| ----------- | ----------- | ---------- | ------- |
| `getPaletteString` | Finds the complete palette string which is used in list (for using it in position strings) | `string` $paletteName | `string` The complete palette string with `--palette--` and the possible label config |
| `getDivString` | Finds the complete div string which is used in list (for using it in position strings), either by position (index, beginning with 0) or by label | `string`&#124;`int` $positionOrLabel | `string` The complete palette string with `--div--` and the div's label |

#### <a id="positioning" />Possible values for positioning fields, palettes or divs

| Value | Description |
| ----- | ----------- |
| `before:<item>` | Moves the item before the given item | 
| `after:<item>` | Moves the item after the given item | 
| `replace:<item>` | Replaces the given item | 

## Examples (TCA builder)

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

## <a id="tcacreator" />Examples (Tca Creator)

### Creating TCA configuration for a new table (inside custom extension)

```php

// Returns the default values of ctrl section
// By default language, version and sorting options are set
// These options can be unset and additional overridden fields can be set
$configuration['ctrl'] = \SpoonerWeb\TcaBuilder\TcaCreator::getControlConfiguration(
    'title',
    'label'    
);

// Returns the default columns depending on the ctrl section configuration
// Example: If ctrl section includes language, this method returns the field configuration
// for all language fields (sys_language_uid, l10n_parent, l10n_diffsource, l10n_source)
$configuration['columns'] = \SpoonerWeb\TcaBuilder\TcaCreator::getColumnsConfiguration(
    $configuration['ctrl'],
    'tx_extension_domain_model_record',
    [
        'title' => [
            'label' => 'My label',
            'config' => [
                'type' => 'input'
            ]
        ]
    ]
);

// Uses TcaBuilder class to create the configuration for the TCA form
$configuration['types'][] = \SpoonerWeb\TcaBuilder\TcaCreator::buildTypesConfiguration()
    ->addDiv('General')
    ->addField('title')
    ->addField('subtitle')
    ->addDiv('Categories')
    ->addField('categories')
    ->returnAsArray();
    
// Now return TCA configuration
return $configuration;
```
