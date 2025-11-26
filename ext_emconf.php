<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'TCA Builder - create and change TCA in an easy way',
    'description' => 'Utility to easily maintain and create your TCA forms',
    'version' => '3.3.0',
    'category' => 'misc',
    'state' => 'stable',
    'author' => 'Thomas Löffler',
    'author_email' => 'loeffler@spooner-web.de',
    'author_company' => 'Spooner Web',
    'constraints' => [
        'depends' => [
            'typo3' => '11.5.0 - 14.4.99',
        ],
    ],
];
