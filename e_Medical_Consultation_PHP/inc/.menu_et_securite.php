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
		"infoBulleMenu" => "Informations générales sur le site",
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
        "id" => "AUTHENTIFICATION_PATIENT",
        "acces" => array(APP_ROLE_VISITEUR),
        "libelleMenu" => "Connexion/Patient",
        "infoBulleMenu" => "Authentifiez-vous pour pouvoir accéder aux fonctionnalités",
        "urlVue" => "*/index.php",
        "sousTitre" => "Authentification",
        "corpsPage" => "Authentification/Patient"
    ),
    "CRUD_UTILISATEUR" => array
    (
        "id" => "UTILISATEURS",
        "acces" => array(APP_ROLE_ADMINISTRATEUR),
        "libelleMenu" => "Gestions des Utilisateurs",
        "infoBulleMenu" => "Gérer les utilisateurs de l'applications",
        "urlVue" => "*/index.php",
        "sousTitre" => "Utilisateurs",
        "corpsPage" => "Utilisateur/Afficher"
    ),
    "CRUD_UTILISATEUR_AJOUT" => array
    (
        "id" => "CRUD_UTILISATEUR_AJOUT",
        "acces" => array(APP_ROLE_ADMINISTRATEUR),
        "libelleMenu" => null,
        "infoBulleMenu" => null,
        "urlVue" => "*/index.php",
        "sousTitre" => "Ajout d'un utilisateur par " . App_DenominationUtilisateur() . " (" . App_RoleActuel() . ")",
        "corpsPage" => "Utilisateur/AfficherAjout"
    ),
    "CRUD_UTILISATEUR_EDITION" => array
    (
        "id" => "CRUD_UTILISATEUR_EDITION",
        "acces" => array(APP_ROLE_ADMINISTRATEUR),
        "libelleMenu" => null,
        "infoBulleMenu" => null,
        "urlVue" => "*/index.php",
        "sousTitre" => "Modification d'un utilisateur par " . App_DenominationUtilisateur() . " (" . App_RoleActuel() . ")",
        "corpsPage" => "Utilisateur/AfficherEdition"
    ),
	"CRUD_DOSSIER_PATIENT" => array
	(
		"id" => "DOSSIERS",
		"acces" => array(APP_ROLE_ADMINISTRATEUR, APP_ROLE_MEDECIN),
		"libelleMenu" => "Dossiers Patiens",
		"infoBulleMenu" => "Gestion des dossiers patients",
		"urlVue" => "*/index.php",
		"sousTitre" => "Dossier Patients de " . App_DenominationUtilisateur() . " (" . App_RoleActuel() . ")",
		"corpsPage" => "Dossier/Afficher"
	),
    "CRUD_DOSSIER_PATIENT_AJOUT" => array
    (
        "id" => "CRUD_DOSSIER_PATIENT_AJOUT",
        "acces" => array(APP_ROLE_ADMINISTRATEUR, APP_ROLE_MEDECIN),
        "libelleMenu" => null,
        "infoBulleMenu" => null,
        "urlVue" => "*/index.php",
        "sousTitre" => "Ajout d'un dossier patient pour " . App_DenominationUtilisateur() . " (" . App_RoleActuel() . ")",
        "corpsPage" => "Dossier/AfficherAjout"
    ),
    "CRUD_DOSSIER_PATIENT_EDITION" => array
    (
        "id" => "CRUD_DOSSIER_PATIENT_EDITION",
        "acces" => array(APP_ROLE_ADMINISTRATEUR, APP_ROLE_MEDECIN),
        "libelleMenu" => null,
        "infoBulleMenu" => null,
        "urlVue" => "*/index.php",
        "sousTitre" => "Modification d'un dossier patient pour " . App_DenominationUtilisateur() . " (" . App_RoleActuel() . ")",
        "corpsPage" => "Dossier/AfficherEdition"
    ),
	"CRUD_CONSULTATION" => array
	(
		"id" => "CRUD_CONSULTATION",
		"acces" => array(APP_ROLE_ADMINISTRATEUR, APP_ROLE_MEDECIN, APP_ROLE_PATIENT),
		"libelleMenu" => "Consultations",
		"infoBulleMenu" => "Gestion des consultations",
		"urlVue" => "*/index.php",
		"sousTitre" => "Consultations de " . App_DenominationUtilisateur() . " (" . App_RoleActuel() . ")",
		"corpsPage" => "Consultation/AfficherListe"
	),
    "CRUD_CONSULTATION_EDITION" => array
    (
        "id" => "CRUD_CONSULTATION_EDITION",
        "acces" => array(APP_ROLE_ADMINISTRATEUR, APP_ROLE_MEDECIN, APP_ROLE_PATIENT),
        "libelleMenu" => null,
        "infoBulleMenu" => null,
        "urlVue" => "*/index.php",
        "sousTitre" => "Modification/Ajout d'une consultation pour " . App_DenominationUtilisateur() . " (" . App_RoleActuel() . ")",
        "corpsPage" => "Consultation/AfficherFormulaire"
    ),
    "CRUD_RENDEZVOUS" => array
    (
        "id" => "CRUD_RENDEZVOUS",
        "acces" => array(APP_ROLE_ADMINISTRATEUR, APP_ROLE_MEDECIN, APP_ROLE_PATIENT),
        "libelleMenu" => "Rendez-vous",
        "infoBulleMenu" => "Gestion des Rendez-vous médical",
        "urlVue" => "*/index.php",
        "sousTitre" => "Rendez-vous de " . App_DenominationUtilisateur() . " (" . App_RoleActuel() . ")",
        "corpsPage" => "RendezVous/AfficherListe"
    ),
    "CRUD_RENDEZVOUS_EDITION" => array
    (
        "id" => "CRUD_RENDEZVOUS_EDITION",
        "acces" => array(APP_ROLE_ADMINISTRATEUR, APP_ROLE_MEDECIN, APP_ROLE_PATIENT),
        "libelleMenu" => null,
        "infoBulleMenu" => null,
        "urlVue" => "*/index.php",
        "sousTitre" => "Modification/Ajout d'un rendez vous pour " . App_DenominationUtilisateur() . " (" . App_RoleActuel() . ")",
        "corpsPage" => "RendezVous/AfficherFormulaire"
    ),
    "DECONNEXION" => array
    (
        "id" => "DECONNEXION",
        "acces" => array(APP_ROLE_ADMINISTRATEUR, APP_ROLE_MEDECIN, APP_ROLE_PATIENT),
        "libelleMenu" => mb_convert_encoding("Déconnectez-vous" ,'Windows-1252', 'UTF-8'),
        "infoBulleMenu" => "Pour des raisons de sécurité, pensez à vous déconnecter du site en fin d'utilisation",
        "urlVue" => "*/index.php",
        "sousTitre" => false,
        "corpsPage" => "Authentification/Deconnecter"
    )
));
App_IdPageActuelle(); // Garantit que l'identifiant de page est initialisé lors de la première visite du site
?>