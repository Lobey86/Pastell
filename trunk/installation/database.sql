SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;


CREATE TABLE entite (
  `type` varchar(32) NOT NULL,
  denomination varchar(128) NOT NULL,
  siren char(9) NOT NULL,
  date_inscription datetime NOT NULL,
  etat int(11) NOT NULL,
  entite_mere varchar(9) DEFAULT NULL,
  PRIMARY KEY (siren)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE journal (
  id_j int(11) NOT NULL AUTO_INCREMENT,
  `type` int(11) NOT NULL,
  id_t varchar(16) NOT NULL,
  message varchar(128) NOT NULL,
  `date` datetime NOT NULL,
  preuve text NOT NULL,
  PRIMARY KEY (id_j)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE TABLE mail_notification (
  siren char(16) NOT NULL,
  mail varchar(128) NOT NULL,
  `type` varchar(16) NOT NULL,
  PRIMARY KEY (siren,mail,`type`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE message (
  id_m int(11) NOT NULL AUTO_INCREMENT,
  id_t varchar(16) NOT NULL,
  `type` varchar(32) NOT NULL,
  emetteur varchar(64) NOT NULL,
  date_envoie datetime NOT NULL,
  message varchar(128) NOT NULL,
  PRIMARY KEY (id_m)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE TABLE message_destinataire (
  id_m int(11) NOT NULL,
  siren varchar(9) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE message_ressource (
  id_r int(11) NOT NULL AUTO_INCREMENT,
  id_m int(11) NOT NULL,
  ressource varchar(255) NOT NULL,
  `type` varchar(32) NOT NULL,
  original_name varchar(128) NOT NULL,
  PRIMARY KEY (id_r)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE TABLE `transaction` (
  id_t varchar(16) NOT NULL,
  `type` varchar(32) NOT NULL,
  etat varchar(32) NOT NULL,
  attente_traitement tinyint(1) NOT NULL,
  date_changement_etat datetime NOT NULL,
  objet varchar(512) NOT NULL,
  PRIMARY KEY (id_t)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE transaction_changement_etat (
  id_t varchar(16) NOT NULL,
  etat varchar(32) NOT NULL,
  `date` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE transaction_role (
  id_t varchar(16) NOT NULL,
  siren char(9) NOT NULL,
  role varchar(16) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE utilisateur (
  id_u int(11) NOT NULL AUTO_INCREMENT,
  email varchar(128) NOT NULL,
  login varchar(128) NOT NULL,
  `password` varchar(128) NOT NULL,
  mail_verif_password varchar(16) NOT NULL,
  date_inscription datetime NOT NULL,
  mail_verifie tinyint(1) NOT NULL,
  nom varchar(128) NOT NULL,
  prenom varchar(128) NOT NULL,
  PRIMARY KEY (id_u)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE TABLE utilisateur_role (
  id_u int(11) NOT NULL,
  role varchar(16) NOT NULL,
  siren char(9) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
