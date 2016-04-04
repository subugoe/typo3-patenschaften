<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Buchpatenschaften',
    'description' => 'Darstellung und Editierung der Patenschaften fuer Buecher an der SUB Goettingen',
    'category' => 'plugin',
    'author' => 'Ingo Pfennigstorf',
    'author_email' => 'pfennigstorf@sub.uni-goettingen.de',
    'shy' => '',
    'dependencies' => '',
    'conflicts' => '',
    'priority' => '',
    'module' => '',
    'state' => 'beta',
    'internal' => '',
    'uploadfolder' => 0,
    'createDirs' => 'uploads/tx_patenschaften/rte/',
    'modify_tables' => '',
    'clearCacheOnLoad' => 1,
    'lockType' => '',
    'author_company' => 'Goettingen State and University Library, Germany http://www.sub.uni-goettingen.de',
    'version' => '2.1.0',
    'constraints' => [
        'depends' => [
            'typo3' => '6.2.0-7.99.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
    'suggests' => [],
];
