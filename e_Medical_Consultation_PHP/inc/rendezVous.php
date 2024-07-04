<?php
Pdweb_IncludeLib("pdweb.html.php");

define("APP_FORM_RENDEZ_VOUS", array
(
	"id" => "consultation",
	"url" => "*/.controleur.php",
	"elements" => array()
));

function RendezVous_AfficherListe()
{
	if (!App_EstAdministrateur() && !App_EstMedecin() && !App_EstPatient()) Http_Redirect("*/");
	Form_ClearErrors(APP_FORM_RENDEZ_VOUS["id"]);
	Form_ClearValues(APP_FORM_RENDEZ_VOUS["id"]);
	Html_GenerateG("section", HTML_CONTENT, function ()
	{
		Html_GenerateG("table", "class", "crud", HTML_CONTENT, function()
		{
			Html_GenerateG("thead", HTML_CONTENT, function()
			{
				Html_GenerateG("tr", HTML_CONTENT, function()
				{
                    Html_GenerateOC("th", HTML_CONTENT, "Description");
                    Html_GenerateOC("th", HTML_CONTENT, "Date ");
                    Html_GenerateOC("th", HTML_CONTENT, "Durée");
                    Html_GenerateOC("th", HTML_CONTENT, "Consultation");
                    Html_GenerateOC("th", HTML_CONTENT, "Patient");
                    Html_GenerateOC("th", HTML_CONTENT, "Médecin");
					Html_GenerateOC("th", HTML_CONTENT, "Statut");
					Html_GenerateOC("th", "colspan", 2, HTML_CONTENT, "Actions");
				});
			});
			Html_GenerateG("tbody", HTML_CONTENT, function()
			{
				foreach (MySql_Rows(
					"SELECT
                            rendez_vous.id AS rdv_id,
                            rendez_vous.description AS rdv_description,
                            rendez_vous.date AS rdv_date,
                            rendez_vous.duree AS rdv_duree,
                            consultation.id AS consultation_id,
                            consultation.motif AS consultation_motif,
                            consultation.prescription AS consultation_prescription,
                            consultation.rapport AS consultation_rapport,
                            statut_rendezvous.id AS statut_id,
                            statut_rendezvous.nom AS statut_nom,
                            statut_rendezvous.description AS statut_description,
                            dossier_patient.id AS dossier_id,
                            dossier_patient.description AS dossier_description,
                            dossier_patient.ref_patient AS dossier_ref_patient,
                            utilisateur.id AS medecin_id,
                            utilisateur.email AS medecin_email,
                            utilisateur.nom AS medecin_nom,
                            utilisateur.prenom AS medecin_prenom
                        FROM rendez_vous 
                            LEFT JOIN consultation ON rendez_vous.ref_consultation = consultation.id                            
                            LEFT JOIN statut_rendezvous ON rendez_vous.ref_statut = statut_rendezvous.id
                            LEFT JOIN utilisateur ON consultation.ref_medecin = utilisateur.id 
                            LEFT JOIN dossier_patient ON consultation.ref_dossier = dossier_patient.id ") as $enregistrement)
				{
                    // Récupérer le patient associé au dossier patient
                    $enregistrement["patient"] = MySql_Row(
                        "SELECT
                                utilisateur.id AS id,
                                utilisateur.email AS email,
                                utilisateur.nom AS nom,
                                utilisateur.prenom AS prenom
                            FROM
                                utilisateur
                            WHERE utilisateur.id = ?", array($enregistrement["dossier_ref_patient"]));

					Html_GenerateG("tr", HTML_CONTENT, function($enregistrement)
					{
                        Html_GenerateOC("td", HTML_CONTENT, $enregistrement["rdv_description"]);
                        Html_GenerateOC("td", HTML_CONTENT, date('Y-m-d', strtotime($enregistrement['rdv_date'])));
                        Html_GenerateOC("td", HTML_CONTENT, $enregistrement["rdv_duree"]);
                        Html_GenerateOC("td", HTML_CONTENT, $enregistrement["consultation_motif"]);
                        Html_GenerateOC("td", HTML_CONTENT, $enregistrement["patient"]["nom"]. " " . $enregistrement["patient"]["prenom"]);
                        Html_GenerateOC("td", HTML_CONTENT, $enregistrement["medecin_nom"]. " " . $enregistrement["medecin_prenom"]);
                        Html_GenerateOC("td", HTML_CONTENT, $enregistrement["statut_nom"]);

						Html_GenerateG("td", HTML_CONTENT, function($enregistrement)
						{
                            Html_GenerateOC("a", "href", Url_PathTo("*/.controleur.php?page=CRUD_RENDEZVOUS_EDITION&id=$enregistrement[rdv_id]"), "class", "bouton", "title", "Éditer ce Rendez-vous", HTML_CONTENT, "E");
                            Html_GenerateOC("a", "href", Url_PathTo("*/.controleur.php?action=" . urlencode("RendezVous/Consultation") . "&id=$enregistrement[rdv_id]"),  "class", "bouton", "title", "Consultation de ce Rendez-vous", HTML_CONTENT, "C");
						}, $enregistrement);

						$parametres = array("td");
						$parametres = array_merge($parametres, array(HTML_CONTENT, function($enregistrement)
						{
                            Html_GenerateOC("a", "href", Url_PathTo("*/.controleur.php?action=" . urlencode("RendezVous/Supprimer") . "&id=$enregistrement[rdv_id]"), "class", "bouton", "title", "Supprimer ce Rendez-vous", HTML_CONTENT, "S");
						}, $enregistrement));
						Html_GenerateG(...$parametres);

					}, $enregistrement);
				}
			});
		});
	});
}

function RendezVous_AfficherFormulaire()
{
	if (!App_EstAdministrateur() && !App_EstMedecin() && !App_EstPatient()) Http_Redirect("*/");

    // si formulaire en Modification
	if (isset($_GET["id"]))
	{
        // Récupérer le Rendez-vous à modifier
        $rdv =  MySql_Row(
            " SELECT
                    rendez_vous.id AS rdv_id,
                    rendez_vous.description AS rdv_description,
                    rendez_vous.date AS rdv_date,
                    rendez_vous.duree AS rdv_duree,
                    consultation.id AS consultation_id,
                    consultation.motif AS consultation_motif,
                    consultation.prescription AS consultation_prescription,
                    consultation.rapport AS consultation_rapport,
                    statut_rendezvous.id AS statut_id,
                    statut_rendezvous.nom AS statut_nom,
                    statut_rendezvous.description AS statut_description
                FROM rendez_vous 
                        LEFT JOIN consultation ON rendez_vous.ref_consultation = consultation.id                            
                        LEFT JOIN statut_rendezvous ON rendez_vous.ref_statut = statut_rendezvous.id
                WHERE rendez_vous.id = ?;", array($idRDV = $_GET["id"]));

        if ($rdv !== false){
            $description = $rdv["rdv_description"];
            $date = date('Y-m-d', strtotime($rdv["rdv_date"]));
            $duree = $rdv["rdv_duree"];

            $statut["id"] = $rdv["statut_id"];
            $statut["nom"] = $rdv["statut_nom"];
            $statut["description"] = $rdv["statut_description"];

            if (isset($rdv["consultation_id"])){
                $consultation["id"] = $rdv["consultation_id"];
                $consultation["motif"] = $rdv["consultation_motif"];
                $consultation["prescription"] = $rdv["consultation_prescription"];
                $consultation["rapport"] = $rdv["consultation_rapport"];
            }

            // Récupérer la liste de tous les statuts de rendez-vous en BD pour un éventuel changement de statut du Rendez-vous
            $listeStatuts = ListeStatuts();

        }else{
            Http_Redirect("*/");
        }
	}elseif (isset($_GET["idConsultation"])){ // si Ajout d'un nouveau rendez vous à une consultation passée en paramétre
        $consultation = MySql_Row("SELECT
                        consultation.id AS id,
                        consultation.motif AS motif,
                        consultation.prescription AS prescription,
                        consultation.rapport AS rapport
                    FROM
                        consultation 
                    WHERE consultation.id = ?", array($_GET["idConsultation"]));

        if ($consultation === false) Http_Redirect("*/");

        // Récupérer la liste de tous les statuts de rendez-vous en BD pour un éventuel changement de statut du Rendez-vous
        $listeStatuts = ListeStatuts();
    }
    else{
        Http_Redirect("*/");
    }

	Html_GenerateG("section", HTML_CONTENT, function ($idRDV, $description, $date, $duree, $statut, $consultation, $listeStatuts)
	{
        // Vérifier si le formulaire a été soumis
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $messageErreur = TraitementFormulaire($_POST);
            if (isset($messageErreur)){
                FormulaireRendezVous($description, $date, $duree, $statut, $consultation, $listeStatuts, $messageErreur);
            }
        } else {
            FormulaireRendezVous($description, $date, $duree, $statut, $consultation, $listeStatuts);
        }
		Html_GenerateOC("a", "href", Url_PathTo("*/.controleur.php?page=CRUD_RENDEZVOUS"), "class", "bouton", "title", "Liste des rendez-vous", HTML_CONTENT, "Retourner à la liste des rendez-vous");
	}, $idRDV, $description, $date, $duree, $statut, $consultation, $listeStatuts);
}

function ListeStatuts()
{
    foreach (MySql_Rows(
                 "SELECT
                            statut_rendezvous.id AS id,
                            statut_rendezvous.nom AS nom,
                            statut_rendezvous.description AS description
                        FROM statut_rendezvous")as $enregistrement){
        $listeStatuts[] = $enregistrement;
    }

    return $listeStatuts;
}

function RendezVous_Supprimer($donnees)
{
	if (!isset($donnees["id"])) Http_Redirect("*/");
	$idRDV = $donnees["id"];
	if (MySql_Execute(
		"DELETE FROM rendez_vous WHERE id = ?",
		array($idRDV)))
	{
		App_CreerFlashInfo("Votre Rendez-vous a bien été supprimé.");
	}
	App_RedirigerVersPage("CRUD_RENDEZVOUS");
}

function FormulaireRendezVous($description = null, $date = null, $duree = null, $selectedStatut = null, $consultation = null, $statuts = null, $messageErreur = null)
{
    echo '
   <div class="container">
            <h1>Formulaire de Rendez-vous</h1>
            <div class="error-message">' . $messageErreur . '</div>
            <form method="POST" action="">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="4" >' . $description . '</textarea>

                <label for="date">Date</label>
                <input type="date" id="date" name="date" value="' . $date . '" required>

                <label for="duree">Durée (en minutes)</label>
                <input type="number" id="duree" name="duree" value="' . $duree . '" required>

                <label for="statut">Statut</label>
                <select id="statut" name="statut" required>
                    <option value="" disabled ' . (empty($selectedStatut) ? 'selected' : '') . '>Choisir un statut</option>';
    foreach ($statuts as $statut) {
        $selected = ($statut['id']  == $selectedStatut['id'] ) ? 'selected' : '';
        echo "<option value=\"{$statut['id']} \" $selected>{$statut['nom']}</option>";
    }
    echo '      </select>
      
                <label for="consultation">Consultation</label>
                <input type="text" id="consultation" name="consultation_motif" value="' . $consultation['motif'] . '" readonly>
                <input type="hidden" name="consultation_id" value="' . $consultation['id'] . '">
                
                <button type="submit">Enregistrer</button>
                
            </form>
   </div>
    ';
}

function TraitementFormulaire($donnees)
{
    if (!App_EstAdministrateur() && !App_EstMedecin() && !App_EstPatient()) Http_Redirect("*/");
    if (!isset($donnees["date"], $donnees["duree"], $donnees["consultation_id"], $donnees["statut"])) Http_Redirect("*/");

    $description = $donnees['description'];
    $date = date('Y-m-d', strtotime($donnees['date']));
    $duree = $donnees['duree'];
    $consultation = $donnees['consultation_id'];
    $statut = $donnees['statut'];

    $erreur = false;
    $messageErreur = null;

    if (isset($_GET["id"]) ) // En Modification
    {
        $resultat = MySql_Execute("UPDATE rendez_vous
                                            SET description = ?, date = ?, duree = ?, ref_statut = ? 
                                            WHERE id = ?;", array($description, $date, $duree, $statut, $_GET["id"]));
    }
    else // En Ajout
    {
        $idConsultation = MySql_Execute("INSERT INTO rendez_vous
                                            SET description = ?, date = ?, duree = ?, ref_consultation = ?, ref_statut = ?;",
            array($description, $date, $duree, $consultation, $statut));

        if (!Pdweb_IsInteger($idConsultation))
        {
            $resultat = false;
        }
        else{ // Ajouter le nouveau Rendez-vous dans la Consultation associée
            $resultat = MySql_Execute("UPDATE consultation
                                            SET ref_rdv = ? 
                                            WHERE id = ?;", array($idConsultation, $consultation));
        }
    }

    if (Pdweb_IsInteger($resultat))
    {
        Http_Redirect("CRUD_RENDEZVOUS");
    }else{
        $erreur = true;
        $messageErreur = 'Erreur interne';
    }

    if ($erreur) {
        return $messageErreur;
    }
}

function RendezVous_Consultation($donnees)
{
    if (!isset($donnees["id"])) Http_Redirect("*/");
    $idRDV = $donnees["id"];

    $rdv =  MySql_Row(
        " SELECT
                        rendez_vous.id AS rdv_id,
                        rendez_vous.description AS rdv_description,
                        rendez_vous.date AS rdv_date,
                        rendez_vous.duree AS rdv_duree,
                        consultation.id AS consultation_id,
                        consultation.prescription AS consultation_prescription,
                        consultation.rapport AS consultation_rapport
                    FROM rendez_vous 
                            LEFT JOIN consultation ON rendez_vous.ref_consultation = consultation.id                            
                    WHERE rendez_vous.id = ?;", array($idRDV));

   if ($rdv === false || !isset($rdv["consultation_id"])) Http_Redirect("*/");

    Http_Redirect("*/.controleur.php?page=CRUD_CONSULTATION_EDITION&id=$rdv[consultation_id]");
}

?>
