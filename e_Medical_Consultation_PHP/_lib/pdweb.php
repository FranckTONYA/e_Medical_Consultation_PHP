<?php
if (!defined("CHARSET_ANSI")) define("CHARSET_ANSI", "windows-1252");
if (!defined("CHARSET_UTF8")) define("CHARSET_UTF8", "utf-8");
if (!isset($_SESSION)) session_start();

Http_DeclareCharset(defined("PDWEB_CHARSET") ? PDWEB_CHARSET : null);

function Http_DeclareCharset($charset = null)
{
	global $_HTML_CHARSET_;
	if (($charset !== CHARSET_ANSI) && ($charset !== CHARSET_UTF8)) $charset = CHARSET_ANSI;
	$_HTML_CHARSET_ = $charset;
	header("content-type:text/html;charset=$charset");
}

function Http_Redirect($url = null, $checkIfExists = null)
{
	if (($url=Url_PathTo($url, $checkIfExists)) === false) return false;
	header("location:$url");
	die();
}

function Pdweb_IncludeLib($url = null)
{
	$url = PDWEB_PATH_TO_LIB . $url;
	if (!file_exists($url)) return false;
	include_once($url);
	return true;
}

function Pdweb_Include($url = null)
{
	if (!($url = Url_PathTo($url))) return false;
	include_once($url);
	return true;
}

function Url_PathTo($url = null, $checkIfExists = null)
{
	if (!is_string($url)) return false;
	if (empty($url=trim($url))) return false;
	if ($url[0] == "*")
	{
		if (strlen($url) < 2) $url = "*/";
		if (!defined("PDWEB_PATH_TO_ROOT")) die("<p>Utilisation de Url_PathTo sans connaître le chemin ascendant vers la racine du site !</p>");
		$url = PDWEB_PATH_TO_ROOT . substr($url, ($url[1] == "/") ? 2 : 1);
	}
	if (!is_bool($checkIfExists)) $checkIfExists = true;
	if ($checkIfExists && (strpos($url, ":") === false) && (strpos($url, "//") === false))
	{
		$urlToFile = $url;
		if (($getSeparatorIndex=strpos($urlToFile, "?")) !== false) $urlToFile = substr($urlToFile, 0, $getSeparatorIndex);
		if (!file_exists($urlToFile)) return false;
	}
	return $url;
}

function IsCallable($content)
{
	return (!is_string($content)
			|| (!empty($content) && ($content[0] == strtoupper($content[0])))
			) && is_callable($content);
}

function String_Get($content = null, ...$arguments)
{
	if (IsCallable($content))
	{
		try
		{
			$content = @$content(...$arguments);
		}
		catch(Exception $error)
		{
			return false;
		}
	}
	if (is_bool($content))
	{
		$content = $content ? "true" : "false";
	}
	else if (is_numeric($content))
	{
		$content = "$content";
	}
	return is_string($content) ? $content : false;
}

function String_HtmlContent($text = null)
{
	if (($text=String_Get($text)) === false) return "";
	return str_replace(array("<", ">", "&"), array("&lt;", "&gt;", "&amp;"), $text);
}

function String_HtmlAttribute($name = null, $value = null)
{
	if ((($name=String_Get($name)) === false) || empty($name=trim($name))) return false;
	$result = $name;
	if (($value=String_Get($value)) !== false)
	{
		$result .= "=\"" . str_replace(array("<", ">", "\"", "&", "\r\n", "\r", "\n"), array("&lt;", "&gt;", "&quot;", "&amp;", "&#10;", "&#10;", "&#10;"), $value) . "\"";
	}
	return $result;
}

function Session_CheckAvailability()
{
	if (!isset($_SESSION))
	{
		if (!@session_start()) die("<p>Session can't start session !</p>");
	}
	return true;
}

function Pdweb_IsInteger($text = null)
{
	return is_numeric($text) && (strpos("$text", ".") === false);
}

function Pdweb_IsFloat($text = null)
{
	return is_numeric($text);
}

function Pdweb_RemoveIndexedItems(&$array)
{
	if (!is_array($array)) return false;
	$keysToRemove = array();
	foreach ($array as $key => $value)
	{
		if (is_int($key) || is_long($key)) $keysToRemove[] = $key;
	}
	foreach ($keysToRemove as $key)
	{
		unset($array[$key]);
	}
	return count($keysToRemove);
}

