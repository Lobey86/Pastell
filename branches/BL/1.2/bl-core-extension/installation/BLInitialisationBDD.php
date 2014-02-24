<?php

require_once( __DIR__ . "/../../web/init.php");
require_once ( __DIR__ . "/BLInitialisationBDDSettings.php");


// Test sur les valeurs présentes dans le fichier scriptSettings
if (!USER_ADMINBLESDEV_EMAIL || !USER_ADMINBLESDEV_PASSWORD || !USER_ADMINBL_EMAIL || !USER_ADMINBL_PASSWORD || !USER_ADMINCOMPTES_EMAIL || !USER_ADMINCOMPTES_PASSWORD
        || !USER_BLREADY_EMAIL || !USER_BLREADY_PASSWORD || !USER_WSBLESDEV_EMAIL || !USER_WSBLESDEV_PASSWORD || !USER_BLSTAT_PASSWORD || !USER_BLSTAT_EMAIL) {
    throw new Exception('Toutes les constantes du fichier BLInitialisationBDDSettings doivent etre renseignees.');
}

/////////////////////////////////////////////////
// Création des rôles  
/////////////////////////////////////////////////
echo 'Creation des roles' . "\n";
$roleSQLClass = $objectInstancier->RoleSQL;
//Rôle AdminEntite
echo '  Creation du Role adminEntite' . "\n";
$roleSQLClass->edit('adminEntite', 'Administrateur d\'entité');
$roleSQLClass->addDroit('adminEntite','entite:edition');
$roleSQLClass->addDroit('adminEntite','entite:lecture');
$roleSQLClass->addDroit('adminEntite','utilisateur:edition');
$roleSQLClass->addDroit('adminEntite','utilisateur:lecture');
$roleSQLClass->addDroit('adminEntite','role:lecture');
$roleSQLClass->addDroit('adminEntite','journal:lecture');
$roleSQLClass->addDroit('adminEntite','system:lecture');
$roleSQLClass->addDroit('adminEntite','system:edition');
$roleSQLClass->addDroit('adminEntite','pesbl:lecture');
$roleSQLClass->addDroit('adminEntite','pesbl:edition');
$roleSQLClass->addDroit('adminEntite','documentinternebl:lecture');
$roleSQLClass->addDroit('adminEntite','documentinternebl:edition');

//Rôle adminDocument
echo '  Creation du Role adminDocument' . "\n";
$roleSQLClass->edit('adminDocument','Administrateur de document');
$roleSQLClass->addDroit('adminDocument','entite:lecture');
$roleSQLClass->addDroit('adminDocument','utilisateur:lecture');
$roleSQLClass->addDroit('adminDocument','role:lecture');
$roleSQLClass->addDroit('adminDocument','journal:lecture');
$roleSQLClass->addDroit('adminDocument','pesbl:lecture');
$roleSQLClass->addDroit('adminDocument','pesbl:edition');
$roleSQLClass->addDroit('adminDocument','documentinternebl:lecture');
$roleSQLClass->addDroit('adminDocument','documentinternebl:edition');

//Rôle apiDocument
echo '  Creation du Role apiDocument' . "\n";
$roleSQLClass->edit('apiDocument','Opérateur API sur document');
$roleSQLClass->addDroit('apiDocument','entite:lecture');
$roleSQLClass->addDroit('apiDocument','journal:lecture');
$roleSQLClass->addDroit('apiDocument','pesbl:lecture');
$roleSQLClass->addDroit('apiDocument','pesbl:edition');
$roleSQLClass->addDroit('apiDocument','documentinternebl:lecture');
$roleSQLClass->addDroit('apiDocument','documentinternebl:edition');

//Rôle apiStat
echo '  Creation du Role apiStat' . "\n";
$roleSQLClass->edit('apiStat','Opérateur API pour export des stats');
$roleSQLClass->addDroit('apiStat','entite:lecture');
$roleSQLClass->addDroit('apiStat','journal:lecture');

/////////////////////////////////////////////////
// Création des entités "entreprise"
/////////////////////////////////////////////////
echo 'Creation des entites entreprise' . "\n";
// Entité BL
echo '  Creation de l\'entite entreprise BL' . "\n";
$id_eBL = $objectInstancier->EntiteControler->edition(null, 'BL', '755800646', 'collectivite', 0, 0, 'non', 'non');
// Entité BLES_DEV
echo '  Creation de l\'entite entreprise BLES-DEV' . "\n";
$id_eBLES_DEV = $objectInstancier->EntiteControler->edition(null, 'BLES_DEV', '752030999', 'collectivite', 0, 0, 'non', 'non');

