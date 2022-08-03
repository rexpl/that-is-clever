<?php 

/**
 * Echo the hreflang tags on guest pages
 *
 * @param Clever\Library\App\Config
 * 
 * @return void
 */
 function hreflang($config) {

	$url = $config->get('url');
	$page = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

	$substractedPage = substr($page, 0, 4);

	if (substr($page, 0, 4) === "/".lang()."/") $page = substr($page, 3);

	foreach ($config->get('supported_lang') as $value) {

		echo "<link rel=\"alternate\" hreflang=\"".$value."\" href=\"".$url."/".$value.$page."\">";
	}

	echo "<link rel=\"alternate\" hreflang=\"x-default\" href=\"".$url."/".$config->get('default_lang').$page."\">";
}

$page = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

if (substr($page, 0, 4) === "/".lang()."/") {

	define('URI_SAFE_LANG', substr($page, 3));
}
else {

	define('URI_SAFE_LANG', $page);
}