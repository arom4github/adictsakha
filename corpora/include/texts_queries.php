<?php

header('Content-type: text/html; charset=utf-8');

$lang = "";

if(isset($_COOKIE["pref_lang"])){
	$lang = $_COOKIE["pref_lang"];
}

if(($lang != "ru") && ($lang != "fr")){
	$lang = "ru";
}

require_once 'lang_'.$lang.'.php';

echo "<h2>Список текстов</h2>";
echo "<ol>";
if ($handle = opendir('../text_db/')) {
    while (false !== ($entry = readdir($handle))) {
	if($entry == '.' || $entry == '..') continue;
        $fhandle = fopen("../text_db/$entry", "r");
	if ($fhandle) {
    		$buffer = fgets($fhandle, 4096);
        	echo "<li>$buffer</li>";
    	        fclose($fhandle);
    	}
    }
    closedir($handle);
}
echo "</ol>";
?>
