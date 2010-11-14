

INSERT INTO utilisateur (id_u, email, login, password, mail_verif_password, date_inscription, mail_verifie, nom, prenom,id_e) VALUES(1, 'eric+adminpastell@sigmalis.com', 'admin', 'admin', '', '2010-10-07 00:00:00', 1, 'Pastell', 'Administrateur',0);
INSERT INTO utilisateur (id_u, email, login, password, mail_verif_password, date_inscription, mail_verifie, nom, prenom,id_e) VALUES(2, 'eric+cdg1@sigmalis.com', 'cdg1', 'cdg1', 'uhJRpTD', '2010-10-07 09:51:35', 1, 'Martin', 'Georges',1);
INSERT INTO utilisateur (id_u, email, login, password, mail_verif_password, date_inscription, mail_verifie, nom, prenom,id_e) VALUES(3, 'eric+col1@sigmalis.com', 'col1', 'col1', 'FofTaPq', '2010-10-07 09:53:58', 1, 'Dubois', 'Jacques',3);


INSERT INTO `entite` (`id_e`, `type`, `denomination`, `siren`, `date_inscription`, `etat`, `entite_mere`, `centre_de_gestion`) VALUES(1, 'centre_de_gestion', 'Centre de gestion du Nord', '507484772', '2010-10-07 09:46:23', 0, '0', 0);
INSERT INTO `entite` (`id_e`, `type`, `denomination`, `siren`, `date_inscription`, `etat`, `entite_mere`, `centre_de_gestion`) VALUES(3, 'collectivite', 'Saint-Amand-les-Eaux', '548729920', '2010-10-07 09:50:21', 0, '0', 1);
INSERT INTO `entite` (`id_e`, `type`, `denomination`, `siren`, `date_inscription`, `etat`, `entite_mere`, `centre_de_gestion`) VALUES(4, 'collectivite', 'Conseil général du Nord', '678517129', '2010-10-07 10:00:14', 0, '0', 1);


INSERT INTO `utilisateur_role` (`id_u`, `role`, `id_e`) VALUES(1, 'admin', 0);
INSERT INTO `utilisateur_role` (`id_u`, `role`, `id_e`) VALUES(2, 'admin', 1);
INSERT INTO `utilisateur_role` (`id_u`, `role`, `id_e`) VALUES(3, 'admin', 3);