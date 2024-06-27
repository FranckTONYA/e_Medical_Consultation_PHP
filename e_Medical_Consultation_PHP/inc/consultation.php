<?php
Pdweb_IncludeLib("pdweb.html.php");

define("APP_FORM_CONSULTATION", array
(
	"id" => "consultation",
	"url" => "*/.controleur.php",
	"elements" => array
	(
        array
        (
            "name" => "motif",
            "type" => "text",
            "label" => "Motif :"
        ),
		array
		(
			"name" => "rapport",
			"type" => "text",
			"label" => "Rapport :"
		),
		array
		(
			"name" => "prescription",
			"type" => "text",
			"label" => "Prescription :"
		),
        array
        (
            "name" => "patient",
            "type" => "text",
            "label" => "Patient :"
        ),
        array
        (
            "name" => "medecin",
            "type" => "text",
            "label" => "Médecin :"
        ),
		array
		(
			"name" => "creer",
			"type" => "submit",
			"value" => "?1? ce rendez-vous",
		),
        array
        (
            "name" => "enregistrer",
            "type" => "submit",
            "value" => "?2? ce rendez-vous",
        ),
        array
        (
            "name" => "supprimer",
            "type" => "submit",
            "value" => "?3? ce rendez-vous",
        )
	)
));

function FormulaireAjout()
{
	return FormulaireAdapte(array("1" => "Ajouter", "2"=>"Ajouter"));
}

function FormulaireEdition($idConsultation, $rapport = null, $prescription = null, $patient = null, $medecin = null)
{
	$formulaire = FormulaireAdapte(array("1" => "Modifier", "2"=>"Modifier"));
    if (Form_GetValue($formulaire["id"], "motif") === false)
    {
        Form_SetValue($formulaire["id"], "motif", $rapport);
    }
	if (Form_GetValue($formulaire["id"], "rapport") === false)
	{
		Form_SetValue($formulaire["id"], "rapport", $rapport);
	}
    if (Form_GetValue($formulaire["id"], "prescription") === false)
    {
        Form_SetValue($formulaire["id"], "prescription", $prescription);
    }
    if (Form_GetValue($formulaire["id"], "patient") === false)
    {
        Form_SetValue($formulaire["id"], "patient", $patient);
    }
    if (Form_GetValue($formulaire["id"], "medecin") === false)
    {
        Form_SetValue($formulaire["id"], "medecin", $medecin);
    }

	$formulaire["elements"][] = array
	(
		"name" => "id_consultation",
		"type" => "hidden",
		"value" => $idConsultation
	);
	return $formulaire;
}

