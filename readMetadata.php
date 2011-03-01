<?php // Copyright (c) 2011, SWITCH - Serving Swiss Universities

// This file is used to dynamically create the list of IdPs to be 
// displayed for the WAYF/DS service based on the federation metadata.
// Configuration parameters are specified in config.php.
//
// The list of Identity Providers can also be updated by running the script
// readMetadata.php periodically as web server user, e.g. with a cron entry like:
// 5 * * * * /usr/bin/php readMetadata.php > /dev/null

// Init log file
openlog("SWITCHwayf.readMetadata.php", LOG_PID | LOG_PERROR, LOG_LOCAL0);

// Make sure this script is not accessed directly
if(isRunViaCLI()){
	// Run in cli mode.
	// Could be used for testing purposes or to facilitate startup confiduration.
	// Results are dumped in $metadataIDPFile (see config.php)
	
	// Set dummy server name
	$_SERVER['SERVER_NAME'] = 'localhost';
	
	// Load configuration files
	require('config.php');
	require($IDPConfigFile);
	
	if (
		!isset($metadataFile) 
		|| !file_exists($metadataFile) 
		|| trim(@file_get_contents($metadataFile)) == '') {
	  exit ("Exiting: File ".$metadataFile." is empty or does not exist\n");
	}
	
	// Check configuration
	if (!isset($metadataSPFile)){
		$errorMsg = 'Please first define a file $metadataSPFile = \'SProvider.metadata.conf.php\'; in config.php before running this script.';
		syslog(LOG_ERR, $errorMsg);
		die($errorMsg);
	}
	
	echo 'Parsing metadata file '.$metadataFile."\n";
	list($metadataIDProviders, $metadataSProviders) = parseMetadata($metadataFile, $defaultLanguage);
	
	// If $metadataIDProviders is not FALSE update $IDProviders and dump results in $metadataIDPFile, else do nothing.
	if(is_array($metadataIDProviders)){ 
		
		echo 'Dumping parsed Identity Providers to file '.$metadataIDPFile."\n";
		dumpFile($metadataIDPFile, $metadataIDProviders, 'metadataIDProviders');
		
		echo 'Merging parsed Identity Providers with data from file '.$IDProviders."\n";
		$IDProviders = mergeInfo($IDProviders, $metadataIDProviders, $SAML2MetaOverLocalConf, $includeLocalConfEntries);
		
		echo "Printing parsed Identity Providers:\n";
		print_r($metadataIDProviders);
		
		echo "Printing effective Identity Providers:\n";
		print_r($IDProviders);
	}
	
	// If $metadataSProviders is not FALSE update $SProviders and dump results in $metadataSPFile, else do nothing.
	if(is_array($metadataSProviders)){ 
		
		echo 'Dumping parsed Service Providers to file '.$metadataSPFile."\n";
		dumpFile($metadataSPFile, $metadataSProviders, 'metadataSProviders');
		
		// Fow now copy the array by reference
		$SProviders = &$metadataSProviders;
		
		echo "Printing parsed Service Providers:\n";
		print_r($metadataSProviders);
	}
	
	
} elseif (isRunViaInclude()) {
	
	// Check configuration
	if (!isset($metadataSPFile)){
		$errorMsg = 'Please first define a file $metadataSPFile = \'SProvider.metadata.conf.php\'; in config.php before running this script.';
		syslog(LOG_ERR, $errorMsg);
		die($errorMsg);
	}
	
	if (!isset($metadataFile)){
		$errorMsg = 'Please first define a file $metadataFile in config.php before running this script.';
		syslog(LOG_ERR, $errorMsg);
		die($errorMsg);
	}
	
	// Run as included file
	if(!file_exists($metadataIDPFile) or filemtime($metadataFile) > filemtime($metadataIDPFile)){
		// Regenerate $metadataIDPFile.
		list($metadataIDProviders, $metadataSProviders) = parseMetadata($metadataFile, $defaultLanguage);
		
		// If $metadataIDProviders is not an array (parse error in metadata),
		// $IDProviders from $IDPConfigFile will be used.
		if(is_array($metadataIDProviders)){
			dumpFile($metadataIDPFile, $metadataIDProviders, 'metadataIDProviders');
			$IDProviders = mergeInfo($IDProviders, $metadataIDProviders, $SAML2MetaOverLocalConf, $includeLocalConfEntries);
		}
		
		if(is_array($metadataSProviders)){
			dumpFile($metadataSPFile, $metadataSProviders, 'metadataSProviders');
			require($metadataSPFile);
		}
		
				// Now merge IDPs from metadata and static file
		$IDProviders = mergeInfo($IDProviders, $metadataIDProviders, $SAML2MetaOverLocalConf, $includeLocalConfEntries);
		
		// Fow now copy the array by reference
		$SProviders = &$metadataSProviders;
		
	} elseif (file_exists($metadataIDPFile)){
		
		// Read SP and IDP files generated with metadata
		require($metadataIDPFile);
		require($metadataSPFile);
	
		// Now merge IDPs from metadata and static file
		$IDProviders = mergeInfo($IDProviders, $metadataIDProviders, $SAML2MetaOverLocalConf, $includeLocalConfEntries);
		
		// Fow now copy the array by reference
		$SProviders = &$metadataSProviders;
	}
	
} else {
	exit('No direct script access allowed');
}

