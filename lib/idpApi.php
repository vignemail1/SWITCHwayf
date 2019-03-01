<?php

/*------------------------------------------------*/
// JSON Api to retrieve IDPs with paging and query
// The API is compliant with select2 (https://select2.org/)
/*------------------------------------------------*/




// TODO : gérer la pré-sélection
// TODO : gérer le groupes
// TODO : gérer les icones
$topLevelDir = dirname(__DIR__);

require('common.php');
require('idpApiObjects.php');

header('Content-Type: application/json');

$repo = new IdpRepository($IDProviders);

global $select2PageSize;

if (array_key_exists("page", $_GET)) {
    if (array_key_exists("search", $_GET)) {
        //error_log("Search with request ".$_GET["search"]);
        if (isset($select2PageSize)) {
            echo $repo->toJsonByQuery($_GET["search"], $_GET["page"], $select2PageSize);
        } else {
            echo $repo->toJsonByQuery($_GET["search"], $_GET["page"]);
        }
    } else {
        //error_log("Search page ".$_GET["page"]);
        if (isset($select2PageSize)) {
            echo $repo->toJsonByPage($_GET["page"], $select2PageSize);
        } else {
            echo $repo->toJsonByPage($_GET["page"]);
        }
    }
} else {
    echo $repo->toJson();
}
