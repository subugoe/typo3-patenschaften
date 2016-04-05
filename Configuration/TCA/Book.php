<?php
if (!defined('TYPO3_MODE')) {
    die ('Access denied.');
}

$TCA['tx_patenschaften_buecher'] = [
    'ctrl' => $TCA['tx_patenschaften_buecher']['ctrl'],
    'interface' => [
        'showRecordFieldList' => 'sys_language_uid,l10n_parent,l10n_diffsource,hidden,starttime,endtime,titel,author,caption,signature,description,price,damage,help,bilder,sponsorship,category'
    ],
    'feInterface' => $TCA['tx_patenschaften_buecher']['feInterface'],
    'columns' => [
        'sys_language_uid' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.language',
            'config' => [
                'type' => 'select',
                'foreign_table' => 'sys_language',
                'foreign_table_where' => 'ORDER BY sys_language.title',
                'items' => [
                    ['LLL:EXT:lang/locallang_general.xml:LGL.allLanguages', -1],
                    ['LLL:EXT:lang/locallang_general.xml:LGL.default_value', 0]
                ]
            ]
        ],
        'l10n_parent' => [
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.l18n_parent',
            'config' => [
                'type' => 'select',
                'items' => [
                    ['', 0],
                ],
                'foreign_table' => 'tx_patenschaften_buecher',
                'foreign_table_where' => 'AND tx_patenschaften_buecher.pid=###CURRENT_PID### AND tx_patenschaften_buecher.sys_language_uid IN (-1,0)',
            ]
        ],
        'l10n_diffsource' => [
            'config' => [
                'type' => 'passthrough'
            ]
        ],
        'hidden' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
            'config' => [
                'type' => 'check',
                'default' => '0'
            ]
        ],
        't3ver_label' => [
            'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.versionLabel',
            'config' => [
                'type' => 'input',
                'size' => '30',
                'max' => '30',
            ]
        ],
        'starttime' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.starttime',
            'config' => [
                'type' => 'input',
                'size' => '8',
                'max' => '20',
                'eval' => 'date',
                'default' => '0',
                'checkbox' => '0'
            ]
        ],
        'endtime' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.endtime',
            'config' => [
                'type' => 'input',
                'size' => '8',
                'max' => '20',
                'eval' => 'date',
                'checkbox' => '0',
                'default' => '0',
                'range' => [
                    'upper' => mktime(3, 14, 7, 1, 19, 2038),
                    'lower' => mktime(0, 0, 0, date('m') - 1, date('d'), date('Y'))
                ]
            ]
        ],
        'titel' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:patenschaften/locallang_db.xml:tx_patenschaften_buecher.titel',
            'config' => [
                'type' => 'input',
                'size' => '30',
                'eval' => 'trim',
            ]
        ],
        'author' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:patenschaften/locallang_db.xml:tx_patenschaften_buecher.author',
            'config' => [
                'type' => 'input',
                'size' => '30',
                'eval' => 'trim',
            ]
        ],
        'caption' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:patenschaften/locallang_db.xml:tx_patenschaften_buecher.caption',
            'config' => [
                'type' => 'text',
                'cols' => '30',
                'rows' => '2',
            ]
        ],
        'signature' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:patenschaften/locallang_db.xml:tx_patenschaften_buecher.signature',
            'config' => [
                'type' => 'input',
                'size' => '30',
            ]
        ],
        'description' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:patenschaften/locallang_db.xml:tx_patenschaften_buecher.description',
            'config' => [
                'type' => 'text',
                'cols' => '30',
                'rows' => '5',
            ]
        ],
        'price' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:patenschaften/locallang_db.xml:tx_patenschaften_buecher.price',
            'config' => [
                'type' => 'input',
                'size' => '30',
            ]
        ],
        'damage' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:patenschaften/locallang_db.xml:tx_patenschaften_buecher.damage',
            'config' => [
                'type' => 'text',
                'cols' => '30',
                'rows' => '5',
            ]
        ],
        'help' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:patenschaften/locallang_db.xml:tx_patenschaften_buecher.help',
            'config' => [
                'type' => 'text',
                'cols' => '30',
                'rows' => '5',
            ]
        ],
        'bilder' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:patenschaften/locallang_db.xml:tx_patenschaften_buecher.bilder',
            'config' => [
                'type' => 'group',
                'internal_type' => 'file',
                'allowed' => 'gif,png,jpeg,jpg',
                'max_size' => $GLOBALS['TYPO3_CONF_VARS']['BE']['maxFileSize'],
                'uploadfolder' => 'fileadmin/media/bilder/patenschaften',
                'show_thumbs' => 1,
                'size' => 5,
                'minitems' => 0,
                'maxitems' => 5,
            ]
        ],
        'sponsorship' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:patenschaften/locallang_db.xml:tx_patenschaften_buecher.sponsorship',
            'config' => [
                'type' => 'input',
                'size' => '30',
            ]
        ],
        'category' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:patenschaften/locallang_db.xml:tx_patenschaften_buecher.category',
            'config' => [
                'type' => 'select',
                'foreign_table' => 'tx_patenschaften_kategorien',
                'foreign_table_where' => 'AND tx_patenschaften_kategorien.pid=###CURRENT_PID### ORDER BY tx_patenschaften_kategorien.uid',
                'size' => 3,
                'minitems' => 0,
                'maxitems' => 1,
            ]
        ],
    ],
    'types' => [
        '0' => ['showitem' => 'sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource, hidden;;1, titel, author, caption, signature, description;;;richtext[]:rte_transform[mode=ts_css|imgpath=uploads/tx_patenschaften/rte/], price, damage, help, bilder, sponsorship, category']
    ],
    'palettes' => [
        '1' => ['showitem' => 'starttime, endtime']
    ]
];
