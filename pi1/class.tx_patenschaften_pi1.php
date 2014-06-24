<?php

/* * *************************************************************
 *  Copyright notice
 *
 *  (c) 2010 Ingo Pfennigstorf <pfennigstorf@sub.uni-goettingen.de>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 * ************************************************************* */

/**
 * Plugin 'Buchpatenschaften' for the 'patenschaften' extension.
 *
 * @author    Ingo Pfennigstorf <pfennigstorf@sub.uni-goettingen.de>, Dominic Simm <simm@sub.uni-goettingen.de>
 * @package    TYPO3
 * @subpackage    tx_patenschaften
 */
class tx_patenschaften_pi1 extends tslib_pibase {

	var $prefixId = 'tx_patenschaften_pi1'; // Same as class name
	var $scriptRelPath = 'pi1/class.tx_patenschaften_pi1.php'; // Path to this script relative to the extension dir.
	var $extKey = 'patenschaften'; // The extension key.
	var $pi_checkHash = true;

	public $conf;
	private $templateFile;
	private $buchtabelle;
	private $kattabelle;
	private $bilderpfad;
	private $bilderbreite;

	/**
	 * ID der Kategorie der bereits uebernommenen Patenschaften
	 *
	 * @var int
	 */
	private $uebernommeneId = 5;

	/**
	 * Main method of your PlugIn
	 *
	 * @param string $content : The content of the PlugIn
	 * @param array $conf : The PlugIn Configuration
	 * @return string The content that should be displayed on the website
	 */
	function main($content, $conf) {
		// Setting the TypoScript passed to this function in $this->conf
		$this->conf = $conf;
		$this->pi_setPiVarDefaults();
		// Loading the LOCAL_LANG values
		$this->pi_loadLL();
		// Tabellen die wir nutzen
		$this->setTableNames();

		// Flexform nutzen
		$this->pi_initPIflexForm();
		// Caching abschalten
		$this->pi_USER_INT_obj = 0;

		// wahrend demo phase xml importieren
		$this->conf['devMode'] == 1 ? $this->xmlImporter() : null;

		// Pfad fuer die Bilder
		$this->conf['imageFolder'] ? $this->bilderpfad = $this->conf['imageFolder'] : $this->bilderpfad = 'fileadmin/media/bilder/patenschaften/';

		// Bildergroesse
		$this->conf['imageWidth'] ? $this->bilderbreite = $this->conf['imageWidth'] : $this->bilderbreite = 180;
		$this->conf['imageHeight'] ? $this->bilderhoehe = $this->conf['imageHeight'] : $this->bilderhoehe = 180;
		$this->conf['catImageWidth'] ? $this->catBilderbreite = $this->conf['catImageWidth'] : $this->catBilderbreite = 60;
		$this->conf['catImageHeight'] ? $this->catBilderhoehe = $this->conf['catImageHeight'] : $this->catBilderhoehe = 60;


		// Template auswaehlen, wenn nichts ueber TS konfiguriert nehmen wir das default Template
		$this->conf['templateFile'] ? $this->templateFile = $this->cObj->fileResource($this->conf['templateFile']) : $this->templateFile = $this->cObj->fileResource('EXT:' . $this->extKey . '/res/patenschaften_template.html');

		// GP Parameter
		$parameter = t3lib_div::_GP('tx_patenschaften_pi1');

		// was will der user?
		$form = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'formwahl');

		switch ($form) {
			case 'listdetail':
				if (t3lib_utility_Math::canBeInterpretedAsInteger($parameter['showBook'])) {
					$content = $this->singleView($parameter['showBook']);
				} elseif (isset($parameter['category'])) {
					$content = $this->catView($parameter['category']);
				} else {
					$content = $this->catListView();
				}
				break;
			case 'form:select-box':
				$content = $this->getAvailableBookSelection();
				break;
			case 'paten':
				$content = $this->getPatenschaften();
				break;
			case 'uebernommene':
				if (t3lib_utility_Math::canBeInterpretedAsInteger($parameter['showBook'])) {
					$content = $this->getUebernommenePatenschaft($parameter['showBook']);
				} else {
					$content = $this->uebernommenePatenschaftListView();
				}
				break;
		}

