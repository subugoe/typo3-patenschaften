<?php

########################################################################
# Extension Manager/Repository config file for ext "patenschaften".
#
# Auto generated 29-11-2010 11:18
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
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
	'version' => '2.0.0',
	'constraints' => array(
		'depends' => array(
				'typo3' => '6.2.0-7.99.99',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:16:{s:9:"ChangeLog";s:4:"ca89";s:10:"README.txt";s:4:"f227";s:12:"ext_icon.gif";s:4:"1dbb";s:17:"ext_localconf.php";s:4:"27e8";s:14:"ext_tables.php";s:4:"1231";s:14:"ext_tables.sql";s:4:"0d1f";s:12:"flexform.xml";s:4:"3619";s:33:"icon_tx_patenschaften_buecher.gif";s:4:"cbf8";s:36:"icon_tx_patenschaften_kategorien.gif";s:4:"744e";s:16:"locallang_db.xml";s:4:"68b0";s:7:"tca.php";s:4:"f4ee";s:34:"pi1/class.tx_patenschaften_pi1.php";s:4:"1599";s:17:"pi1/locallang.xml";s:4:"df20";s:20:"pi1/static/setup.txt";s:4:"c098";s:20:"res/buecherliste.xml";s:4:"688e";s:31:"res/patenschaften_template.html";s:4:"2289";}',
	'suggests' => array(
	),
);

?>
