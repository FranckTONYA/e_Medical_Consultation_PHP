<?php
if (!defined("HTML_CONTENT")) define("HTML_CONTENT", array());

function Html_GenerateDocument($title = null, $jsUrls = null, $cssUrls = null, $bodyContent = null)
{
	if (!is_array($bodyContent)) $bodyContent = array();
	print("<!doctype html>\r\n\r\n");
	Html_GenerateG("html", HTML_CONTENT, function($title, $jsUrls, $cssUrls, $bodyContent)
	{
		Html_GenerateG("head", HTML_CONTENT, function($title, $jsUrls, $cssUrls)
		{
			global $_HTML_CHARSET_;
			if (isset($_HTML_CHARSET_))
			{
				Html_GenerateA("meta", "charset", $_HTML_CHARSET_);
				Html_GenerateA("meta", "http-equiv", "content-type", "content", "text/html;charset=$_HTML_CHARSET_");
			}
			if (($title=String_Get($title)) !== false)
			{
				Html_GenerateOC("title", HTML_CONTENT, $title);
			}
			foreach (array("Html_GenerateIncludeCss" => $cssUrls, "Html_GenerateIncludeJs" => $jsUrls) as $fctn => $urls)
			{
				if (is_string($urls))
				{
					$fctn($urls);
				}
				else if (is_array($urls))
				{
					foreach ($urls as $url)
					{
						$fctn($url);
					}
				}
			}
		}, $title, $jsUrls, $cssUrls);
		Html_GenerateG("body", HTML_CONTENT, function($title, $bodyContent)
		{
			$title = String_Get($title);
			foreach ($bodyContent as $tagName => $tagContent)
			{
				if (in_array(strtolower($tagName), array("header", "main", "footer", "aside")))
				{
					$content = is_array($tagContent) ? $tagContent : array($tagContent);
					$contentIsCallable = IsCallable($content[0]);
					if ($contentIsCallable)
					{
						array_splice($content, 1, 0, $title);
					}
					
					array_splice($content, 0, 0, array(HTML_CONTENT));
					if ($contentIsCallable) Html_GenerateG($tagName, ...$content); else Html_GenerateOC($tagName, ...$content);
				}
			}
		}, $title, $bodyContent);
	}, $title, $jsUrls, $cssUrls, $bodyContent);
}

function Html_GenerateIncludeJs($url = null, $checkIfExists = null)
{
	if (($url=Url_PathTo($url, $checkIfExists)) === false) return false;
	Html_GenerateOC("script", "src", $url);
	return true;
}

function Html_GenerateIncludeCss($url = null, $checkIfExists = null)
{
	if (($url=Url_PathTo($url, $checkIfExists)) === false) return false;
	Html_GenerateA("link", "rel", "stylesheet", "type", "text/css", "href", $url);
	return true;
}

// Permet de générer un élément HTML à balises ouvrante/fermante avec un contenu qui sera généré par l'appel d'une fonction
function Html_GenerateG($tagName = null, ...$attributeNameValuePairs_ContentGenerator)
{
	return _Html_GenerateElement_(false, true, $tagName, ...$attributeNameValuePairs_ContentGenerator);
}

// Permet de générer un élément HTML à balises ouvrante/fermante avec éventuellement le contenu spécifié
function Html_GenerateOC($tagName = null, ...$attributeNameValuePairs_Content)
{
	return _Html_GenerateElement_(false, false, $tagName, ...$attributeNameValuePairs_Content);
}

// Permet de générer un élément HTML à balise autonome
function Html_GenerateA($tagName = null, ...$attributeNameValuePairs)
{
	return _Html_GenerateElement_(true, false, $tagName, ...$attributeNameValuePairs);
}

// Permet de générer un commentaire HTML
function Html_GenerateCM(...$comment)
{
	if (!is_array($comment)) $comment = array($comment);
	if (!empty($comment) && !is_array($comment[0])) array_splice($comment, 0, 0, array(HTML_CONTENT));
	return _Html_GenerateElement_(true, false, "!--", ...$comment);
}

// Permet de générer une liste à puce avec contenu de ses éléments
function Html_GenerateUL($content = null, ...$attributeNameValuePairs)
{
	return _Html_GenerateList_("ul", false, $content, ...$attributeNameValuePairs);
}

