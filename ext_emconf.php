<?php

$EM_CONF['tcabuilder'] = [
    'title' => 'TCA Builder - create and change TCA in an easy way',
    'description' => 'Utility to easily maintain and create your TCA forms',
    'version' => '1.1.0',
    'category' => 'misc',
    'state' => 'beta',
    'clearCacheOnLoad' => true,
    'constraints' => [
        'depends' => [
            'typo3' => '8.7.0 - 10.4.99'
        ]
    ]
];
