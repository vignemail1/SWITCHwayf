<?php // Copyright (c) 2018, SWITCH
$MAN=<<<PAGE
Name:        SWITCHwayf
Author:      Lukas Haemmerle, SWITCH
Description: This file is used to dynamically create the list of 
             IdPs and SP to be displayed for the WAYF/DS service 
             based on the federation metadata.
             Configuration parameters are specified in config.php.
             The list of Identity Providers can also be updated 
             by running the script update-metadata.php 
             periodically as web server user, e.g. with a cron 
             entry like:
             5 * * * * /usr/bin/php update-metadata.php > /dev/null
        
Usage: 
php update-metadata.php -help|-h
php update-metadata.php --metadata-file <file> \
    --metadata-idp-file <file> --metadata-sp-file <file> \
    [--verbose | -v]


Example usage: 
php update-metadata.php \
    --metadata-file /var/cache/shibboleth/metadata.switchaai.xml \
    --metadata-idp-file /tmp/IDProvider.metadata.php \
    --metadata-sp-file /tmp/SProvider.metadata.php


Argument Description 
-------------------
--metadata-file <file>      SAML2 metadata file
--metadata-idp-file <file>  File containing Service Providers 
--metadata-sp-file <file>   File containing Identity Providers 
--language <locale>         Language locale, e.g. 'en', 'jp', ...
--verbose | -v              Verbose mode
--help | -h                  Print this man page


PAGE;

require_once('functions.php');
require_once('readMetadata.php');

// Script options
$longopts = array(
    "metadata-file:",
    "metadata-idp-file:",
    "metadata-sp-file:",
    "language:",
    "verbose",
    "help",
);

$options = getopt('hv', $longopts);

if (isset($options['help']) || isset($options['h'])) {
	exit($MAN);
} 

if (!isset($options['metadata-file'])) {
	exit("Exiting: mandatory --metadata-file parameter missing\n");
} else {
	$metadataFile = $options['metadata-file'];
}

if (!isset($options['metadata-sp-file'])) {
	exit("Exiting: mandatory --metadata-sp-file parameter missing\n");
} else {
	$metadataSPFile = $options['metadata-sp-file'];
	$metadataTempSPFile = $metadataSPFile.'.swp';
}

if (!isset($options['metadata-idp-file'])) {
	exit("Exiting: mandatory --metadata-idp-file parameter missing\n");
} else {
	$metadataIDPFile = $options['metadata-idp-file'];
	$metadataTempIDPFile = $metadataIDPFile.'.swp';
}

// Set other options
$language = isset($options['language']) ? $options['language'] : 'en';
$verbose  = isset($options['verbose']) || isset($options['v']) ? true : false;

// Input validation
if (
	!file_exists($metadataFile)
	|| filesize($metadataFile) == 0
	) {
	exit("Exiting: File $metadataFile is empty or does not exist\n");
}

if (!is_readable($metadataFile)){
	exit("Exiting: File $metadataFile is not readable\n");
}

if ($verbose) {
	echo "Parsing metadata file $metadataFile\n";
}

// Parse metadata
list($metadataIDProviders, $metadataSProviders) = parseMetadata($metadataFile, $language);

// If $metadataIDProviders is not FALSE, dump results in $metadataIDPFile.
if (is_array($metadataIDProviders)){

	if ($verbose) {
		echo "Dumping parsed Identity Providers to file $metadataIDPFile\n";
	}
	dumpFile($metadataTempIDPFile, $metadataIDProviders, 'metadataIDProviders');
	
	if(!rename($metadataTempIDPFile, $metadataIDPFile)){
		exit("Exiting: Could not rename temporary file $metadataTempIDPFile to $metadataIDPFile");
	}
}

// If $metadataSProviders is not FALSE, dump results in $metadataSPFile.
if (is_array($metadataSProviders)){

	if ($verbose) {
		echo "Dumping parsed Service Providers to file $metadataSPFile\n";
	}
	dumpFile($metadataTempSPFile, $metadataSProviders, 'metadataSProviders');
	
	if(!rename($metadataTempSPFile, $metadataSPFile)){
		exit("Exiting: Could not rename temporary file $metadataTempSPFile to $metadataSPFile");
	}
}