// Permet de générer une liste numérotée avec contenu de ses éléments
function Html_GenerateOL($content = null, ...$attributeNameValuePairs)
{
	return _Html_GenerateList_("ol", false, $content, ...$attributeNameValuePairs);
}

// Permet de générer une liste à puce avec générateur du contenu de ses éléments
function Html_GenerateUL_G($content = null, ...$attributeNameValuePairs)
{
	return _Html_GenerateList_("ul", true, $content, ...$attributeNameValuePairs);
}

// Permet de générer une liste à puce avec générateur du contenu de ses éléments
function Html_GenerateOL_G($content = null, ...$attributeNameValuePairs)
{
	return _Html_GenerateList_("ol", true, $content, ...$attributeNameValuePairs);
}

function Html_GenerateForm($form = null)
{
	if (!is_array($form) || !isset($form["id"], $form["url"], $form["elements"])
		||	!is_string($form["id"]) || empty($formId = trim($form["id"]))
		||	(($url = Url_PathTo($form["url"])) === false)
		||	!is_array($form["elements"])
		) return false;
	return Html_GenerateG("form", "id", $formId, "method", "post", "action", $url, "enctype", "multipart/form-data", HTML_CONTENT, function($formId, $form)
	{
		foreach ($form["elements"] as $elementKey => $element)
		{
			if (!is_array($element) || !isset($element["name"], $element["type"])
				|| !is_string($element["name"]) || empty($elementName = trim($element["name"]))
				|| !is_string($element["type"]) || empty($elementType = strtolower(trim($element["type"])))
				) return false;
			if ($elementType != "hidden") continue;
			$elementId = $formId . "__" . $elementName;
			$attributes = array();
			$attributes[] = "value";
			$attributes[] = isset($element["value"]) && is_string($element["value"]) && !empty($value = trim($element["value"])) ? $value : "";
			if (!Html_GenerateA("input", "type", $elementType, "id", $elementId, "name", $elementName, ...$attributes)) return false;
		}
		return Html_GenerateG("ul", HTML_CONTENT, function($formId, $form)
		{
			foreach ($form["elements"] as $elementKey => $element)
			{
				if (!is_array($element) || !isset($element["name"], $element["type"])
					|| !is_string($element["name"]) || empty($elementName = trim($element["name"]))
					|| !is_string($element["type"]) || empty($elementType = strtolower(trim($element["type"])))
					) return false;
				if ($elementType == "hidden") continue;
				$elementLabel = isset($element["label"]) && is_string($element["label"]) && !empty($label = trim($element["label"])) ? $label : false;
				$elementId = $formId . "__" . $elementName;
				$attributes = array();
				$attributes[] = "data-name";
				$attributes[] = $elementName;
				if (Form_GetError($formId, $elementName) !== false)
				{
					$attributes[] = "class";
					$attributes[] = "error";
				}
				$attributes[] = HTML_CONTENT;
				$attributes[] = function($formId, $elementId, $elementName, $elementType, $elementLabel, $element)
				{
					$typeAttribute = $elementType;
					$attributes = array();
					switch ($elementType)
					{
						case "text":
						case "password":
						case "textarea":
							foreach ($element as $key=>$value)
							{
								$attributeName = strtolower($key);
								if (in_array($attributeName, array("minlength", "maxlength", "required", "placeholder")))
								{
									$attributes[] = $attributeName;
									$attributes[] = $value;
								}
							}
							if ($elementType != "textarea")
							{
								if (($value=Form_GetValue($formId, $elementName)) !== false)
								{
									$attributes[] = "value";
									$attributes[] = $value;
								}
							}
							else
							{
								$typeAttribute = false;
								if (($value=Form_GetValue($formId, $elementName)) !== false)
								{
									$attributes[] = HTML_CONTENT;
									$attributes[] = $value;
								}
							}
							break;
						case "number":
							foreach ($element as $key=>$value)
							{
								$attributeName = strtolower($key);
								if (in_array($attributeName, array("min", "max", "inc", "required", "placeholder")))
								{
									$attributes[] = $attributeName;
									$attributes[] = $value;
								}
							}
							if ((($value=Form_GetValue($formId, $elementName)) !== false) && is_numeric($value))
							{
								$attributes[] = "value";
								$attributes[] = $value;
							}
							break;
						case "submit":
							$attributes[] = "value";
							$attributes[] = isset($element["value"]) && is_string($element["value"]) && !empty($value = trim($element["value"])) ? $value : "";
							break;
						case "file":
						case "image":
							if ($elementType == "image")
							{
								$attributes[] = "data-filetype";
								$attributes[] = "image";
							}
							$typeAttribute = "file";
							break;
						default:
							return false;
					}
					if (($elementLabel !== false) && !Html_GenerateOC("label", "for", $elementId, HTML_CONTENT, $elementLabel)) return false;
					if ($typeAttribute !== false)
					{
						if (!Html_GenerateA("input", "type", $typeAttribute, "id", $elementId, "name", $elementName, ...$attributes)) return false;
					}
					else if ($elementType == "textarea")
					{
						if (!Html_GenerateOC("textarea", "id", $elementId, "name", $elementName, ...$attributes)) return false;
					}
					else
					{
						$attributes[] = HTML_CONTENT;
						$attributes[] = function($formId, $elementId, $elementName, $element)
						{
							// code de génération des options de ce select
						};
						$attributes[] = $formId;
						$attributes[] = $elementId;
						$attributes[] = $elementName;
						$attributes[] = $element;
						if (!Html_GenerateG("select", "id", $elementId, "name", $elementName, ...$attributes)) return false;
					}
					if (($errorMessage=Form_GetError($formId, $elementName)) !== false)
					{
						Html_GenerateOC("span", "class", "error", HTML_CONTENT, $errorMessage);
					}
				};
				$attributes[] = $formId;
				$attributes[] = $elementId;
				$attributes[] = $elementName;
				$attributes[] = $elementType;
				$attributes[] = $elementLabel;
				$attributes[] = $element;
				if (!Html_GenerateG("li", ...$attributes)) return false;
			}
		}, $formId, $form);
	}, $formId, $form);
}

