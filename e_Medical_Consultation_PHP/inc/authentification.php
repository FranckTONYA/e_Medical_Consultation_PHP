<?php
Pdweb_IncludeLib("pdweb.html.php");

define("APP_FORM_AUTHENTIFICATION", array
(
	"id" => "authentification",
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
			"name" => "action",
			"type" => "hidden",
			"value" => "Authentification/Authentifier"
		),
		array
		(
			"name" => "authentifier",
			"type" => "submit",
			"value" => "S'authentifier"
		)
	)
));

define("APP_FORM_RECUPERATION", array
(
	"id" => "recuperation",
	"url" => "*/.controleur.php",
	"elements" => array
	(
		array
		(
			"name" => "email",
			"type" => "text",
			"label" => "Adresse e-mail (de récupération) :"
		),
		array
		(
			"name" => "action",
			"type" => "hidden",
			"value" => "Authentification/DemanderRecuperation"
		),
		array
		(
			"name" => "recuperer_login",
			"type" => "submit",
			"value" => "Récupérer le login oublié"
		),
		array
		(
			"name" => "recuperer_mdp",
			"type" => "submit",
			"value" => "Demander le changement du mot de passe oublié"
		)
	)
));

define("APP_FORM_CHANGEMENT_MDP", array
(
	"id" => "changement_mdp",
	"url" => "*/.controleur.php",
	"elements" => array
	(
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
		/*array
		(
			"name" => "token",
			"type" => "hidden",
			"value" => isset($_SESSION["changement_mdp_token"]) ? $_SESSION["changement_mdp_token"] : ""
		),*/
		array
		(
			"name" => "action",
			"type" => "hidden",
			"value" => "Authentification/ModifierMdp"
		),
		array
		(
			"name" => "modifier_mdp",
			"type" => "submit",
			"value" => "Modifier le mot de passe"
		)
	)
));

function Authentification_Afficher()
{
	Html_GenerateG("section", HTML_CONTENT, function ()
	{
		Html_GenerateForm(APP_FORM_AUTHENTIFICATION);
		Html_GenerateForm(APP_FORM_RECUPERATION);
	});
}

function Authentification_Authentifier($donnees)
{
	if (!App_EstVisiteur()) Http_Redirect("*/");
	if (!isset($donnees["login"], $donnees["mdp"])) Http_Redirect("*/");
	Form_ClearErrors(APP_FORM_AUTHENTIFICATION["id"]);
	$login = trim($donnees["login"]);
	$resultat = MySql_Value
	(
		"SELECT aevua_Authentifier(?,?,?,?);",
		array($login, $donnees["mdp"], 3, 30),
		"!Erreur interne"
	);
	if (!Pdweb_IsInteger($resultat))
	{
		Form_SetError(APP_FORM_AUTHENTIFICATION["id"], "authentifier", trim(substr($resultat, 1)) . " !");
		Form_SetValue(APP_FORM_AUTHENTIFICATION["id"], "login", $login);
	}
	else if (($idUtilisateur = (int)$resultat) <= 0)
	{
		Form_SetError(APP_FORM_AUTHENTIFICATION["id"], "authentifier", "Le login ou le mot de passe ne semblent pas valides !");
		Form_SetValue(APP_FORM_AUTHENTIFICATION["id"], "login", $login);
		Http_Redirect("*/");
	}
	else
	{
		if (!is_array($enreg = MySql_Row("CALL aevua_InfoUtilisateur(?);", array($idUtilisateur)))
			|| (Pdweb_RemoveIndexedItems($enreg) === false)
			|| !App_ConnecterUtilisateur($enreg))
		{
			Form_SetError(APP_FORM_AUTHENTIFICATION["id"], "authentifier", "Erreur interne !");
			Http_Redirect("*/");
		}
		Form_ClearValues(APP_FORM_AUTHENTIFICATION["id"]);
		App_RedirigerVersPage("TABLEAU_DE_BORD");
	}
}

function Authentification_Deconnecter()
{
	if (App_EstVisiteur()) Http_Redirect("*/");
	App_DefinirRole(APP_ROLE_VISITEUR);
	App_RedirigerVersPage("ACCUEIL");
}

function Authentification_DemanderRecuperation($donnees)
{
	if (!App_EstVisiteur()) Http_Redirect("*/");
	if (!isset($donnees["email"])) Http_Redirect("*/");
	if (isset($donnees["recuperer_login"])) Authentification_RecupererLogin($donnees["email"]);
	if (isset($donnees["recuperer_mdp"])) Authentification_DemanderChangerMdp($donnees["email"]);
	Http_Redirect("*/");
}

function Authentification_RecupererLogin($email)
{
	Form_ClearErrors(APP_FORM_RECUPERATION["id"]);
	if (!is_string($email) || empty($email))
	{
		Form_SetError(APP_FORM_RECUPERATION["id"], "email", "L'adresse e-mail doit être définie !");
		Http_Redirect("*/");
	}
	if (!filter_var($email, FILTER_VALIDATE_EMAIL))
	{
		Form_SetError(APP_FORM_RECUPERATION["id"], "email", "Le format de cette adresse e-mail n'est pas valide !");
		Http_Redirect("*/");
	}
	if (($login = MySql_Value("SELECT login FROM utilisateur WHERE email = ?", array($email))) === false)
	{
		Form_SetError(APP_FORM_RECUPERATION["id"], "email", "Erreur interne !");
		Http_Redirect("*/");
	}
	if ($login !== null)
	{
		Pdweb_SendMail($email, "Récupération de votre login", "<p>A priori, vous avez effectué une demande de récupération de votre login pour notre site.</p><p>Si vous n'êtes pas l'auteur de cette demande, il vous suffit d'ignorer ce message.</p><p>Vore login est : $login</p>");
	}
	Form_ClearValues(APP_FORM_RECUPERATION["id"]);
	App_CreerFlashInfo("Vous pouvez consulter votre boîte e-mail pour y trouver un courrier avec votre login...");
	Http_Redirect("*/");
}

