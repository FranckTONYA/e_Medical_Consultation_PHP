<?php
Pdweb_IncludeLib("pdweb.html.php");
$titreComplet = (App_SousTitreActuel() !== false) ? APP_TITRE . " - " . App_SousTitreActuel() : APP_TITRE;
Html_GenerateDocument($titreComplet, APP_JS, APP_CSS, array
(
	"header" => array(function($titreComplet)
				{
					Html_GenerateOC("h1", HTML_CONTENT, $titreComplet);
					Html_GenerateG("nav", HTML_CONTENT, function()
					{
						Html_GenerateG("ul", HTML_CONTENT, function()
						{
							foreach (APP_MENU as $idPoint => $pointMenu)
							{
								if (is_string($pointMenu["libelleMenu"]) && App_PageEstAccessible($idPoint))
								{
									Html_GenerateG("li", "id", "MENUITEM_$idPoint", HTML_CONTENT, function($pointMenu)
									{
										if (App_EstPageActuelle($pointMenu["id"]))
										{
											Html_GenerateOC("span", "class", "page_active", "title", $pointMenu["infoBulleMenu"], HTML_CONTENT, $pointMenu["libelleMenu"]);
										}
										else
										{
											Html_GenerateOC("a", "href", Url_PathTo("*/.controleur.php?page=$pointMenu[id]"), "title", $pointMenu["infoBulleMenu"], HTML_CONTENT, $pointMenu["libelleMenu"]);
										}
									}, $pointMenu);
								}
							}
						});
					});
					App_AfficherFlashInfo();
				}, $titreComplet),
	"main" => App_GenerateurCorpsPage(),
	"footer" => APP_PIEDPAGE
));
?>