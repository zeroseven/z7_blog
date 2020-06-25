CREATE TABLE pages (
	post_top tinyint(1) unsigned DEFAULT '0' NOT NULL,
	post_archive int(11) DEFAULT '0' NOT NULL,
	post_date int(11) DEFAULT '0' NOT NULL,
	post_author int(11) unsigned DEFAULT '0',
	post_topics int(11) unsigned DEFAULT '0' NOT NULL,
	post_tags varchar(255) DEFAULT '' NOT NULL,
	post_relations_to int(11) unsigned DEFAULT '0' NOT NULL,
	post_relations_from int(11) unsigned DEFAULT '0' NOT NULL,
	post_redirect_category tinyint(1) unsigned DEFAULT '0' NOT NULL
);

CREATE TABLE tx_z7blog_domain_model_author (

	uid int(11) NOT NULL auto_increment,

	firstname varchar(255) DEFAULT '' NOT NULL,
	lastname varchar(255) DEFAULT '' NOT NULL,
	expertise varchar(255) DEFAULT '' NOT NULL,
	email varchar(255) DEFAULT '' NOT NULL,
	image int(11) unsigned NOT NULL default '0',
	description text NOT NULL,
	page varchar(255) DEFAULT '' NOT NULL,
    twitter varchar(255) DEFAULT '' NOT NULL,
    linkedin varchar(255) DEFAULT '' NOT NULL,
    xing varchar(255) DEFAULT '' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid),
	KEY language (l10n_parent,sys_language_uid)
);

CREATE TABLE tx_z7blog_domain_model_topic (

	uid int(11) NOT NULL auto_increment,
	title varchar(255) DEFAULT '' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid),
	KEY language (l10n_parent,sys_language_uid)
);


CREATE TABLE tx_z7blog_post_mm (
	uid_local int(11) unsigned DEFAULT '0' NOT NULL,
	uid_foreign int(11) unsigned DEFAULT '0' NOT NULL,
	sorting int(11) unsigned DEFAULT '0' NOT NULL,
	sorting_foreign int(11) unsigned DEFAULT '0' NOT NULL,

    PRIMARY KEY (uid_local,uid_foreign),
	KEY uid_local (uid_local),
	KEY uid_foreign (uid_foreign)
);

CREATE TABLE tx_z7blog_post_topic_mm (
	uid_local int(11) unsigned DEFAULT '0' NOT NULL,
	uid_foreign int(11) unsigned DEFAULT '0' NOT NULL,
	sorting int(11) unsigned DEFAULT '0' NOT NULL,
	sorting_foreign int(11) unsigned DEFAULT '0' NOT NULL,

    PRIMARY KEY (uid_local,uid_foreign),
	KEY uid_local (uid_local),
	KEY uid_foreign (uid_foreign)
);
