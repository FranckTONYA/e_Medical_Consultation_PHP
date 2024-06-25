<?php
Pdweb_IncludeLib("pdweb.html.php");

function Joueur_AfficherAccueil()
{
	if (!App_EstJoueur()) Http_Redirect("*/");
	Html_GenerateG("section", HTML_CONTENT, function ()
	{
		Html_GenerateOC("p", HTML_CONTENT, "...blabla personnel du joueur...");
	});
}
?>