function FormulaireAdapte($remplacements)
{
	$formulaire = APP_FORM_CONSULTATION;
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

function Consultation_AfficherListe()
{
	if (!App_EstAdministrateur()) Http_Redirect("*/");
	Form_ClearErrors(APP_FORM_CONSULTATION["id"]);
	Form_ClearValues(APP_FORM_CONSULTATION["id"]);
	Html_GenerateG("section", HTML_CONTENT, function ()
	{
		Html_GenerateG("table", "class", "crud", HTML_CONTENT, function()
		{
			Html_GenerateG("thead", HTML_CONTENT, function()
			{
				Html_GenerateG("tr", HTML_CONTENT, function()
				{
                    Html_GenerateOC("th", HTML_CONTENT, "Motif");
                    Html_GenerateOC("th", HTML_CONTENT, "Rapport");
                    Html_GenerateOC("th", HTML_CONTENT, "Prescription");
                    Html_GenerateOC("th", HTML_CONTENT, "Patient");
					Html_GenerateOC("th", HTML_CONTENT, "Médecin");
					Html_GenerateOC("th", "colspan", 2, HTML_CONTENT, "Actions");
				});
			});
			Html_GenerateG("tbody", HTML_CONTENT, function()
			{
				foreach (MySql_Rows(
					"SELECT
                        consultation.id AS consultation_id,
                        consultation.motif AS motif,
                        consultation.prescription AS prescription,
                        consultation.rapport AS rapport,
                        dossier_patient.id AS dossier_id,
                        dossier_patient.description AS dossier_description,
                        dossier_patient.ref_patient AS dossier_ref_patient,
                        utilisateur.id AS medecin_id,
                        utilisateur.email AS medecin_email,
                        utilisateur.password AS medecin_motDePasse,
                        utilisateur.token AS medecin_token,
                        utilisateur.nom AS medecin_nom,
                        utilisateur.prenom AS medecin_prenom,
                        utilisateur.telephone AS medecin_telephone,
                        utilisateur.date_naissance AS medecin_dateNaissance,
                        utilisateur.adresse AS medecin_adresse
                    FROM
                        consultation 
                            LEFT JOIN utilisateur ON consultation.ref_medecin = utilisateur.id 
                            LEFT JOIN dossier_patient ON consultation.ref_dossier = dossier_patient.id
                    ORDER BY
                        utilisateur.nom ASC") as $enregistrement);

//                Récupérer le patient associé au dossier patient
                    foreach (MySql_Rows(
                        "SELECT
                        utilisateur.id AS patient_id,
                        utilisateur.email AS patient_email,
                        utilisateur.password AS patient_motDePasse,
                        utilisateur.token AS patient_token,
                        utilisateur.nom AS patient_nom,
                        utilisateur.prenom AS patient_prenom,
                        utilisateur.telephone AS patient_telephone,
                        utilisateur.date_naissance AS patient_dateNaissance,
                        utilisateur.adresse AS patient_adresse
                    FROM
                        utilisateur 
                    WHERE utilisateur.id = ?
                    ORDER BY
                        utilisateur.nom ASC", $enregistrement["dossier_ref_patient"]) as $enregistrement["patient"])

				{
					Html_GenerateG("tr", HTML_CONTENT, function($enregistrement)
					{
                        Html_GenerateOC("td", HTML_CONTENT, $enregistrement["motif"]);
                        Html_GenerateOC("td", HTML_CONTENT, $enregistrement["rapport"]);
                        Html_GenerateOC("td", HTML_CONTENT, $enregistrement["prescription"]);
                        Html_GenerateOC("td", HTML_CONTENT, $enregistrement["patient"]["patient_nom"]);
                        Html_GenerateOC("td", HTML_CONTENT, $enregistrement["medecin_nom"]);
						Html_GenerateG("td", HTML_CONTENT, function($enregistrement)
						{
							GenererHtmlImage($enregistrement["id"]);
						}, $enregistrement);
						Html_GenerateG("td", HTML_CONTENT, function($enregistrement)
						{
							Html_GenerateOC("a", "href", Url_PathTo("*/.controleur.php?page=CRUD_CONSULTATION&id=$enregistrement[id]"), "class", "bouton", "title", "Éditer cette consultation", HTML_CONTENT, "E");
						}, $enregistrement);

						$parametres = array("td");
						$parametres = array_merge($parametres, array(HTML_CONTENT, function($enregistrement)
						{
                            Html_GenerateOC("a", "href", Url_PathTo("*/.controleur.php?action=" . urlencode("Consultation/Supprimer") . "&id=$enregistrement[id]"), "class", "bouton", "title", "Supprimer cette consultation", HTML_CONTENT, "S");
						}, $enregistrement));
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
						Html_GenerateOC("a", "href", Url_PathTo("*/.controleur.php?page=CRUD_CONSULTATION_AJOUT"), "class", "bouton", "title", "Ajouter une nouvelle consultation", HTML_CONTENT, "Ajouter une illustration");
					});
				});
			});
		});
	});
}

function Consultation_AfficherAjout()
{
	if (!App_EstAdministrateur()()) Http_Redirect("*/");
	Html_GenerateG("section", HTML_CONTENT, function ()
	{
		Html_GenerateForm(FormulaireAjout());
		Html_GenerateOC("a", "href", Url_PathTo("*/.controleur.php?page=CRUD_IMAGE"), "class", "bouton", "title", "Ajouter une nouvelle consultation", HTML_CONTENT, "Retourner à la liste des Consultations");
	});
}

function Consultation_AfficherEdition()
{
	if (!App_EstAdministrateur()) Http_Redirect("*/");
	if (!isset($_GET["id"])
		|| (($motif = MySql_Value(
				"SELECT motif FROM consultation WHERE id = ?",
				array($idConsultation=$_GET["id"]),
				false)) === false))
	{
		Http_Redirect("*/");
	}
	Html_GenerateG("section", HTML_CONTENT, function ($idConsultation, $motif)
	{
		Html_GenerateForm(FormulaireEdition($idConsultation, $motif));
		Html_GenerateOC("a", "href", Url_PathTo("*/.controleur.php?page=CRUD_CONSULTATION"), "class", "bouton", "title", "Ajouter une nouvelle consultation", HTML_CONTENT, "Retourner à la liste des consultations");
	}, $idConsultation, $motif);
}

function Consultation_Ajouter($donnees)
{
	EnregistrerConsultation(FormulaireAjout(), 0, $donnees);
}

function Consultation_Modifier($donnees)
{
	if (!isset($donnees["id_consultation"])) Http_Redirect("*/");
	$idConsultation = $donnees["id_consultation"];
	EnregistrerConsultation(FormulaireEdition($donnees["id_consultation"]), $idConsultation, $donnees);
}

function EnregistrerConsultation($formulaire, $idConsultation, $donnees)
{
	if (!App_EstAdministrateur()) Http_Redirect("*/");
	if (!isset($donnees["consultation"])) Http_Redirect("*/");
	$estEnAjout = ($idConsultation == 0);
	Form_ClearErrors($formulaire["id"]);
	Form_ClearValues($formulaire["id"]);
	$erreurPresente = false;
    $rapport = trim($donnees["rapport"]);
    $prescription = trim($donnees["prescription"]);

	if (($longueur=strlen($motif = trim($donnees["motif"]))) < 1)
	{
		$erreurPresente = true;
		Form_SetError($formulaire["id"], "motif", "Le motif doit comporter au moins un caractère significatif !");
	}
	else if ($longueur > 80)
	{
		$erreurPresente = true;
		Form_SetError($formulaire["id"], "motif", "Le motif doit comporter au plus 80 caractères significatifs !");
	}

	if (!$erreurPresente)
	{
		if ($estEnAjout)
		{
			$resultat = MySql_Execute("INSERT INTO consultation SET motif = ?, rapport = ?, prescription = ?;", array($motif, $rapport, $prescription));
		}
		else
		{
			$resultat = MySql_Execute("UPDATE consultation SET motif = ?, rapport = ?, prescription = ? WHERE id = ?;", array($motif, $rapport, $prescription, $idConsultation));
		}
		if (!Pdweb_IsInteger($resultat))
		{
			$erreurPresente = true;
			Form_SetError($formulaire["id"], "enregistrer", "Erreur interne !");
		}
		else
		{
            $erreurPresente = true;
            Form_SetError($formulaire["id"], "motif", "Erreur motif !");
        }

	}
	if ($erreurPresente)
	{
		Form_SetValue($formulaire["id"], "motif", $motif);
		if ($estEnAjout)
			App_RedirigerVersPage("CRUD_CONSULTATION_AJOUT");
		else
			App_RedirigerVersPage("CRUD_CONSULTATION_EDITION", "id", $idConsultation);
	}
	else
	{
		App_CreerFlashInfo("Votre consultation \"$motif\" a bien été " . ($estEnAjout ? "ajoutée." : "modifiée."));
		App_RedirigerVersPage("CRUD_CONSULTATION");
	}
}

function Consultation_Supprimer($donnees)
{
	if (!isset($donnees["id"])) Http_Redirect("*/");
	$idConsultation = $donnees["id"];
	if (MySql_Execute(
		"DELETE FROM consultation WHERE id = ?",
		array($idConsultation)))
	{
		App_CreerFlashInfo("Votre consultation a bien été supprimée.");
	}
	App_RedirigerVersPage("CRUD_CONSULTATION");
}
?>