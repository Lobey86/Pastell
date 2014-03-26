CREATE TABLE action_auto_log (
	`id_e` int(11) NOT NULL,
	`id_d` varchar(16) NOT NULL,
	`etat_source` varchar(64) NOT NULL,
	`etat_cible` varchar(64) NOT NULL,
	`first_try` datetime NOT NULL,
	`last_try` datetime NOT NULL,
	`nb_try` int(11) NOT NULL,
	`last_message` varchar(255) NOT NULL,
	UNIQUE KEY id_e (`id_e`,`id_d`,`first_try`)
)  ENGINE=MyISAM  ;
CREATE TABLE agent (
	`id_a` int(11) NOT NULL AUTO_INCREMENT,
	`matricule` varchar(64) NOT NULL,
	`titre` varchar(16) NOT NULL,
	`nom_usage` varchar(128) NOT NULL,
	`nom_patronymique` varchar(128) NOT NULL,
	`prenom` varchar(128) NOT NULL,
	`emploi_grade_code` varchar(16) NOT NULL,
	`emploi_grade_libelle` varchar(128) NOT NULL,
	`collectivite_code` varchar(16) NOT NULL,
	`collectivite_libelle` varchar(128) NOT NULL,
	`siren` varchar(16) NOT NULL,
	`type_dossier_code` varchar(16) NOT NULL,
	`type_dossier_libelle` varchar(128) NOT NULL,
	`train_traitement_code` varchar(16) NOT NULL,
	`train_traitement_libelle` varchar(128) NOT NULL,
	PRIMARY KEY (`id_a`),
	UNIQUE KEY siren (`siren`,`matricule`,`emploi_grade_code`),
	KEY siren_2 (`siren`,`nom_patronymique`,`prenom`,`id_a`)
)  ENGINE=MyISAM  ;
CREATE TABLE annuaire (
	`id_a` int(11) NOT NULL AUTO_INCREMENT,
	`description` varchar(64) NOT NULL,
	`email` varchar(64) NOT NULL,
	`id_e` int(11) NOT NULL,
	PRIMARY KEY (`id_a`)
)  ENGINE=MyISAM  ;
CREATE TABLE annuaire_groupe (
	`id_g` int(11) NOT NULL AUTO_INCREMENT,
	`id_e` int(11) NOT NULL,
	`nom` varchar(32) NOT NULL,
	`partage` tinyint(1) NOT NULL,
	PRIMARY KEY (`id_g`)
)  ENGINE=MyISAM  ;
CREATE TABLE annuaire_groupe_contact (
	`id_a` int(11) NOT NULL,
	`id_g` int(11) NOT NULL
)  ENGINE=MyISAM  ;
CREATE TABLE annuaire_role (
	`id_r` int(11) NOT NULL AUTO_INCREMENT,
	`nom` varchar(64) NOT NULL,
	`id_e_owner` int(11) NOT NULL,
	`id_e` int(11) NOT NULL,
	`role` varchar(32) NOT NULL,
	`partage` tinyint(1) NOT NULL,
	PRIMARY KEY (`id_r`)
)  ENGINE=MyISAM  ;
CREATE TABLE connecteur_entite (
	`id_ce` int(11) NOT NULL AUTO_INCREMENT,
	`id_e` int(11) NOT NULL,
	`libelle` varchar(32) NOT NULL,
	`id_connecteur` varchar(32) NOT NULL,
	`type` varchar(32) NOT NULL,
	PRIMARY KEY (`id_ce`)
)  ENGINE=MyISAM  ;
CREATE TABLE document (
	`id_d` varchar(32) NOT NULL,
	`type` varchar(32) NOT NULL,
	`titre` varchar(256) NOT NULL,
	`creation` datetime NOT NULL,
	`modification` datetime NOT NULL,
	PRIMARY KEY (`id_d`),
	FULLTEXT KEY titre (`titre`)
)  ENGINE=MyISAM  ;
CREATE TABLE document_action (
	`id_a` int(11) NOT NULL AUTO_INCREMENT,
	`id_d` varchar(16) NOT NULL,
	`action` varchar(64) NOT NULL,
	`date` datetime NOT NULL,
	`id_e` int(11) NOT NULL,
	`id_u` int(11) NOT NULL,
	PRIMARY KEY (`id_a`)
)  ENGINE=MyISAM  ;
CREATE TABLE document_action_entite (
	`id_a` int(11) NOT NULL,
	`id_e` int(11) NOT NULL,
	`id_j` int(11) NOT NULL,
	KEY id_a (`id_a`,`id_e`,`id_j`)
)  ENGINE=MyISAM  ;
CREATE TABLE document_email (
	`key` varchar(32) NOT NULL,
	`id_d` varchar(32) NOT NULL,
	`email` varchar(256) NOT NULL,
	`lu` tinyint(1) NOT NULL,
	`date_envoie` datetime NOT NULL,
	`date_lecture` datetime NOT NULL,
	`type_destinataire` varchar(4) NOT NULL,
	PRIMARY KEY (`key`)
)  ENGINE=MyISAM  ;
CREATE TABLE document_entite (
	`id_d` varchar(8) NOT NULL,
	`id_e` int(11) NOT NULL,
	`role` varchar(16) NOT NULL,
	`last_action` varchar(64) NOT NULL,
	`last_action_date` datetime NOT NULL,
	KEY id_e (`id_e`,`id_d`)
)  ENGINE=MyISAM  ;
CREATE TABLE droit (
	`id_u` int(11) NOT NULL,
	`droit` varchar(16) NOT NULL,
	`type_objet` varchar(16) NOT NULL,
	`id_o` varchar(16) NOT NULL
)  ENGINE=MyISAM  ;
CREATE TABLE entite (
	`id_e` int(11) NOT NULL AUTO_INCREMENT,
	`type` varchar(32) NOT NULL,
	`denomination` varchar(128) NOT NULL,
	`siren` char(9) NOT NULL,
	`date_inscription` datetime NOT NULL,
	`etat` int(11) NOT NULL,
	`entite_mere` varchar(9),
	`centre_de_gestion` int(11) NOT NULL,
	PRIMARY KEY (`id_e`),
	KEY entite_mere (`entite_mere`,`type`,`id_e`),
	KEY denomination_2 (`denomination`),
	FULLTEXT KEY denomination (`denomination`)
)  ENGINE=MyISAM  ;
CREATE TABLE entite_ancetre (
	`id_e_ancetre` int(11) NOT NULL,
	`id_e` int(11) NOT NULL,
	`niveau` int(11) NOT NULL,
	PRIMARY KEY (`id_e`,`id_e_ancetre`),
	KEY id_e_ancetre (`id_e_ancetre`,`id_e`)
)  ENGINE=MyISAM  ;
CREATE TABLE entite_properties (
	`id_e` int(11) NOT NULL,
	`flux` varchar(16) NOT NULL,
	`properties` varchar(32) NOT NULL,
	`values` varchar(32) NOT NULL
)  ENGINE=MyISAM  ;
CREATE TABLE extension (
	`id_e` int(11) NOT NULL AUTO_INCREMENT,
	`nom` varchar(128) NOT NULL,
	`path` text NOT NULL,
	PRIMARY KEY (`id_e`)
)  ENGINE=MyISAM  ;
CREATE TABLE flux_entite (
	`id_fe` int(11) NOT NULL AUTO_INCREMENT,
	`id_e` int(11) NOT NULL,
	`flux` varchar(32) NOT NULL,
	`id_ce` int(11) NOT NULL,
	`type` varchar(32) NOT NULL,
	PRIMARY KEY (`id_fe`)
)  ENGINE=MyISAM  ;
CREATE TABLE grade (
	`libelle` varchar(256) NOT NULL,
	`filiere` varchar(255) NOT NULL,
	`cadre_emploi` varchar(255) NOT NULL,
	KEY libelle (`filiere`,`cadre_emploi`,`libelle`)
)  ENGINE=MyISAM  ;
CREATE TABLE journal (
	`id_j` int(11) NOT NULL AUTO_INCREMENT,
	`type` int(11) NOT NULL,
	`id_e` int(11) NOT NULL,
	`id_u` int(11) NOT NULL,
	`id_d` varchar(16) NOT NULL,
	`action` varchar(64) NOT NULL,
	`message` varchar(1024) NOT NULL,
	`date` datetime NOT NULL,
	`preuve` text NOT NULL,
	`date_horodatage` datetime NOT NULL,
	`message_horodate` text NOT NULL,
	`document_type` varchar(128) NOT NULL,
	PRIMARY KEY (`id_j`),
	KEY id_j (`id_u`,`id_j`),
	FULLTEXT KEY message_horodate (`message_horodate`)
)  ENGINE=MyISAM  ;
CREATE TABLE notification (
	`id_n` int(11) NOT NULL AUTO_INCREMENT,
	`id_u` int(11) NOT NULL,
	`id_e` int(11) NOT NULL,
	`type` varchar(32) NOT NULL,
	`action` varchar(64) NOT NULL,
	PRIMARY KEY (`id_n`)
)  ENGINE=MyISAM  ;
CREATE TABLE role (
	`role` varchar(64) NOT NULL,
	`libelle` varchar(255) NOT NULL,
	PRIMARY KEY (`role`)
)  ENGINE=MyISAM  ;
CREATE TABLE role_droit (
	`role` varchar(64) NOT NULL,
	`droit` varchar(64) NOT NULL,
	PRIMARY KEY (`role`,`droit`)
)  ENGINE=MyISAM  ;
CREATE TABLE utilisateur (
	`id_u` int(11) NOT NULL AUTO_INCREMENT,
	`email` varchar(128) NOT NULL,
	`login` varchar(128) NOT NULL,
	`password` varchar(128) NOT NULL,
	`mail_verif_password` varchar(16) NOT NULL,
	`date_inscription` datetime NOT NULL,
	`mail_verifie` tinyint(1) NOT NULL,
	`nom` varchar(128) NOT NULL,
	`prenom` varchar(128) NOT NULL,
	`certificat` text NOT NULL,
	`certificat_verif_number` varchar(32) NOT NULL,
	`id_e` int(11) NOT NULL,
	PRIMARY KEY (`id_u`),
	KEY id_e (`id_e`)
)  ENGINE=MyISAM  ;
CREATE TABLE utilisateur_new_email (
	`id_u` int(11) NOT NULL,
	`email` varchar(255) NOT NULL,
	`password` varchar(32) NOT NULL,
	`date` datetime NOT NULL,
	PRIMARY KEY (`id_u`)
)  ENGINE=MyISAM  ;
CREATE TABLE utilisateur_role (
	`id_u` int(11) NOT NULL,
	`role` varchar(32) NOT NULL,
	`id_e` int(11) NOT NULL,
	KEY id_u (`id_u`,`id_e`),
	KEY id_u_2 (`id_e`,`id_u`)
)  ENGINE=MyISAM  ;