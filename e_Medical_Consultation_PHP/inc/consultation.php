<?php
Pdweb_IncludeLib("pdweb.html.php");

define("APP_FORM_CONSULTATION", array
(
	"id" => "consultation",
	"url" => "*/.controleur.php",
	"elements" => array
	()
));

function Consultation_AfficherListe()
{
	if (!App_EstAdministrateur() && !App_EstMedecin() && !App_EstPatient()) Http_Redirect("*/");
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
                    Html_GenerateOC("th", HTML_CONTENT, "Rendez-vous");
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
                        utilisateur.nom AS medecin_nom,
                        utilisateur.prenom AS medecin_prenom,
                        rendez_vous.id AS rdv_id,
                        rendez_vous.description AS rdv_description,
                        rendez_vous.date AS rdv_date,
                        rendez_vous.duree AS rdv_duree
                    FROM
                        consultation 
                            LEFT JOIN utilisateur ON consultation.ref_medecin = utilisateur.id 
                            LEFT JOIN dossier_patient ON consultation.ref_dossier = dossier_patient.id
                            LEFT JOIN rendez_vous ON consultation.ref_rdv = rendez_vous.id
                    ORDER BY
                        utilisateur.nom ASC") as $enregistrement)

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
                        Html_GenerateOC("td", HTML_CONTENT, $enregistrement["motif"]);
                        Html_GenerateOC("td", HTML_CONTENT, $enregistrement["rapport"]);
                        Html_GenerateOC("td", HTML_CONTENT, $enregistrement["prescription"]);
                        Html_GenerateOC("td", HTML_CONTENT, $enregistrement["patient"]["nom"]. " " . $enregistrement["patient"]["prenom"]);
                        Html_GenerateOC("td", HTML_CONTENT, $enregistrement["medecin_nom"]. " " . $enregistrement["medecin_prenom"]);
                        Html_GenerateOC("td", HTML_CONTENT, isset($enregistrement['rdv_id']) ? (date('Y-m-d', strtotime($enregistrement['rdv_date'])). " " . $enregistrement["rdv_duree"]) : "");

						Html_GenerateG("td", HTML_CONTENT, function($enregistrement)
						{
                            Html_GenerateOC("a", "href", Url_PathTo("*/.controleur.php?page=CRUD_CONSULTATION_EDITION&id=$enregistrement[consultation_id]"), "class", "bouton", "title", "Éditer cette consultation", HTML_CONTENT, "E");
                            Html_GenerateOC("a", "href", Url_PathTo("*/.controleur.php?action=" . urlencode("Consultation/RendezVous") . "&id=$enregistrement[consultation_id]"),  "class", "bouton", "title", "Rendez-vous de cette consultation", HTML_CONTENT, "RDV");
						}, $enregistrement);

						$parametres = array("td");
						$parametres = array_merge($parametres, array(HTML_CONTENT, function($enregistrement)
						{
                            Html_GenerateOC("a", "href", Url_PathTo("*/.controleur.php?action=" . urlencode("Consultation/Supprimer") . "&id=$enregistrement[consultation_id]"), "class", "bouton", "title", "Supprimer cette consultation", HTML_CONTENT, "S");
						}, $enregistrement));
						Html_GenerateG(...$parametres);
//
//                        $parametres = array("td");
//                        $parametres2 = array_merge($parametres, array(HTML_CONTENT, function($enregistrement)
//                        {
//                            Html_GenerateOC("a", "href", Url_PathTo("*/.controleur.php?page=CRUD_CONSULTATION_EDITION&id=$enregistrement[consultation_id]"), "class", "bouton", "title", "Rendez-vous de cette consultation", HTML_CONTENT, "RDV");
//                        }, $enregistrement));
//                        Html_GenerateG(...$parametres2);
					}, $enregistrement);
				}
			});
			Html_GenerateG("tfoot", HTML_CONTENT, function()
			{
				Html_GenerateG("tr", HTML_CONTENT, function()
				{
					Html_GenerateG("td", "colspan", 4, HTML_CONTENT, function()
					{
						Html_GenerateOC("a", "href", Url_PathTo("*/.controleur.php?page=CRUD_CONSULTATION_EDITION"), "class", "bouton", "title", "Ajouter une nouvelle consultation", HTML_CONTENT, "Ajouter une consultation");
					});
				});
			});
		});
	});
}

