INSERT INTO `role` VALUES('admin', 'Administrateur d''entité');
INSERT INTO `role` VALUES('gestionnaire_de_role', 'Gestionnaire de rôle');
INSERT INTO `role` VALUES('lecteur', 'Lecteur');
INSERT INTO `role` VALUES('fournisseur', 'Fournisseur');
INSERT INTO `role` VALUES('agent collectivite', 'Agent d''une collectivité');
INSERT INTO `role` VALUES('agent centre de gestion', 'Agent d''un centre de gestion');
INSERT INTO `role` VALUES('testeur', 'Testeur');
INSERT INTO `role` VALUES('citoyen', 'Citoyen');

INSERT INTO `role_droit` VALUES('admin', 'actes:edition');
INSERT INTO `role_droit` VALUES('admin', 'actes:lecture');
INSERT INTO `role_droit` VALUES('admin', 'annuaire:edition');
INSERT INTO `role_droit` VALUES('admin', 'annuaire:lecture');
INSERT INTO `role_droit` VALUES('admin', 'entite:edition');
INSERT INTO `role_droit` VALUES('admin', 'entite:lecture');
INSERT INTO `role_droit` VALUES('admin', 'fournisseur-inscription:edition');
INSERT INTO `role_droit` VALUES('admin', 'fournisseur-inscription:lecture');
INSERT INTO `role_droit` VALUES('admin', 'fournisseur-message:edition');
INSERT INTO `role_droit` VALUES('admin', 'fournisseur-message:lecture');
INSERT INTO `role_droit` VALUES('admin', 'gf-bon-de-commande:edition');
INSERT INTO `role_droit` VALUES('admin', 'gf-bon-de-commande:lecture');
INSERT INTO `role_droit` VALUES('admin', 'gf-devis:edition');
INSERT INTO `role_droit` VALUES('admin', 'gf-devis:lecture');
INSERT INTO `role_droit` VALUES('admin', 'gf-facture:edition');
INSERT INTO `role_droit` VALUES('admin', 'gf-facture:lecture');
INSERT INTO `role_droit` VALUES('admin', 'journal:lecture');
INSERT INTO `role_droit` VALUES('admin', 'mailsec:edition');
INSERT INTO `role_droit` VALUES('admin', 'mailsec:lecture');
INSERT INTO `role_droit` VALUES('admin', 'message-service:edition');
INSERT INTO `role_droit` VALUES('admin', 'message-service:lecture');
INSERT INTO `role_droit` VALUES('admin', 'rh-messages:edition');
INSERT INTO `role_droit` VALUES('admin', 'rh-messages:lecture');
INSERT INTO `role_droit` VALUES('admin', 'utilisateur:edition');
INSERT INTO `role_droit` VALUES('admin', 'utilisateur:lecture');
INSERT INTO `role_droit` VALUES('agent centre de gestion', 'actes:edition');
INSERT INTO `role_droit` VALUES('agent centre de gestion', 'actes:lecture');
INSERT INTO `role_droit` VALUES('agent centre de gestion', 'annuaire:lecture');
INSERT INTO `role_droit` VALUES('agent centre de gestion', 'entite:lecture');
INSERT INTO `role_droit` VALUES('agent centre de gestion', 'journal:lecture');
INSERT INTO `role_droit` VALUES('agent centre de gestion', 'mailsec:edition');
INSERT INTO `role_droit` VALUES('agent centre de gestion', 'mailsec:lecture');
INSERT INTO `role_droit` VALUES('agent centre de gestion', 'message-service:edition');
INSERT INTO `role_droit` VALUES('agent centre de gestion', 'message-service:lecture');
INSERT INTO `role_droit` VALUES('agent centre de gestion', 'rh-messages:edition');
INSERT INTO `role_droit` VALUES('agent centre de gestion', 'rh-messages:lecture');
INSERT INTO `role_droit` VALUES('agent centre de gestion', 'utilisateur:lecture');
INSERT INTO `role_droit` VALUES('agent collectivite', 'actes:edition');
INSERT INTO `role_droit` VALUES('agent collectivite', 'actes:lecture');
INSERT INTO `role_droit` VALUES('agent collectivite', 'annuaire:lecture');
INSERT INTO `role_droit` VALUES('agent collectivite', 'entite:lecture');
INSERT INTO `role_droit` VALUES('agent collectivite', 'journal:lecture');
INSERT INTO `role_droit` VALUES('agent collectivite', 'mailsec:edition');
INSERT INTO `role_droit` VALUES('agent collectivite', 'mailsec:lecture');
INSERT INTO `role_droit` VALUES('agent collectivite', 'message-service:lecture');
INSERT INTO `role_droit` VALUES('agent collectivite', 'rh-messages:edition');
INSERT INTO `role_droit` VALUES('agent collectivite', 'rh-messages:lecture');
INSERT INTO `role_droit` VALUES('agent collectivite', 'utilisateur:lecture');
INSERT INTO `role_droit` VALUES('fournisseur', 'entite:lecture');
INSERT INTO `role_droit` VALUES('fournisseur', 'fournisseur-inscription:edition');
INSERT INTO `role_droit` VALUES('fournisseur', 'fournisseur-inscription:lecture');
INSERT INTO `role_droit` VALUES('fournisseur', 'fournisseur-message:edition');
INSERT INTO `role_droit` VALUES('fournisseur', 'fournisseur-message:lecture');
INSERT INTO `role_droit` VALUES('fournisseur', 'journal:lecture');
INSERT INTO `role_droit` VALUES('gestionnaire_de_role', 'role:edition');
INSERT INTO `role_droit` VALUES('gestionnaire_de_role', 'role:lecture');
INSERT INTO `role_droit` VALUES('lecteur', 'entite:lecture');
INSERT INTO `role_droit` VALUES('lecteur', 'utilisateur:lecture');
INSERT INTO `role_droit` VALUES('testeur', 'test:edition');
INSERT INTO `role_droit` VALUES('testeur', 'test:lecture');
INSERT INTO `role_droit` (`role`, `droit`) VALUES
('citoyen', 'citoyen-courrier:edition'),
('citoyen', 'citoyen-courrier:lecture'),
('citoyen', 'entite:lecture'),
('citoyen', 'journal:lecture'),
('citoyen', 'utilisateur:lecture');
INSERT INTO `role_droit` VALUES('admin', 'citoyen-courrier:lecture');
INSERT INTO `role_droit` VALUES('admin', 'citoyen-courrier:edition');
INSERT INTO `role_droit` VALUES('agent collectivite', 'citoyen-courrier:lecture');

INSERT INTO `role_droit` VALUES('admin', 'helios:edition');
INSERT INTO `role_droit` VALUES('admin', 'helios:lecture');

