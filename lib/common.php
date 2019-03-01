<?php // Copyright (c) 2018, SWITCH

/*------------------------------------------------*/
// Common stuff for PHP executable URI (WAYF, idps)
/*------------------------------------------------*/


/*------------------------------------------------*/
// Load general configuration and template file
/*------------------------------------------------*/

$topLevelDir = dirname(__DIR__);

if (isset($_SERVER{'SWITCHWAYF_CONFIG'})) {
    require_once($_SERVER{'SWITCHWAYF_CONFIG'});
} else {
    require_once($topLevelDir . '/etc/config.php');
}
require_once($topLevelDir . '/lib/languages.php');
require_once($topLevelDir . '/lib/functions.php');
require_once($topLevelDir . '/lib/templates.php');

// Set default config options
initConfigOptions();

// Read custom locales
if (file_exists($topLevelDir . '/lib/custom-languages.php')) {
    require_once($topLevelDir . '/lib/custom-languages.php');
}

/*------------------------------------------------*/
// Turn on PHP error reporting
/*------------------------------------------------*/
if ($developmentMode) {
    ini_set('error_reporting', E_ALL);
    ini_set('display_errors', 'On');
    ini_set('log_erros', 'Off');
} else {
    error_reporting(0);
}

/*------------------------------------------------*/
// Read IDP configuration file
/*------------------------------------------------*/

// Determine language
$language = determineLanguage();

// Check if IdP files differ
// If not load file
if ($IDPConfigFile == $backupIDPConfigFile) {
    require_once($IDPConfigFile);
// If they do, check config file
} elseif (checkConfig($IDPConfigFile, $backupIDPConfigFile)) {
    require_once($IDPConfigFile);
// Use backup file if something went wrong
} else {
    require_once($backupIDPConfigFile);
}

// Read metadata file if configuration option is set
if ($useSAML2Metadata && function_exists('xml_parser_create')) {
    require($topLevelDir . '/lib/readMetadata.php');
    updateMetadata();
}

// Set default type
foreach ($IDProviders as $key => $values) {
    if (!isset($IDProviders[$key]['Type'])) {
        $IDProviders[$key]['Type'] = 'unknown';
    }
}

/*------------------------------------------------*/
// Sort Identity Providers
/*------------------------------------------------*/

if ($useSAML2Metadata) {
    // Only automatically sort if list of Identity Provider is parsed
    // from metadata instead of being manualy managed
    sortIdentityProviders($IDProviders);
}