function Consultation_AfficherFormulaire()
{
	if (!App_EstAdministrateur() && !App_EstMedecin() && !App_EstPatient()) Http_Redirect("*/");

    // si formulaire en Modification
	if (isset($_GET["id"]))
	{
        // Récupérer la consultation à modifier
        $consultation = MySql_Row("SELECT
                        consultation.id AS consultation_id,
                        consultation.motif AS motif,
                        consultation.prescription AS prescription,
                        consultation.rapport AS rapport,
                        dossier_patient.id AS dossier_id,
                        dossier_patient.description AS dossier_description,
                        dossier_patient.ref_patient AS dossier_ref_patient,
                        utilisateur.id AS medecin_id,
                        utilisateur.email AS medecin_email,
                        utilisateur.nom AS medecin_nom,
                        utilisateur.prenom AS medecin_prenom
                    FROM
                        consultation 
                            LEFT JOIN utilisateur ON consultation.ref_medecin = utilisateur.id 
                            LEFT JOIN dossier_patient ON consultation.ref_dossier = dossier_patient.id
                    WHERE consultation.id = ?", array($idConsultation=$_GET["id"]));

        if ($consultation !== false){
            $medecin["id"] = $consultation["medecin_id"];
            $medecin["email"] = $consultation["medecin_email"];
            $medecin["nom"] = $consultation["medecin_nom"];
            $medecin["prenom"] = $consultation["medecin_prenom"];

            $motif = $consultation["motif"];
            $rapport = $consultation["rapport"];
            $prescription = $consultation["prescription"];

            // Récupérer le patient associé à la consultation
            $patient = MySql_Row("SELECT
                        utilisateur.id AS id,
                        utilisateur.email AS email,
                        utilisateur.nom AS nom,
                        utilisateur.prenom AS prenom
                    FROM
                        utilisateur 
                    WHERE utilisateur.id = ?", array($consultation["dossier_ref_patient"]));

            if ($patient !== false){
                // Récupérer la liste de tous les patients exitants pour un éventuel changement de patient lié à la consultation
                $listePatients = ListePatients();

                // Récupérer la liste de tous les médecins exitants pour un éventuel changement de médecin lié à la consultation
                $listeMedecins = ListeMedecins();

            }else{
                Http_Redirect("*/");
            }
        }else{
            Http_Redirect("*/");
        }
	}
    else{ // Formulaire en Ajout
        // Récupérer la liste de tous les patients exitants pour un éventuel changement de patient lié à la consultation
        $listePatients = ListePatients();

        // Récupérer la liste de tous les médecins exitants pour un éventuel changement de médecin lié à la consultation
        $listeMedecins = ListeMedecins();
    }

	Html_GenerateG("section", HTML_CONTENT, function ($idConsultation, $motif, $rapport, $prescription, $patient, $medecin, $listePatients, $listeMedecins)
	{
        // Vérifier si le formulaire a été soumis
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $messageErreur = TraitementFormulaire($_POST);
            if (isset($messageErreur)){
                FormulaireConsultation($motif, $rapport, $prescription, $patient, $medecin, $listePatients, $listeMedecins, $messageErreur);
            }
        } else {
            FormulaireConsultation($motif, $rapport, $prescription, $patient, $medecin, $listePatients, $listeMedecins);
        }
//		Html_GenerateForm(FormulaireEdition($idConsultation, $motif, $rapport, $prescription, $patient, $medecin, $listeMedecins, $listePatients));
		Html_GenerateOC("a", "href", Url_PathTo("*/.controleur.php?page=CRUD_CONSULTATION"), "class", "bouton", "title", "Liste des consultations", HTML_CONTENT, "Retourner à la liste des consultations");
	}, $idConsultation, $motif, $rapport, $prescription, $patient, $medecin, $listePatients, $listeMedecins);
}

function ListePatients()
{
    foreach (MySql_Rows(
                 "SELECT
                        utilisateur.id AS id,
                        utilisateur.email AS email,
                        utilisateur.nom AS nom,
                        utilisateur.prenom AS prenom,
                        role_utilisateur.id AS role_id,
                        role_utilisateur.nom AS role_nom,                        
                        role_utilisateur.description AS role_description
                    FROM
                        utilisateur LEFT JOIN role_utilisateur ON utilisateur.ref_role = role_utilisateur.id 
                    WHERE role_utilisateur.id = ?
                    ORDER BY
                        utilisateur.nom ASC", array(3) )as $enregistrement){
        $listePatients[] = $enregistrement;
    }

    return $listePatients;
}

function ListeMedecins()
{
    foreach (MySql_Rows(
                 "SELECT
                        utilisateur.id AS id,
                        utilisateur.email AS email,
                        utilisateur.nom AS nom,
                        utilisateur.prenom AS prenom,
                        role_utilisateur.id AS role_id,
                        role_utilisateur.nom AS role_nom,
                        role_utilisateur.description AS role_description
                    FROM
                        utilisateur LEFT JOIN role_utilisateur ON utilisateur.ref_role = role_utilisateur.id 
                    WHERE role_utilisateur.id = ?
                    ORDER BY
                        utilisateur.nom ASC", array(2) ) as $enregistrement){
        $listeMedecins[] = $enregistrement;
    }

    return $listeMedecins;
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

function FormulaireConsultation($motif = null, $rapport = null, $prescription = null, $selectedPatient = null, $selectedMedecin = null, $patients= null, $medecins= null, $messageErreur = null)
{
    echo '
   <div class="container">
            <h1>Formulaire de Consultation</h1>
            <div class="error-message">' . $messageErreur . '</div>
            <form action="" method="POST">
                <label for="motif">Motif</label>
                <input type="text" id="motif" name="motif" value="' . $motif . '" required>

                <label for="rapport">Rapport</label>
                <textarea id="rapport" name="rapport" rows="4">' . $rapport . '</textarea>

                <label for="prescription">Prescription</label>
                <textarea id="prescription" name="prescription" rows="4">' . $prescription . '</textarea>

                <label for="medecin">Médecin</label>
                <select id="medecin" name="medecin" required>
                    <option value="" disabled ' . (empty($selectedMedecin) ? 'selected' : '') . '>Choisir un médecin</option>';
    foreach ($medecins as $medecin) {
        $selected = ($medecin['id'] == $selectedMedecin['id']) ? 'selected' : '';
        echo "<option value=\"{$medecin['id']}\" $selected>{$medecin['prenom']} {$medecin['nom']}</option>";
    }
    echo '      </select>

                <label for="patient">Patient</label>
                <select id="patient" name="patient" required>
                    <option value="" disabled ' . (empty($selectedPatient) ? 'selected' : '') . '>Choisir un patient</option>';
    foreach ($patients as $patient) {
        $selected = ($patient['id'] == $selectedPatient['id']) ? 'selected' : '';
        echo "<option value=\"{$patient['id']}\" $selected>{$patient['prenom']} {$patient['nom']}</option>";
    }
    echo '      </select>

                <button type="submit">Enregistrer</button>
            </form>
   </div>
    ';
}

function TraitementFormulaire($donnees)
{
    if (!App_EstAdministrateur() && !App_EstMedecin() && !App_EstPatient()) Http_Redirect("*/");
    if (!isset($donnees["motif"], $donnees["medecin"], $donnees["patient"])) Http_Redirect("*/");

    $motif = $donnees['motif'];
    $rapport = $donnees['rapport'];
    $prescription = $donnees['prescription'];
    $medecin = $donnees['medecin'];
    $patient = $donnees['patient'];

    $erreur = false;
    $messageErreur = null;

    $dossier =  MySql_Row(
        " SELECT
                        dossier_patient.id AS id,
                        dossier_patient.description AS description,
                        utilisateur.id AS patient_id
                    FROM
                        dossier_patient 
                            LEFT JOIN utilisateur ON dossier_patient.ref_patient = utilisateur.id                            
                    WHERE
                        utilisateur.id = ?;", array($patient));

    if (empty($dossier)){
        Http_Redirect("*/");
    }else{
        if (isset($_GET["id"]) ) // En Modification
        {
            $resultat = MySql_Execute("UPDATE consultation
                                            SET motif = ?, rapport = ?, prescription = ?, ref_medecin = ?, ref_dossier = ? 
                                            WHERE id = ?;", array($motif, $rapport, $prescription, $medecin, $dossier["id"], $_GET["id"]));
        }
        else // En Ajout
        {
            $resultat = MySql_Execute("INSERT INTO consultation
                                            SET motif = ?, rapport = ?, prescription = ?, ref_medecin = ?, ref_dossier = ?;",
                                            array($motif, $rapport, $prescription, $medecin, $dossier["id"]));
        }
        if (Pdweb_IsInteger($resultat))
        {
            Http_Redirect("CRUD_CONSULTATION");
        }else{
            $erreur = true;
            $messageErreur = 'Erreur interne';
        }

        if ($erreur) {
            return $messageErreur;
        }
    }
}

function Consultation_RendezVous($donnees)
{
    if (!isset($donnees["id"])) Http_Redirect("*/");
    $idConsultation = $donnees["id"];
    $rdv =  MySql_Row(
        " SELECT
                    rendez_vous.id AS id,
                    rendez_vous.description AS description,
                    consultation.id AS consultation_id,
                    consultation.motif AS consultation_motif,
                    consultation.prescription AS consultation_prescription,
                    consultation.rapport AS consultation_rapport
                FROM rendez_vous 
                        LEFT JOIN consultation ON rendez_vous.ref_consultation = consultation.id
                WHERE consultation.id = ?;", array($idConsultation));

   if (empty($rdv)){
       Http_Redirect("*/.controleur.php?page=CRUD_RENDEZVOUS_EDITION&idConsultation=$idConsultation");
   }else{
       Http_Redirect("*/.controleur.php?page=CRUD_RENDEZVOUS_EDITION&id=$rdv[id]");
   }
}

?>
