<?php

// Copyright (c) 2019 Geoffroy Arnoud, Guillaume Rousse, and SWITCHwayf contributors

/*------------------------------------------------*/
// JSON Api to retrieve IDPs with paging and query
// The API is compliant with select2 (https://select2.org/)
/*------------------------------------------------*/

$topLevelDir = dirname(__DIR__);

require('common.php');
require('idpApiObjects.php');

header('Content-Type: application/json');

global $allowedCORSDomain;

header('Access-Control-Allow-Origin: ' . $allowedCORSDomain);

$repo = new IdpRepository($IDProviders, $IDPArray);

if (array_key_exists("page", $_GET)) {
    if (array_key_exists("search", $_GET)) {
        //error_log("Search with request ".$_GET["search"]);
        echo $repo->toJsonByQuery($_GET["search"], $_GET["page"], getSelect2PageSize());
    } else {
        //error_log("Search page ".$_GET["page"]);
        echo $repo->toJsonByPage($_GET["page"], getSelect2PageSize());
    }
} else {
    echo $repo->toJson();
}