function Pdweb_RemoveNamedItems(&$array)
{
	if (!is_array($array)) return false;
	$keysToRemove = array();
	foreach ($array as $key => $value)
	{
		if (is_string($key)) $keysToRemove[] = $key;
	}
	foreach ($keysToRemove as $key)
	{
		unset($array[$key]);
	}
	return count($keysToRemove);
}

function Pdweb_IsLocal()
{
	return (isset($_SERVER["HTTP_REFERER"]) && (strpos($_SERVER["HTTP_REFERER"], "/localhost/") !== false))
	    || (isset($_SERVER["HTTP_HOST"]) && (strpos($_SERVER["HTTP_HOST"], "/localhost/") !== false))
	    || (isset($_SERVER["SERVER_NAME"]) && (strpos($_SERVER["SERVER_NAME"], "/localhost/") !== false));
}

function Pdweb_ExternalUrl($url = null, $checkIfExists = null)
{
	$url = Url_PathTo($url, $checkIfExists);
	if (!is_string($url)) return false;
	if (isset($_SERVER["HTTP_REFERER"]) && !empty($_SERVER["HTTP_REFERER"]))
	{
		$rootUrl = $_SERVER["HTTP_REFERER"];
		if (substr($rootUrl, -1) != "/") $rootUrl .= "/";
	}
	else
	{
		$externalUrl = "$_SERVER[REQUEST_SCHEME]::$_SERVER[HTTP_HOST]$_SERVER[CONTEXT_PREFIX]";
		if (substr($externalUrl, -1) != "/") $externalUrl .= "/";
		$n1 = strlen($_SERVER["CONTEXT_DOCUMENT_ROOT"]);
		$n2 = strlen($_SERVER["SCRIPT_FILENAME"]);
		if ($n2 < $n1) return false;
		$internalUrl = dirname(substr($_SERVER["SCRIPT_FILENAME"], $n1));
		if (substr($internalUrl, 0, 1) == "/") $internalUrl = substr($internalUrl, 1);
		if (substr($internalUrl, -1) != "/") $internalUrl .= "/";
		$rootUrl = $externalUrl . $internalUrl;
	}
	if (substr($url, 0, 1) == "/") $url = substr($url, 1);
	if (substr($url, 0, 2) == "./") $url = substr($url, 2);
	return $rootUrl . $url;
}

function Pdweb_SendMail($receiverAddress, $subject, $content, $replyAddress=null, $senderAddress=null, $charset=null)
{
	if (!is_string($receiverAddress) || !filter_var($receiverAddress, FILTER_VALIDATE_EMAIL)) return false;
	if (!is_string($subject) || empty($subject)) return false;
	if (!is_string($content) || empty($content)) return false;
	if (!is_string($charset) || (($charset != CHARSET_ANSI) && ($charset != CHARSET_UTF8)))
	{
		$charset = defined("PDWEB_CHARSET") ? PDWEB_CHARSET : CHARSET_ANSI;
	}
	if ($senderAddress === null)
	{
		$senderAddress = ini_get("sendmail_from");
	}
	if (!is_string($replyAddress) || !filter_var($replyAddress, FILTER_VALIDATE_EMAIL))
	{
		$replyAddress = $senderAddress;
	}
	$headers = "From: $senderAddress\r\n";
	$headers .= "Reply-To: $replyAddress\r\n";
	$headers .= "X-Mailer: Microsoft Outlook 15.0\r\n";
	$headers .= "Content-type: text/html;charset=$charset\r\n";
	if (Pdweb_IsLocal())
	{
		if (($rootMailFolder = Url_PathTo("*/../aevua.mail/", false)) === false) return false;
		$receiverMailFolder = $rootMailFolder . "$receiverAddress/";
		$mailFilename = $receiverMailFolder . date("YmdHis") . ".txt";
		$mailData = "MAILTO $receiverAddress\r\n"
		          . "\r\n"
		          . $headers
		          . "\r\n"
		          . "$subject\r\n"
		          . "\r\n"
		          . $content;
		if (!file_exists($rootMailFolder))
		{
			if (!@mkdir($rootMailFolder)) return false;
		}
		if (!file_exists($receiverMailFolder))
		{
			if (!@mkdir($receiverMailFolder)) return false;
		}
		if (file_put_contents($mailFilename, $mailData) === false) return false;
		return true;
	}
	else
	{
		return @mail($receiverAddress, $subject, $content, $headers);
	}
}
?>