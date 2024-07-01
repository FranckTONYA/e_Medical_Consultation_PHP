<?php
Pdweb_IncludeLib("pdweb.mysql.php");
Pdweb_Include("*/inc/.app.php");

define("APP_ROLE_VISITEUR",         "Visiteur");
define("APP_ROLE_ADMINISTRATEUR",   "Administrateur");
define("APP_ROLE_MEDECIN",          "Medecin");
define("APP_ROLE_PATIENT",          "Patient");

define("APP_MENU", array
(
	"ACCUEIL" => array
	(
		"id" => "ACCUEIL",
		"acces" => array(APP_ROLE_VISITEUR, APP_ROLE_ADMINISTRATEUR, APP_ROLE_MEDECIN, APP_ROLE_PATIENT),
		"libelleMenu" => "Accueil",
		"infoBulleMenu" => "Informations g�n�rales sur le site",
		"urlVue" => "*/index.php",
		"sousTitre" => "Accueil",
		"corpsPage" => "Accueil/Afficher"
	),
    "AUTHENTIFICATION_ADMIN" => array
    (
        "id" => "AUTHENTIFICATION_ADMIN",
        "acces" => array(APP_ROLE_VISITEUR),
        "libelleMenu" => "Connexion/Admin",
        "infoBulleMenu" => "Authentifiez-vous pour pouvoir accéder aux fonctionnalités",
        "urlVue" => "*/index.php",
        "sousTitre" => "Authentification",
        "corpsPage" => "Authentification/Admin"
    ),
    "AUTHENTIFICATION_MEDECIN" => array
    (
        "id" => "AUTHENTIFICATION_MEDECIN",
        "acces" => array(APP_ROLE_VISITEUR),
        "libelleMenu" => "Connexion/Medecin",
        "infoBulleMenu" => "Authentifiez-vous pour pouvoir accéder aux fonctionnalités",
        "urlVue" => "*/index.php",
        "sousTitre" => "Authentification",
        "corpsPage" => "Authentification/Medecin"
    ),
    "AUTHENTIFICATION_PATIENT" => array
    (
        "id" => "AUTHENTIFICATION_MEDECIN",
        "acces" => array(APP_ROLE_VISITEUR),
        "libelleMenu" => "Connexion/Patient",
        "infoBulleMenu" => "Authentifiez-vous pour pouvoir accéder aux fonctionnalités",
        "urlVue" => "*/index.php",
        "sousTitre" => "Authentification",
        "corpsPage" => "Authentification/Patient"
    ),
	"DECONNEXION" => array
	(
		"id" => "DECONNEXION",
		"acces" => array(APP_ROLE_ADMINISTRATEUR, APP_ROLE_MEDECIN, APP_ROLE_PATIENT),
		"libelleMenu" => "D�connectez-vous",
		"infoBulleMenu" => "Pour des raisons de s�curit�, pensez � vous d�connecter du site en fin d'utilisation",
		"urlVue" => "*/index.php",
		"sousTitre" => false,
		"corpsPage" => "Authentification/Deconnecter"
	),
//	"TABLEAU_DE_BORD" => array
//	(
//		"id" => "TABLEAU_DE_BORD",
//		"acces" => array(APP_ROLE_ADMINISTRATEUR, APP_ROLE_MEDECIN, APP_ROLE_PATIENT),
//		"libelleMenu" => "Tableau de bord",
//		"infoBulleMenu" => "Votre page personnelle affichant vos informations",
//		"urlVue" => "*/index.php",
//		"sousTitre" => "Tableau de bord de " . App_DenominationUtilisateur(),
//		"corpsPage" => App_EstAdministrateur() ? "Admin/AfficherAccueil" : (App_EstMedecin() ? "Medecin/AfficherAccueil" : "Patient/AfficherAccueil")
//	),
    "CRUD_UTILISATEUR" => array
    (
        "id" => "UTILISATEURS",
        "acces" => array(APP_ROLE_ADMINISTRATEUR),
        "libelleMenu" => "Gestions des Utilisateurs",
        "infoBulleMenu" => "Gérer les utilisateurs de l'applications",
        "urlVue" => "*/index.php",
        "sousTitre" => "Utilisateurs",
        "corpsPage" => "Utilisateurs/Afficher"
    ),
    "CRUD_UTILISATEUR_AJOUT" => array
    (
        "id" => "CRUD_UTILISATEUR_AJOUT",
        "acces" => array(APP_ROLE_ADMINISTRATEUR),
        "libelleMenu" => null,
        "infoBulleMenu" => null,
        "urlVue" => "*/index.php",
        "sousTitre" => "Ajout d'un utilisateur par " . App_DenominationUtilisateur(),
        "corpsPage" => "Utilisateur/AfficherAjout"
    ),
    "CRUD_UTILISATEUR_EDITION" => array
    (
        "id" => "CRUD_UTILISATEUR_EDITION",
        "acces" => array(APP_ROLE_ADMINISTRATEUR),
        "libelleMenu" => null,
        "infoBulleMenu" => null,
        "urlVue" => "*/index.php",
        "sousTitre" => "Modification d'un utilisateur par " . App_DenominationUtilisateur(),
        "corpsPage" => "Utilisateur/AfficherEdition"
    ),
	"CRUD_DOSSIER_PATIENT" => array
	(
		"id" => "DOSSIERS",
		"acces" => array(APP_ROLE_ADMINISTRATEUR, APP_ROLE_MEDECIN),
		"libelleMenu" => "Dossiers Patiens",
		"infoBulleMenu" => "Gestion des dossiers patients",
		"urlVue" => "*/index.php",
		"sousTitre" => "Dossier Patients de " . App_DenominationUtilisateur(),
		"corpsPage" => App_EstAdministrateur() ? "Admin/AfficherDossiers" : "Medecin/AfficherDossiers"
	),
    "CRUD_DOSSIER_PATIENT_AJOUT" => array
    (
        "id" => "CRUD_DOSSIER_PATIENT_AJOUT",
        "acces" => array(APP_ROLE_ADMINISTRATEUR, APP_ROLE_MEDECIN),
        "libelleMenu" => null,
        "infoBulleMenu" => null,
        "urlVue" => "*/index.php",
        "sousTitre" => "Ajout d'un dossier patient pour " . App_DenominationUtilisateur(),
        "corpsPage" => "DossierPatient/AfficherAjout"
    ),
    "CRUD_DOSSIER_PATIENT_EDITION" => array
    (
        "id" => "CRUD_DOSSIER_PATIENT_EDITION",
        "acces" => array(APP_ROLE_ADMINISTRATEUR, APP_ROLE_MEDECIN),
        "libelleMenu" => null,
        "infoBulleMenu" => null,
        "urlVue" => "*/index.php",
        "sousTitre" => "Modification d'un dossier patient pour " . App_DenominationUtilisateur(),
        "corpsPage" => "DossierPatient/AfficherEdition"
    ),
	"CRUD_CONSULTATION" => array
	(
		"id" => "CRUD_CONSULTATION",
		"acces" => array(APP_ROLE_ADMINISTRATEUR, APP_ROLE_MEDECIN, APP_ROLE_PATIENT),
		"libelleMenu" => "Consultations",
		"infoBulleMenu" => "Gestion des consultations",
		"urlVue" => "*/index.php",
		"sousTitre" => "Consultations de " . App_DenominationUtilisateur(),
		"corpsPage" => "Consultation/AfficherListe"
	),
    "CRUD_CONSULTATION_AJOUT" => array
    (
        "id" => "CRUD_CONSULTATION_AJOUT",
        "acces" => array(APP_ROLE_ADMINISTRATEUR, APP_ROLE_MEDECIN, APP_ROLE_PATIENT),
        "libelleMenu" => null,
        "infoBulleMenu" => null,
        "urlVue" => "*/index.php",
        "sousTitre" => "Ajout d'une consultation pour " . App_DenominationUtilisateur(),
        "corpsPage" => "Consultation/AfficherAjout"
    ),
    "CRUD_CONSULTATION_EDITION" => array
    (
        "id" => "CRUD_CONSULTATION_EDITION",
        "acces" => array(APP_ROLE_ADMINISTRATEUR, APP_ROLE_MEDECIN, APP_ROLE_PATIENT),
        "libelleMenu" => null,
        "infoBulleMenu" => null,
        "urlVue" => "*/index.php",
        "sousTitre" => "Modification d'une consultation pour " . App_DenominationUtilisateur(),
        "corpsPage" => "Consultation/AfficherEdition"
    ),
    "CRUD_RENDEZVOUS" => array
    (
        "id" => "RENDEZ_VOUS",
        "acces" => array(APP_ROLE_ADMINISTRATEUR, APP_ROLE_MEDECIN, APP_ROLE_PATIENT),
        "libelleMenu" => "Rendez-vous",
        "infoBulleMenu" => "Gestion des Rendez-vous médical",
        "urlVue" => "*/index.php",
        "sousTitre" => "Rendez-vous de " . App_DenominationUtilisateur(),
        "corpsPage" => App_EstAdministrateur() ? "Admin/AfficherRendezVous" : (App_EstMedecin() ? "Medecin/AfficherRendezVous" : "Patient/AfficherRendezVous")
    ),
    "CRUD_RENDEZ_VOUS_AJOUT" => array
    (
        "id" => "CRUD_RENDEZ_VOUS_AJOUT",
        "acces" => array(APP_ROLE_ADMINISTRATEUR, APP_ROLE_MEDECIN, APP_ROLE_PATIENT),
        "libelleMenu" => null,
        "infoBulleMenu" => null,
        "urlVue" => "*/index.php",
        "sousTitre" => "Ajout d'un rendez-vous pour " . App_DenominationUtilisateur(),
        "corpsPage" => "RendezVous/AfficherAjout"
    ),
    "CRUD_RENDEZ_VOUS_EDITION" => array
    (
        "id" => "CRUD_RENDEZ_VOUS_EDITION",
        "acces" => array(APP_ROLE_ADMINISTRATEUR, APP_ROLE_MEDECIN, APP_ROLE_PATIENT),
        "libelleMenu" => null,
        "infoBulleMenu" => null,
        "urlVue" => "*/index.php",
        "sousTitre" => "Modification d'un rendez vous pour " . App_DenominationUtilisateur(),
        "corpsPage" => "RendezVous/AfficherEdition"
    )
));
App_IdPageActuelle(); // Garantit que l'identifiant de page est initialis� lors de la premi�re visite du site
?>