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
    [--verbose | -v] [--min-sp-count <count>] [--min-idp-count <count>]
php update-metadata.php --metadata-url <url> \
    --metadata-idp-file <file> --metadata-sp-file <file> \
    [--verbose | -v] [--min-sp-count <count>] [--min-idp-count <count>]


Example usage: 
php update-metadata.php \
    --metadata-file /var/cache/shibboleth/metadata.switchaai.xml \
    --metadata-idp-file /tmp/IDProvider.metadata.php \
    --metadata-sp-file /tmp/SProvider.metadata.php


Argument Description 
-------------------
--metadata-url <url>        SAML2 metadata URL
--metadata-file <file>      SAML2 metadata file
--metadata-idp-file <file>  File containing Service Providers 
--metadata-sp-file <file>   File containing Identity Providers 
--min-idp-count <count>     Minimum expected number of IdPs in metadata
--min-sp-count <count>      Minimum expected number of SPs in metadata
--language <locale>         Language locale, e.g. 'en', 'jp', ...
--verbose | -v              Verbose mode
--help | -h                  Print this man page


PAGE;

require_once('functions.php');
require_once('readMetadata.php');

// Script options
$longopts = array(
    "metadata-url:",
    "metadata-file:",
    "metadata-idp-file:",
    "metadata-sp-file:",
    "min-idp-count:",
    "min-sp-count:",
    "language:",
    "verbose",
    "help",
);

$options = getopt('hv', $longopts);

if (isset($options['help']) || isset($options['h'])) {
	exit($MAN);
} 

if (isset($options['metadata-url'])) {
	$metadataURL = $options['metadata-url'];
} elseif (isset($options['metadata-file'])) {
	$metadataFile = $options['metadata-file'];
} else {
	exit("Exiting: both --metadata-url and --metadata-file parameters missing\n");
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

if (isset($options['min-sp-count'])) {
	if (!is_numeric($options['min-sp-count'])) {
		exit("Exiting: invalid value for --min-sp-count parameter\n");
	} else {
		$minSPCount = $options['min-sp-count'];
	}
} else {
	$minSPCount = 0;
}

if (isset($options['min-idp-count'])) {
	if (!is_numeric($options['min-idp-count'])) {
		exit("Exiting: invalid value for --min-idp-count parameter\n");
	} else {
		$minIDPCount = $options['min-idp-count'];
	}
} else {
	$minIDPCount = 0;
}

// Set other options
$language = isset($options['language']) ? $options['language'] : 'en';
$verbose  = isset($options['verbose']) || isset($options['v']) ? true : false;

// Input validation
if ($metadataURL) {
	$metadataFile = tempnam(sys_get_temp_dir(), 'metadata');
	if (!ini_get('allow_url_fopen')) {
		exit("Exiting: allow_url_fopen disabled, unabled to download $metadataURL\n");
	}
	if ($verbose) {
		echo "Downloading metadata from $metadataURL to $metadataFile\n";
	}
	$result = copy($metadataURL, $metadataFile);
	if (!$result) {
		$error = error_get_last();
		exit("Exiting: could not download $metadataURL: $error\n");
	}
} else {
	if (
		!file_exists($metadataFile)
		|| filesize($metadataFile) == 0
		) {
		exit("Exiting: File $metadataFile is empty or does not exist\n");
	}

	if (!is_readable($metadataFile)){
		exit("Exiting: File $metadataFile is not readable\n");
	}
}

if ($verbose) {
	echo "Parsing metadata file $metadataFile\n";
}

// Parse metadata
list($metadataIDProviders, $metadataSProviders) = parseMetadata($metadataFile, $language);

// If $metadataIDProviders is not FALSE, dump results in $metadataIDPFile.
if (is_array($metadataIDProviders)){
	$IDPCount = count($metadataIDProviders);
	if ($IDPCount < $minIDPCount) {
		exit("Exiting: number of Identity Providers found ($IDPCount) lower than expected ($minIDPCount)\n");
	}

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
	$SPCount = count($metadataSProviders);
	if ($SPCount < $minSPCount) {
		exit("Exiting: number of Service Providers found ($SPCount) lower than expected ($minSPCount)\n");
	}

	if ($verbose) {
		echo "Dumping parsed Service Providers to file $metadataSPFile\n";
	}
	dumpFile($metadataTempSPFile, $metadataSProviders, 'metadataSProviders');
	
	if(!rename($metadataTempSPFile, $metadataSPFile)){
		exit("Exiting: Could not rename temporary file $metadataTempSPFile to $metadataSPFile");
	}
}

// clean up if needed
if ($metadataURL) {
	$result = unlink($metadataFile);
	if (!$result) {
		$error = error_get_last();
		exit("Exiting: could not delete temporary file $metadataFile: $error\n");
	}
}
