=Buchpatenschaften=

Anzeige von Buchpatenschaften und Kategorien im Frontend.

==Installation==
* Bilddateien inklusive Unterverzeichnisse in /fileadmin/media/bilder/patenschaften/ kopieren
* Erstellen der Seiten.
* Installation patschaften Extension
* Hinzufuegen des FrontendPlugins auf die entsprechenden Seiten und dabei Auswahl der gewuenschten Funktion im Flexform
* Anlage SysFolder fuer die Patenschaften und Eingabe der ID in plugin.tx_patenschaften_pi1.pidList = 1184
* Anlage der Formularseite und im TypoScript plugin.tx_patenschaften_pi1.pidList.formPage = 1191 (z.B.) setzen
* Im TypoScript den devMode auf 1 setzen. Damit werden die vorhandenen Daten migriert.

==TODO==
* Autoren alphabetisch auflisten
** Extra Autorenfeld
* Hochkant und Querformatbilder in gleicher Größe 
* Head nur einmal in der Buecherliste

==TYPOSCRIPT==

#Angenommen die UID des Powermailfeldes ist {$selectID}:

lib.uebergabe = TEXT
lib.uebergabe.data = GPvar:tx_powermail_pi1|uid{$selectID}
lib.uebergabe.intval = 1
lib.uebergabe.required = 1
lib.uebergabe.wrap = uid = |

# lib.uebergabeNo = TEXT
# lib.uebergabeNo.data = GPvar:tx_powermail_pi1|uid{$selectID}
# lib.uebergabeNo.intval = 1
# lib.uebergabeNo.required = 1
# lib.uebergabeNo.wrap = uid != |

#alle buecher aus der DB auslesen
lib.buecher = COA_INT
lib.buecher {
  10 = TEXT
  10.value = <div class="tx_powermail_pi1_fieldwrap_html tx_powermail_pi1_fieldwrap_html_text tx_powermail_pi1_fieldwrap_html_{$selectID} even" id="powermaildiv_uid{$selectID}"><label for="uid{$selectID}">Ihre Patenschaft:</label>

  15 = COA
  15.wrap = <select id="uid{$selectID}" class="powermail_ihrebuchpatenschaft powermail_text powermail_uid{$selectID}" tabindex="9" name="tx_powermail_pi1[uid{$selectID}]" size="1">|</select></div>

  15{
    5 = TEXT
    5.data < lib.uebergabe
    10 = CONTENT
    10{
      table= tx_patenschaften_buecher
      select{
        pidInList = {$pidInList}
        where = sponsorship = ""
        andWhere  < lib.uebergabe
        max = 1

      }
      renderObj = COA
      renderObj {
        10 = COA
        10 {
          10 = TEXT
          10 {
            field = titel
            wrap = <option selected="selected" value="|">
            stdWrap.htmlSpecialChars = 1
          }
          20 = TEXT
          20 {
            field = titel
            wrap = |</option>
          }
        }
      }
    }
    20 = CONTENT
    20 {
      table = tx_patenschaften_buecher
      select {
        pidInList = {$pidInList}
        orderBy = titel
        where = sponsorship = ""
       }
      renderObj = COA
      renderObj {
        10 = COA
        10 {
          10 = TEXT
          10 {
            field = titel
            wrap = <option value="|">
            stdWrap.htmlSpecialChars = 1
          }
          20 = TEXT
          20 {
            field = titel
            wrap = |</option>
          }
        }
      }
    }
  }
}