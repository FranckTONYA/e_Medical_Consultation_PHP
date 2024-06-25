<?php
include("index.php");
Pdweb_Include("*/inc/.session.php");
(function()
{
	if (isset($_GET["action"]))
	{
		$_POST = $_GET;
	}
	if (empty($_POST))
	{
		if (isset($_GET["page"]))
		{
			App_ReinitialiserPage();
			if (!App_DefinirPage($_GET["page"]))
			{
				App_DefinirPage(App_IdPageActuelle()); // Suite à l'appel de App_ReinitialiserPage, App_IdPageActuelle retourne l'identifiant de la première page disponible en fonction des droits d'accès de l'utilisateur "identifié"
			}
			$urlVue = App_Page(App_IdPageActuelle())["urlVue"];
			unset($_GET["page"]);
			$parametresGet = "";
			foreach ($_GET as $cle=>$valeur)
			{
				$parametresGet .= ($parametresGet == "") ? "?" : "&";
				$parametresGet .= urlencode($cle) . "=" . urlencode($valeur);
			}
			Http_Redirect("$urlVue$parametresGet");
		}
		else
		{
			App_ReinitialiserPage();
			Http_Redirect("*/");
		}
	}
	else
	{
		if (isset($_POST["action"]) && !empty($action=trim($_POST["action"]))
			&& (count($parties=explode("/", $action)) == 2)
			&& Pdweb_Include("*/inc/" . strtolower($parties[0]) . ".php")
			&& is_callable($nomFonction=$parties[0] . "_" . $parties[1])
			)
		{
			unset($_POST["action"]);
			$donnees = array_merge($_POST, $_FILES);
			//$_FILES = array();
			$nomFonction($donnees);
			die("KO");
		}
		Http_Redirect("*/");
	}
})();
?>