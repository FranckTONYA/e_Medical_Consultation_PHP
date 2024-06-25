<?php
Pdweb_IncludeLib("pdweb.html.php");

function Patient_AfficherAccueil()
{
	if (!App_EstPatient()) Http_Redirect("*/");
	Html_GenerateG("section", HTML_CONTENT, function ()
	{
		Html_GenerateOC("p", HTML_CONTENT, "...blabla personnel du patient...");
	});
}
?>