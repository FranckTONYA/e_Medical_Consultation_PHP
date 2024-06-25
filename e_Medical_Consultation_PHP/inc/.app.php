<?php
MySql_SetConnection(APP_MYSQL_USERNAME, APP_MYSQL_PASSWORD, APP_MYSQL_DBNAME, defined("APP_MYSQL_SERVER") ? APP_MYSQL_SERVER : null);

function App_DefinirRole($role)
{
	if (($role !== APP_ROLE_VISITEUR) && ($role !== APP_ROLE_JOUEUR) && ($role !== APP_ROLE_AUTEUR)) return false;
	Session_CheckAvailability();
	$_SESSION["utilisateur"] = array();
	$_SESSION["utilisateur"]["role"] = $role;
	return true;
}

function App_RoleActuel()
{
	Session_CheckAvailability();
	if (!isset($_SESSION["utilisateur"], $_SESSION["utilisateur"]["role"]))
	{
		App_DefinirRole(APP_ROLE_VISITEUR);
	}
	return $_SESSION["utilisateur"]["role"];
}

function App_ALeRole($role)
{
	return (App_RoleActuel() === $role);
}

function App_EstVisiteur()
{
	return App_ALeRole(APP_ROLE_VISITEUR);
}

function App_EstJoueur()
{
	return App_ALeRole(APP_ROLE_JOUEUR);
}

function App_EstAuteur()
{
	return App_ALeRole(APP_ROLE_AUTEUR);
}

function App_PageEstAccessible($idPage)
{
	if (!is_string($idPage) || !isset(APP_MENU[$idPage])) return false;
	Session_CheckAvailability();
	if (!in_array(App_RoleActuel(), APP_MENU[$idPage]["acces"])) return false;
	return true;
}

function App_Page($idPage)
{
	if (!is_string($idPage) || !isset(APP_MENU[$idPage])) return false;
	return APP_MENU[$idPage];
}

function App_DefinirPage($idPage)
{
	if (!is_string($idPage) || !isset(APP_MENU[$idPage])) return false;
	Session_CheckAvailability();
	if (!isset($_SESSION["page"]) || !is_array($_SESSION["page"])) $_SESSION["page"] = array();
	if (!in_array(App_RoleActuel(), APP_MENU[$idPage]["acces"])) return false;
	$_SESSION["page"] = APP_MENU[$idPage];
	return true;
}

function App_ReinitialiserPage()
{
	Session_CheckAvailability();
	unset($_SESSION["page"]);
	return true;
}

function App_IdPageActuelle()
{
	Session_CheckAvailability();
	if (!isset($_SESSION["page"]) || !is_array($_SESSION["page"]))
	{
		unset($_SESSION["page"]);
		foreach (APP_MENU as $page)
		{
			if (in_array(App_RoleActuel(), $page["acces"]))
			{
				$_SESSION["page"] = $page;
				break;
			}
		}
		if (!isset($_SESSION["page"])) die("<p>Il n'existe aucun point du menu accessible au rôle d'utilisateur de type '" . App_RoleActuel() . "' !</p>");
	}
	return $_SESSION["page"]["id"];
}

function App_PageActuelle()
{
	return APP_MENU[App_IdPageActuelle()];
}

function App_EstPageActuelle($idPage)
{
	return (App_IdPageActuelle() === $idPage);
}

function App_SousTitreActuel()
{
	Session_CheckAvailability();
	if (!isset($_SESSION["page"]) || !is_array($_SESSION["page"])) die("<p>La page actuelle n'a pas été définie !</p>");
	return isset($_SESSION["page"]["sousTitre"]) && is_string($_SESSION["page"]["sousTitre"]) && !empty($_SESSION["page"]["sousTitre"])
	     ? $_SESSION["page"]["sousTitre"]
		 : false;
}

function App_GenerateurCorpsPage()
{
	Session_CheckAvailability();
	if (!isset($_SESSION["page"]) || !is_array($_SESSION["page"])) die("<p>La page actuelle n'a pas été définie !</p>");
	$parties = explode("/", $_SESSION["page"]["corpsPage"]);
	if (count($parties) != 2) die("<p>Le générateur de corps de page n'est pas correctement défini !</p>");
	$nomFichier = "*/inc/" . strtolower($parties[0]) . ".php";
	if (!Pdweb_Include($nomFichier)) die("<p>Le générateur de corps de page fait référence à une partie dont le fichier n'existe pas !</p>");
	$nomFonction = $parties[0] . "_" . $parties[1];
	if (!is_callable($nomFonction)) die("<p>Le générateur de corps de page fait référence à une fonction inexistante !</p>");
	return $nomFonction;
}

function App_RedirigerVersPage($idPage, ...$params)
{
	if (!App_PageEstAccessible($idPage)) die("<p>La page $idPage n'est pas définie, ou n'est pas accessible au role " . App_RoleActuel() . " !</p>");
	$parametresGet = "";
	$cle = false;
	foreach ($params as $valeur)
	{
		if ($cle === false)
		{
			$cle = $valeur;
		}
		else
		{
			$parametresGet .= "&" . urlencode($cle) . "=" . urlencode($valeur);
			$cle = false;
		}
	}
	Http_Redirect("*/.controleur.php?page=$idPage$parametresGet");
}

function App_ConnecterUtilisateur($infoUtilisateur)
{
	if (!is_array($infoUtilisateur) || empty($infoUtilisateur)) return false;
	if (!isset($infoUtilisateur["role"]))
	{
		if (App_EstVisiteur()) return false;
		$role = App_RoleActuel();
		Session_CheckAvailability();
		$_SESSION["utilisateur"] = $infoUtilisateur;
		$_SESSION["utilisateur"]["role"] = $role;
	}
	else
	{
		if (!App_DefinirRole($infoUtilisateur["role"])) return false;
		Session_CheckAvailability();
		$_SESSION["utilisateur"] = $infoUtilisateur;
	}
	return true;
}

function App_DenominationUtilisateur()
{
	if (App_EstVisiteur()) return false;
	Session_CheckAvailability();
	return $_SESSION["utilisateur"]["denomination"];
}

function App_IdUtilisateur()
{
	if (App_EstVisiteur()) return false;
	Session_CheckAvailability();
	return $_SESSION["utilisateur"]["id"];
}

function App_IdJoueur()
{
	if (!App_EstJoueur()) return false;
	Session_CheckAvailability();
	return $_SESSION["utilisateur"]["idJoueur"];
}

function App_IdAuteur()
{
	if (!App_EstAuteur()) return false;
	Session_CheckAvailability();
	return $_SESSION["utilisateur"]["idAuteur"];
}

function App_AfficherFlashInfo()
{
	if (!isset($_SESSION["flash_info"])) return true;
	if (is_string($_SESSION["flash_info"])) $_SESSION["flash_info"] = array($_SESSION["flash_info"]);
	if (is_array($_SESSION["flash_info"]))
	{
		foreach ($_SESSION["flash_info"] as $info)
		{
			Html_GenerateOC("div", "class", "flash_info", HTML_CONTENT, $info);
		}
	}
	unset($_SESSION["flash_info"]);
	return true;
}

function App_CreerFlashInfo($info)
{
	if (!is_string($info) || empty($info=trim($info))) return false;
	if (isset($_SESSION["flash_info"]))
	{
		if (!is_array($_SESSION["flash_info"])) $_SESSION["flash_info"] = array($_SESSION["flash_info"]);
		$_SESSION["flash_info"][] = $info;
	}
	else
	{
		$_SESSION["flash_info"] = $info;
	}
	return true;
}
?>