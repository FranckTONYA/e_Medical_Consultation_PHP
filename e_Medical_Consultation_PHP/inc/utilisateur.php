<?php
Pdweb_IncludeLib("pdweb.html.php");

define("APP_FORM_UTILISATEUR", array
(
	"id" => "utilisateur",
	"url" => "*/.controleur.php",
	"elements" => array()
));

function Utilisateur_Afficher()
{
    Form_ClearErrors(APP_FORM_UTILISATEUR["id"]);
    Form_ClearValues(APP_FORM_UTILISATEUR["id"]);

    Html_GenerateForm(APP_FORM_UTILISATEUR);
    echo '
        <h1>Fonctionnalité pas encore disponible...</h1>
    ';
}

?>
