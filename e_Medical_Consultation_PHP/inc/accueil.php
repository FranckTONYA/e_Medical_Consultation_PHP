<?php
function Accueil_Afficher()
{
	Html_GenerateG("section", "class", "une_section", "class", "une_autre_classe", "id", "sec1", "id", "sec2", HTML_CONTENT, function ()
	{
		if (($denomination = App_DenominationUtilisateur()) === false) $denomination = "nouvel utilisateur";
		Html_GenerateOC("p", HTML_CONTENT, "Bienvenue à vous, $denomination.");
		Html_GenerateOC("p", HTML_CONTENT, "Ce site a pour but de permettre des consultations Médicale en ligne !");
		if (App_EstAdministrateur())
		{
			Html_GenerateOC("p", HTML_CONTENT, "...blabla ADMINISTRATEUR...");
		}
		else if (App_EstMedecin())
		{
			Html_GenerateOC("p", HTML_CONTENT, "...blabla MEDECIN...");
		}
        else if (App_EstPatient())
        {
            Html_GenerateOC("p", HTML_CONTENT, "...blabla PATIENT...");
        }
		else
		{
			Html_GenerateOC("p", HTML_CONTENT, "...blabla VISITEUR...");
		}
		var_dump($_SESSION);
	});
}
?>