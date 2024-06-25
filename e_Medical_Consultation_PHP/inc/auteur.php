<?php
Pdweb_IncludeLib("pdweb.html.php");

function Auteur_AfficherAccueil()
{
	if (!App_EstAuteur()) Http_Redirect("*/");
	Html_GenerateG("section", HTML_CONTENT, function ()
	{
		Html_GenerateOC("p", HTML_CONTENT, "...blabla personnel de l'auteur...");
	});
}
?>