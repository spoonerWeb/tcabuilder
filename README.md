# The TCA Builder

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
    ->setType(3) // define type
    ->load() // load definitions
    ->removeField('doktype')
    ->removePalette('external')
    ->removeDiv(1)
    ->addPalette('external', 'after:--palette--;;layout')
    ->saveToTca(); // save back to TCA
```

### Do minimal changes

```php
$tcaBuilder = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\SpoonerWeb\TcaBuilder\TcaBuilder::class);
$tcaBuilder
    ->loadConfiguration('tt_content', 'textmedia')
    ->removePalette('headers')
    ->saveToTca();
```
