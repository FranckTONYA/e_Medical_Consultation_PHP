<?php
Pdweb_IncludeLib("pdweb.html.php");

define("APP_FORM_INSCRIPTION_JOUEUR", array
(
	"id" => "inscription_joueur",
	"url" => "*/.controleur.php",
	"elements" => array
	(
		array
		(
			"name" => "login",
			"type" => "text",
			"label" => "Login :"
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
			"name" => "email",
			"type" => "text",
			"label" => "Adresse e-mail :"
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
			"label" => "Pr�nom :"
		),
		array
		(
			"name" => "action",
			"type" => "hidden",
			"value" => "Inscription/InscrireJoueur"
		),
		array
		(
			"name" => "inscrire",
			"type" => "submit",
			"value" => "S'inscrire en tant que joueur"
		)
	)
));

define("APP_FORM_INSCRIPTION_AUTEUR", array
(
	"id" => "inscription_auteur",
	"url" => "*/.controleur.php",
	"elements" => array
	(
		array
		(
			"name" => "login",
			"type" => "text",
			"label" => "Login :"
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
			"name" => "email",
			"type" => "text",
			"label" => "Adresse e-mail :"
		),
		array
		(
			"name" => "denomination",
			"type" => "text",
			"label" => "D�nomination :"
		),
		array
		(
			"name" => "action",
			"type" => "hidden",
			"value" => "Inscription/InscrireAuteur"
		),
		array
		(
			"name" => "inscrire",
			"type" => "submit",
			"value" => "S'inscrire en tant qu'auteur"
		)
	)
));

function Inscription_Afficher()
{
	Html_GenerateG("section", HTML_CONTENT, function ()
	{
		Html_GenerateForm(APP_FORM_INSCRIPTION_JOUEUR);
		Html_GenerateForm(APP_FORM_INSCRIPTION_AUTEUR);
	});
}

function Inscription_InscrireJoueur($donnees)
{
	if (!App_EstVisiteur()) Http_Redirect("*/");
	if (!isset($donnees["email"], $donnees["login"], $donnees["mdp"], $donnees["confirmation_mdp"], $donnees["nom"], $donnees["prenom"])) Http_Redirect("*/");
	Form_ClearErrors(APP_FORM_INSCRIPTION_JOUEUR["id"]);
	Form_ClearValues(APP_FORM_INSCRIPTION_JOUEUR["id"]);
	$erreurPresente = false;
	if (($longueur=strlen($login = trim($donnees["login"]))) < 4)
	{
		$erreurPresente = true;
		Form_SetError(APP_FORM_INSCRIPTION_JOUEUR["id"], "login", "Le login doit comporter au moins 4 caract�res significatifs !");
	}
	else if ($longueur > 40)
	{
		$erreurPresente = true;
		Form_SetError(APP_FORM_INSCRIPTION_JOUEUR["id"], "login", "Le login doit comporter au plus 40 caract�res significatifs !");
	}
	if (($longueur=strlen($mdp = $donnees["mdp"])) < 3)
	{
		$erreurPresente = true;
		Form_SetError(APP_FORM_INSCRIPTION_JOUEUR["id"], "mdp", "Le mot de passe doit comporter au moins 3 caract�res !");
	}
	if ($donnees["confirmation_mdp"] != $donnees["mdp"])
	{
		$erreurPresente = true;
		Form_SetError(APP_FORM_INSCRIPTION_JOUEUR["id"], "confirmation_mdp", "La confirmation de mot de passe ne co�ncide pas avec le mot de passe encod� !");
	}
	if (($longueur=strlen($email = trim($donnees["email"]))) < 6)
	{
		$erreurPresente = true;
		Form_SetError(APP_FORM_INSCRIPTION_JOUEUR["id"], "email", "L'adresse e-mail doit comporter au moins 6 caract�res significatifs !");
	}
	else if ($longueur > 120)
	{
		$erreurPresente = true;
		Form_SetError(APP_FORM_INSCRIPTION_JOUEUR["id"], "email", "L'adresse e-mail doit comporter au plus 120 caract�res significatifs !");
	}
	else if (filter_var($email, FILTER_VALIDATE_EMAIL) === false)
	{
		$erreurPresente = true;
		Form_SetError(APP_FORM_INSCRIPTION_JOUEUR["id"], "email", "L'adresse e-mail ne semble pas valide !");
	}
	if (($longueur=strlen($nom = trim($donnees["nom"]))) < 2)
	{
		$erreurPresente = true;
		Form_SetError(APP_FORM_INSCRIPTION_JOUEUR["id"], "nom", "Le nom doit comporter au moins 2 caract�res significatifs !");
	}
	else if ($longueur > 40)
	{
		$erreurPresente = true;
		Form_SetError(APP_FORM_INSCRIPTION_JOUEUR["id"], "nom", "Le nom doit comporter au plus 40 caract�res significatifs !");
	}
	if (($longueur=strlen($prenom = trim($donnees["prenom"]))) < 2)
	{
		$erreurPresente = true;
		Form_SetError(APP_FORM_INSCRIPTION_JOUEUR["id"], "prenom", "Le pr�nom doit comporter au moins 2 caract�res significatifs !");
	}
	else if ($longueur > 40)
	{
		$erreurPresente = true;
		Form_SetError(APP_FORM_INSCRIPTION_JOUEUR["id"], "prenom", "Le pr�nom doit comporter au plus 40 caract�res significatifs !");
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