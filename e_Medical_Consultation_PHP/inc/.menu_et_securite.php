<?php
Pdweb_IncludeLib("pdweb.mysql.php");
Pdweb_Include("*/inc/.app.php");

define("APP_ROLE_VISITEUR",         "V");
define("APP_ROLE_ADMINISTRATEUR",   "A");
define("APP_ROLE_MEDECIN",          "M");
define("APP_ROLE_PATIENT",          "P");

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
	"AUTHENTIFICATION" => array
	(
		"id" => "AUTHENTIFICATION",
		"acces" => array(APP_ROLE_VISITEUR),
		"libelleMenu" => "Authentifiez-vous",
		"infoBulleMenu" => "Authentifiez-vous pour pouvoir accéder aux fonctionnalités",
		"urlVue" => "*/index.php",
		"sousTitre" => "Authentification",
		"corpsPage" => "Authentification/Afficher"
	),
	"AUTHENTIFICATION_MDP" => array
	(
		"id" => "AUTHENTIFICATION_MDP",
		"acces" => array(APP_ROLE_VISITEUR),
		"libelleMenu" => null,
		"infoBulleMenu" => null,
		"urlVue" => "*/index.php",
		"sousTitre" => "Changement du mot de passe",
		"corpsPage" => "Authentification/AfficherMdp"
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
	"TABLEAU_DE_BORD" => array
	(
		"id" => "TABLEAU_DE_BORD",
		"acces" => array(APP_ROLE_ADMINISTRATEUR, APP_ROLE_MEDECIN, APP_ROLE_PATIENT),
		"libelleMenu" => "Tableau de bord",
		"infoBulleMenu" => "Votre page personnelle affichant vos informations",
		"urlVue" => "*/index.php",
		"sousTitre" => "Tableau de bord de " . App_DenominationUtilisateur(),
		"corpsPage" => App_EstAdministrateur() ? "Admin/AfficherAccueil" : (App_EstMedecin() ? "Medecin/AfficherAccueil" : "Patient/AfficherAccueil")
	),
    "GESTION_UTILISATEURS" => array
    (
        "id" => "UTILISATEURS",
        "acces" => array(APP_ROLE_ADMINISTRATEUR),
        "libelleMenu" => "Gestions des Utilisateurs",
        "infoBulleMenu" => "Gérer les utilisateurs de l'applications",
        "urlVue" => "*/index.php",
        "sousTitre" => "Utilisateurs",
        "corpsPage" => "Utilisateurs/Afficher"
    ),
	"DOSSIERS_PATIENTS" => array
	(
		"id" => "DOSSIERS",
		"acces" => array(APP_ROLE_ADMINISTRATEUR, APP_ROLE_MEDECIN),
		"libelleMenu" => "Dossiers Patiens",
		"infoBulleMenu" => "Gestion des dossiers patients",
		"urlVue" => "*/index.php",
		"sousTitre" => "Dossier Patients de " . App_DenominationUtilisateur(),
		"corpsPage" => App_EstAdministrateur() ? "Admin/AfficherDossiers" : "Medecin/AfficherDossiers"
	),
	"CONSULTATIONS" => array
	(
		"id" => "CONSULTATIONS",
		"acces" => array(APP_ROLE_ADMINISTRATEUR, APP_ROLE_MEDECIN, APP_ROLE_PATIENT),
		"libelleMenu" => "Consultations",
		"infoBulleMenu" => "Gestion des consultations",
		"urlVue" => "*/index.php",
		"sousTitre" => "Consultations de " . App_DenominationUtilisateur(),
		"corpsPage" => App_EstAdministrateur() ? "Admin/AfficherConsultations" : (App_EstMedecin() ? "Medecin/AfficherConsultations" : "Patient/AfficherConsultations")
	),
    "RENDEZ_VOUS" => array
    (
        "id" => "RENDEZ_VOUS",
        "acces" => array(APP_ROLE_ADMINISTRATEUR, APP_ROLE_MEDECIN, APP_ROLE_PATIENT),
        "libelleMenu" => "Rendez-vous",
        "infoBulleMenu" => "Gestion des Rendez-vous médical",
        "urlVue" => "*/index.php",
        "sousTitre" => "Rendez-vous de " . App_DenominationUtilisateur(),
        "corpsPage" => App_EstAdministrateur() ? "Admin/AfficherRendezVous" : (App_EstMedecin() ? "Medecin/AfficherRendezVous" : "Patient/AfficherRendezVous")
    )
));
App_IdPageActuelle(); // Garantit que l'identifiant de page est initialis� lors de la premi�re visite du site
?>