// Fonction permettant de supprimer tous les messages d'erreur d'un formulaire
function Form_ClearErrors($formId = null)
{
	if (!is_string($formId) || empty($formId)) return false;
	Session_CheckAvailability();
	if (!isset($_SESSION["pdweb_forms"])) $_SESSION["pdweb_forms"] = array();
	if (isset($_SESSION["pdweb_forms"][$formId])) unset($_SESSION["pdweb_forms"][$formId]["errorMessages"]);
	return true;
}

// Fonction permettant de définir un message d'erreur sur un élément d'un formulaire
function Form_SetError($formId = null, $elementName = null, $errorMessage = null)
{
	if (!is_string($formId) || empty($formId)) return false;
	if (!is_string($elementName) || empty($elementName)) return false;
	if (!is_string($errorMessage) || empty($errorMessage)) return false;
	Session_CheckAvailability();
	if (!isset($_SESSION["pdweb_forms"])) $_SESSION["pdweb_forms"] = array();
	if (!isset($_SESSION["pdweb_forms"][$formId])) $_SESSION["pdweb_forms"][$formId] = array("errorMessages");
	if (!isset($_SESSION["pdweb_forms"][$formId]["errorMessages"])) $_SESSION["pdweb_forms"][$formId]["errorMessages"] = array();
	$_SESSION["pdweb_forms"][$formId]["errorMessages"][$elementName] = $errorMessage;
	return true;
}

// Fonction permettant de récupérer si possible le message d'erreur associé à un élément d'un formulaire
function Form_GetError($formId = null, $elementName = null)
{
	if (!is_string($formId) || empty($formId)) return false;
	if (!is_string($elementName) || empty($elementName)) return false;
	Session_CheckAvailability();
	if (!isset($_SESSION["pdweb_forms"])) $_SESSION["pdweb_forms"] = array();
	if (!isset($_SESSION["pdweb_forms"][$formId], $_SESSION["pdweb_forms"][$formId]["errorMessages"], $_SESSION["pdweb_forms"][$formId]["errorMessages"][$elementName])) return false;
	return $_SESSION["pdweb_forms"][$formId]["errorMessages"][$elementName];
}

