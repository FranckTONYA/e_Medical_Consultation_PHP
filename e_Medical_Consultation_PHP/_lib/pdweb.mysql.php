<?php
/**********************************************************/
/* Bibliothèque de fonctionnalités pour le cours de PDWEB */
/**********************************************************/

// -----------------------------------------------------
// FONCTIONS DE MANIPULATION D'UNE BASE DE DONNEES MYSQL
// -----------------------------------------------------

function MySql_SetConnection($username, $password, $databaseName, $serverAddress = null, $charset = null)
{
	global $PDWEB_MYSQL_CONN;
	if (!is_string($username) || empty($username = trim($username))) return false;
	if (!is_string($password) || empty($password)) return false;
	if (!is_string($databaseName) || empty($databaseName = trim($databaseName))) return false;
	if ($serverAddress === null) $serverAddress = "localhost";
	if (!is_string($serverAddress) || empty($serverAddress = trim($serverAddress))) return false;
	if ($charset === null) $charset = defined("PDWEB_CHARSET") ? PDWEB_CHARSET : CHARSET_ANSI;
	if (!is_string($charset) || empty($charset = trim($charset))) return false;
	$PDWEB_MYSQL_CONN = array
	(
		"parameters" => array
		(
			"username" => $username,
			"password" => $password,
			"databaseName" => $databaseName,
			"serverAddress" => $serverAddress,
			"charset" => $charset
		),
		"pdo" => null
	);
	$_SESSION["PDWEB_MYSQL_CONN"] = $PDWEB_MYSQL_CONN["parameters"];
	return true;
}

function MySql_GetConnection()
{
	global $PDWEB_MYSQL_CONN;
	if (!isset($PDWEB_MYSQL_CONN))
	{
		if (!isset($_SESSION))
		{
			@session_start();
			if (!isset($_SESSION)) return false;
		}
		if (!isset($_SESSION["PDWEB_MYSQL_CONN"])) return false;
		$PDWEB_MYSQL_CONN = array
		(
			"parameters" => $_SESSION["PDWEB_MYSQL_CONN"],
			"pdo" => null
		);
	}
	if ($PDWEB_MYSQL_CONN["pdo"] !== null) return $PDWEB_MYSQL_CONN["pdo"];
	try
	{
		$charset = $PDWEB_MYSQL_CONN["parameters"]["charset"];
		if ($charset == CHARSET_ANSI)
		{
			$charset = "latin1";
		}
		else if ($charset == CHARSET_UTF8)
		{
			$charset = "utf8mb4";
		}
		$PDWEB_MYSQL_CONN["pdo"] = @new PDO
		(
			"mysql:dbname=" . $PDWEB_MYSQL_CONN["parameters"]["databaseName"] . ";host=" . $PDWEB_MYSQL_CONN["parameters"]["serverAddress"],
			$PDWEB_MYSQL_CONN["parameters"]["username"],
			$PDWEB_MYSQL_CONN["parameters"]["password"],
			array
			(
				PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
				PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES $charset;SET CHARSET $charset;"
			)
		);
	}
	catch (Exception $error)
	{
		return MySql_NotifyError("Erreur de connexion", "", null, $error);
	}
	return $PDWEB_MYSQL_CONN["pdo"];
}

function MySql_NotifyError($message, $sql, $arguments, $error)
{
	$filename = defined("PDWEB_MYSQL_LOG") ? PDWEB_MYSQL_LOG : "*/../mysql.log";
	$args = "";
	if (is_array($arguments))
	{
		foreach ($arguments as $key=>$value)
		{
			$args .= "\t";
			$args .= "$key = " . str_replace(array("\\", "\t", "\r\n", "\n", "\r"), array("\\\\", "\\t", "\\n", "\\n", "\\n"), "$value");
		}
	}
	if (is_a($error, "Exception"))
	{
		$error = $error->getMessage();
	}
	if (!is_string($error)) $error = "!";
	$sql = str_replace(array("\\", "\t", "\r\n", "\n", "\r"), array("\\\\", "\\t", "\\n", "\\n", "\\n"), $sql);
	$error = str_replace(array("\\", "\t", "\r\n", "\n", "\r"), array("\\\\", "\\t", "\\n", "\\n", "\\n"), $error);
	@file_put_contents
	(
		Url_PathTo($filename, false),
		"$message\t$sql$args\t$error\r\n",
		FILE_APPEND
	);
	return false;
}

function MySql_Execute($sql, $arguments = null)
{
	if (($conn = MySql_GetConnection()) === false) return MySql_NotifyError("Erreur d'exécution d'une requête d'action", $sql, $arguments, "Connexion non établie !");
	$sqlType = strtoupper(substr(trim($sql), 0, 6));
	if (!in_array($sqlType, array("INSERT", "UPDATE", "DELETE"))) return MySql_NotifyError("Erreur d'exécution d'une requête d'action", $sql, $arguments, "Il ne s'agit pas d'une requête d'action !");
	try
	{
		$command = @$conn->prepare($sql);
		@$command->execute($arguments);
		$affectedRowCount = $command->rowCount();
		if (($sqlType == "INSERT") && ($affectedRowCount == 1)) return $conn->lastInsertId();
		return $affectedRowCount;
	}
	catch (Exception $error)
	{
		return MySql_NotifyError("Erreur d'exécution d'une requête d'action", $sql, $arguments, $error);
	}
}

