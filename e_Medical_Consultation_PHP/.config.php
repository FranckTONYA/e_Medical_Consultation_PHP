<?php
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Ce fichier de configuration est obligatoire par site, en un seul exemplaire, et doit être placé dans la racine du site ! //
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

if (!defined("CHARSET_ANSI")) define("CHARSET_ANSI", "windows-1252");
if (!defined("CHARSET_UTF8")) define("CHARSET_UTF8", "utf-8");

// Configuration de ce site vis à vis de la biliothèque PDWEB
define("PDWEB_CHARSET", CHARSET_ANSI);
define("PDWEB_LIB_PATH", "_lib");

// Configuration de ce site vis à vis de l'application ici présente
define("APP_TITRE", "AeVuA - Apprenez en vivant une aventure");
define("APP_JS", array("*/js/jquery-3.7.1.js", "*/js/app.js"));
define("APP_CSS", "*/res/base.css");
define("APP_PIEDPAGE", "Version 2024 - Apprenez en Vivant une Aventure");

// Configuration de la connexion au serveur MySQL et à la base de données de l'application
define("APP_MYSQL_USERNAME", "u_aevua");
define("APP_MYSQL_PASSWORD", "ZKE4iz3qXmS8tYs8N");
define("APP_MYSQL_SERVER", "localhost");
define("APP_MYSQL_DBNAME", "aevua");

?>