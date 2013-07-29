<?php
// This script checks for missing and incomplete locales
if (isset($_SERVER['REMOTE_ADDR'])){
	exit('No direct script access allowed');
}

require_once('languages.php');
include('custom-languages.php');

echo "The following problems were found:\n";
$refLang = 'en';
foreach(array_keys($langStrings) as $lang){
	if ($lang == 'en'){
		continue;
	}
	
	foreach ($langStrings['en'] as $k => $v){
		if (!isset($langStrings[$lang][$k])){
			echo "* In '$lang' missing locale '$k'\n";
		} else if (substr_count($langStrings['en'][$k], '%s') != substr_count($langStrings[$lang][$k], '%s')){
			echo "* In '$lang' the number of substitutions (%s) differ for '$k': ";
			echo substr_count($langStrings['en'][$k], '%s').' vs '.substr_count($langStrings[$lang][$k], '%s');
			echo "\n";
		}
		
	}
	
	foreach ($langStrings[$lang] as $k => $v){
		if (!isset($langStrings['en'][$k])){
			echo "In $lang obsolete locale $k\n";
		}
	}
	
}
?>