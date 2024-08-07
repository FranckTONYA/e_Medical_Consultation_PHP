<?php
Pdweb_IncludeLib("pdweb.html.php");

define("APP_FORM_AUTHENTIFICATION", array
(
	"id" => "authentification",
	"url" => "*/.controleur.php",
	"elements" => array
	(
		array
		(
			"name" => "email",
			"type" => "text",
			"label" => "Login :"
		),
		array
		(
			"name" => "mdp",
			"type" => "password",
			"label" => "Mot de passe :"
		),
		array
		(
			"name" => "action",
			"type" => "hidden",
			"value" => "Authentification/Authentifier"
		),
		array
		(
			"name" => "authentifier",
			"type" => "submit",
			"value" => "S'authentifier"
		)
	)
));

function Authentification_Admin()
{
    $_SESSION["pageConnexion"]["roleChoisi"] = APP_ROLE_ADMINISTRATEUR;
    Authentification_Afficher();
}
function Authentification_Medecin()
{
    $_SESSION["pageConnexion"]["roleChoisi"] = APP_ROLE_MEDECIN;
    Authentification_Afficher();
}

function Authentification_Patient()
{
    $_SESSION["pageConnexion"]["roleChoisi"] = APP_ROLE_PATIENT;
    Authentification_Afficher();
}

function Authentification_Afficher()
{
	Html_GenerateG("section", HTML_CONTENT, function ()
	{
        $titre = "Connexion - ". $_SESSION["pageConnexion"]["roleChoisi"];
        print("<p> $titre </p>");
		Html_GenerateForm(APP_FORM_AUTHENTIFICATION);
	});
}

function Authentification_Authentifier($donnees)
{
	if (!App_EstVisiteur()) Http_Redirect("*/");
	if (!isset($donnees["email"], $donnees["mdp"])) Http_Redirect("*/");
	Form_ClearErrors(APP_FORM_AUTHENTIFICATION["id"]);
    $email = trim($donnees["email"]);
	$resultat = MySql_Row
	(
        "SELECT     
                utilisateur.id AS id,
                utilisateur.email AS email,
                utilisateur.password AS motDePasse,
                utilisateur.token AS token,
                utilisateur.nom AS nom,
                utilisateur.prenom AS prenom,
                utilisateur.telephone AS telephone,
                utilisateur.date_naissance AS dateNaissance,
                utilisateur.adresse AS adresse,
                role_utilisateur.id AS role_id,
                role_utilisateur.nom AS role,                        
                role_utilisateur.description AS role_description
            FROM
                utilisateur LEFT JOIN role_utilisateur ON utilisateur.ref_role = role_utilisateur.id 
            WHERE email = ?",
        array($email)
	);

	if (!is_array($resultat) || empty($resultat))
	{
		Connexion_Erreur($email);
        Http_Redirect("*/");
	}
	else if ($resultat["role"] != $_SESSION["pageConnexion"]["roleChoisi"])
	{
        Connexion_Erreur($email);
        Http_Redirect("*/");
	}
    else if (!VerifierMDP($donnees["mdp"], $resultat["motDePasse"]))
    {
        Connexion_Erreur($email);
        Http_Redirect("*/");
    }
	else
	{
		if (!App_ConnecterUtilisateur($resultat))
		{
			Form_SetError(APP_FORM_AUTHENTIFICATION["id"], "authentifier", "Erreur interne !");
			Http_Redirect("*/");
		}
		Form_ClearValues(APP_FORM_AUTHENTIFICATION["id"]);
        $_SESSION["utilisateur"]["denomination"] = $resultat["nom"] . " " . $resultat["prenom"];
		App_RedirigerVersPage("ACCUEIL");
	}
}

function Connexion_Erreur($email){
    Form_SetError(APP_FORM_AUTHENTIFICATION["id"], "authentifier", "Le login ou le mot de passe ne semblent pas valides !. Assurez-vous �galement d'avoir le bon privil�ge");
    Form_SetValue(APP_FORM_AUTHENTIFICATION["id"], "email", $email);
}

function Authentification_Deconnecter()
{
	if (App_EstVisiteur()) Http_Redirect("*/");
	App_DefinirRole(APP_ROLE_VISITEUR);
	App_RedirigerVersPage("ACCUEIL");
}

// Hacher le mot de passe
function HacherMDP($password)
{
    // G�n�rer un sel
    $salt = random_bytes(16);

    // G�n�rer le hachage
    $iterations = 10000;
    $hash = hash_pbkdf2("sha1", $password, $salt, $iterations, 20, true);

    // Combiner le sel et le hachage
    $hashBytes = $salt . $hash;

    // Convertir en cha�ne base64
    $savedPasswordHash = base64_encode($hashBytes);
    return $savedPasswordHash;
}

// V�rifier le mot de passe
function VerifierMDP($enteredPassword, $storedHash)
{
    // Convertir le hash stock� en bytes
    $hashBytes = base64_decode($storedHash);

    // Extraire le sel
    $salt = substr($hashBytes, 0, 16);

    // G�n�rer le hachage � partir du mot de passe entr�
    $iterations = 10000;
    $hash = hash_pbkdf2("sha1", $enteredPassword, $salt, $iterations, 20, true);

    // Comparer le hachage g�n�r� avec le hachage stock�
    for ($i = 0; $i < 20; $i++) {
        if ($hashBytes[$i + 16] !== $hash[$i]) {
            return false;
        }
    }
    return true;
}
?>