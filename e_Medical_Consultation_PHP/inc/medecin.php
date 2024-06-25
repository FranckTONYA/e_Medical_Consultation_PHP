<?php
Pdweb_IncludeLib("pdweb.html.php");

function Medecin_AfficherAccueil()
{
	if (!App_EstMedecin()) Http_Redirect("*/");
	Html_GenerateG("section", HTML_CONTENT, function ()
	{
		Html_GenerateOC("p", HTML_CONTENT, "...blabla personnel du médecin...");
	});
}
?>