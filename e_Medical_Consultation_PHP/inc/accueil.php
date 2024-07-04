<?php
function Accueil_Afficher()
{
	Html_GenerateG("section", "class", "une_section", "class", "une_autre_classe", "id", "sec1", "id", "sec2", HTML_CONTENT, function ()
	{
		if (($denomination = App_DenominationUtilisateur()) === false) $denomination = "nouvel utilisateur";
		Html_GenerateOC("p", HTML_CONTENT, "Bienvenue � vous, $denomination.");
		Html_GenerateOC("p", HTML_CONTENT, "Ce site a pour but de permettre des Consultations M�dicale en ligne !");
		if (App_EstAdministrateur())
		{
			Html_GenerateOC("p", HTML_CONTENT, "Vous �tes connect� en tant que " . APP_ROLE_ADMINISTRATEUR);
		}
		else if (App_EstMedecin())
		{
			Html_GenerateOC("p", HTML_CONTENT, "Vous �tes connect� en tant que " . APP_ROLE_MEDECIN);
		}
        else if (App_EstPatient())
        {
            Html_GenerateOC("p", HTML_CONTENT, "Vous �tes connect� en tant que " . APP_ROLE_PATIENT);
        }
		else
		{
			Html_GenerateOC("p", HTML_CONTENT, "Vous �tes " . APP_ROLE_VISITEUR);
		}
	});
}
?>