<?php
if (count(get_included_files()) == 1)
{
	// Commande pour (re)installer le fichier index.php dans toute l'arborescense du site :
	//   http...../index.php?setup_pdweb
	if (isset($_GET["setup_pdweb"]))
	{
		if (file_exists("./.config.php"))
		{
			(function()
			{
				$exploreFctn = function(&$fctn, $folderPath)
				{
					$hasFolder = false;
					$filepaths = glob("$folderPath/*", GLOB_ONLYDIR);
					foreach ($filepaths as $filepath)
					{
						if (($filepath != ".") && ($filepath != ".."))
						{
							if (!$hasFolder)
							{
								$hasFolder = true;
								print("<ul>");
							}
							print("<li>");
							if (@copy("./index.php", "$filepath/index.php"))
							{
								print("<p>index.php has been installed into $filepath</p>");
							}
							else
							{
								print("<p>Error : can't copy index.php into $filepath !</p>");
							}
							$fctn($fctn, $filepath);
							print("</li>");
						}
					}
					if ($hasFolder) print("</ul>");
				};
				$exploreFctn($exploreFctn, ".");
			})();
			print("<script>window.setTimeout('document.location.href = \"./\";', 10000);</script>");
			die();
		}
	}
	if (!file_exists("index.content.php"))
	{
		if (file_exists(".config.php")) die("<p>La racine du site, marquée par la présence du fichier de configuration de la bibliothèque PDWEB, ne contient pas de contenu d'accueil (index.content.php) !</p>");
		header("location:../");
		die();
	}
}
(function()
{
	$indexIsHttpRequested = (count(get_included_files()) == 1);
	$pathToRoot = "./";
	for ($remainingJump = substr_count(str_replace("\\", "/", get_included_files()[0]), "/") - 1; $remainingJump > 0; $remainingJump--)
	{
		if (file_exists($pathToRoot . ".config.php"))
		{
			include_once($pathToRoot . ".config.php");
			if (!defined("PDWEB_CHARSET")) die("<p>Le fichier de configuration de la bibliothèque PDWEB ne définit pas le jeu de caractères utilisé par le site !</p>");
			if (!defined("PDWEB_LIB_PATH")) die("<p>Le fichier de configuration de la bibliothèque PDWEB n'a pas été défini dans le chemin d'accès à cette bibliothèque !</p>");
			if (empty(PDWEB_LIB_PATH)) die();
			$pathToPdweb = $pathToRoot . ((PDWEB_LIB_PATH[0] == "/") ? substr(PDWEB_LIB_PATH, 1) : PDWEB_LIB_PATH);
			if (substr($pathToPdweb, -1) != "/") $pathToPdweb .= "/";
			$mainPdwebUrl = $pathToPdweb . "pdweb.php";
			if (!file_exists($mainPdwebUrl)) die("<p>Le fichier de configuration de la bibliothèque PDWEB a défini un chemin d'accès incorrect vers cette bibliothèque !</p>");
			define("PDWEB_PATH_TO_ROOT", $pathToRoot);
			define("PDWEB_PATH_TO_LIB", $pathToPdweb);
			include_once($mainPdwebUrl);
			break;
		}
		if ($pathToRoot == "./") $pathToRoot = "";
		$pathToRoot .= "../";
	}
	if (!isset($pathToPdweb)) die("<p>Le fichier de configuration de la bibliothèque PDWEB n'est pas présent dans la racine du site !</p>");
	Pdweb_Include("*/inc/.menu_et_securite.php");
	if ($indexIsHttpRequested) include("index.content.php");
})();
?>