/////////////////////////////////////////////////
// Création des utilisateurs
/////////////////////////////////////////////////
echo 'Creation des utilisateurs' . "\n";
$RoleUtilisateurClass = $objectInstancier->RoleUtilisateur;
//Utilisateur adminComptes
echo '  Creation de l\'utilisateur admincomptes' . "\n";
$id_u = $objectInstancier->UtilisateurControler->editionUtilisateur(0,null,USER_ADMINCOMPTES_EMAIL,'admincomptes',USER_ADMINCOMPTES_PASSWORD,USER_ADMINCOMPTES_PASSWORD,'admincomptes','admincomptes',null);
$RoleUtilisateurClass->addRole($id_u,'adminEntite',0);
//Utilisateur blready
echo '  Creation de l\'utilisateur blready' . "\n";
$id_u = $objectInstancier->UtilisateurControler->editionUtilisateur(0,null,USER_BLREADY_EMAIL, 'blready',USER_BLREADY_PASSWORD,USER_BLREADY_PASSWORD,'blready','blready',null);
$RoleUtilisateurClass->addRole($id_u,'adminEntite',0);
//Utilisateur admin@BL
echo '  Creation de l\'utilisateur admin@BL' . "\n";
$id_u = $objectInstancier->UtilisateurControler->editionUtilisateur($id_eBL,null,USER_ADMINBL_EMAIL,'admin@BL',USER_ADMINBL_PASSWORD,USER_ADMINBL_PASSWORD,'admin','BL',null);
$RoleUtilisateurClass->addRole($id_u,'adminEntite',$id_eBL);
//Utilisateur admin@BLES_DEV
echo '  Creation de l\'utilisateur admin@BLES_DEV' . "\n";
$id_u = $objectInstancier->UtilisateurControler->editionUtilisateur($id_eBLES_DEV,null,USER_ADMINBLESDEV_EMAIL,'admin@BLES_DEV',USER_ADMINBLESDEV_PASSWORD,USER_ADMINBLESDEV_PASSWORD,'admin','BLES_DEV',null);
$RoleUtilisateurClass->addRole($id_u,'adminEntite',$id_eBLES_DEV);
//Utilisateur ws@BLES_DEV
echo '  Creation de l\'utilisateur ws@BLES_DEV' . "\n";
$id_u = $objectInstancier->UtilisateurControler->editionUtilisateur($id_eBLES_DEV,null,USER_WSBLESDEV_EMAIL,'ws@BLES_DEV',USER_WSBLESDEV_PASSWORD, USER_WSBLESDEV_PASSWORD,'ws','BLES_DEV',null);
$RoleUtilisateurClass->addRole($id_u,'apiDocument',$id_eBLES_DEV);
//Utilisateur blstat
echo '  Creation de l\'utilisateur blstat' . "\n";
$id_u = $objectInstancier->UtilisateurControler->editionUtilisateur(0,null,USER_BLSTAT_EMAIL,'blstat',USER_BLSTAT_PASSWORD, USER_BLSTAT_PASSWORD,'blstat','blstat',null);
$RoleUtilisateurClass->addRole($id_u,'apiStat',0);

/////////////////////////////////////////
// Connecteurs globaux 
/////////////////////////////////////////
echo 'Creation des connecteurs globaux' . "\n";
// Connecteur iparapheur
echo '  Creation du connecteur global iparapheurbl' . "\n";
$id_ce = $objectInstancier->ConnecteurControler->nouveau(0, 'iparapheurbl', 'iparapheurbl global');
$id_fe = $objectInstancier->FluxControler->editionModif(0, null, 'signature', $id_ce);

// Connecteur horodateur interne
echo '  Creation du connecteur global horodateur interne' . "\n";
$id_ce = $objectInstancier->ConnecteurControler->nouveau(0, 'horodateur-interne', 'horodateur interne global');
$id_fe = $objectInstancier->FluxControler->editionModif(0, '', 'horodateur', $id_ce);

$data['id_e'] = 0;
$data['id_ce'] = $id_ce;
$data['signer_key_password'] = '';

 $_FILES['signer_certificate']['name']='autorite-cert.pem';
 $_FILES['signer_certificate']['type']= 'application/octet-stream';
 $_FILES['signer_certificate']['tmp_name']= '/var/www/pastell/data-exemple/timestamp-cert.pem';
 $_FILES['signer_certificate']['error']= 0;
 $_FILES['signer_certificate']['size']= 3462;
 
 $_FILES['signer_key']['name']='signer_key.pem';
 $_FILES['signer_key']['type']= 'application/octet-stream';
 $_FILES['signer_key']['tmp_name']= '/var/www/pastell/data-exemple/timestamp-key.pem';
 $_FILES['signer_key']['error']= 0;
 $_FILES['signer_key']['size']= 887;
 
 $_FILES['ca_certificate']['name']='ca_certificate.pem';
 $_FILES['ca_certificate']['type']= 'application/octet-stream';
 $_FILES['ca_certificate']['tmp_name']= '/var/www/pastell/data-exemple/autorite-cert.pem';
 $_FILES['ca_certificate']['error']= 0;
 $_FILES['ca_certificate']['size']= 863;

 

$fileUploader = new FileUploader();

unset($data['id_e']);
unset($data['id_ce']);

$donneesFormulaire = $objectInstancier->DonneesFormulaireFactory->getConnecteurEntiteFormulaire($id_ce);

$donneesFormulaire->setTabDataVerif($data);
if ($fileUploader) {  
    $donneesFormulaire->saveAllFile($fileUploader);
    // La fonction saveAllFile utilise move_uploaded_file. Comme les fichiers ne sont pas uploadés, la copie ne se fait pas.
    // --> Copie manuelle des 3 fichiers.
    copy($fileUploader->getFilePath('signer_certificate'),$donneesFormulaire->getFilePath('signer_certificate',0));   
    copy($fileUploader->getFilePath('signer_key'),$donneesFormulaire->getFilePath('signer_key',0));   
    copy($fileUploader->getFilePath('ca_certificate'),$donneesFormulaire->getFilePath('ca_certificate',0)); 
} 

foreach($donneesFormulaire->getOnChangeAction() as $action) {	
    $resultAction = $objectInstancier->ActionExecutorFactory->executeOnConnecteur($id_ce,$this->objectInstancier->Authentification->getId(),true,$action);
}
  
?>