		return $this->pi_wrapInBaseClass($content);
	}

	/**
	 * Importiert XML files in die TYPO3 Tabelle
	 *
	 * @return void
	 */
	private function xmlImporter() {

		$GLOBALS['TYPO3_DB']->exec_TRUNCATEQuery(
				$this->buchtabelle
		);

		$xml = $this->cObj->fileResource('EXT:' . $this->extKey . '/res/buecherliste.xml');
		$buecher = t3lib_div::xml2tree($xml);
		$buecher = $buecher['booklist'][0]['ch']['book'];

		for ($i = 0; $i < count($buecher); $i++) {
			$this->buecherInDB($buecher[$i]);
		}
	}

	/**
	 * Aus einem Bucharray wird in die Datenbank geschrieben
	 *
	 * @param array $buch
	 */
	private function buecherInDB($buch) {

		$werte = array(
				'pid' => $this->conf['pidList'],
				'crdate' => time(),
				'tstamp' => time(),
				'titel' => $buch['ch']['title'][0]['values'][0],
				'author' => $buch['ch']['author'][0]['values'][0],
				'search' => $buch['ch']['search'][0]['values'][0],
				'caption' => $buch['ch']['caption'][0]['values'][0],
				'signature' => trim(str_replace('Signatur:', '', $buch['ch']['signature'][0]['values'][0])),
				'description' => $buch['ch']['description'][0]['values'][0],
				'price' => $buch['ch']['price'][0]['values'][0],
				'damage' => $buch['ch']['damage'][0]['values'][0],
				'help' => $buch['ch']['help'][0]['values'][0],
				'sponsorship' => $buch['ch']['sponsorship'][0]['values'][0],
				'category' => $buch['ch']['subject'][0]['values'][0],
				'bilder' => $this->importiereBilder($buch['ch']['images'][0]['values'][0])
		);

		$GLOBALS['TYPO3_DB']->exec_INSERTquery(
				$this->buchtabelle,
				$werte,
				$no_quote_fields = FALSE
		);
	}

	/**
	 * Importieren von Bildern
	 * Kopieren aus Unterordnern in ein gemeinsames Verzeichnis
	 *
	 * @param string $werte
	 * @return string
	 */
	private function importiereBilder($werte) {
		$alles = explode(';', $werte);

		// erste angabe im XML ist der Pfad an dem sich die Bilder befinden.
		$pfad = $alles[0];

		// Pfadangabe aus dem Array loeschen
		unset($alles[0]);

		// bilder verschieben
		foreach ($alles as $bild) {
			$source = t3lib_div::getFileAbsFileName('fileadmin/media/bilder/patenschaften/' . $pfad . '/' . $bild);
			$destination = t3lib_div::getFileAbsFileName('fileadmin/media/bilder/patenschaften/' . $bild);
			t3lib_div::upload_copy_move($source, $destination);
		}

		// bilder kommasepariert in datenbank
		$bilder = implode(',', $alles);

		return $bilder;
	}

	/**
	 * Ausgabe der Namen aller Patenschaften
	 *
	 * @return string Patenschaften als Liste
	 */
	private function getPatenschaften() {

		$patenschaftenconf = $this->conf['patenschaften.'];
		$singleWrap = $patenschaftenconf['singleWrap'];
		$listWrap = $patenschaftenconf['listWrap'];

		// Query fuer alle Paten
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
				'DISTINCT(sponsorship)',
				$this->buchtabelle,
				' sponsorship !="" AND sponsorship NOT LIKE "%genannt%"',
				'',
				'sponsorship ASC',
				''
		);
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			$sponsoren .= $this->cObj->wrap(implode(" ", explode(";", $row['sponsorship'])), $singleWrap);
		}
		// Wrappen das ganze noch mit dem was im TS festgelegt ist
		$sponsoren = $this->cObj->wrap($sponsoren, $listWrap);
		return $sponsoren;
	}

	/**
	 * Ausgabe der uebernommenen Patenschaften
	 *
	 * @return string
	 */
	private function uebernommenePatenschaftListView() {
		// Liste alle Kategorien
		$kategorien = $this->getAllKategorien();

		// Subpart des Templates waehlen
		$template = $this->cObj->getSubpart($this->templateFile, '###KATLIST###');

		$markerArray['###UEBERSICHT###'] = '';

		$inhalt = '';

		foreach ($kategorien as $kategorie) {
			// Marker mit Inhalten fuellen
			if ($kategorie['uid'] == $this->uebernommeneId) {
				$markerArray['###KATNAME###'] = $this->cObj->wrap($kategorie['catname'], '<a id="buchkat-' . $kategorie['uid'] . '"></a><h2 class="category">|</h2>');
				$markerArray['###BUECHER###'] = $this->getAllBuecherFromCat($kategorie['uid']);
				$markerArray['###SPACER###'] = '';

				$inhalt .= $this->cObj->substituteMarkerArrayCached($template, $markerArray);
			}
		}

		$daten = $inhalt;

		return $daten;
	}


	/**
	 * Generiert die Kategorienansicht
	 *
	 * @return string
	 */
	private function catListView() {
		// Liste alle Kategorien
		$kategorien = $this->getAllKategorien();

		// Subpart des Templates waehlen
		$template = $this->cObj->getSubpart($this->templateFile, '###KATLIST###');

		$markerArray['###UEBERSICHT###'] = '';

		$numberOfCategories = count($kategorien) - 1; // abzuegl. uebernommene
		foreach ($kategorien as $kategorie) {
			// Marker mit Inhalten fuellen
			if ($kategorie['uid'] != $this->uebernommeneId) {
				$numberOfCategories--;
				$markerArray['###KATNAME###'] = $this->cObj->wrap($kategorie['catname'], '<a id="buchkat-' . $kategorie['uid'] . '"></a><h2 class="category">|</h2>');
				$markerArray['###BUECHER###'] = $this->getAllBuecherFromCat($kategorie['uid']);
				$markerArray['###SPACER###'] = "<br />";
				$inhalt .= $this->cObj->substituteMarkerArrayCached($template, $markerArray);
			}
		}

		$daten = $inhalt;

		return $daten;
	}


	/**
	 * Generiert die Kategorienansicht
	 *
	 * @param int
	 * @return string
	 */
	private function catView($category) {
		$match = false;

		// Liste alle Kategorien
		$kategorien = $this->getAllKategorien();

		// Subpart des Templates waehlen
		$template = $this->cObj->getSubpart($this->templateFile, '###KATLIST###');

		$markerArray['###UEBERSICHT###'] = '';

		// abzueglich bereits uebernommener
		$numberOfCategories = count($kategorien) - 1;
		foreach ($kategorien as $kategorie) {
			// Marker mit Inhalten fuellen
			if ($kategorie['uid'] == $category && $kategorie['uid'] != $this->uebernommeneId) {
				$match = true;
				$markerArray['###KATNAME###'] = $this->cObj->wrap($kategorie['catname'], '<a id="buchkat-' . $kategorie['uid'] . '"></a><h2 class="category">|</h2>');
				$markerArray['###BUECHER###'] = $this->getAllBuecherFromCat($kategorie['uid']);
				$markerArray['###SPACER###'] = '<br />';
				$inhalt .= $this->cObj->substituteMarkerArrayCached($template, $markerArray);
			}
		}

		if ($match) {
			$daten = $inhalt;
			return $daten;
		} else {
			return $this->catListView();
		}
	}

	/**
	 * Generiert die Kategorienansicht für Hook in nkwsubmenu_pi2 (infobox)
	 * jedoch wenn man sich auf der Seite ... befindet
	 *
	 * @param string &$tmp
	 * @param object &$obj
	 * @return string
	 */
	public function hookFunc(&$tmp, &$obj) {
		// Liste aller Kategorien
		$object = new tx_patenschaften_pi1();
		$object->setTableNames();
		$object->pi_loadLL();
		$tsParser = t3lib_div::makeInstance('t3lib_TSparser');
		foreach ($GLOBALS['TSFE']->tmpl->constants as $value) {
			$tsParser->parse($value, $matchObj = '');
		}
		$object->pageID = array($tsParser->setup['newListID'], $tsParser->setup['takenListID']);

		$kategorien = $object->getAllKategorien();
		$parameter = t3lib_div::_GP('tx_patenschaften_pi1');

		if (in_array($GLOBALS['TSFE']->id, $object->pageID)) {
			if (!isset($parameter['showBook'])) {
				if ($GLOBALS['TSFE']->id == $object->pageID[0]) {
					$tmp = '';
					foreach ($kategorien as $kategorie) {
						if ($kategorie['uid'] != $object->uebernommeneId) {
							$tmp .= '<li>';
							// Anker
							$uebersicht = $obj->pi_getPageLink($GLOBALS['TSFE']->id) . '?tx_patenschaften_pi1[category]=' . $kategorie['uid'];
							$tmp .= $obj->cObj->getTypoLink($kategorie['catname'], $uebersicht);
							$tmp .= '</li>';
						}
					}
				}
			} else {
				$i = 0;
				$bookID = $parameter['showBook'];
				if ($GLOBALS['TSFE']->id == $object->pageID[1]) $where = "`sponsorship` != '' AND ";
				else                                            $where = "`sponsorship` = '' AND ";
				$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
						'*',
						$object->buchtabelle,
						$where . "`deleted` = 0 AND `hidden` = 0",
						'',
						'`search` ASC , `author` ASC',
						''
				);
				while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
					if ($row['uid'] == $bookID) $id = $i;
					$books[$i++] = $row;
				}

				$tmp = '<li>';
				$tmp .= $obj->pi_linkTP($object->pi_getLL('infobox_previousbook'), array('tx_patenschaften_pi1[showBook]' => $books[$id - 1]['uid']), 1);
				$tmp .= '</li>' . "\n" . '<li>';
				$tmp .= $obj->pi_linkTP($object->pi_getLL('infobox_nextbook'), array('tx_patenschaften_pi1[showBook]' => $books[$id + 1]['uid']), 1);
				$tmp .= '</li>';
			}
		}
	}

	/**
	 * Generiert die Kategorienansicht für Hook in nkwsubmenu_pi2 (infobox)
	 * jedoch wenn man sich auf der Seite ... befindet
	 *
	 * @param string
	 * @param object
	 * @return string
	 */
	public function hookPicFunc(&$tmp, &$obj) {
		$object = new tx_patenschaften_pi1();
		$tsParser = t3lib_div::makeInstance('t3lib_TSparser');
		foreach ($GLOBALS['TSFE']->tmpl->constants as $value) {
			$tsParser->parse($value, $matchObj = '');
		}
		$object->pageID = array($tsParser->setup['newListID'], $tsParser->setup['takenListID']);
	}

	/**
	 * Initialisiere Variablen mit Tabellennamen
	 *
	 * @return void
	 */
	private function setTableNames() {
		$this->buchtabelle = 'tx_patenschaften_buecher';
		$this->kattabelle = 'tx_patenschaften_kategorien';
	}

	/**
	 * Uebersicht ueber die Kategorien
	 *
	 * @param array Kategorien
	 * @return string Kategorienliste
	 */
	private function generiereCatUebersicht($kategorien) {

		foreach ($kategorien as $kategorie) {
			// Link erzeugen und hash fuer Sprungmarke anfuegen
			$uebersicht = $this->pi_getPageLink($GLOBALS['TSFE']->id) . '#buchkat-' . $kategorie['uid'];
			$link .= $this->cObj->getTypoLink($kategorie['catname'], $uebersicht) . ' | ';
		}
		return $link;
	}

	/**
	 * Gibt alle Buecher aus einer Kategorie aus
	 *
	 * @param int $catId
	 * @return string
	 */
	private function getAllBuecherFromCat($catId) {

		// Subpart des Templates waehlen
		$template = $this->cObj->getSubpart($this->templateFile, '###BUCHUEBERBLICK###');

		// DB Abfrage nach Buechern der Kategorie
		if ($catId != $this->uebernommeneId) {
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
					'*',
					$this->buchtabelle,
					' FIND_IN_SET(' . $catId . ',category) AND deleted=0 AND hidden=0 AND sponsorship="" ',
					'',
					' `search` ASC , `author` ASC',
					''
			);
		} else {
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
					'*',
					$this->buchtabelle,
					' deleted=0 AND hidden=0 AND sponsorship != "" ',
					'',
					' `search` ASC , `author` ASC',
					''
			);
		}
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {

			// Parameter fuer den Link
			$urlParameters = array(
					'tx_patenschaften_pi1[showBook]' => $row['uid']
			);

			// Marker ersetzen
			$markerArray['###AUTHOR###'] = ($row['author'] != "") ? $row['author'] . "<br />" : "";
			$markerArray['###TITELLINK###'] = $this->pi_linkTP($row['titel'], $urlParameters, 1);
			$markerArray['###KURZBESCHREIBUNG###'] = nl2br($row['caption']);
			$markerArray['###RESTAURIERUNGSKOSTEN###'] = $this->pi_getLL('restaurierungskosten');
			$markerArray['###PREIS###'] = $row['price'];
			if (!empty($row['bilder'])) {
				$bilder = explode(',', $row['bilder']);
				$bild = $bilder[0];
				$bildconf = array(
						'file' => $this->bilderpfad . $bild,
						'file.' => array(
								'maxW' => $this->catBilderbreite,
								'height' => $this->catBilderhoehe,
						),
				);
				$bildconf['stdWrap.']['addParams.']['alt'] = 'Bild ' . $i . ' zum Titel ' . $row['titel'];
				$bildconf['stdWrap.']['addParams.']['class'] = "picture" . " " . "nr0";

				$wrap = "<a href='" . $this->bilderpfad . $bild . "' rel='shadowbox' title='" . $row['titel'] . "'>|</a>";
				$markerArray['###IMG###'] = $this->cObj->linkWrap($this->cObj->IMAGE($bildconf), $wrap);
			} else {
				$markerArray['###IMG###'] = '';
			}

			$inhalt .= $this->cObj->substituteMarkerArrayCached($template, $markerArray);
		}
		return $inhalt;
	}

	/**
	 * Display a single item from the database
	 *
	 * @param int $id UID eines Datensatzes
	 * @return string HTML of a single database entry
	 */
	function singleView($id) {

		$template = $this->cObj->getSubpart($this->templateFile, '###BUCHANSICHT###');

		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
				'*',
				$this->buchtabelle,
				'deleted=0 AND hidden=0 AND uid=' . $id,
				'',
				'',
				''
		);
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			$urlParameters = array(
					'tx_powermail_pi1[uid' . $this->conf['selectID'] . ']' => $row['uid']
			);
			$markerArray['###CATEGORY###'] = $this->getKategorie($row['category']);
			$markerArray['###TITEL###'] = $row['titel'];
			$markerArray['###AUTHOR###'] = ($row['author'] != "") ? $row['author'] . ':<br />' : NULL;
			$markerArray['###CAPTION###'] = nl2br($row['caption']);
			$markerArray['###HEADER_SIGNATURE###'] = $this->pi_getLL('listFieldHeader_signature');
			$markerArray['###SIGNATURE###'] = '<a href="https://opac.sub.uni-goettingen.de/DB=1/SET=2/TTL=30/CMD?ACT=SRCHA&IKT=1016&SRT=YOP&TRM=SGN+' . preg_replace('/[\W]+/', '+', $row['signature']) . '">' . $row['signature'] . '</a>';
			$markerArray['###DESCRIPTION###'] = nl2br($this->pi_RTEcssText($row['description']));
			$markerArray['###HEADER_RESTAURIERUNGSKOSTEN###'] = $this->pi_getLL('restaurierungskosten');
			$markerArray['###PRICE###'] = $row['price'];
			$markerArray['###HEADER_ZUSTANDSBESCHREIBUNG###'] = $this->pi_getLL('zustandsbeschreibung');
			$markerArray['###DAMAGE###'] = $row['damage'];
			$markerArray['###HEADER_HELP###'] = $this->pi_getLL('listFieldHeader_help');
			$markerArray['###HELP###'] = $row['help'];
			$markerArray['###BACK###'] = $this->pi_linkToPage($this->pi_getLL('back', 'Back'), $this->conf['newListID']);
			$markerArray['###IWANT###'] = $this->pi_linkToPage($this->pi_getLL('iwant'), $this->conf['formPage'], $target, $urlParameters);

			$bilder = explode(',', $row['bilder']);

			// Bilder aufbereiten
			$i = 1;
			foreach ($bilder as $bild) {
				$bildconf = array(
						'file' => $this->bilderpfad . $bild,
						'file.' => array(
								'maxW' => $this->bilderbreite,
								'height' => $this->bilderhoehe,
						),
				);
				$bildconf['stdWrap.']['addParams.']['alt'] = 'Bild ' . $i . ' zum Titel ' . $row['titel'];
				$bildconf['stdWrap.']['addParams.']['class'] = "picture" . " " . "nr" . ($i % 2);

				$wrap = "<a href='" . $this->bilderpfad . $bild . "' rel='shadowbox[preview]' title='" . $row['titel'] . "'>|</a>";
				$markerArray['###IMG' . $i . '###'] = $this->cObj->linkWrap($this->cObj->IMAGE($bildconf), $wrap);
				$i++;
			}
			// falls mal nur zwei Bilder vorhanden sind
			(count($bilder) == 2) ? $markerArray['###IMG3###'] = '' : null;
			$content = $this->cObj->substituteMarkerArrayCached($template, $markerArray);
		}
		return $content;
	}

	/**
	 * Ausgabe der uebernommenen Patenschaften
	 *
	 * @return string
	 */
	private function getUebernommenePatenschaft($id) {
		$template = $this->cObj->getSubpart($this->templateFile, '###UEBERNOMMENE###');

		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
				'*', //WHAT
				$this->buchtabelle,
				' deleted=0 AND hidden=0 AND uid=' . $id,
				'',
				'',
				''
		);
		if ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			$urlParameters = array(
					'tx_powermail_pi1[uid' . $this->conf['selectID'] . ']' => $row['uid']
			);
			$markerArray['###CATEGORY###'] = $this->getKategorie($row['category']);
			$markerArray['###TITEL###'] = $row['titel'];
			$markerArray['###AUTHOR###'] = ($row['author'] != "") ? $row['author'] . ":<br />" : NULL;
			$markerArray['###CAPTION###'] = $row['caption'];
			$markerArray['###HEADER_SIGNATURE###'] = $this->pi_getLL('listFieldHeader_signature');
			$markerArray['###SIGNATURE###'] = '<a href="https://opac.sub.uni-goettingen.de/DB=1/SET=2/TTL=30/CMD?ACT=SRCHA&IKT=1016&SRT=YOP&TRM=SGN+' . preg_replace('/[\W]+/', '+', $row['signature']) . '">' . $row['signature'] . '</a>';
			$markerArray['###DESCRIPTION###'] = $this->pi_RTEcssText($row['description']);
			$markerArray['###HEADER_RESTAURIERUNGSKOSTEN###'] = $this->pi_getLL('restaurierungskosten');
			$markerArray['###PRICE###'] = $row['price'];
			$markerArray['###HEADER_ZUSTANDSBESCHREIBUNG###'] = $this->pi_getLL('zustandsbeschreibung');
			$markerArray['###DAMAGE###'] = $row['damage'];
			$markerArray['###HEADER_HELP###'] = $this->pi_getLL('listFieldHeader_help');
			$markerArray['###HELP###'] = $row['help'];
			$markerArray['###BACK###'] = $this->pi_linkToPage($this->pi_getLL('back', 'Back'), $this->conf['takenListID']);
			$markerArray['###HEADER_SPONSOR###'] = $this->pi_getLL('listFieldHeader_sponsorship');
			$markerArray['###SPONSOR###'] = implode(" ", explode(";", $row['sponsorship']));

			$bilder = explode(',', $row['bilder']);

			$replacedImageMarkers = array();

			// Bilder aufbereiten
			$i = 1;
			foreach ($bilder as $bild) {
				$bildconf = array(
						'file' => $this->bilderpfad . $bild,
						'file.' => array(
								'maxW' => $this->bilderbreite,
								'height' => $this->bilderhoehe,
						),
				);
				$bildconf['stdWrap.']['addParams.']['alt'] = 'Bild ' . $i . ' zum Titel ' . $row['titel'];
				$bildconf['stdWrap.']['addParams.']['class'] = "picture" . " " . "nr" . ($i % 2);

				$wrap = "<a href='" . $this->bilderpfad . $bild . "' rel='shadowbox[preview]' title='" . $row['titel'] . "'>|</a>";
				$markerArray['###IMG' . $i . '###'] = $this->cObj->linkWrap($this->cObj->IMAGE($bildconf), $wrap);
				$replacedImageMarkers[] = $i;
				$i++;
			}
			// falls mal nur zwei Bilder vorhanden sind
			(count($bilder) == 2) ? $markerArray['###IMG3###'] = '' : null;
			if (count($replacedImageMarkers) < 2) {
				$markerArray['###IMG2###'] = '';
				$markerArray['###IMG3###'] = '';
			}

			$content = $this->cObj->substituteMarkerArrayCached($template, $markerArray);
		}
		return $content;
	}

	/**
	 * Kategorie(n) als String ausgeben
	 *
	 * @param string $kat
	 * @return string Kategorien
	 */
	private function getKategorie($kat) {

		try {
			$kats = explode(',', $kat);
			foreach ($kats as $kategorie) {
				$kategorien .= $this->leseKatAusDb($kategorie);
			}
		} catch (Exception $e) {
			$kategorien = $e->getTraceAsString();
		}

		return $kategorien;
	}

	/**
	 * Ausgabe der Kategorien
	 *
	 * @param int $id
	 * @return string Kategorien
	 */
	private function leseKatAusDb($id) {
		$cat = '';
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
				'catname',
				$this->kattabelle,
				' deleted=0 AND hidden = 0 AND uid =' . $id,
				'',
				'catname ASC',
				''
		);
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			$cat .= $row['catname'];
		}
		return $cat;
	}

	/**
	 * Ausgabe aller vorhandenen Kategorien
	 *
	 * @return array
	 */
	private function getAllKategorien() {
		/*
		 * 0 => ohne
		 * 1 => "Schöne Literatur",
		 * 2 => "Philosophie und Theologie",
		 * 3 => "Mathematik und Naturwissenschaften",
		 * 4 => "Künste und Handwerke",
		 * 5 => "Bereits übernommene Patenschaften"
		 * 6 => "Geschichte",
		 * 7 => "Geographie und Reisebeschreibungen",
		 * 8 => "Naturwissenschaften und Medizin"
		 */
		$kategorien = array();
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
				'uid, catname ',
				$this->kattabelle,
				' deleted=0 AND hidden=0',
				'',
				'uid ASC',
				''
		);
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			$kategorien[] = $row;
		}
		return $kategorien;
	}

	/**
	 * Verfuegbare Buecher
	 *
	 * @return string
	 */
	private function getAvailableBookSelection() {
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
				'uid, titel',
				$this->buchtabelle,
				' deleted=0 AND hidden=0 AND sponsorship = "" ',
				'',
				' search ASC , author ASC',
				''
		);
		$selection = "<label>" . $this->pi_getLL('formFieldHeader_sponsorship') . ":</label>\n";
		$selection .= "<select>\n";
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			$selection .= '<option title="' . $row['titel'] . '">' . $row['titel'] . "</option>\n";
		}
		$selection .= "</select>";
		return $selection;
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/patenschaften/pi1/class.tx_patenschaften_pi1.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/patenschaften/pi1/class.tx_patenschaften_pi1.php']);
}
?>