<?php
Pdweb_IncludeLib("pdweb.html.php");

define("APP_FORM_ILLUSTRATION", array
(
	"id" => "illustration",
	"url" => "*/.controleur.php",
	"elements" => array
	(
		array
		(
			"name" => "denomination",
			"type" => "text",
			"label" => "Dénomination :"
		),
		array
		(
			"name" => "image",
			"type" => "image",
			"label" => "Image :"
		),
		array
		(
			"name" => "action",
			"type" => "hidden",
			"value" => "Illustration/?1?"
		),
		array
		(
			"name" => "enregistrer",
			"type" => "submit",
			"value" => "?2? cette illustration"
		)
	)
));

function FormulaireAjout()
{
	return FormulaireAdapte(array("1" => "Ajouter", "2"=>"Ajouter"));
}

function FormulaireEdition($idIllustration, $denomination = null)
{
	$formulaire = FormulaireAdapte(array("1" => "Modifier", "2"=>"Modifier"));
	if (Form_GetValue($formulaire["id"], "denomination") === false)
	{
		Form_SetValue($formulaire["id"], "denomination", $denomination);
	}
	$formulaire["elements"][] = array
	(
		"name" => "id_illustration",
		"type" => "hidden",
		"value" => $idIllustration
	);
	return $formulaire;
}

function FormulaireAdapte($remplacements)
{
	$formulaire = APP_FORM_ILLUSTRATION;
	foreach ($formulaire["elements"] as $indice=>$element)
	{
		if (isset($formulaire["elements"][$indice]["value"]) && (strpos($valeur=$formulaire["elements"][$indice]["value"], "?") !== false))
		{
			foreach ($remplacements as $cle=>$texte)
			{
				$valeur = str_replace("?$cle?", $texte, $valeur);
			}
			$formulaire["elements"][$indice]["value"] = $valeur;
		}
	}
	return $formulaire;
}

