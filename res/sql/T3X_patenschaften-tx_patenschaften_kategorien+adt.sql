# TYPO3 Extension Manager dump 1.1
#
# Host: localhost    Database: typo3_43_w3d
#--------------------------------------------------------


#
# Table structure for table "tx_patenschaften_kategorien"
#
DROP TABLE IF EXISTS tx_patenschaften_kategorien;
CREATE TABLE tx_patenschaften_kategorien (
  uid int(11) NOT NULL auto_increment,
  pid int(11) NOT NULL default '0',
  tstamp int(11) NOT NULL default '0',
  crdate int(11) NOT NULL default '0',
  cruser_id int(11) NOT NULL default '0',
  sys_language_uid int(11) NOT NULL default '0',
  l10n_parent int(11) NOT NULL default '0',
  l10n_diffsource mediumtext,
  deleted tinyint(4) NOT NULL default '0',
  hidden tinyint(4) NOT NULL default '0',
  catname tinytext,
  PRIMARY KEY (uid),
  KEY parent (pid)
);

# 0 => ohne
# 1 => "Schöne Literatur",
# 2 => "Philosophie und Theologie",
# 3 => "Mathematik und Naturwissenschaften",
# 4 => "Künste und Handwerke",
# 5 => "Bereits übernommene Patenschaften"
# 6 => "Geschichte",
# 7 => "Geographie und Reisebeschreibungen",
# 8 => "Naturwissenschaften und Medizin"

INSERT INTO `tx_patenschaften_kategorien` (`uid`, `pid`, `tstamp`, `crdate`, `cruser_id`, `sys_language_uid`, `l10n_parent`, `l10n_diffsource`, `deleted`, `hidden`, `catname`) VALUES
(1, 1530, 0, 0, 0, 0, 0, NULL, 0, 0, 'Schöne Literatur'),
(2, 1530, 0, 0, 0, 0, 0, NULL, 0, 0, 'Philosophie und Theologie'),
(3, 1530, 0, 0, 0, 0, 0, NULL, 0, 0, 'Mathematik und Naturwissenschaften'),
(4, 1530, 0, 0, 0, 0, 0, NULL, 0, 0, 'Künste und Handwerke'),
(5, 1530, 0, 0, 0, 0, 0, NULL, 0, 0, 'Bereits übernommene Patenschaften'),
(6, 1530, 0, 0, 0, 0, 0, NULL, 0, 0, 'Geschichte'),
(7, 1530, 0, 0, 0, 0, 0, NULL, 0, 0, 'Geographie und Reisebeschreibungen'),
(8, 1530, 0, 0, 0, 0, 0, NULL, 0, 0, 'Naturwissenschaften und Medizin');