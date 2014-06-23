<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

$TCA['tx_patenschaften_kategorien'] = array (
	'ctrl' => $TCA['tx_patenschaften_kategorien']['ctrl'],
	'interface' => array (
		'showRecordFieldList' => 'sys_language_uid,l10n_parent,l10n_diffsource,hidden,catname'
	),
	'feInterface' => $TCA['tx_patenschaften_kategorien']['feInterface'],
	'columns' => array (
		'sys_language_uid' => array (		
			'exclude' => 1,
			'label'  => 'LLL:EXT:lang/locallang_general.xml:LGL.language',
			'config' => array (
				'type'                => 'select',
				'foreign_table'       => 'sys_language',
				'foreign_table_where' => 'ORDER BY sys_language.title',
				'items' => array(
					array('LLL:EXT:lang/locallang_general.xml:LGL.allLanguages', -1),
					array('LLL:EXT:lang/locallang_general.xml:LGL.default_value', 0)
				)
			)
		),
		'l10n_parent' => array (		
			'displayCond' => 'FIELD:sys_language_uid:>:0',
			'exclude'     => 1,
			'label'       => 'LLL:EXT:lang/locallang_general.xml:LGL.l18n_parent',
			'config'      => array (
				'type'  => 'select',
				'items' => array (
					array('', 0),
				),
				'foreign_table'       => 'tx_patenschaften_kategorien',
				'foreign_table_where' => 'AND tx_patenschaften_kategorien.pid=###CURRENT_PID### AND tx_patenschaften_kategorien.sys_language_uid IN (-1,0)',
			)
		),
		'l10n_diffsource' => array (		
			'config' => array (
				'type' => 'passthrough'
			)
		),
		'hidden' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config'  => array (
				'type'    => 'check',
				'default' => '0'
			)
		),
		'catname' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:patenschaften/locallang_db.xml:tx_patenschaften_kategorien.catname',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',	
				'eval' => 'required',
			)
		),
	),
	'types' => array (
		'0' => array('showitem' => 'sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource, hidden;;1, catname')
	),
	'palettes' => array (
		'1' => array('showitem' => '')
	)
);



$TCA['tx_patenschaften_buecher'] = array (
	'ctrl' => $TCA['tx_patenschaften_buecher']['ctrl'],
	'interface' => array (
		'showRecordFieldList' => 'sys_language_uid,l10n_parent,l10n_diffsource,hidden,starttime,endtime,titel,author,caption,signature,description,price,damage,help,bilder,sponsorship,category'
	),
	'feInterface' => $TCA['tx_patenschaften_buecher']['feInterface'],
	'columns' => array (
		'sys_language_uid' => array (		
			'exclude' => 1,
			'label'  => 'LLL:EXT:lang/locallang_general.xml:LGL.language',
			'config' => array (
				'type'                => 'select',
				'foreign_table'       => 'sys_language',
				'foreign_table_where' => 'ORDER BY sys_language.title',
				'items' => array(
					array('LLL:EXT:lang/locallang_general.xml:LGL.allLanguages', -1),
					array('LLL:EXT:lang/locallang_general.xml:LGL.default_value', 0)
				)
			)
		),
		'l10n_parent' => array (		
			'displayCond' => 'FIELD:sys_language_uid:>:0',
			'exclude'     => 1,
			'label'       => 'LLL:EXT:lang/locallang_general.xml:LGL.l18n_parent',
			'config'      => array (
				'type'  => 'select',
				'items' => array (
					array('', 0),
				),
				'foreign_table'       => 'tx_patenschaften_buecher',
				'foreign_table_where' => 'AND tx_patenschaften_buecher.pid=###CURRENT_PID### AND tx_patenschaften_buecher.sys_language_uid IN (-1,0)',
			)
		),
		'l10n_diffsource' => array (		
			'config' => array (
				'type' => 'passthrough'
			)
		),
		'hidden' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config'  => array (
				'type'    => 'check',
				'default' => '0'
			)
		),
		'starttime' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.starttime',
			'config'  => array (
				'type'     => 'input',
				'size'     => '8',
				'max'      => '20',
				'eval'     => 'date',
				'default'  => '0',
				'checkbox' => '0'
			)
		),
		'endtime' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.endtime',
			'config'  => array (
				'type'     => 'input',
				'size'     => '8',
				'max'      => '20',
				'eval'     => 'date',
				'checkbox' => '0',
				'default'  => '0',
				'range'    => array (
					'upper' => mktime(3, 14, 7, 1, 19, 2038),
					'lower' => mktime(0, 0, 0, date('m')-1, date('d'), date('Y'))
				)
			)
		),
		'titel' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:patenschaften/locallang_db.xml:tx_patenschaften_buecher.titel',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',	
				'eval' => 'trim',
			)
		),
		'author' => array (
			'exclude' => 0,
			'label' => 'LLL:EXT:patenschaften/locallang_db.xml:tx_patenschaften_buecher.author',
			'config' => array (
				'type' => 'input',
				'size' => '30',
				'eval' => 'trim',
			)
		),
		'caption' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:patenschaften/locallang_db.xml:tx_patenschaften_buecher.caption',		
			'config' => array (
				'type' => 'text',
				'cols' => '30',	
				'rows' => '2',
			)
		),
		'signature' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:patenschaften/locallang_db.xml:tx_patenschaften_buecher.signature',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',
			)
		),
		'description' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:patenschaften/locallang_db.xml:tx_patenschaften_buecher.description',		
			'config' => array (
				'type' => 'text',
				'cols' => '30',
				'rows' => '5',
			)
		),
		'price' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:patenschaften/locallang_db.xml:tx_patenschaften_buecher.price',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',
			)
		),
		'damage' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:patenschaften/locallang_db.xml:tx_patenschaften_buecher.damage',		
			'config' => array (
				'type' => 'text',
				'cols' => '30',
				'rows' => '5',
			)
		),
		'help' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:patenschaften/locallang_db.xml:tx_patenschaften_buecher.help',		
			'config' => array (
				'type' => 'text',
				'cols' => '30',
				'rows' => '5',
			)
		),
		'bilder' => array (
            'exclude' => 0,
            'label' => 'LLL:EXT:patenschaften/locallang_db.xml:tx_patenschaften_buecher.bilder',
            'config' => array (
                'type' => 'group',
                'internal_type' => 'file',
                'allowed' => 'gif,png,jpeg,jpg',
                'max_size' => $GLOBALS['TYPO3_CONF_VARS']['BE']['maxFileSize'],
                'uploadfolder' => 'fileadmin/media/bilder/patenschaften',
                'show_thumbs' => 1,
                'size' => 5,
                'minitems' => 0,
                'maxitems' => 5,
            )
        ),
		'sponsorship' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:patenschaften/locallang_db.xml:tx_patenschaften_buecher.sponsorship',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',
			)
		),
		'category' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:patenschaften/locallang_db.xml:tx_patenschaften_buecher.category',		
			'config' => array (
				'type' => 'select',	
				'foreign_table' => 'tx_patenschaften_kategorien',	
				'foreign_table_where' => 'AND tx_patenschaften_kategorien.pid=###CURRENT_PID### ORDER BY tx_patenschaften_kategorien.uid',	
				'size' => 3,	
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
	),
	'types' => array (
		'0' => array('showitem' => 'sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource, hidden;;1, titel, author, caption, signature, description;;;richtext[]:rte_transform[mode=ts_css|imgpath=uploads/tx_patenschaften/rte/], price, damage, help, bilder, sponsorship, category')
	),
	'palettes' => array (
		'1' => array('showitem' => 'starttime, endtime')
	)
);
?>