function Illustration_AfficherListe()
{
	if (!App_EstAuteur()) Http_Redirect("*/");
	Form_ClearErrors(APP_FORM_ILLUSTRATION["id"]);
	Form_ClearValues(APP_FORM_ILLUSTRATION["id"]);
	Html_GenerateG("section", HTML_CONTENT, function ()
	{
		Html_GenerateG("table", "class", "crud", HTML_CONTENT, function()
		{
			Html_GenerateG("thead", HTML_CONTENT, function()
			{
				Html_GenerateG("tr", HTML_CONTENT, function()
				{
					Html_GenerateOC("th", HTML_CONTENT, "Dénomination");
					Html_GenerateOC("th", HTML_CONTENT, "Image");
					Html_GenerateOC("th", "colspan", 2, HTML_CONTENT, "Actions");
				});
			});
			Html_GenerateG("tbody", HTML_CONTENT, function()
			{
				foreach (MySql_Rows(
					"SELECT
						illustration.id AS id,
						illustration.denomination AS denomination,
						(SELECT COUNT(*) FROM plateau WHERE plateau.ref_illustration = illustration.id)
                        + (SELECT COUNT(*) FROM question WHERE question.ref_illustration = illustration.id) AS nombre_referencements
					FROM illustration INNER JOIN auteur ON illustration.ref_auteur = auteur.id
					WHERE ref_utilisateur = ?
					GROUP BY illustration.id, illustration.denomination
					ORDER BY illustration.denomination ASC", array(App_IdUtilisateur())) as $enregistrement)
				{
					Html_GenerateG("tr", HTML_CONTENT, function($enregistrement)
					{
						Html_GenerateOC("td", HTML_CONTENT, $enregistrement["denomination"]);
						Html_GenerateG("td", HTML_CONTENT, function($enregistrement)
						{
							GenererHtmlImage($enregistrement["id"]);
						}, $enregistrement);
						Html_GenerateG("td", HTML_CONTENT, function($enregistrement)
						{
							Html_GenerateOC("a", "href", Url_PathTo("*/.controleur.php?page=CRUD_IMAGE_EDITION&id=$enregistrement[id]"), "class", "bouton", "title", "Éditer cette illustration pour en modifier l'image et/ou la dénomination", HTML_CONTENT, "E");
						}, $enregistrement);
						$estReferencee = ((int)$enregistrement["nombre_referencements"] >= 1);
						$parametres = array("td");
						if ($estReferencee) $parametres = array_merge($parametres, array("title", "Cette illustration étant déjà référencée, elle ne peut pas être supprimée !"));
						$parametres = array_merge($parametres, array(HTML_CONTENT, function($enregistrement, $estReferencee)
						{
							if (!$estReferencee)
							{
								Html_GenerateOC("a", "href", Url_PathTo("*/.controleur.php?action=" . urlencode("Illustration/Supprimer") . "&id=$enregistrement[id]"), "class", "bouton", "title", "Supprimer cette illustration non encore utilisée", HTML_CONTENT, "S");
							}
						}, $enregistrement, $estReferencee));
						Html_GenerateG(...$parametres);
					}, $enregistrement);
				}
			});
			Html_GenerateG("tfoot", HTML_CONTENT, function()
			{
				Html_GenerateG("tr", HTML_CONTENT, function()
				{
					Html_GenerateG("td", "colspan", 4, HTML_CONTENT, function()
					{
						Html_GenerateOC("a", "href", Url_PathTo("*/.controleur.php?page=CRUD_IMAGE_AJOUT"), "class", "bouton", "title", "Ajouter une nouvelle illustration", HTML_CONTENT, "Ajouter une illustration");
					});
				});
			});
		});
	});
}

function GenererHtmlImage($idIllustration)
{
	$url = Url_PathTo("*/res/illustration/$idIllustration.png");
	if ($url !== false)
	{
		Html_GenerateG("a", "href", $url, "target", "ILLUSTRATION_$idIllustration", HTML_CONTENT, function($url)
		{
			Html_GenerateA("img", "src", $url);
		}, $url);
	}
	else
	{
		Html_GenerateOC("span", HTML_CONTENT, "Image manquante !");
	}
}

function Illustration_AfficherAjout()
{
	if (!App_EstAuteur()) Http_Redirect("*/");
	Html_GenerateG("section", HTML_CONTENT, function ()
	{
		Html_GenerateForm(FormulaireAjout());
		Html_GenerateOC("a", "href", Url_PathTo("*/.controleur.php?page=CRUD_IMAGE"), "class", "bouton", "title", "Ajouter une nouvelle illustration", HTML_CONTENT, "Retourner à la liste des illustrations");
	});
}

function Illustration_AfficherEdition()
{
	if (!App_EstAuteur()) Http_Redirect("*/");
	if (!isset($_GET["id"])
		|| (($denomination = MySql_Value(
				"SELECT denomination FROM illustration WHERE (ref_auteur = ?) AND (id = ?)",
				array(App_IdAuteur(), $idIllustration=$_GET["id"]),
				false)) === false))
	{
		Http_Redirect("*/");
	}
	Html_GenerateG("section", HTML_CONTENT, function ($idIllustration, $denomination)
	{
		GenererHtmlImage($idIllustration);
		Html_GenerateForm(FormulaireEdition($idIllustration, $denomination));
		Html_GenerateOC("a", "href", Url_PathTo("*/.controleur.php?page=CRUD_IMAGE"), "class", "bouton", "title", "Ajouter une nouvelle illustration", HTML_CONTENT, "Retourner à la liste des illustrations");
	}, $idIllustration, $denomination);
}

function Illustration_Ajouter($donnees)
{
	EnregistrerIllustration(FormulaireAjout(), 0, $donnees);
}

function Illustration_Modifier($donnees)
{
	if (!isset($donnees["id_illustration"])) Http_Redirect("*/");
	$idIllustration = $donnees["id_illustration"];
	if (MySql_Value(
				"SELECT COUNT(*) FROM illustration WHERE (ref_auteur = ?) AND (id = ?)",
				array(App_IdAuteur(), $idIllustration),
				0) == 0) Http_Redirect("*/");
	EnregistrerIllustration(FormulaireEdition($donnees["id_illustration"]), $idIllustration, $donnees);
}

function EnregistrerIllustration($formulaire, $idIllustration, $donnees)
{
	if (!App_EstAuteur()) Http_Redirect("*/");
	if (!isset($donnees["denomination"], $donnees["image"])) Http_Redirect("*/");
	$estEnAjout = ($idIllustration == 0);
	Form_ClearErrors($formulaire["id"]);
	Form_ClearValues($formulaire["id"]);
	$erreurPresente = false;
	if (($longueur=strlen($denomination = trim($donnees["denomination"]))) < 1)
	{
		$erreurPresente = true;
		Form_SetError($formulaire["id"], "denomination", "La dénomination doit comporter au moins un caractère significatif !");
	}
	else if ($longueur > 80)
	{
		$erreurPresente = true;
		Form_SetError($formulaire["id"], "denomination", "La dénomination doit comporter au plus 80 caractères significatifs !");
	}
	else if (MySql_Value(
		"SELECT COUNT(*) FROM illustration WHERE (id <> ?) AND (ref_auteur = ?) AND (denomination = ?);",
		array($idIllustration, App_IdAuteur(), $denomination),
		0) == 1)
	{
		$erreurPresente = true;
		Form_SetError($formulaire["id"], "denomination", "Vous avez déjà utilisé cette dénomination pour une autre illustration !");
	}
	$imagePresente = isset($donnees["image"]) && ($donnees["image"]["size"] > 0);
	if ($estEnAjout || $imagePresente)
	{
		if ($donnees["image"]["size"] < 16)
		{
			$erreurPresente = true;
			Form_SetError($formulaire["id"], "image", "Le fichier d'image doit faire au moins 16 bytes !");
		}
		else if ($donnees["image"]["size"] > (4 * 1024 * 1024))
		{
			$erreurPresente = true;
			Form_SetError($formulaire["id"], "image", "Le fichier d'image doit faire au plus 4 MB (" . (4 * 1024 * 1024) . " bytes) !");
		}
	}
	if (!$erreurPresente)
	{
		if ($estEnAjout)
		{
			$resultat = MySql_Execute("INSERT INTO illustration SET ref_auteur = ?, denomination = ?;", array(App_IdAuteur(), $denomination));
		}
		else
		{
			$resultat = MySql_Execute("UPDATE illustration SET denomination = ? WHERE id = ?;", array($denomination, $idIllustration));
		}
		if (!Pdweb_IsInteger($resultat))
		{
			$erreurPresente = true;
			Form_SetError($formulaire["id"], "enregistrer", "Erreur interne !");
		}
		else
		{
			if ($estEnAjout) $idIllustration = (int)$resultat;
			if ($estEnAjout || $imagePresente)
			{
				if ((($nomFichier = Url_PathTo("*/res/illustration/$idIllustration.png", false)) === false)
					|| !@move_uploaded_file($donnees["image"]["tmp_name"], $nomFichierTemp = $nomFichier .".tmp")
					|| (($infoImage = getimagesize($nomFichierTemp)) === false))
				{
					$erreurPresente = true;
					Form_SetError($formulaire["id"], "image", "Erreur interne pour le chargement du fichier d'image !");
				}
				else if (!in_array($infoImage["mime"], array("image/png", "image/gif", "image/jpeg")))
				{
					$erreurPresente = true;
					Form_SetError($formulaire["id"], "image", "Format non supporté pour cette image !");
				}
				else if ($infoImage[0] < 20)
				{
					$erreurPresente = true;
					Form_SetError($formulaire["id"], "image", "Une image d'illustration doit faire au moins 20 pixels de large !");
				}
				else if ($infoImage[0] > 1000)
				{
					$erreurPresente = true;
					Form_SetError($formulaire["id"], "image", "Une image d'illustration doit faire au plus 1000 pixels de large !");
				}
				else if ($infoImage[1] < 20)
				{
					$erreurPresente = true;
					Form_SetError($formulaire["id"], "image", "Une image d'illustration doit faire au moins 20 pixels de haut !");
				}
				else if ($infoImage[1] > 1000)
				{
					$erreurPresente = true;
					Form_SetError($formulaire["id"], "image", "Une image d'illustration doit faire au plus 1000 pixels de haut !");
				}
			}
			if ($erreurPresente)
			{
				if ($estEnAjout || $imagePresente) @unlink($nomFichierTemp);
				if ($estEnAjout) MySql_Execute("DELETE FROM illustration WHERE id = ?", array($idIllustration));
			}
			else if ($estEnAjout || $imagePresente) 
			{
				@unlink($nomFichier);
				rename($nomFichierTemp, $nomFichier);
			}
		}
	}
	if ($erreurPresente)
	{
		Form_SetValue($formulaire["id"], "denomination", $denomination);
		if ($estEnAjout)
			App_RedirigerVersPage("CRUD_IMAGE_AJOUT");
		else
			App_RedirigerVersPage("CRUD_IMAGE_EDITION", "id", $idIllustration);
	}
	else
	{
		App_CreerFlashInfo("Votre illustration \"$denomination\" a bien été " . ($estEnAjout ? "chargée dans votre collection d'images." : "modifiée."));
		App_RedirigerVersPage("CRUD_IMAGE");
	}
}

function Illustration_Supprimer($donnees)
{
	if (!isset($donnees["id"])) Http_Redirect("*/");
	$idIllustration = $donnees["id"];
	if (MySql_Execute(
		"DELETE FROM illustration WHERE (ref_auteur = ?) AND (id = ?)",
		array(App_IdAuteur(), $idIllustration)))
	{
		$nomFichier = Url_PathTo("*/res/illustration/$idIllustration.png");
		if ($nomFichier !== false) @unlink($nomFichier);
		App_CreerFlashInfo("Votre illustration a bien été supprimée.");
	}
	App_RedirigerVersPage("CRUD_IMAGE");
}
?>