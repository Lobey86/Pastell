
INSERT INTO `entite` VALUES('collectivite', 'Saint-Amand-les-Eaux', '160641569', '2010-06-09 10:09:04', 2);
INSERT INTO `entite` VALUES('centre_de_gestion', 'Centre de gestion du Nord', '186536850', '2010-06-09 10:09:53', 2);
INSERT INTO `entite` VALUES('fournisseur', 'Canonical Ltd', '830231668', '2010-06-09 10:12:13', 0);

--
-- Contenu de la table `journal`
--


--
-- Contenu de la table `mail_notification`
--


--
-- Contenu de la table `message`
--


--
-- Contenu de la table `message_destinataire`
--


--
-- Contenu de la table `message_ressource`
--


--
-- Contenu de la table `transaction`
--


--
-- Contenu de la table `transaction_changement_etat`
--


--
-- Contenu de la table `transaction_role`
--


--
-- Contenu de la table `utilisateur`
--

INSERT INTO `utilisateur` VALUES(1, 'nomail@sigmalis.com', 'admin', 'admin', '', '2010-06-09 10:07:00', 1, 'Dupond', 'Pierre');
INSERT INTO `utilisateur` VALUES(2, 'col1@sigmalis.com', 'col1', 'col1', '2rXwC0u', '2010-06-09 10:09:28', 1, 'Durand', 'Paul');
INSERT INTO `utilisateur` VALUES(3, 'cdg1@sigmalis.com', 'cdg1', 'cdg1', 'K8HMoJK', '2010-06-09 10:10:29', 1, 'Martin', 'Jacques');
INSERT INTO `utilisateur` VALUES(4, 'fournisseur1@sigmalis.com', 'fournisseur1', 'fournisseur1', 'lbDQ9s9', '2010-06-09 10:12:13', 1, 'Shuttleworh', 'Mark');

--
-- Contenu de la table `utilisateur_role`
--

INSERT INTO `utilisateur_role` VALUES(1, 'super_admin', '');
INSERT INTO `utilisateur_role` VALUES(2, 'proprietaire', '160641569');
INSERT INTO `utilisateur_role` VALUES(3, 'proprietaire', '186536850');
INSERT INTO `utilisateur_role` VALUES(4, 'proprietaire', '830231668');