function MySql_Value($sql, $arguments = null, $defaultValue = null)
{
	if (($conn = MySql_GetConnection()) === false) return MySql_NotifyError("Erreur d'exécution d'une requête de consultation d'une value", $sql, $arguments, "Connexion non établie !");
	$sqlType = strtoupper(substr(trim($sql), 0, 4));
	if (!in_array($sqlType, array("SELE", "SHOW", "CALL"))) return MySql_NotifyError("Erreur d'exécution de consultation d'une value", $sql, $arguments, "Il ne s'agit pas d'une requête de consultation !");
	try
	{
		$command = @$conn->prepare($sql);
		@$command->execute($arguments);
		if (($row = $command->fetch(PDO::FETCH_NUM)) === false) return $defaultValue;
		return $row[0];
	}
	catch (Exception $error)
	{
		return MySql_NotifyError("Erreur d'exécution d'une requête de consultation de value", $sql, $arguments, $error);
	}
}

function MySql_Row($sql, $arguments = null)
{
	if (($conn = MySql_GetConnection()) === false) return MySql_NotifyError("Erreur d'exécution d'une requête de consultation d'un rowistrement", $sql, $arguments, "Connexion non établie !");
	$sqlType = strtoupper(substr(trim($sql), 0, 4));
	if (!in_array($sqlType, array("SELE", "SHOW", "CALL"))) return MySql_NotifyError("Erreur d'exécution de consultation d'un rowistrement", $sql, $arguments, "Il ne s'agit pas d'une requête de consultation !");
	try
	{
		$command = @$conn->prepare($sql);
		@$command->execute($arguments);
		if (($row = $command->fetch(PDO::FETCH_BOTH)) === false) return null;
		return $row;
	}
	catch (Exception $error)
	{
		return MySql_NotifyError("Erreur d'exécution d'une requête consultation d'un rowistrement", $sql, $arguments, $error);
	}
}

function MySql_Rows($sql, $arguments = null)
{
	if (($conn = MySql_GetConnection()) === false)
	{
		MySql_NotifyError("Erreur d'exécution d'une requête de consultation d'un rowistrement", $sql, $arguments, "Connexion non établie !");
		return CRowIterator::None();
	}
	$sqlType = strtoupper(substr(trim($sql), 0, 4));
	if (!in_array($sqlType, array("SELE", "SHOW", "CALL")))
	{
		MySql_NotifyError("Erreur d'exécution de consultation d'un rowistrement", $sql, $arguments, "Il ne s'agit pas d'une requête de consultation !");
		return CRowIterator::None();
	}
	try
	{
		$command = @$conn->prepare($sql);
		@$command->execute($arguments);
		return new CRowIterator($command, $sql, $arguments);
	}
	catch (Exception $error)
	{
		MySql_NotifyError("Erreur d'exécution d'une requête consultation d'un rowistrement", $sql, $arguments, $error);
		return CRowIterator::None();
	}
}

// Classe permettant l'itération sur les résultats de requête de consultation (classe utilisée conjointement avec la classe CDatabase)
class CRowIterator implements Iterator
{
	// Membre statique privé stockant une itération d'aucun rowistrement
	private static $s_None;

	// Accesseur statique publique fournissant une itération d'aucun rowistrement
	public static function None()
	{
		if (CRowIterator::$s_None == null) CRowIterator::$s_None = new CRowIterator(null);
		return CRowIterator::$s_None;
	}

	// Membre privé référençant l'objet résultant de l'exécution d'une requête de consultation
	private $m_Command;
	
	// Membre privé contenant le texte de la requête SQL
	private $m_Sql;

	// Membre privé contenant les éventuelles arguments des parties variables de la requête SQL
	private $m_Arguments;

	// Membre privé stockant le contenu du dernier rowistrement lu
	private $m_Row;
	
	// Membre privé stockant l'indice du dernier rowistrement lu
	private $m_RowNumber;
	
	// Contructeur de cet objet d'itération des rowistrements résultant d'une requête de consultation
	public function __construct($command, $sql = "", $arguments = null)
	{
		$this->m_Command = $command;
		$this->m_Sql = $sql;
		$this->m_Arguments = $arguments;
		$this->m_Row = false;
		$this->m_RowNumber = 0;
	}
	
	// Méthode permettant de récupérer l'élément courant de l'itération
	public function current()
	{
		return is_array($this->m_Row) ? $this->m_Row : array();
	}
	
	// Méthode permettant de récupérer la clé de l'élément courant de l'itération
	public function key()
	{
		return $this->m_RowNumber;
	}
	
	// Méthode permettant de passer à l'élément suivant de l'itération
	public function next()
	{
		$this->m_RowNumber++;
	}
	
	// Méthode permettant de recommencer l'itération (si possible)
	public function rewind()
	{
		$this->m_RowNumber = 0;
	}
	
	// Méthode permettant de vérifier si un autre élément est encore disponible dans l'itération en cours
	public function valid()
	{
		if ($this->m_Command == null) return false;
		try
		{
			$this->m_Row = @$this->m_Command->fetch(PDO::FETCH_BOTH);
			$continue = ($this->m_Row !== false);
			if (!$continue)
			{
				$continue = @$this->m_Command->nextRowset();
				if ($continue) $this->m_RowNumber = 0;
			}
			return $continue;
		}
		catch (Exception $error)
		{
			return MySql_NotifyError("Impossible de lire un rowistrement à travers l'itérateur", $this->m_Sql, $this->m_Arguments, $error->getMessage());
		}
	}
};
?>