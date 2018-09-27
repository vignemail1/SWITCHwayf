<?php // Copyright (c) 2018, SWITCH
$MAN=<<<PAGE
Name:        SWITCHwayf
Author:      Lukas Haemmerle, SWITCH
Description: This script is used to dynamically create the list of
             IdPs and SP to be displayed for the WAYF/DS service 
             based on the federation metadata.
             It is intended to be run periodically, e.g. with a cron
             entry like:
             5 * * * * /usr/bin/php update-metadata.php \
                 --metadata-file /var/cache/shibboleth/metadata.switchaai.xml \
                 --metadata-idp-file /tmp/IDProvider.metadata.php \
                 --metadata-sp-file /tmp/SProvider.metadata.php \
                 > /dev/null
        
Usage
-----
php update-metadata.php -help|-h
php update-metadata.php --metadata-file <file> \
    --metadata-idp-file <file> --metadata-sp-file <file> \
    [--verbose | -v] [--min-sp-count <count>] [--min-idp-count <count>] \
    [--language <locale>] [--syslog] [--syslog-id <id>]
php update-metadata.php --metadata-url <url> \
    --metadata-idp-file <file> --metadata-sp-file <file> \
    [--verbose | -v] [--min-sp-count <count>] [--min-idp-count <count>] \
    [--language <locale>] [--syslog] [--syslog-id <id>]

Argument Description
--------------------
--metadata-url <url>        SAML2 metadata URL
--metadata-file <file>      SAML2 metadata file
--metadata-idp-file <file>  File containing service providers 
--metadata-sp-file <file>   File containing identity providers 
--min-idp-count <count>     Minimum expected number of IdPs in metadata
--min-sp-count <count>      Minimum expected number of SPs in metadata
--language <locale>         Language locale, e.g. 'en', 'jp', ...
--filter-idps-by-ec         Only process IdPs that are in given 
                            entity category. Multiple categories
                            can be provided space separated. 
                            If the IdP is in none, the IdP is ignored.
--syslog                    Use syslog for reporting
--syslog-id <id>            Process identity for syslog messages
--verbose | -v              Verbose mode
--help | -h                 Print this man page


PAGE;

$topLevelDir = dirname(__DIR__);

require_once($topLevelDir . '/lib/functions.php');
require_once($topLevelDir . '/lib/readMetadata.php');

// Script options
$longopts = array(
    "metadata-url:",
    "metadata-file:",
    "metadata-idp-file:",
    "metadata-sp-file:",
    "min-idp-count:",
    "min-sp-count:",
    "filter-idps-by-ec:",
    "language:",
    "verbose",
    "syslog",
    "syslog-id:",
    "help",
);

$options = getopt('hv', $longopts);

if (isset($options['help']) || isset($options['h'])) {
	exit($MAN);
} 

// simple options
$language = isset($options['language']) ? $options['language'] : 'en';
$verbose  = isset($options['verbose']) || isset($options['v']) ? true : false;
$syslog   = isset($options['syslog']) ? true : false;
$syslogId = isset($options['syslog-id']) ? $options['syslog-id'] : 'SWITCHwayf';

if ($syslog) {
	openlog($syslogId, LOG_NDELAY, LOG_USER);
}

if (isset($options['metadata-url'])) {
	$metadataURL = $options['metadata-url'];
} elseif (isset($options['metadata-file'])) {
	$metadataFile = $options['metadata-file'];
} else {
	reportError("Exiting: both --metadata-url and --metadata-file parameters missing\n");
	exit(1);
}

if (!isset($options['metadata-sp-file'])) {
	reportError("Exiting: mandatory --metadata-sp-file parameter missing\n");
	exit(1);
} else {
	$metadataSPFile = $options['metadata-sp-file'];
	$metadataTempSPFile = $metadataSPFile.'.swp';
}

if (!isset($options['metadata-idp-file'])) {
	reportError("Exiting: mandatory --metadata-idp-file parameter missing\n");
	exit(1);
} else {
	$metadataIDPFile = $options['metadata-idp-file'];
	$metadataTempIDPFile = $metadataIDPFile.'.swp';
}

if (isset($options['min-sp-count'])) {
	if (preg_match('/^(\d+)%$/', $options['min-sp-count'], $matches)) {
		if (file_exists($metadataSPFile)) {
			require_once($metadataSPFile);
			$SPCount = count($metadataSProviders);
			$minSPCount = floor($SPCount * $matches[1] / 100);
		} else {
			$minSPCount = 0;
		}
	} elseif (preg_match('/^\d+$/', $options['min-sp-count'])) {
		$minSPCount = $options['min-sp-count'];
	} else {
		reportError("Exiting: invalid value for --min-sp-count parameter\n");
		exit(1);
	}
} else {
	$minSPCount = 0;
}