function Authentification_DemanderChangerMdp($email)
{
	Form_ClearErrors(APP_FORM_RECUPERATION["id"]);
	if (!is_string($email) || empty($email))
	{
		Form_SetError(APP_FORM_RECUPERATION["id"], "email", "L'adresse e-mail doit être définie !");
		Http_Redirect("*/");
	}
	if (!filter_var($email, FILTER_VALIDATE_EMAIL))
	{
		Form_SetError(APP_FORM_RECUPERATION["id"], "email", "Le format de cette adresse e-mail n'est pas valide !");
		Http_Redirect("*/");
	}
	if (($mailToken = MySql_Value("SELECT aevua_ObtenirMailToken(?)", array($email))) === false)
	{
		Form_SetError(APP_FORM_RECUPERATION["id"], "email", "Erreur interne !");
		Http_Redirect("*/");
	}
	if ($mailToken !== null)
	{
		$url = Pdweb_ExternalUrl("*/.controleur.php");
		if ($url === false)
		{
			Form_SetError(APP_FORM_RECUPERATION["id"], "recuperer_mdp", "Erreur interne !");
			Http_Redirect("*/");
		}
		$lien = "$url?action=" . urlencode("Authentification/ChangerMdp") . "&token=" . urlencode($mailToken);
		Pdweb_SendMail($email, "Demande de réinitialisation de votre mot de passe", "<p>A priori, vous avez effectué une demande de réinitialisation de votre mot de passe pour notre site.</p><p>Si vous n'êtes pas l'auteur de cette demande, il vous suffit d'ignorer ce message.</p><p>Par contre, pour passer à la modification de votre mot de passe, veuillez cliquer sur ce lien : <a href=\"$lien\" target=\"_blank\">$lien</a></p>");
	}
	Form_ClearValues(APP_FORM_RECUPERATION["id"]);
	App_CreerFlashInfo("Vous pouvez consulter votre boîte e-mail où vous devriez y trouver un courrier avec un lien de réinitialisation de votre mot de passe...");
	Http_Redirect("*/");
}

function Authentification_ChangerMdp($donnees)
{
	if (!App_EstVisiteur()) Http_Redirect("*/");
	if (!isset($donnees["token"])) Http_Redirect("*/");
	if (MySql_Value("SELECT COUNT(*) FROM utilisateur WHERE mailtoken = ?", array($donnees["token"]), 0) != 1)
	{
		App_RedirigerVersPage("ACCUEIL");
	}
	$_SESSION["changement_mdp_token"] = $donnees["token"];
	App_RedirigerVersPage("AUTHENTIFICATION_MDP");
}

function Authentification_AfficherMdp()
{
	if (!isset($_SESSION["changement_mdp_token"])) App_RedirigerVersPage("ACCUEIL");
	Html_GenerateG("section", HTML_CONTENT, function ()
	{
		Html_GenerateForm(APP_FORM_CHANGEMENT_MDP);
	});
}

function Authentification_ModifierMdp($donnees)
{
	if (!App_EstVisiteur()) Http_Redirect("*/");
	if (!isset($donnees["mdp"], $donnees["confirmation_mdp"]/*, $donnees["token"]*/)) Http_Redirect("*/");
	Form_ClearErrors(APP_FORM_CHANGEMENT_MDP["id"]);
	Form_ClearValues(APP_FORM_CHANGEMENT_MDP["id"]);
	$erreurPresente = false;
	if (($longueur=strlen($mdp = $donnees["mdp"])) < 3)
	{
		$erreurPresente = true;
		Form_SetError(APP_FORM_CHANGEMENT_MDP["id"], "mdp", "Le mot de passe doit comporter au moins 3 caractères !");
	}
	if ($donnees["confirmation_mdp"] != $donnees["mdp"])
	{
		$erreurPresente = true;
		Form_SetError(APP_FORM_CHANGEMENT_MDP["id"], "confirmation_mdp", "La confirmation de mot de passe ne coïncide pas avec le mot de passe encodé !");
	}
	if (!$erreurPresente)
	{
		if (MySql_Execute
		(
			"UPDATE utilisateur SET motdepasse = aevua_CrypterMotDePasse(?), mailtoken = NULL WHERE mailtoken = ?;",
			array($mdp, $_SESSION["changement_mdp_token"])
		) === false)
		{
			$erreurPresente = true;
			Form_SetError(APP_FORM_CHANGEMENT_MDP["id"], "modifier_mdp", "Erreur interne !");
		}
	}
	if ($erreurPresente)
	{
		//Form_SetValue(APP_FORM_CHANGEMENT_MDP["id"], "mdp", $mdp);
		//Form_SetValue(APP_FORM_CHANGEMENT_MDP["id"], "confirmation_mdp", $donnees["confirmation_mdp"]);
		Http_Redirect("*/");
	}
	else
	{
		unset($_SESSION["changement_mdp_token"]);
		App_RedirigerVersPage("AUTHENTIFICATION");
	}
}
?>