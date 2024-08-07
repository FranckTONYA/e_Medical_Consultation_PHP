<?php
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Ce fichier de configuration est obligatoire par site, en un seul exemplaire, et doit �tre plac� dans la racine du site ! //
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

if (!defined("CHARSET_ANSI")) define("CHARSET_ANSI", "windows-1252");
if (!defined("CHARSET_UTF8")) define("CHARSET_UTF8", "utf-8");

// Configuration de ce site vis à vis de la biliothèque PDWEB
define("PDWEB_CHARSET", CHARSET_ANSI);
define("PDWEB_LIB_PATH", "_lib");

// Configuration de ce site vis à vis de l'application ici présente
define("APP_TITRE", mb_convert_encoding("Consultation Médicale en ligne", 'Windows-1252', 'UTF-8'));
define("APP_JS", array("*/js/jquery-3.7.1.js", "*/js/app.js"));
define("APP_CSS", "*/res/base.css");
define("APP_PIEDPAGE", mb_convert_encoding("Version 2024 - Consultation Médicale en ligne",'Windows-1252', 'UTF-8'));

// Configuration de la connexion au serveur MySQL et à la base de données de l'application
define("APP_MYSQL_USERNAME", "u_consultation");
define("APP_MYSQL_PASSWORD", "YY836O3eew!Vy0OQ");
define("APP_MYSQL_SERVER", "localhost");
define("APP_MYSQL_DBNAME", "consultation");

?>