/*****************************************************************************/
// Function parseMetadata, parses metadata file and returns Array($IdPs, SPs)  or
// Array(false, false) if error occurs while parsing metadata file
function parseMetadata($metadataFile, $defaultLanguage){
	
	if(!file_exists($metadataFile)){
		$errorMsg = 'File '.$metadataFile." does not exist"; 
		if (isRunViaCLI()){
			echo $errorMsg."\n";
		} else {
			syslog(LOG_ERR, $errorMsg);
		}
		return Array(false, false);
	}

	if(!is_readable($metadataFile)){
		$errorMsg = 'File '.$metadataFile." cannot be read due to insufficient permissions"; 
		if (isRunViaCLI()){
			echo $errorMsg."\n";
		} else {
			syslog(LOG_ERR, $errorMsg);
		}
		return Array(false, false);
	}
	
	$doc = new DOMDocument();
	if(!$doc->load( $metadataFile )){
		$errorMsg = 'Could not parse metadata file '.$metadataFile; 
		if (isRunViaCLI()){
			echo $errorMsg."\n";
		} else {
			syslog(LOG_ERR, $errorMsg);
		}
		return Array(false, false);
	}
	
	$EntityDescriptors = $doc->getElementsByTagNameNS( 'urn:oasis:names:tc:SAML:2.0:metadata', 'EntityDescriptor' );
	
	$metadataIDProviders = Array();
	$metadataSProviders = Array();
	foreach( $EntityDescriptors as $EntityDescriptor ){
		$entityID = $EntityDescriptor->getAttribute('entityID');
		
		foreach($EntityDescriptor->childNodes as $RoleDescriptor) {
			$nodeName = $RoleDescriptor->nodeName;
 			$nodeName = preg_replace('/^(\w+\:)/', '', $nodeName);
 			switch($nodeName){
				case 'IDPSSODescriptor':
					$IDP = processIDPRoleDescriptor($RoleDescriptor);
					if ($IDP){
						$metadataIDProviders[$entityID] = $IDP;
					}
					break;
				case 'SPSSODescriptor':
					$SP = processSPRoleDescriptor($RoleDescriptor);
					if ($SP){
						$metadataSProviders[$entityID] = $SP;
					} else {
						$errorMsg = "Failed to load SP with entityID $entityID from metadata file $metadataFile";
						if (isRunViaCLI()){
							echo $errorMsg."\n";
						} else {
							syslog(LOG_WARNING, $errorMsg);
						}
					}
					break;
				default:
			}
		}
	}
	
	
	// Output result
	$infoMsg = "Successfully parsed metadata file ".$metadataFile. ". Found ".count($metadataIDProviders)." IdPs and ".count($metadataSProviders)." SPs";
	if (isRunViaCLI()){
		echo $infoMsg."\n";
	} else {
		syslog(LOG_INFO, $infoMsg);
	}
	
	
	return Array($metadataIDProviders, $metadataSProviders);
}

