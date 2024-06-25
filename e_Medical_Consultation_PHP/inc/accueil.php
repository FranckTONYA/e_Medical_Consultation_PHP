<?php
function Accueil_Afficher()
{
	Html_GenerateG("section", "class", "une_section", "class", "une_autre_classe", "id", "sec1", "id", "sec2", HTML_CONTENT, function ()
	{
		if (($denomination = App_DenominationUtilisateur()) === false) $denomination = "nouvel utilisateur";
		Html_GenerateOC("p", HTML_CONTENT, "Bienvenue à vous, $denomination.");
		Html_GenerateOC("p", HTML_CONTENT, "Ce site a pour but de permettre aux joueurs d'apprendre en vivant une aventure !");
		if (App_EstJoueur())
		{
			Html_GenerateOC("p", HTML_CONTENT, "...blabla JOUEUR...");
		}
		else if (App_EstAuteur())
		{
			Html_GenerateOC("p", HTML_CONTENT, "...blabla AUTEUR...");
		}
		else
		{
			Html_GenerateOC("p", HTML_CONTENT, "...blabla VISITEUR...");
		}
		var_dump($_SESSION);
	});
}
?>