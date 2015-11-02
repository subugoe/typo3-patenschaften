<?php

if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addUserTSConfig('
	options.saveDocNew.tx_patenschaften_kategorien=1
');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addUserTSConfig('
	options.saveDocNew.tx_patenschaften_buecher=1
');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig('

	# ***************************************************************************************
	# CONFIGURATION of RTE in table "tx_patenschaften_buecher", field "description"
	# ***************************************************************************************
RTE.config.tx_patenschaften_buecher.description {
  hidePStyleItems = H1, H4, H5, H6
  proc.exitHTMLparser_db=1
  proc.exitHTMLparser_db {
    keepNonMatchedTags=1
    tags.font.allowedAttribs= color
    tags.font.rmTagIfNoAttrib = 1
    tags.font.nesting = global
  }
}
');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPItoST43($_EXTKEY, 'pi1/class.tx_patenschaften_pi1.php', '_pi1',
    'list_type', 0);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScript($_EXTKEY, 'setup', '
	tt_content.shortcut.20.0.conf.tx_patenschaften_buecher = < plugin.' . \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getCN($_EXTKEY) . '_pi1
	tt_content.shortcut.20.0.conf.tx_patenschaften_buecher.CMD = singleView
', 43);

$TYPO3_CONF_VARS['EXTCONF']['nkwsubmenu']['extendTOC'][$_EXTKEY] = 'EXT:' . $_EXTKEY . '/pi1/class.tx_patenschaften_pi1.php:tx_patenschaften_pi1->hookFunc';
$TYPO3_CONF_VARS['EXTCONF']['nkwsubmenu']['addImages'][$_EXTKEY] = 'EXT:' . $_EXTKEY . '/pi1/class.tx_patenschaften_pi1.php:tx_patenschaften_pi1->hookPicFunc';
