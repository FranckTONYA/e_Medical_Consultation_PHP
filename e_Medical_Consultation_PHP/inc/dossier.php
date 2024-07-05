<?php
Pdweb_IncludeLib("pdweb.html.php");

define("APP_FORM_DOSSIER", array
(
	"id" => "dossier",
	"url" => "*/.controleur.php",
	"elements" => array()
));

function Dossier_Afficher()
{
    Form_ClearErrors(APP_FORM_DOSSIER["id"]);
    Form_ClearValues(APP_FORM_DOSSIER["id"]);

    Html_GenerateForm(APP_FORM_DOSSIER);
    echo '
        <h1>Fonctionnalité pas encore disponible...</h1>
    ';
}

?>
