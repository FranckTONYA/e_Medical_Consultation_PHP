<?php
Pdweb_IncludeLib("pdweb.html.php");

define("APP_FORM_GESTION_UTILISATEUR", array
(
	"id" => "gestion_utilisateur",
	"url" => "*/.controleur.php",
	"elements" => array
	(
		array
		(
			"name" => "email",
			"type" => "text",
			"label" => "Email :"
		),
		array
		(
			"name" => "mdp",
			"type" => "password",
			"label" => "Mot de passe :"
		),
		array
		(
			"name" => "confirmation_mdp",
			"type" => "password",
			"label" => "Confirmation du mot de passe :"
		),
		array
		(
			"name" => "role",
			"type" => "select",
			"label" => "Rôle :"
		),
		array
		(
			"name" => "nom",
			"type" => "text",
			"label" => "Nom :"
		),
		array
		(
			"name" => "prenom",
			"type" => "text",
			"label" => "Prénom :"
		),
        array
        (
            "name" => "telephone",
            "type" => "text",
            "label" => "Téléphone :"
        ),
        array
        (
            "name" => "dateNaissance",
            "type" => "text",
            "label" => "Date de naissance :"
        ),
        array
        (
            "name" => "adresse",
            "type" => "text",
            "label" => "Adresse :"
        ),
		array
		(
			"name" => "action",
			"type" => "hidden",
			"value" => "Creer/CreerUtilisateur"
		),
		array
		(
			"name" => "creer",
			"type" => "submit",
			"value" => "Creer un Utilisateur"
		)
	)
));

function Utilisateurs_Afficher()
{
	Html_GenerateG("section", HTML_CONTENT, function ()
	{
		Html_GenerateForm(APP_FORM_GESTION_UTILISATEUR);
	});
}

function Utilisateurs_Creer($donnees)
{
	if (!App_EstVisiteur()) Http_Redirect("*/");
	if (!isset($donnees["email"], $donnees["mdp"], $donnees["confirmation_mdp"], $donnees["nom"], $donnees["prenom"], $donnees["telephone"], $donnees["dateNaissance"], $donnees["adresse"])) Http_Redirect("*/");
	Form_ClearErrors(APP_FORM_GESTION_UTILISATEUR["id"]);
	$erreurPresente = false;
    if (($longueur=strlen($email = trim($donnees["email"]))) < 6)
    {
        $erreurPresente = true;
        Form_SetError(APP_FORM_GESTION_UTILISATEUR["id"], "email", "L'adresse e-mail doit comporter au moins 6 caractères significatifs !");
    }
    else if ($longueur > 120)
    {
        $erreurPresente = true;
        Form_SetError(APP_FORM_GESTION_UTILISATEUR["id"], "email", "L'adresse e-mail doit comporter au plus 120 caractères significatifs !");
    }
	if (($longueur=strlen($mdp = $donnees["mdp"])) < 5)
	{
		$erreurPresente = true;
		Form_SetError(APP_FORM_GESTION_UTILISATEUR["id"], "mdp", "Le mot de passe doit comporter au moins 5 caractères !");
	}
	if ($donnees["confirmation_mdp"] != $donnees["mdp"])
	{
		$erreurPresente = true;
		Form_SetError(APP_FORM_GESTION_UTILISATEUR["id"], "confirmation_mdp", "La confirmation de mot de passe ne coïncide pas avec le mot de passe encodé !");
	}
	if (($longueur=strlen($nom = trim($donnees["nom"]))) < 2)
	{
		$erreurPresente = true;
		Form_SetError(APP_FORM_GESTION_UTILISATEUR["id"], "nom", "Le nom doit comporter au moins 2 caractères significatifs !");
	}
	else if ($longueur > 40)
	{
		$erreurPresente = true;
		Form_SetError(APP_FORM_GESTION_UTILISATEUR["id"], "nom", "Le nom doit comporter au plus 40 caractères significatifs !");
	}
	if (($longueur=strlen($prenom = trim($donnees["prenom"]))) < 2)
	{
		$erreurPresente = true;
		Form_SetError(APP_FORM_GESTION_UTILISATEUR["id"], "prenom", "Le prénom doit comporter au moins 2 caractères significatifs !");
	}
	else if ($longueur > 40)
	{
		$erreurPresente = true;
		Form_SetError(APP_FORM_GESTION_UTILISATEUR["id"], "prenom", "Le prénom doit comporter au plus 40 caractères significatifs !");
	}
    if (($longueur=strlen($prenom = trim($donnees["telephone"]))) < 2)
    {
        $erreurPresente = true;
        Form_SetError(APP_FORM_GESTION_UTILISATEUR["id"], "telephone", "Le téléphone doit comporter au moins 2 caractères significatifs !");
    }
    else if ($longueur > 40)
    {
        $erreurPresente = true;
        Form_SetError(APP_FORM_GESTION_UTILISATEUR["id"], "telephone", "Le téléphone doit comporter au plus 40 caractères significatifs !");
    }
    if (($longueur=strlen($prenom = trim($donnees["telephone"]))) < 2)
    {
        $erreurPresente = true;
        Form_SetError(APP_FORM_GESTION_UTILISATEUR["id"], "dateNaissance", "La date de naissance doit comporter au moins 2 caractères significatifs !");
    }
    else if ($longueur > 40)
    {
        $erreurPresente = true;
        Form_SetError(APP_FORM_GESTION_UTILISATEUR["id"], "dateNaissance", "La date de naissance doit comporter au plus 40 caractères significatifs !");
    }
    if (($longueur=strlen($prenom = trim($donnees["adresse"]))) < 2)
    {
        $erreurPresente = true;
        Form_SetError(APP_FORM_GESTION_UTILISATEUR["id"], "adresse", "L'adresse doit comporter au moins 2 caractères significatifs !");
    }
    else if ($longueur > 40)
    {
        $erreurPresente = true;
        Form_SetError(APP_FORM_GESTION_UTILISATEUR["id"], "adresse", "L'adresse doit comporter au plus 40 caractères significatifs !");
    }
	if (!$erreurPresente)
	{
		$resultat = MySql_Value
		(
			"SELECT aevua_CreerJoueur(?,?,?,?,?);",
			array($login, $mdp, $email, $nom, $prenom),
			"!Erreur interne"
		);
		if (!Pdweb_IsInteger($resultat))
		{
			$erreurPresente = true;
			Form_SetError(APP_FORM_INSCRIPTION_JOUEUR["id"], "inscrire", trim(substr($resultat, 1)) . " !");
		}
		else
		{
			$idJoueur = (int)$resultat;
		}
	}
	if ($erreurPresente)
	{
		Form_SetValue(APP_FORM_INSCRIPTION_JOUEUR["id"], "login", $login);
		Form_SetValue(APP_FORM_INSCRIPTION_JOUEUR["id"], "mdp", $mdp);
		if ($donnees["confirmation_mdp"] == $mdp) Form_SetValue(APP_FORM_INSCRIPTION_JOUEUR["id"], "confirmation_mdp", $donnees["confirmation_mdp"]);
		Form_SetValue(APP_FORM_INSCRIPTION_JOUEUR["id"], "email", $email);
		Form_SetValue(APP_FORM_INSCRIPTION_JOUEUR["id"], "nom", $nom);
		Form_SetValue(APP_FORM_INSCRIPTION_JOUEUR["id"], "prenom", $prenom);
		Http_Redirect("*/");
	}
	else
	{
		App_RedirigerVersPage("AUTHENTIFICATION");
	}
}
?>