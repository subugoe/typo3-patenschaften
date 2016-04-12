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
 */
class tx_patenschaften_pi1 extends \TYPO3\CMS\Frontend\Plugin\AbstractPlugin
{

    /**
     * @var string
     */
    public $prefixId = 'tx_patenschaften_pi1';

    /**
     * @var string
     */
    public $scriptRelPath = 'pi1/class.tx_patenschaften_pi1.php';

    /**
     * @var string
     */
    public $extKey = 'patenschaften';

    /**
     * @var bool
     */
    public $pi_checkHash = true;

    /**
     * @var array
     */
    public $conf;

    /**
     * @var string
     */
    protected $templateFile;

    /**
     * @var string
     */
    protected $buchtabelle;

    /**
     * @var string
     */
    protected $kattabelle;

    /**
     * @var string
     */
    protected $bilderpfad;

    /**
     * @var int
     */
    protected $pageID;

    /**
     * @var int
     */
    protected $bilderbreite;

    /**
     * ID der Kategorie der bereits uebernommenen Patenschaften
     *
     * @var int
     */
    protected $uebernommeneId = 5;

    /**
     * @var \TYPO3\CMS\Core\Database\DatabaseConnection
     */
    protected $db;

    /**
     * @var int
     */
    protected $bilderhoehe;

    /**
     * @var int
     */
    protected $catBilderhoehe;

    /**
     * @var int
     */
    protected $catBilderbreite;

    /**
     * @var \TYPO3\CMS\Core\Page\PageRenderer
     */
    protected $pageRenderer;

    /**
     * Main method of your PlugIn
     *
     * @param string $content : The content of the PlugIn
     * @param array $conf : The PlugIn Configuration
     * @return string The content that should be displayed on the website
     */
    public function main($content, $conf)
    {

        $this->db = $GLOBALS['TYPO3_DB'];

        /** @var \TYPO3\CMS\Core\Page\PageRenderer pageRenderer */
        $this->pageRenderer = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Page\PageRenderer::class);
        $this->pageRenderer->addCssFile(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('patenschaften') . 'Resources/Public/Css/patenschaften.css');

        // Setting the TypoScript passed to this function in $this->conf
        $this->conf = $conf;
        $this->pi_setPiVarDefaults();
        // Loading the LOCAL_LANG values
        $this->pi_loadLL();
        // names of tables being used here
        $this->setTableNames();

        // Flexform nutzen
        $this->pi_initPIflexForm();
        // deactivate caching
        $this->pi_USER_INT_obj = 0;

        // image path
        $this->conf['imageFolder'] ? $this->bilderpfad = $this->conf['imageFolder'] : $this->bilderpfad = 'fileadmin/media/bilder/patenschaften/';

        $this->setImageSizes();

        // Template choser
        $this->conf['templateFile'] ? $this->templateFile = $this->cObj->fileResource($this->conf['templateFile']) : $this->templateFile = $this->cObj->fileResource('EXT:' . $this->extKey . '/Resources/Private/Templates/patenschaften.html');

        // GP Parameter
        $parameter = \TYPO3\CMS\Core\Utility\GeneralUtility::_GP('tx_patenschaften_pi1');

