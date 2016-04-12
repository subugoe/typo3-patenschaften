#
# Table structure for table 'tx_patenschaften_kategorien'
#
CREATE TABLE tx_patenschaften_kategorien (
		uid int(11) NOT NULL auto_increment,
		pid int(11) DEFAULT '0' NOT NULL,
		tstamp int(11) DEFAULT '0' NOT NULL,
		crdate int(11) DEFAULT '0' NOT NULL,
		cruser_id int(11) DEFAULT '0' NOT NULL,
		sys_language_uid int(11) DEFAULT '0' NOT NULL,
		l10n_parent int(11) DEFAULT '0' NOT NULL,
		l10n_diffsource mediumtext,
		deleted tinyint(4) DEFAULT '0' NOT NULL,
		hidden tinyint(4) DEFAULT '0' NOT NULL,
		catname tinytext,
		t3ver_oid int(11) DEFAULT '0' NOT NULL,
		t3ver_id int(11) DEFAULT '0' NOT NULL,
		t3ver_wsid int(11) DEFAULT '0' NOT NULL,
		t3ver_label varchar(30) DEFAULT '' NOT NULL,
		t3ver_state tinyint(4) DEFAULT '0' NOT NULL,
		t3ver_stage tinyint(4) DEFAULT '0' NOT NULL,
		t3ver_count int(11) DEFAULT '0' NOT NULL,
		t3ver_tstamp int(11) DEFAULT '0' NOT NULL,
		t3_origuid int(11) DEFAULT '0' NOT NULL,

		PRIMARY KEY (uid),
		KEY parent (pid)
);


#
# Table structure for table 'tx_patenschaften_buecher'
#
CREATE TABLE tx_patenschaften_buecher (
		uid int(11) NOT NULL auto_increment,
		pid int(11) DEFAULT '0' NOT NULL,
		tstamp int(11) DEFAULT '0' NOT NULL,
		crdate int(11) DEFAULT '0' NOT NULL,
		cruser_id int(11) DEFAULT '0' NOT NULL,
		sys_language_uid int(11) DEFAULT '0' NOT NULL,
		l10n_parent int(11) DEFAULT '0' NOT NULL,
		l10n_diffsource mediumtext,
		deleted tinyint(4) DEFAULT '0' NOT NULL,
		hidden tinyint(4) DEFAULT '0' NOT NULL,
		starttime int(11) DEFAULT '0' NOT NULL,
		endtime int(11) DEFAULT '0' NOT NULL,
		titel varchar(255) DEFAULT '' NOT NULL,
		author tinytext,
		search text,
		caption text,
		signature tinytext,
		description text,
		price tinytext,
		damage text,
		help text,
		sponsorship tinytext,
		category text,
		bilder text,
		t3ver_oid int(11) DEFAULT '0' NOT NULL,
		t3ver_id int(11) DEFAULT '0' NOT NULL,
		t3ver_wsid int(11) DEFAULT '0' NOT NULL,
		t3ver_label varchar(30) DEFAULT '' NOT NULL,
		t3ver_state tinyint(4) DEFAULT '0' NOT NULL,
		t3ver_stage tinyint(4) DEFAULT '0' NOT NULL,
		t3ver_count int(11) DEFAULT '0' NOT NULL,
		t3ver_tstamp int(11) DEFAULT '0' NOT NULL,
		t3_origuid int(11) DEFAULT '0' NOT NULL,

		PRIMARY KEY (uid),
		KEY parent (pid)
);