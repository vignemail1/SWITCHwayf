<?php
/* 
 * This file is used to dynamically create the list of IdPs to be 
 * displayed for the WAYF/DS service based on the federation metadata.
 * Configuration parameters are specified in config.php.
 */

// Make sure this script is not accessed directly
if(!isset($_SERVER['REMOTE_ADDR'])){
	// Run in cli mode.
	// Could be used for testing purposes or to facilitate startup confiduration.
	// Results are dumped in $metadataIDPFile (see config.php)
	require('config.php');
	require($IDPConfigFile);
	
	if (
		!isset($metadataFile) 
		|| !file_exists($metadataFile) 
		|| trim(@file_get_contents($metadataFile)) == '') {
	  exit ("Exiting: File ".$metadataFile." is empty or does not exist\n");
	}

	echo 'Enforced parsing of metadata file '.$metadataFile.'... ';
	$metadataIDProviders = parseMetadata($metadataFile, $defaultLanguage);
	echo "done\n";
	
	// If $metadataIDProviders is not FALSE update $IDProviders and dump results in $metadataIDPFile, else do nothing.
	if(is_array($metadataIDProviders)){ 
		
		echo 'Dumping parsed Identity Providers to file '.$metadataIDPFile.'... ';
		dumpFile($metadataIDPFile, $metadataIDProviders);
		echo "done\n";
		
		echo 'Merging parsed Identity Providers with data from file '.$IDProviders.'... ';
		$IDProviders = mergeInfo($IDProviders, $metadataIDProviders, $SAML2MetaOverLocalConf, $includeLocalConfEntries);
		echo "done\n";
		
		echo "Printing parsed Identity Providers:\n";
		print_r($IDProviders);
	}
	
} elseif (basename($_SERVER['SCRIPT_NAME']) != 'readMetadata.php') {
	// Run as included file
	
	if(!file_exists($metadataIDPFile) or filemtime($metadataFile) > filemtime($metadataIDPFile)){
		// Regenerate $metadataIDPFile.
		$metadataIDProviders = parseMetadata($metadataFile, $defaultLanguage);
		
		// If $metadataIDProviders is not an array (parse error in metadata),
		// $IDProviders from $IDPConfigFile will be used.
		if(is_array($metadataIDProviders)){
			dumpFile($metadataIDPFile, $metadataIDProviders);
			$IDProviders = mergeInfo($IDProviders, $metadataIDProviders, $SAML2MetaOverLocalConf, $includeLocalConfEntries);
		}
	} elseif (file_exists($metadataIDPFile)){
		
		// Read metadata file with generated IDProviders
		require($metadataIDPFile);
	}
	
	// Now merge IDPs from metadata and static file
	$IDProviders = mergeInfo($IDProviders, $metadataIDProviders, $SAML2MetaOverLocalConf, $includeLocalConfEntries);
	
} else {
	exit('No direct script access allowed');
}