        $form = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'formwahl');

        switch ($form) {
            case 'listdetail':
                if (\TYPO3\CMS\Core\Utility\MathUtility::canBeInterpretedAsInteger($parameter['showBook'])) {
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
                if (\TYPO3\CMS\Core\Utility\MathUtility::canBeInterpretedAsInteger($parameter['showBook'])) {
                    $content = $this->getUebernommenePatenschaft($parameter['showBook']);
                } else {
                    $content = $this->uebernommenePatenschaftListView();
                }
                break;
        }

        return $this->pi_wrapInBaseClass($content);
    }

    /**
     * Initialize table names
     */
    private function setTableNames()
    {
        $this->buchtabelle = 'tx_patenschaften_buecher';
        $this->kattabelle = 'tx_patenschaften_kategorien';
    }

    /**
     * Display a single item from the database
     *
     * @param int $id UID eines Datensatzes
     * @return string HTML of a single database entry
     */
    function singleView($id)
    {

        $template = $this->cObj->getSubpart($this->templateFile, '###BUCHANSICHT###');

        $res = $this->db->exec_SELECTquery(
            '*',
            $this->buchtabelle,
            'deleted=0 AND hidden=0 AND uid=' . $id,
            '',
            '',
            ''
        );
        $content = '';
        while ($row = $this->db->sql_fetch_assoc($res)) {
            $urlParameters = [
                'tx_powermail_pi1[uid' . $this->conf['selectID'] . ']' => $row['uid']
            ];
            $markerArray['###CATEGORY###'] = $this->getKategorie($row['category']);
            $markerArray['###TITEL###'] = $row['titel'];
            $markerArray['###AUTHOR###'] = ($row['author'] != "") ? $row['author'] . ':<br />' : NULL;
            $markerArray['###CAPTION###'] = nl2br($row['caption']);
            $markerArray['###HEADER_SIGNATURE###'] = $this->pi_getLL('listFieldHeader_signature');
            $markerArray['###SIGNATURE###'] = '<a href="https://opac.sub.uni-goettingen.de/DB=1/SET=2/TTL=30/CMD?ACT=SRCHA&IKT=1016&SRT=YOP&TRM=SGN+' . preg_replace('/[\W]+/',
                    '+', $row['signature']) . '">' . $row['signature'] . '</a>';
            $markerArray['###DESCRIPTION###'] = nl2br($this->pi_RTEcssText($row['description']));
            $markerArray['###HEADER_RESTAURIERUNGSKOSTEN###'] = $this->pi_getLL('restaurierungskosten');
            $markerArray['###PRICE###'] = $row['price'];
            $markerArray['###HEADER_ZUSTANDSBESCHREIBUNG###'] = $this->pi_getLL('zustandsbeschreibung');
            $markerArray['###DAMAGE###'] = $row['damage'];
            $markerArray['###HEADER_HELP###'] = $this->pi_getLL('listFieldHeader_help');
            $markerArray['###HELP###'] = $row['help'];
            $markerArray['###BACK###'] = $this->pi_linkToPage(
                $this->pi_getLL('back', 'Back'),
                $this->conf['newListID']
            );
            $markerArray['###IWANT###'] = $this->pi_linkToPage(
                $this->pi_getLL('iwant'),
                $this->conf['formPage'],
                '',
                $urlParameters
            );

            $bilder = explode(',', $row['bilder']);

            // enhance images
            $i = 1;
            foreach ($bilder as $bild) {
                $bildconf = [
                    'file' => $this->bilderpfad . $bild,
                    'file.' => [
                        'maxW' => $this->bilderbreite,
                        'height' => $this->bilderhoehe,
                    ],
                ];
                $bildconf['stdWrap.']['addParams.']['alt'] = 'Bild ' . $i . ' zum Titel ' . $row['titel'];
                $bildconf['stdWrap.']['addParams.']['class'] = "picture" . " " . "nr" . ($i % 2);

                $wrap = "<a href='" . $this->bilderpfad . $bild . "' rel='shadowbox[preview]' title='" . $row['titel'] . "'>|</a>";
                $markerArray['###IMG' . $i . '###'] = $this->cObj->linkWrap($this->cObj->cObjGetSingle('IMAGE', $bildconf), $wrap);
                $i++;
            }
            // if there are 2 images
            (count($bilder) == 2) ? $markerArray['###IMG3###'] = '' : null;
            $content = $this->cObj->substituteMarkerArrayCached($template, $markerArray);
        }
        return $content;
    }

    /**
     * Return categories as string
     *
     * @param string $kat
     * @return string Kategorien
     */
    private function getKategorie($kat)
    {
        $kategorien = '';
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
    private function leseKatAusDb($id)
    {
        $res = $this->db->exec_SELECTquery(
            'catname',
            $this->kattabelle,
            ' deleted=0 AND hidden = 0 AND uid =' . $id,
            '',
            'catname ASC',
            ''
        );
        $cat = '';
        while ($row = $this->db->sql_fetch_assoc($res)) {
            $cat .= $row['catname'];
        }
        return $cat;
    }

    /**
     * Generiert die Kategorienansicht
     *
     * @param int
     * @return string
     */
    private function catView($category)
    {
        $match = false;

        // List all categories
        $kategorien = $this->getAllKategorien();

        // Chose template subpart
        $template = $this->cObj->getSubpart($this->templateFile, '###KATLIST###');

        $markerArray['###UEBERSICHT###'] = '';
        $inhalt = '';
        foreach ($kategorien as $kategorie) {
            // Marker mit Inhalten fuellen
            if ($kategorie['uid'] == $category && $kategorie['uid'] != $this->uebernommeneId) {
                $match = true;
                $markerArray['###KATNAME###'] = $this->cObj->wrap($kategorie['catname'],
                    '<a id="buchkat-' . $kategorie['uid'] . '"></a><h2 class="category">|</h2>');
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
     * Get all available categories
     *
     * @return array
     */
    protected function getAllKategorien()
    {

        $this->db = $GLOBALS['TYPO3_DB'];
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
        $kategorien = [];
        $res = $this->db->exec_SELECTquery(
            'uid, catname ',
            $this->kattabelle,
            ' deleted=0 AND hidden=0',
            '',
            'uid ASC',
            ''
        );
        while ($row = $this->db->sql_fetch_assoc($res)) {
            $kategorien[] = $row;
        }
        return $kategorien;
    }

    /**
     * Gibt alle Buecher aus einer Kategorie aus
     *
     * @param int $catId
     * @return string
     */
    protected function getAllBuecherFromCat($catId)
    {

        // Subpart des Templates waehlen
        $template = $this->cObj->getSubpart($this->templateFile, '###BUCHUEBERBLICK###');

        // DB Abfrage nach Buechern der Kategorie
        if ($catId != $this->uebernommeneId) {
            $res = $this->db->exec_SELECTquery(
                '*',
                $this->buchtabelle,
                ' FIND_IN_SET(' . $catId . ',category) AND deleted=0 AND hidden=0 AND sponsorship="" ',
                '',
                ' `search` ASC , `author` ASC',
                ''
            );
        } else {
            $res = $this->db->exec_SELECTquery(
                '*',
                $this->buchtabelle,
                ' deleted=0 AND hidden=0 AND sponsorship != "" ',
                '',
                ' `search` ASC , `author` ASC',
                ''
            );
        }
        $inhalt = '';
        while ($row = $this->db->sql_fetch_assoc($res)) {

            // Parameter fuer den Link
            $urlParameters = [
                'tx_patenschaften_pi1[showBook]' => $row['uid'],
            ];

            // Marker ersetzen
            $markerArray['###AUTHOR###'] = ($row['author'] != "") ? $row['author'] . "<br />" : "";
            $markerArray['###TITELLINK###'] = $this->pi_linkTP($row['titel'], $urlParameters, 1);
            $markerArray['###KURZBESCHREIBUNG###'] = nl2br($row['caption']);
            $markerArray['###RESTAURIERUNGSKOSTEN###'] = $this->pi_getLL('restaurierungskosten');
            $markerArray['###PREIS###'] = $row['price'];
            if (!empty($row['bilder'])) {
                $bilder = explode(',', $row['bilder']);
                $bild = $bilder[0];
                $bildconf = [
                    'file' => $this->bilderpfad . $bild,
                    'file.' => [
                        'maxW' => $this->catBilderbreite,
                        'height' => $this->catBilderhoehe,
                    ],
                ];
                $bildconf['stdWrap.']['addParams.']['alt'] = 'Bild zum Titel ' . $row['titel'];
                $bildconf['stdWrap.']['addParams.']['class'] = "picture" . " " . "nr0";

                $wrap = "<a href='" . $this->bilderpfad . $bild . "' rel='shadowbox' title='" . $row['titel'] . "'>|</a>";
                $markerArray['###IMG###'] = $this->cObj->linkWrap($this->cObj->cObjGetSingle('IMAGE', $bildconf), $wrap);
            } else {
                $markerArray['###IMG###'] = '';
            }

            $inhalt .= $this->cObj->substituteMarkerArrayCached($template, $markerArray);
        }
        return $inhalt;
    }

    /**
     * Generiert die Kategorienansicht
     *
     * @return string
     */
    private function catListView()
    {
        // Liste alle Kategorien
        $kategorien = $this->getAllKategorien();

        // Subpart des Templates waehlen
        $template = $this->cObj->getSubpart($this->templateFile, '###KATLIST###');

        $markerArray['###UEBERSICHT###'] = '';

        $numberOfCategories = count($kategorien) - 1; // abzuegl. uebernommene

        $inhalt = '';

        foreach ($kategorien as $kategorie) {
            // Marker mit Inhalten fuellen
            if ($kategorie['uid'] != $this->uebernommeneId) {
                $numberOfCategories--;
                $markerArray['###KATNAME###'] = $this->cObj->wrap($kategorie['catname'],
                    '<a id="buchkat-' . $kategorie['uid'] . '"></a><h2 class="category">|</h2>');
                $markerArray['###BUECHER###'] = $this->getAllBuecherFromCat($kategorie['uid']);
                $markerArray['###SPACER###'] = "<br />";
                $inhalt .= $this->cObj->substituteMarkerArrayCached($template, $markerArray);
            }
        }

        $daten = $inhalt;

        return $daten;
    }

    /**
     * Verfuegbare Buecher
     *
     * @return string
     */
    private function getAvailableBookSelection()
    {
        $res = $this->db->exec_SELECTquery(
            'uid, titel',
            $this->buchtabelle,
            ' deleted=0 AND hidden=0 AND sponsorship = "" ',
            '',
            ' search ASC , author ASC',
            ''
        );
        $selection = "<label>" . $this->pi_getLL('formFieldHeader_sponsorship') . ":</label>" . PHP_EOL;
        $selection .= "<select>" . PHP_EOL;
        while ($row = $this->db->sql_fetch_assoc($res)) {
            $selection .= '<option title="' . $row['titel'] . '">' . $row['titel'] . "</option>" . PHP_EOL;
        }
        $selection .= "</select>";
        return $selection;
    }

    /**
     * Ausgabe der Namen aller Patenschaften
     *
     * @return string Patenschaften als Liste
     */
    private function getPatenschaften()
    {

        $patenschaftenconf = $this->conf['patenschaften.'];
        $singleWrap = $patenschaftenconf['singleWrap'];
        $listWrap = $patenschaftenconf['listWrap'];

        // Query fuer alle Paten
        $res = $this->db->exec_SELECTquery(
            'DISTINCT(sponsorship)',
            $this->buchtabelle,
            ' sponsorship !="" AND sponsorship NOT LIKE "%genannt%"',
            '',
            'sponsorship ASC',
            ''
        );
        $sponsoren = '';
        while ($row = $this->db->sql_fetch_assoc($res)) {
            $sponsoren .= $this->cObj->wrap(implode(" ", explode(";", $row['sponsorship'])), $singleWrap);
        }
        // Wrappen das ganze noch mit dem was im TS festgelegt ist
        $sponsoren = $this->cObj->wrap($sponsoren, $listWrap);
        return $sponsoren;
    }

    /**
     * Ausgabe der uebernommenen Patenschaften
     *
     * @param int $id
     *
     * @return string
     */
    private function getUebernommenePatenschaft($id)
    {
        $content = '';
        $template = $this->cObj->getSubpart($this->templateFile, '###UEBERNOMMENE###');

        $res = $this->db->exec_SELECTquery(
            '*',
            $this->buchtabelle,
            ' deleted=0 AND hidden=0 AND uid=' . $id,
            '',
            '',
            ''
        );
        if ($row = $this->db->sql_fetch_assoc($res)) {
            $urlParameters = [
                'tx_powermail_pi1[uid' . $this->conf['selectID'] . ']' => $row['uid']
            ];
            $markerArray['###CATEGORY###'] = $this->getKategorie($row['category']);
            $markerArray['###TITEL###'] = $row['titel'];
            $markerArray['###AUTHOR###'] = ($row['author'] != "") ? $row['author'] . ":<br />" : NULL;
            $markerArray['###CAPTION###'] = $row['caption'];
            $markerArray['###HEADER_SIGNATURE###'] = $this->pi_getLL('listFieldHeader_signature');
            $markerArray['###SIGNATURE###'] = '<a href="https://opac.sub.uni-goettingen.de/DB=1/SET=2/TTL=30/CMD?ACT=SRCHA&IKT=1016&SRT=YOP&TRM=SGN+' . preg_replace('/[\W]+/',
                    '+', $row['signature']) . '">' . $row['signature'] . '</a>';
            $markerArray['###DESCRIPTION###'] = $this->pi_RTEcssText($row['description']);
            $markerArray['###HEADER_RESTAURIERUNGSKOSTEN###'] = $this->pi_getLL('restaurierungskosten');
            $markerArray['###PRICE###'] = $row['price'];
            $markerArray['###HEADER_ZUSTANDSBESCHREIBUNG###'] = $this->pi_getLL('zustandsbeschreibung');
            $markerArray['###DAMAGE###'] = $row['damage'];
            $markerArray['###HEADER_HELP###'] = $this->pi_getLL('listFieldHeader_help');
            $markerArray['###HELP###'] = $row['help'];
            $markerArray['###BACK###'] = $this->pi_linkToPage(
                $this->pi_getLL('back', 'Back'),
                $this->conf['takenListID']
            );
            $markerArray['###HEADER_SPONSOR###'] = $this->pi_getLL('listFieldHeader_sponsorship');
            $markerArray['###SPONSOR###'] = implode(' ', explode(';', $row['sponsorship']));

            $bilder = explode(',', $row['bilder']);

            // Bilder aufbereiten
            $i = 1;
            foreach ($bilder as $bild) {
                $bildconf = [
                    'file' => $this->bilderpfad . $bild,
                    'file.' => [
                        'maxW' => $this->bilderbreite,
                        'height' => $this->bilderhoehe,
                    ],
                ];
                $bildconf['stdWrap.']['addParams.']['alt'] = 'Bild ' . $i . ' zum Titel ' . $row['titel'];
                $bildconf['stdWrap.']['addParams.']['class'] = "picture" . " " . "nr" . ($i % 2);

                $wrap = "<a href='" . $this->bilderpfad . $bild . "' rel='shadowbox[preview]' title='" . $row['titel'] . "'>|</a>";
                $markerArray['###IMG' . $i . '###'] = $this->cObj->linkWrap($this->cObj->cObjGetSingle('IMAGE', $bildconf), $wrap);
                $i++;
            }
            // falls mal nur zwei Bilder vorhanden sind
            (count($bilder) == 2) ? $markerArray['###IMG3###'] = '' : NULL;
            $content = $this->cObj->substituteMarkerArrayCached($template, $markerArray);
        }
        return $content;
    }

    /**
     * Ausgabe der uebernommenen Patenschaften
     *
     * @return string
     */
    private function uebernommenePatenschaftListView()
    {
        // Liste alle Kategorien
        $kategorien = $this->getAllKategorien();

        // Subpart des Templates waehlen
        $template = $this->cObj->getSubpart($this->templateFile, '###KATLIST###');

        $markerArray['###UEBERSICHT###'] = '';

        $inhalt = '';

        foreach ($kategorien as $kategorie) {
            // Marker mit Inhalten fuellen
            if ($kategorie['uid'] == $this->uebernommeneId) {
                $markerArray['###KATNAME###'] = $this->cObj->wrap($kategorie['catname'],
                    '<a id="buchkat-' . $kategorie['uid'] . '"></a><h2 class="category">|</h2>');
                $markerArray['###BUECHER###'] = $this->getAllBuecherFromCat($kategorie['uid']);
                $markerArray['###SPACER###'] = '';

                $inhalt .= $this->cObj->substituteMarkerArrayCached($template, $markerArray);
            }
        }

        $daten = $inhalt;

        return $daten;
    }

    /**
     * Generiert die Kategorienansicht für Hook in nkwsubmenu_pi2 (infobox)
     * jedoch wenn man sich auf der Seite ... befindet
     *
     * @param string &$tmp
     * @param object &$obj
     * @return string
     */
    public function hookFunc(&$tmp, &$obj)
    {
        // Liste aller Kategorien
        $object = new tx_patenschaften_pi1();
        $this->db = $GLOBALS['TYPO3_DB'];
        $object->setTableNames();
        $object->pi_loadLL();
        /** @var \TYPO3\CMS\Core\TypoScript\Parser\TypoScriptParser $tsParser */
        $tsParser = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(TYPO3\CMS\Core\TypoScript\Parser\TypoScriptParser::class);
        foreach ($GLOBALS['TSFE']->tmpl->constants as $value) {
            $tsParser->parse($value, $matchObj = '');
        }

        $object->pageID = [
            $tsParser->setup['plugin.']['tx_patenschaften.']['newListID'],
            $tsParser->setup['plugin.']['tx_patenschaften.']['takenListID']
        ];

        $kategorien = $object->getAllKategorien();
        $parameter = \TYPO3\CMS\Core\Utility\GeneralUtility::_GP('tx_patenschaften_pi1');

        if (in_array($GLOBALS['TSFE']->id, $object->pageID)) {
            if (!isset($parameter['showBook'])) {
                if ($GLOBALS['TSFE']->id == $object->pageID[0] || $GLOBALS['TSFE']->id == $object->pageID[1]) {
                    $tmp = '';
                    foreach ($kategorien as $kategorie) {
                        if ($kategorie['uid'] != $object->uebernommeneId) {
                            $tmp .= '<li>';
                            // anchor
                            $uebersicht = $obj->cObj->getTypoLink_URL($GLOBALS['TSFE']->id) . '?tx_patenschaften_pi1[category]=' . $kategorie['uid'];
                            $tmp .= $obj->cObj->getTypoLink($kategorie['catname'], $uebersicht);
                            $tmp .= '</li>';
                        }
                    }
                }
            } else {
                $i = 0;
                $bookID = $parameter['showBook'];
                if ($GLOBALS['TSFE']->id == $object->pageID[1]) {
                    $where = "`sponsorship` != '' AND ";
                } else {
                    $where = "`sponsorship` = '' AND ";
                }
                $res = $this->db->exec_SELECTquery(
                    '*',
                    $object->buchtabelle,
                    $where . "`deleted` = 0 AND `hidden` = 0",
                    '',
                    '`search` ASC , `author` ASC',
                    ''
                );
                while ($row = $this->db->sql_fetch_assoc($res)) {
                    if ($row['uid'] == $bookID) {
                        $id = $i;
                    }
                    $books[$i++] = $row;
                }

                $tmp = '<li>';
                $tmp .= $obj->pi_linkTP($object->pi_getLL('infobox_previousbook'),
                    ['tx_patenschaften_pi1[showBook]' => $books[$id - 1]['uid']], 1);
                $tmp .= '</li>' . PHP_EOL . '<li>';
                $tmp .= $obj->pi_linkTP($object->pi_getLL('infobox_nextbook'),
                    ['tx_patenschaften_pi1[showBook]' => $books[$id + 1]['uid']], 1);
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
    public function hookPicFunc(&$tmp, &$obj)
    {
        $object = new \tx_patenschaften_pi1();
        /** @var \TYPO3\CMS\Core\TypoScript\Parser\TypoScriptParser $tsParser */
        $tsParser = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\TypoScript\Parser\TypoScriptParser::class);
        foreach ($GLOBALS['TSFE']->tmpl->constants as $value) {
            $tsParser->parse($value, $matchObj = '');
        }
        $object->pageID = [
            $tsParser->setup['plugin.']['tx_patenschaften.']['newListID'],
            $tsParser->setup['plugin.']['tx_patenschaften.']['takenListID']
        ];
    }

    protected function setImageSizes()
    {
        $this->conf['imageWidth'] ? $this->bilderbreite = $this->conf['imageWidth'] : $this->bilderbreite = 180;
        $this->conf['imageHeight'] ? $this->bilderhoehe = $this->conf['imageHeight'] : $this->bilderhoehe = 180;
        $this->conf['catImageWidth'] ? $this->catBilderbreite = $this->conf['catImageWidth'] : $this->catBilderbreite = 60;
        $this->conf['catImageHeight'] ? $this->catBilderhoehe = $this->conf['catImageHeight'] : $this->catBilderhoehe = 60;
    }

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/patenschaften/pi1/class.tx_patenschaften_pi1.php']) {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/patenschaften/pi1/class.tx_patenschaften_pi1.php']);
}
