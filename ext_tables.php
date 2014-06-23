<?php

if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

t3lib_extMgm::allowTableOnStandardPages('tx_patenschaften_kategorien');

$TCA['tx_patenschaften_kategorien'] = array(
	'ctrl' => array(
		'title' => 'LLL:EXT:patenschaften/locallang_db.xml:tx_patenschaften_kategorien',
		'label' => 'catname',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'languageField' => 'sys_language_uid',
		'transOrigPointerField' => 'l10n_parent',
		'transOrigDiffSourceField' => 'l10n_diffsource',
		'default_sortby' => 'ORDER BY crdate',
		'delete' => 'deleted',
		'enablecolumns' => array(
			'disabled' => 'hidden',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY) . 'tca.php',
		'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY) . 'icon_tx_patenschaften_kategorien.gif',
	),
);

$TCA['tx_patenschaften_buecher'] = array(
	'ctrl' => array(
		'title' => 'LLL:EXT:patenschaften/locallang_db.xml:tx_patenschaften_buecher',
		'label' => 'titel',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'languageField' => 'sys_language_uid',
		'transOrigPointerField' => 'l10n_parent',
		'transOrigDiffSourceField' => 'l10n_diffsource',
		'default_sortby' => 'ORDER BY crdate',
		'delete' => 'deleted',
		'enablecolumns' => array(
			'disabled' => 'hidden',
			'starttime' => 'starttime',
			'endtime' => 'endtime',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY) . 'tca.php',
		'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY) . 'icon_tx_patenschaften_buecher.gif',
	),
);


t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY . '_pi1'] = 'layout,select_key';
$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY . '_pi1'] = 'pi_flexform';

t3lib_extMgm::addPlugin(array(
			'LLL:EXT:patenschaften/locallang_db.xml:tt_content.list_type_pi1',
			$_EXTKEY . '_pi1',
			t3lib_extMgm::extRelPath($_EXTKEY) . 'ext_icon.gif'
				), 'list_type');

t3lib_extMgm::addPiFlexFormValue($_EXTKEY . '_pi1', 'FILE:EXT:' . $_EXTKEY . '/flexform.xml');

t3lib_extMgm::addStaticFile($_EXTKEY, 'pi1/static/', 'Buchpatenschaften');
?>