// Fonction permettant de supprimer toutes les valeurs d'un formulaire
function Form_ClearValues($formId = null)
{
	if (!is_string($formId) || empty($formId)) return false;
	Session_CheckAvailability();
	if (!isset($_SESSION["pdweb_forms"])) $_SESSION["pdweb_forms"] = array();
	if (isset($_SESSION["pdweb_forms"][$formId])) unset($_SESSION["pdweb_forms"][$formId]["values"]);
	return true;
}

// Fonction permettant de définir une valeur sur un élément d'un formulaire
function Form_SetValue($formId = null, $elementName = null, $value = null)
{
	if (!is_string($formId) || empty($formId)) return false;
	if (!is_string($elementName) || empty($elementName)) return false;
	if (!is_string($value) && !is_numeric($value)) return false;
	Session_CheckAvailability();
	if (!isset($_SESSION["pdweb_forms"])) $_SESSION["pdweb_forms"] = array();
	if (!isset($_SESSION["pdweb_forms"][$formId])) $_SESSION["pdweb_forms"][$formId] = array("values");
	if (!isset($_SESSION["pdweb_forms"][$formId]["values"])) $_SESSION["pdweb_forms"][$formId]["values"] = array();
	$_SESSION["pdweb_forms"][$formId]["values"][$elementName] = $value;
	return true;
}

// Fonction permettant de récupérer si possible la valeur associée à un élément d'un formulaire
function Form_GetValue($formId = null, $elementName = null)
{
	if (!is_string($formId) || empty($formId)) return false;
	if (!is_string($elementName) || empty($elementName)) return false;
	Session_CheckAvailability();
	if (!isset($_SESSION["pdweb_forms"])) $_SESSION["pdweb_forms"] = array();
	if (!isset($_SESSION["pdweb_forms"][$formId], $_SESSION["pdweb_forms"][$formId]["values"], $_SESSION["pdweb_forms"][$formId]["values"][$elementName])) return false;
	return $_SESSION["pdweb_forms"][$formId]["values"][$elementName];
}

// Fonction à usage interne permettant de générer une liste à puce ou numérotée
function _Html_GenerateList_($listType, $useItemGenerator, $content = null, ...$attributeNameValuePairs)
{
	if (($listType !== "ul") && ($listType !== "ol")) return false;
	if (!is_array($content) && !is_a($content, "iterator")) return false;
	$generateDataAttributeForIL = false;
	if (is_array($attributeNameValuePairs) && !empty($attributeNameValuePairs))
	{
		$itemIndex = -1;
		foreach ($attributeNameValuePairs as $anyItem)
		{
			$itemIndex++;
			if (is_bool($anyItem))
			{
				$generateDataAttributeForIL = ($anyItem === true);
				$attributeNameValuePairs = array_slice($attributeNameValuePairs, $itemIndex + 1);
			}
			break;
		}
	}
	$attributeNameValuePairs[] = HTML_CONTENT;
	$attributeNameValuePairs[] = function($content, $generateDataAttributeForIL, $useItemGenerator)
	{
		foreach ($content as $key=>$item)
		{
			$attributes = array();
			if ($generateDataAttributeForIL && !is_numeric($key))
			{
				$attributes[] = "data-key";
				$attributes[] = $key;
			}
			$attributes[] = HTML_CONTENT;
			$attributes[] = $item;
			if ($useItemGenerator && IsCallable($item))
			{
				array_splice($attributes, count($attributes), 0, array($key, $generateDataAttributeForIL));
				if (!Html_GenerateG("li", ...$attributes)) return false;
			}
			else
			{
				if (!Html_GenerateOC("li", ...$attributes)) return false;
			}
		}
		return true;
	};
	$attributeNameValuePairs[] = &$content;
	$attributeNameValuePairs[] = $generateDataAttributeForIL;
	$attributeNameValuePairs[] = $useItemGenerator;
	return Html_GenerateG($listType, ...$attributeNameValuePairs);
}

