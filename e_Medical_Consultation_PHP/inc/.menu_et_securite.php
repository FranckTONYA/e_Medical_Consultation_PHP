<?php
Pdweb_IncludeLib("pdweb.mysql.php");
Pdweb_Include("*/inc/.app.php");

define("APP_ROLE_VISITEUR", "V");
define("APP_ROLE_JOUEUR",   "J");
define("APP_ROLE_AUTEUR",   "A");

define("APP_MENU", array
(
	"ACCUEIL" => array
	(
		"id" => "ACCUEIL",
		"acces" => array(APP_ROLE_VISITEUR, APP_ROLE_JOUEUR, APP_ROLE_AUTEUR),
		"libelleMenu" => "Accueil",
		"infoBulleMenu" => "Informations générales sur le site",
		"urlVue" => "*/index.php",
		"sousTitre" => "Accueil",
		"corpsPage" => "Accueil/Afficher"
	),
	"INSCRIPTION" => array
	(
		"id" => "INSCRIPTION",
		"acces" => array(APP_ROLE_VISITEUR),
		"libelleMenu" => "Inscrivez-vous",
		"infoBulleMenu" => "Inscrivez-vous soit en tant que joueur, soit en tant qu'auteur",
		"urlVue" => "*/index.php",
		"sousTitre" => "Inscription",
		"corpsPage" => "Inscription/Afficher"
	),
	"AUTHENTIFICATION" => array
	(
		"id" => "AUTHENTIFICATION",
		"acces" => array(APP_ROLE_VISITEUR),
		"libelleMenu" => "Authentifiez-vous",
		"infoBulleMenu" => "Authentifiez-vous pour pouvoir soit jouer, soit créer",
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
		"acces" => array(APP_ROLE_JOUEUR, APP_ROLE_AUTEUR),
		"libelleMenu" => "Déconnectez-vous",
		"infoBulleMenu" => "Pour des raisons de sécurité, pensez à vous déconnecter du site en fin d'utilisation",
		"urlVue" => "*/index.php",
		"sousTitre" => false,
		"corpsPage" => "Authentification/Deconnecter"
	),
	"TABLEAU_DE_BORD" => array
	(
		"id" => "TABLEAU_DE_BORD",
		"acces" => array(APP_ROLE_JOUEUR, APP_ROLE_AUTEUR),
		"libelleMenu" => "Tableau de bord",
		"infoBulleMenu" => "Votre page personnelle affichant vos informations",
		"urlVue" => "*/index.php",
		"sousTitre" => "Tableau de bord de " . App_DenominationUtilisateur(),
		"corpsPage" => App_EstJoueur() ? "Joueur/AfficherAccueil" : "Auteur/AfficherAccueil"
	),
	"CRUD_IMAGE" => array
	(
		"id" => "CRUD_IMAGE",
		"acces" => array(APP_ROLE_AUTEUR),
		"libelleMenu" => "Vos images",
		"infoBulleMenu" => "Gestion de vos images uploadées",
		"urlVue" => "*/index.php",
		"sousTitre" => "Images de " . App_DenominationUtilisateur(),
		"corpsPage" => "Illustration/AfficherListe"
	),
	"CRUD_IMAGE_AJOUT" => array
	(
		"id" => "CRUD_IMAGE_AJOUT",
		"acces" => array(APP_ROLE_AUTEUR),
		"libelleMenu" => null,
		"infoBulleMenu" => null,
		"urlVue" => "*/index.php",
		"sousTitre" => "Ajout d'une image pour " . App_DenominationUtilisateur(),
		"corpsPage" => "Illustration/AfficherAjout"
	),
	"CRUD_IMAGE_EDITION" => array
	(
		"id" => "CRUD_IMAGE_EDITION",
		"acces" => array(APP_ROLE_AUTEUR),
		"libelleMenu" => null,
		"infoBulleMenu" => null,
		"urlVue" => "*/index.php",
		"sousTitre" => "Modification d'une image pour " . App_DenominationUtilisateur(),
		"corpsPage" => "Illustration/AfficherEdition"
	)
));
App_IdPageActuelle(); // Garantit que l'identifiant de page est initialisé lors de la première visite du site
?>