/******************************************************************************/
// Is this script run in CLI mode
function isRunViaCLI(){
	return !isset($_SERVER['REMOTE_ADDR']);
}

/******************************************************************************/
// Is this script run in CLI mode
function isRunViaInclude(){
	return basename($_SERVER['SCRIPT_NAME']) != 'readMetadata.php';
}

/******************************************************************************/
// Processes an IDPRoleDescriptor XML node and returns an IDP entry or false if 
// something went wrong
function processIDPRoleDescriptor($IDPRoleDescriptorNode){
	global $defaultLanguage;
	
	$IDP = Array();
	
	// Get SSO URL
	$SSOServices = $IDPRoleDescriptorNode->getElementsByTagNameNS( 'urn:oasis:names:tc:SAML:2.0:metadata', 'SingleSignOnService' );
	foreach( $SSOServices as $SSOService ){
		if ($SSOService->getAttribute('Binding') == 'urn:mace:shibboleth:1.0:profiles:AuthnRequest'){
			$IDP['SSO'] =  $SSOService->getAttribute('Location');
		}
	}
	
	// Set a default value for backward compatibility
	if (!isset($IDP['SSO'])){
		$IDP['SSO'] = 'https://no.saml1.sso.url.defined.com/error';
	}
	
	
	// Get Name
	$Orgnization = $IDPRoleDescriptorNode->parentNode->getElementsByTagNameNS('urn:oasis:names:tc:SAML:2.0:metadata', 'Organization' )->item(0);
	if ($Orgnization){
		// Get DisplayNames
		$DisplayNames = $Orgnization->getElementsByTagNameNS('urn:oasis:names:tc:SAML:2.0:metadata', 'OrganizationDisplayName');
		foreach ($DisplayNames as $DisplayName){
			$lang = $DisplayName->getAttributeNodeNS('http://www.w3.org/XML/1998/namespace', 'lang')->nodeValue;
			$IDP['Name'] = $DisplayName->nodeValue;
			$IDP[$lang]['Name'] = $DisplayName->nodeValue;
		}
		
		// Set default name
		if (isset($IDP[$defaultLanguage])){
			$IDP['Name'] = $IDP[$defaultLanguage]['Name'];
		} elseif (isset($IDP['en'])){
			$IDP['Name'] = $IDP['en']['Name'];
		} else {
			$IDP['Name'] = $DisplayNames->item(0)->nodeValue;
		}
	} else {
		// Set entityID as Name if no organization is available
		$IDP['Name'] = $IDPRoleDescriptorNode->parentNode->getAttribute('entityID');
	}
	
	return $IDP;
}

/******************************************************************************/
// Processes an SPRoleDescriptor XML node and returns an SP entry or false if 
// something went wrong
function processSPRoleDescriptor($SPRoleDescriptorNode){
	$SP = Array();
	
	// Get <idpdisc:DiscoveryResponse> extensions
	$DResponses = $SPRoleDescriptorNode->getElementsByTagNameNS('urn:oasis:names:tc:SAML:profiles:SSO:idp-discovery-protocol', 'DiscoveryResponse');
	foreach( $DResponses as $DResponse ){
		if ($DResponse->getAttribute('Binding') == 'urn:oasis:names:tc:SAML:profiles:SSO:idp-discovery-protocol'){
			$SP['DSURL'][] =  $DResponse->getAttribute('Location');
		}
	}
	
	// Get Assertion Consumer Services and store their hostnames
	$ACServices = $SPRoleDescriptorNode->getElementsByTagNameNS('urn:oasis:names:tc:SAML:2.0:metadata', 'AssertionConsumerService');
	foreach( $ACServices as $ACService ){
		$SP['ACURL'][] =  $ACService->getAttribute('Location');
	}
	
	return $SP;
}

