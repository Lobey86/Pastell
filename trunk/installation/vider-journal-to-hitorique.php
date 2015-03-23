<?php
require_once( __DIR__ . "/../web/init.php");
/*
 
 CREATE TABLE `journal_historique` (
  `id_j` int(11) NOT NULL AUTO_INCREMENT,
  `type` int(11) NOT NULL,
  `id_e` int(11) NOT NULL,
  `id_u` int(11) NOT NULL,
  `id_d` varchar(16) NOT NULL,
  `action` varchar(64) NOT NULL,
  `message` text NOT NULL,
  `date` datetime NOT NULL,
  `preuve` text NOT NULL,
  `date_horodatage` datetime NOT NULL,
  `message_horodate` text NOT NULL,
  `document_type` varchar(128) NOT NULL,
  PRIMARY KEY (`id_j`),
  KEY `id_j` (`id_u`,`id_j`),
  KEY `date` (`date`),
  KEY `id_e` (`id_e`),
  KEY `id_d` (`id_d`),
  KEY `type` (`type`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

*/


$sql = "SELECT *";