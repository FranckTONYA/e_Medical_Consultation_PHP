<?php
Pdweb_IncludeLib("pdweb.html.php");

function Admin_AfficherAccueil()
{
	if (!App_EstAdministrateur()) Http_Redirect("*/");
	Html_GenerateG("section", HTML_CONTENT, function ()
	{
		Html_GenerateOC("p", HTML_CONTENT, "...blabla personnel de l'administrateur...");
	});
}
?>