/******************************************************************************/
// Dump variable to a file 
function dumpFile($dumpFile, $providers, $variableName){
	 
	if(($fp = fopen($dumpFile, 'w')) !== false){
		
		if (flock($fp, LOCK_EX)) { // do an exclusive lock
			fwrite($fp, "<?php\n\n");
			fwrite($fp, "// This file was automatically generated by readMetadata.php\n");
			fwrite($fp, "// Don't edit!\n\n");
			
			fwrite($fp, '$'.$variableName.' = ');
			fwrite($fp, var_export($providers,true));
			
			fwrite($fp, "\n?>");
			
			// release the lock
			flock($fp, LOCK_UN); 
		} else {
			$errorMsg = 'Could not lock file '.$dumpFile.' for writting';
			if (isRunViaCLI()){
				echo $errorMsg."\n";
			} else {
				syslog(LOG_ERR, $errorMsg);
			}
		}
		
		fclose($fp);
	} else {
		$errorMsg = 'Could not open file '.$dumpFile.' for writting';
		if (isRunViaCLI()){
			echo $errorMsg."\n";
		} else {
			syslog(LOG_ERR, $errorMsg);
		}
	}
}


/******************************************************************************/
// Function mergeInfo is used to create the effective $IDProviders array.
// For each IDP found in the metadata, merge the values from IDProvider.conf.php.
// If an IDP is found in IDProvider.conf as well as in metadata, use metadata  
// information if $SAML2MetaOverLocalConf is true or else use IDProvider.conf data
function mergeInfo($IDProviders, $metadataIDProviders, $SAML2MetaOverLocalConf, $includeLocalConfEntries){

	// If $includeLocalConfEntries parameter is set to true, mergeInfo() will also consider IDPs
	// not listed in metadataIDProviders but defined in IDProviders file
	// This is required if you need to add local exceptions over the federation metadata
	$allIDPS = $metadataIDProviders;
	$mergedArray = Array();
	if ($includeLocalConfEntries) {
		  $allIDPS = array_merge($metadataIDProviders, $IDProviders);
	}
	
	foreach ($allIDPS as $allIDPsKey => $allIDPsEntry){
		if(isset($IDProviders[$allIDPsKey])){
			// Entry exists also in local IDProviders.conf.php
			if (isset($metadataIDProviders[$allIDPsKey]) && is_array($metadataIDProviders[$allIDPsKey])) {
				
				// Remove IdP if there is a removal rule in local IDProviders.conf.php 
				if (!is_array($IDProviders[$allIDPsKey])){
					unset($metadataIDProviders[$allIDPsKey]);
					continue;
				}
				
				// Entry exists in both IDProviders sources and is an array
				if($SAML2MetaOverLocalConf){
					// Metadata entry overwrite local conf
					$mergedArray[$allIDPsKey] = array_merge($IDProviders[$allIDPsKey], $metadataIDProviders[$allIDPsKey]);
				} else {
					// Local conf overwrites metada entry
					$mergedArray[$allIDPsKey] = array_merge($metadataIDProviders[$allIDPsKey], $IDProviders[$allIDPsKey]);
				}
			  } else {
					// Entry only exists in local IDProviders file
					$mergedArray[$allIDPsKey] = $IDProviders[$allIDPsKey];
			  }
		} else {
			// Entry doesnt exist in in local IDProviders.conf.php
			$mergedArray[$allIDPsKey] = $metadataIDProviders[$allIDPsKey];
		}
	}
	
	return $mergedArray;
}

?>