/*****************************************************************************/
// Function parseMetadata, parses metadata file ($metadataFile declared in config.php).
// Returns FALSE if error occurs while parsing metadata file, else returns $metadataIDProviders array.
function parseMetadata($metadataFile, $defaultLanguage){
	
	$data = implode("", file($metadataFile));
	$parser = xml_parser_create('UTF-8');
	xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 1);
	xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
	
	$xmlParseResult = xml_parse_into_struct($parser, $data, $values, $tags);
	if(!$xmlParseResult){
		//Could not parse metadata file.
		//Log error to syslog (these errors are to be seen only by administrators).
		$errorMsg = 'ERROR: Line:'.xml_get_current_line_number($parser).', Column:'.xml_get_current_column_number($parser).': '.xml_error_string(xml_get_error_code($parser)).'. Could not parse metadata file.'; 
		syslog(LOG_ERR, $errorMsg);
		xml_parser_free($parser);
		return FALSE;
	} else {
		xml_parser_free($parser);
		//If entityID contains federation info, set this to true, else set it to false
		$federationPartOfEntityId = false;
		
		$entities = array();
		$idps = array();
		foreach ($tags as $tag => $positions){
			if (preg_match('/:?EntityDescriptor$/i', $tag)){
				$entities = array_merge($entities, $positions);
			} else if (preg_match('/:?IDPSSODescriptor$/i', $tag)){
				$idps = array_merge($idps, $positions);
			}
			
		}
		
		if(count($idps) > 0){
			for($i=0; $i < count($entities); $i+=2){
				// $i runs on entities array, which contains info for identity and service providers.
				// Every entity has two entries in entities array (open and close element in metadata),
				// so the iteration step is 2. Same thing applies to IdPs array.
				// In every iteration check if $idps[0]>$entities[$i] && $idps[1]<$entities[$i+1].
				// If the above is true, then entities[$i], $entities[$i+1] refer to an identity provider, so use them
				// and remove from idps array the first 2 entries
				 
				if(
					   isset($idps[0]) 
					&& isset($entities[$i]) 
					&& $idps[0] > $entities[$i] 
					&& $idps[1] < $entities[$i+1]
				   ){
					
					// Get entity info
					$entity = array_slice($values, $entities[$i], ($entities[$i+1]-$entities[$i]+1));
					
					//Remove used entries from array $idps
					$usedIdp = array_splice($idps, 0, 2);
					$entityAttrs = count($entity);
					$entityId = '';
					$location = '';
					
					for($k=0; $k<$entityAttrs; $k++){
						if(strcmp($entity[$k]['type'], 'close')){
						// do not get attributes twice (only for the opening element)
							
							if (preg_match('/:?EntityDescriptor$/i', $entity[$k]['tag'])){
								$entityId = $entity[$k]['attributes']['ENTITYID'];
							} elseif (
								preg_match('/:?SingleSignOnService$/i', $entity[$k]['tag'])
								&& $entity[$k]['attributes']['BINDING'] == 'urn:mace:shibboleth:1.0:profiles:AuthnRequest'
								){
								// We have to guarantee that only the SSO URL of the shibboleth:1.0 binding is used
								$location = $entity[$k]['attributes']['LOCATION'];
							} elseif (preg_match('/:?OrganizationDisplayName$/i', $entity[$k]['tag'])){
								$name[$entity[$k]['attributes']['XML:LANG']] = $entity[$k]['value'];
							}
						}
					}
					
					$metadataIDProviders[$entityId]['SSO'] = $location;
					
					if(isset($name)){
						foreach($name as $lang => $value){
							if($lang == $defaultLanguage){
								$metadataIDProviders[$entityId]['Name'] = $value;
							} else {
								$metadataIDProviders[$entityId][$lang]['Name'] = $value;
							}
						}
						
						// Set last value as default name if non could be found
						if (!isset($metadataIDProviders[$entityId]['Name'])){
							$metadataIDProviders[$entityId]['Name'] = $value;
						}
					}
					
					// If we can not determine the name of an idp found in metadata
					// (no <OrganizationDisplayName xml:lang = "$defaultLanguage"> element in metadata),
					// use SSO location hostname as its name.
					
					if(!isset($metadataIDProviders[$entityId]['Name'])){
						$metadataIDProviders[$entityId]['Name'] = parse_url($location, PHP_URL_HOST);
					}
					unset($name);
					
				}
			}
		}
	}
	
	return $metadataIDProviders;
}

/******************************************************************************/
// Dump $IDProviders array to a file (specified in config.php), using IDProviders.conf.php style.
function dumpFile($metadataIDPFile, $IDProviders){
	 
	if(($idpFp = fopen($metadataIDPFile, 'w')) !== false){
		
		fwrite($idpFp, "<?php\n\n");
		fwrite($idpFp, "// This file was automatically generated by readMetadata.php\n");
		fwrite($idpFp, "// In case you want to overwrite some of these values, do this\n");
		fwrite($idpFp, "// in the file IDProviders.conf.php\n\n");
		
		if (isset($IDProviders)){
			foreach($IDProviders as $idps => $field){
				foreach($field as $f => $value){
					if(is_array($value)){
						foreach($value as $v => $val){
							if(is_int($v)){
								fwrite($idpFp, '$metadataIDProviders[\''.$idps.'\'][\''.$f.'\']['.$v.'] = \''.str_replace("'", "\\'", $val).'\';'."\n");
							} else {
								fwrite($idpFp, '$metadataIDProviders[\''.$idps.'\'][\''.$f.'\'][\''.$v.'\'] = \''.str_replace("'", "\\'", $val).'\';'."\n");
							}
						}
					} else{
						fwrite($idpFp, '$metadataIDProviders[\''.$idps.'\'][\''.$f.'\'] = \''.str_replace("'", "\\'", $value).'\';'."\n");
					}
				}
				fwrite($idpFp, "\n");
			}
		}
		
		fwrite($idpFp, "\n?>");
		fclose($idpFp);
	} else {
		syslog(LOG_ERR, 'Could not open '.$metadataIDPFile.' for writting.');
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