// Fonction à usage interne permettant de générer un élément HTML
function _Html_GenerateElement_($isAutonomous, $useContentGenerator, $tagName = null, ...$attributeNameValuePairs_Content)
{
	global $_HTML_TABS_;
	if (!isset($_HTML_TABS_)) $_HTML_TABS_ = "";
	if (!is_bool($isAutonomous)) return false;
	if (!is_bool($useContentGenerator)) return false;
	if ($isAutonomous && $useContentGenerator) return false;
	$tagName = String_Get($tagName);
	if (empty($tagName=strtolower(trim($tagName)))) return false;
	$isComment = ($tagName === "!--");
	$step = 0;
	$attributeName = false;	
	$attributes = array();
	$content = false;
	$contentArguments = array();
	$itemIndex = -1;
	foreach ($attributeNameValuePairs_Content as $anyItem)
	{
		$itemIndex++;
		$item = $anyItem;
		switch ($step)
		{
			case 0: // $item doit être un nom d'attribut
				if (is_array($item))
				{
					$step = 2;
				}
				else
				{
					if (($item=String_Get($item)) === false) return false;
					if (empty($item=trim($item))) return false;
					$attributeName = strtolower($item);
					$step = 1;
				}
				break;
			case 1: // $item doit être la valeur de l'attribut dont le nom a été défini par l'élément précédent ($item précédent)
				if (is_string($attributeName))
				{
					if ($item !== null)
					{
						if (($item=String_Get($item)) === false) return false;
					}
					if (!isset($attributes[$attributeName]) || ($attributes[$attributeName] === null))
					{
						$attributes[$attributeName] = $item;
					}
					else if ($item !== null)
					{
						if (!is_array($attributes[$attributeName]))
							$attributes[$attributeName] = array($attributes[$attributeName], $item);
						else
							$attributes[$attributeName][] = $item;
					}
					$attributeName = false;
					$step = 0;
				}
				else
				{
					return false;
				}
				break;
			case 2: // $item doit être le contenu à placer entre les balises ouvrantes et fermantes de cet élément HTML
				if ($isComment)
				{
					if (!empty($attributes)) return false;
				}
				else
				{
					if ($isAutonomous) return false;
				}
				$contentArguments = array_slice($attributeNameValuePairs_Content, $itemIndex + 1);
				if (!$useContentGenerator)
				{
					$content = String_Get($item, $contentArguments);
					if (!is_string($content)) return false;
				}
				else
				{
					if (!IsCallable($item)) return false;
					$content = $item;
				}
				break 2;
		}
	}
	$tabs = $_HTML_TABS_;
	print("$tabs<$tagName");
	foreach ($attributes as $name => $value)
	{
		if ($value !== null)
		{
			if (is_array($value))
			{
				if (in_array($name, array("class")))
				{
					$value = implode(" ", $value);
				}
				else
				{
					$value = $value[0];
				}
			}
			if (in_array($name, array("title", "type"))) $value = trim($value);
		}
		print(" ");
		print(String_HtmlAttribute($name, $value));
	}
	if (!$isComment && $isAutonomous)
	{
		print("/>\r\n");
		return true;
	}
	if ($isComment) print(" "); else print(">");
	$isSuccess = true;
	if (!$useContentGenerator)
	{
		if (in_array($tagName, array("textarea", "pre", "code")) || (!$isComment && strpos($content, "\n") === false))
		{
			if (is_string($content)) $content = str_replace(array("\r", "\n"), "\r\n", $content);
			print("$content</$tagName>\r\n");
		}
		else
		{
			if (is_string($content)) $content = str_replace(array("\r\n", "\r"), "\n", String_HtmlContent($content));
			if (!$isComment && !str_starts_with($content, "\n")) print("\r\n");
			print(str_replace("\n", "\r\n", $content));
			if (!$isComment && !str_ends_with($content, "\n")) print("\r\n");
			if ($isComment) print(" -->\r\n"); else print("$tabs</$tagName>\r\n");
		}
	}
	else
	{
		if (IsCallable($content))
		{
			if (!$isComment) print("\r\n");
			try
			{
				$_HTML_TABS_ .= "\t";
				if (@$content(...$contentArguments) === false) $isSuccess = false;
				$_HTML_TABS_ = $tabs;
			}
			catch(Exception $error)
			{
				$_HTML_TABS_ = $tabs;
				if ($isComment) print(" -->"); else print("\r\n$tabs</$tagName>\r\n");
				return false;
			}
			if ($isComment) print(" -->\r\n"); else print("$tabs</$tagName>\r\n");
		}
		else
		{
			if ($isComment) print(" -->\r\n"); else print("</$tagName>\r\n");
		}
	}
	return $isSuccess;
}
?>