if (isset($options['min-idp-count'])) {
	if (preg_match('/^(\d+)%$/', $options['min-idp-count'], $matches)) {
		if (file_exists($metadataIDPFile)) {
			require_once($metadataIDPFile);
			$IDPCount = count($metadataIDProviders);
			$minIDPCount = floor($IDPCount * $matches[1] / 100);
		} else {
			$minIDPCount = 0;
		}
	} elseif (preg_match('/^\d+$/', $options['min-idp-count'])) {
		$minIDPCount = $options['min-idp-count'];
	} else {
		reportError("Exiting: invalid value for --min-idp-count parameter\n");
		exit(1);
	}
} else {
	$minIDPCount = 0;
}

if(isset($options['filter-idps-by-ec'])){
	$filterEntityCategory = $options['filter-idps-by-ec'];
} else {
	$filterEntityCategory = false;
}

// Input validation
if (isset($metadataURL) && $metadataURL) {
	$metadataFile = tempnam(sys_get_temp_dir(), 'metadata');
	if (!ini_get('allow_url_fopen')) {
		reportError("Exiting: allow_url_fopen disabled, unabled to download $metadataURL\n");
		exit(1);
	}
	if ($verbose) {
		reportInfo("Downloading metadata file from $metadataURL\n");
	}
	$result = @copy($metadataURL, $metadataFile);
	if (!$result) {
		$error = error_get_last();
		$message = explode(': ', $error['message'])[2];
		reportError("Exiting: could not download $metadataURL: $message");
		exit(1);
	}
} else {
	if (
		!file_exists($metadataFile)
		|| filesize($metadataFile) == 0
		) {
		reportError("Exiting: file $metadataFile is empty or does not exist\n");
		exit(1);
	}

	if (!is_readable($metadataFile)){
		reportError("Exiting: file $metadataFile is not readable\n");
		exit(1);
	}
}

if ($verbose) {
	reportInfo("Parsing metadata file $metadataFile\n");
}

// Parse metadata
list($metadataIDProviders, $metadataSProviders) = parseMetadata($metadataFile, $language);

// If $metadataIDProviders is not FALSE, dump results in $metadataIDPFile.
if (is_array($metadataIDProviders)){
	$IDPCount = count($metadataIDProviders);
	if ($IDPCount < $minIDPCount) {
		reportError("Exiting: number of identity providers found ($IDPCount) lower than expected ($minIDPCount)\n");
		exit(1);
	}

	if ($verbose) {
		reportInfo("Dumping $IDPCount extracted identity providers to file $metadataIDPFile\n");
	}
	dumpFile($metadataTempIDPFile, $metadataIDProviders, 'metadataIDProviders');
	
	if(!rename($metadataTempIDPFile, $metadataIDPFile)){
		reportError("Exiting: could not rename temporary file $metadataTempIDPFile to $metadataIDPFile\n");
		exit(1);
	}
}

// If $metadataSProviders is not FALSE, dump results in $metadataSPFile.
if (is_array($metadataSProviders)){
	$SPCount = count($metadataSProviders);
	if ($SPCount < $minSPCount) {
		reportError("Exiting: number of service providers found ($SPCount) lower than expected ($minSPCount)\n");
		exit(1);
	}

	if ($verbose) {
		reportInfo("Dumping $SPCount extracted service providers to file $metadataSPFile\n");
	}
	dumpFile($metadataTempSPFile, $metadataSProviders, 'metadataSProviders');
	
	if(!rename($metadataTempSPFile, $metadataSPFile)){
		reportError("Exiting: could not rename temporary file $metadataTempSPFile to $metadataSPFile\n");
		exit(1);
	}
}

// clean up if needed
if (isset($metadataURL) && $metadataURL) {
	$result = @unlink($metadataFile);
	if (!$result) {
		$error = error_get_last();
		$message = $error['message'];
		reportError("Exiting: could not delete temporary file $metadataFile: $message");
		exit(1);
	}
}

if ($syslog) {
	closelog();
}

function reportError($message) {
	global $syslog;

	if ($syslog) {
		syslog(LOG_ERR, $message);
	} else {
		fwrite(STDERR, $message);
	}
}

function reportInfo($message) {
	global $syslog;

	if ($syslog) {
		syslog(LOG_INFO, $message);
	} else {
		fwrite(STDOUT, $message);
	}
}
