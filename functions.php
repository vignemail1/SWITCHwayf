<?php
/******************************************************************************/
// Commonly used functions for the WAYF
/******************************************************************************/

/******************************************************************************/
// Generates an array of IDPs using the cookie value
function getIdPArrayFromValue($value){

	// Decodes and splits cookie value
	$CookieArray = preg_split('/ /', $value);
	$CookieArray = array_map('base64_decode', $CookieArray);
	
	return $CookieArray;
}

/******************************************************************************/
// Generate the value that is stored in the cookie using the list of IDPs
function getValueFromIdPArray($CookieArray){

	// Merges cookie content and encodes it
	$CookieArray = array_map('base64_encode', $CookieArray);
	$value = implode(' ', $CookieArray);
	return $value;
}

/******************************************************************************/
// Append a value to the array of IDPs
function appendValueToIdPArray($value, $CookieArray){
	
	// Remove value if it already existed in array
	foreach (array_keys($CookieArray) as $i){
		if ($CookieArray[$i] == $value){
			unset($CookieArray[$i]);
		}
	}
	
	// Add value to end of array
	$CookieArray[] = $value;
	
	return $CookieArray;
}

/******************************************************************************/
// Checks if the configuration file has changed. If it has, check the file
// and change its timestamp.
function checkConfig($IDPConfigFile, $backupIDPConfigFile){
	
	// Do files have the same modification time
	if (filemtime($IDPConfigFile) == filemtime($backupIDPConfigFile))
		return true;
	
	// Availability check
	if (!file_exists($IDPConfigFile))
		return false;
	
	// Readability check
	if (!is_readable($IDPConfigFile))
		return false;
	
	// Size check
	if (filesize($IDPConfigFile) < 200)
		return false;
	
	// Make modification time the same
	// If that doesnt work we won't notice it
	touch ($IDPConfigFile, filemtime($backupIDPConfigFile));
	
	return true;
}

/******************************************************************************/
// Checks if an IDP exists and prints an error if it doesnt
function checkIDP($IDP, $showError = true){
	
	global $IDProviders, $redirectCookieName;
	
	if (isset($IDProviders[$IDP])){
		return true;
	} elseif ($IDP == '-' || $IDP == ''){ 
		return false;
	} elseif(!$showError){
		return false;
	} else {
		$message = sprintf(getLocalString('invalid_user_idp'), htmlentities($IDP))."</p><p>\n<tt>";
					foreach ($IDProviders as $key => $value){
						if (isset($value['SSO'])){
							$message .= $key."<br>\n";
						}
					}
		$message .= "</tt>\n";
		
		printError($message);
		exit;
	}
}



/******************************************************************************/
// Validates the URL format and returns the URL without GET arguments and fragment
function verifyAndStripReturnURL($url){
	
	$components = parse_url($url);
	
	if (!$components){
		return false;
	}
	
	$recomposedURL = $components['scheme'].'://';
	
	if (isset($components['user'])){
		$recomposedURL .= $components['user'];
		
		if (isset($components['pass'])){
			$recomposedURL .= ':'.$components['pass'];
		}
		
		$recomposedURL .= '@';
	}
	
	if (isset($components['host'])){
		$recomposedURL .= $components['host'];
	}
	
	if (isset($components['port'])){
		$recomposedURL .= ':'.$components['port'];
	}
	
	if (isset($components['path'])){
		$recomposedURL .= $components['path'];
	}
	
	return $recomposedURL;
}

/******************************************************************************/
// Parses the hostname out of a string and returns it
function getHostNameFromURI($string){
	
	// Check if string is URN
	if (preg_match('/^urn:mace:/i', $string)){
		// Return last component of URN
		return end(explode(':', $string));
	}
	
	// Apparently we are dealing with something like a URL
	if (preg_match('/([a-zA-Z0-9\-\.]+\.[a-zA-Z0-9\-\.]{2,6})/', $string, $matches)){
		return $matches[0];
	} else {
		return '';
	}
}

/******************************************************************************/
// Parses the domain out of a string and returns it
function getDomainNameFromURI($string){
	
	// Check if string is URN
	if (preg_match('/^urn:mace:/i', $string)){
		// Return last component of URN
		return getTopLevelDomain(end(explode(':', $string)));
	}
	
	// Apparently we are dealing with something like a URL
	if (preg_match('/[a-zA-Z0-9\-\.]+\.([a-zA-Z0-9\-\.]{2,6})/', $string, $matches)){
		return getTopLevelDomain($matches[0]);
	} else {
		return '';
	}
}

/******************************************************************************/
// Returns top level domain name from a DNS name
function getTopLevelDomain($string){
	$hostnameComponents = explode('.', $string);
	if (count($hostnameComponents) >= 2){
		return $hostnameComponents[count($hostnameComponents)-2].'.'.$hostnameComponents[count($hostnameComponents)-1];
	} else {
		return $string;
	}
}

/******************************************************************************/
// Parses the reverse dns lookup hostname out of a string and returns domain
function getDomainNameFromURIHint(){
	
	global $IDProviders;
	
	$hostname = gethostbyaddr($_SERVER['REMOTE_ADDR']);
	if ($hostname == $_SERVER['REMOTE_ADDR']){
		return '-';
	}
	
	// Do we still have something
	$domainname = getDomainNameFromURI($hostname);
	if ($domainname != ''){
		// Find a matching IdP SSO, must be matching the IdP urn 
		// or at least the last part of the urn
		foreach ($IDProviders as $key => $value){
			if (preg_match('/'.$domainname.'$/', $key)){
				return $key;
			}
		}
	} else {
		return '-';
	}
	
}

/******************************************************************************/
// Get the user's language using the accepted language http header
function determineLanguage(){
	
	global $langStrings, $defaultLanguage;
	
	// Check if language is enforced by PATH-INFO argument
	if (isset($_SERVER['PATH_INFO']) && !empty($_SERVER['PATH_INFO'])){
		foreach ($langStrings as $lang => $values){
			if (preg_match('#/'.$lang.'($|/)#',$_SERVER['PATH_INFO'])){
				return $lang;
			}
		}
	}
	
	// Check if there is a language GET argument
	if (isset($_GET['lang'])){
		$localeComponents = decomposeLocale($_GET['lang']);
		if (
		    $localeComponents !== false 
		    && isset($langStrings[$localeComponents[0]])
		    ){
			
			// Return language
			return $localeComponents[0];
		}
	}
	
	// Return default language if no headers are present otherwise
	if (!isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])){
		return $defaultLanguage;
	}
	
	// Inspect Accept-Language header which looks like:
	// Accept-Language: en,de-ch;q=0.8,fr;q=0.7,fr-ch;q=0.5,en-us;q=0.3,de;q=0.2
	$languages = explode( ',', trim($_SERVER['HTTP_ACCEPT_LANGUAGE']));
	foreach ($languages as $language){
		$languageParts = explode(';', $language);
		
		// Only treat art before the prioritization
		$localeComponents = decomposeLocale($languageParts[0]);
		if (
		    $localeComponents !== false 
		    && isset($langStrings[$localeComponents[0]])
		    ){
			
			// Return language
			return $localeComponents[0];
		}
	}
	
	return $defaultLanguage;
}

/******************************************************************************/

// Splits up a  string (relazed) according to
// http://www.debian.org/doc/manuals/intro-i18n/ch-locale.en.html#s-localename
// and returns an array with the four components
function decomposeLocale($locale){
	
	// Locale name syntax:  language[_territory][.codeset][@modifier]
	if (!preg_match('/^([a-zA-Z]{2})([-_][a-zA-Z]{2})?(\.[^@]+)?(@.+)?$/', $locale, $matches)){
		return false;
	} else {
		// Remove matched string in first position
		array_shift($matches);
		
		return $matches;
	}
}

/******************************************************************************/
// Gets a string in the user's language. If no localized version is available
// for the string, the English string is returned as default.
function getLocalString($string, $encoding = ''){
	
	global $defaultLanguage, $langStrings, $language;
	
	$textString = '';
	if (isset($langStrings[$language][$string])){
		$textString = $langStrings[$language][$string];
	} else {
		$textString = $langStrings[$defaultLanguage][$string];
	}
	
	// Change encoding if necessary
	if ($encoding == 'js'){
		$textString = convertToJSString($textString);
	}
	
	return $textString;
}

/******************************************************************************/
// Converts string to a JavaScript format that can be used in JS alert
function convertToJSString($string){
	return addslashes(utf8_encode(html_entity_decode($string)));
}

/******************************************************************************/
// Checks if entityID hostname of a valid IdP exists in path info
function getIdPPathInfoHint(){
	
	global $IDProviders;
	
	// Check if path info is available at all
	if (!isset($_SERVER['PATH_INFO']) || empty($_SERVER['PATH_INFO'])){
		return '-';
	}
	
	// Check for entityID hostnames of all available IdPs
	foreach ($IDProviders as $key => $value){
		// Only check actual IdPs
		if (
				isset($value['SSO']) 
				&& !empty($value['SSO'])
				&& $value['Type'] != 'wayf'
				&& checkPathInfo(getHostNameFromURI($key))
				){
			return $key;
		}
	}
	
	// Check for entityID domain names of all available IdPs
	foreach ($IDProviders as $key => $value){
		// Only check actual IdPs
		if (
				isset($value['SSO']) 
				&& !empty($value['SSO'])
				&& $value['Type'] != 'wayf'
				&& checkPathInfo(getDomainNameFromURI($key))
				){
			return $key;
		}
	}
	
	return '-';
}

/******************************************************************************/
// Parses the Kerbores realm out of the string and returns it
function getKerberosRealm($string){
	
	global $IDProviders;
	
	if ($string !='' ) {
		// Find a matching Kerberos realm
		foreach ($IDProviders as $key => $value){
			if ($value['Realm'] == $string) return $key;
		}
	}
	
	return '-';
}

/******************************************************************************/
// Tries to match an IP to a network mask
function getNetMatch($network, $ip) {
	$ip_arr = explode('/', $network);
	$network_long = ip2long($ip_arr[0]);
	
	$x = ip2long($ip_arr[1]);
	$mask = long2ip($x) == $ip_arr[1] ? $x : 0xffffffff << (32 - $ip_arr[1]);
	$ip_long = ip2long($ip);
	
	return ($ip_long & $mask) == ($network_long & $mask);
}

/******************************************************************************/
// Determines the IdP according to the IP address if possible
function getIPAdressHint() {
	global $IDProviders;
	
	foreach($IDProviders as $name => $idp) {
		if (is_array($idp) && array_key_exists("IP", $idp)) {
			$clientIP = $_SERVER["REMOTE_ADDR"];
			
			foreach( $idp["IP"] as $network ) {
				if (getNetMatch($network, $clientIP)) {
					return $name;
				}
			}
		}
	}
	return '-';
}
/******************************************************************************/
// Returns true if URL could be verified, false otherwise
function isVerifiedReturnURL($entityID, $returnURL) {
	global $SProviders, $enableDSReturnParamCheck, $useACURLsForReturnParamCheck;
	
	// Is check necessary
	if (!isset($enableDSReturnParamCheck) || !$enableDSReturnParamCheck){
		return true;
	}
	
	// SP unknown, therefore return false
	if (!isset($SProviders[$entityID])){
		return false;
	}
	
	// Check using DiscoveryResponse extension
	if (isset($SProviders[$entityID]['DSURL']) && in_array($returnURL, $SProviders[$entityID]['DSURL'])){
		return true;
	}
	
	if ($useACURLsForReturnParamCheck && isset($SProviders[$entityID]['ACURL'])){
		$returnURLHostName = getHostNameFromURI($returnURL);
		foreach($SProviders[$entityID]['ACURL'] as $ACURL){
			if (getHostNameFromURI($ACURL) == $returnURLHostName){
				return true;
			}
		}
	}
	
	// Default return value
	return false;
}

/******************************************************************************/
// Returns a reasonable value for returnIDParam
function getReturnIDParam() {
	
	if (isset($_GET['returnIDParam']) && !empty($_GET['returnIDParam'])){
		return $_GET['returnIDParam'];
	} else {
		return 'entityID';
	}
}

/******************************************************************************/
// Returns true if valid Shibboleth 1.x request or Directory Service request
function isValidShibRequest(){
	return (isValidShib1Request() || isValidDSRequest());
}

/******************************************************************************/
// Returns true if valid Shibboleth request
function isValidShib1Request(){
	if (isset($_GET['shire']) && isset($_GET['target'])){
		return true;
	} else {
		return false;
	}
}

/******************************************************************************/
// Returns true if valid Directory Service request
function isValidDSRequest(){
	if (isset($_GET['entityID']) && isset($_GET['return'])){
		return true;
	} else {
		return false;
	}
}

/******************************************************************************/
// Returns true if valid Directory Service request
function logAccessEntry($protocol, $type, $sp, $idp){
	global $WAYFLogFile, $useLogging;
	
	if (!$useLogging){
		return;
	}
	
	// Let's make sure the file exists and is writable first.
	if (is_writable($WAYFLogFile)) {
			// In our example we're opening $filename in append mode.
			// The file pointer is at the bottom of the file hence
			// that's where $somecontent will go when we fwrite() it.
			if (!$handle = fopen($WAYFLogFile, 'a')) {
					return;
			}
			
			// Create log entry
			$entry = date('Y-m-d H:i:s').' '.$_SERVER['REMOTE_ADDR'].' '.$protocol.' '.$type.' '.$idp.' '.$sp."\n";
			
			// Write $somecontent to our opened file.
			if (fwrite($handle, $entry) === FALSE) {
					return;
			}
			fclose($handle);
	}
}
/******************************************************************************/
// Returns true if PATH info indicates a request of type $type
function isRequestType($type){
	// Make sure the type is checked at end of path info
	return checkPathInfo($type.'$');
}

/******************************************************************************/
// Checks for substrings in Path Info and returns true if match was found
function checkPathInfo($needle){
	if (
		isset($_SERVER['PATH_INFO']) 
		&& !empty($_SERVER['PATH_INFO'])
		&& preg_match('|/'.$needle.'|', $_SERVER['PATH_INFO'])){
		
		return true;
	} else {
		return false;
	}
}

/******************************************************************************/
// Converts to the unified datastructure that the Shibboleth DS will be using
function convertToShibDSStructure($IDProviders){
	global $federationName;
	
	$ShibDSIDProviders['identityProviders'] = array();
	
	foreach ($IDProviders as $key => $value){
		
		// Skip unknown and category entries
		if(
			!isset($value['Type']) 
			|| $value['Type'] == 'category'
			|| $value['Type'] == 'wayf'
			){
			continue;
		}
		
		// Init and fill IdP data
		$identityProvider = array();
		$identityProvider['entityID'] = $key;
		$identityProvider['shibSSOEndpoint'] =  $value['SSO'];
		$identityProvider['displayNames'][] = array('lang' => 'en', 'name' => $value['Name']);
		$identityProvider['attributes'][] = array(
		'name' => 'type', 'value' => $value['Type']
		);
		
		// Add displayNames in other languages
		foreach($value as $lang => $name){
			if(
				   $lang == 'Name'
				|| $lang == 'SSO'
				|| $lang == 'Realm'
				|| $lang == 'Type'
				|| $lang == 'IP'
				
			){
				continue;
			}
			
			if (isset($name['Name'])){
				$identityProvider['displayNames'][] = array('lang' => $lang, 'name' => $name['Name']);
			}
		}
		
		
		// Add kerberos realm
		if(isset($value['Realm'])){
			$identityProvider['attributes'][] = array(
		'name' => 'kerberosRealm', 'value' => $value['Realm']
		);
		}
		
		
		// Add IP ranges
		if(isset($value['IP'])){
			$identityProvider['attributes'][] = array(
		'name' => 'IP', 'value' => $value['IP']
		);
		}
		
		// Add data to ShibDSIDProviders
		$ShibDSIDProviders['identityProviders'][] = $identityProvider;
	}
	
	return $ShibDSIDProviders;
	
}

/******************************************************************************/
// Sorts the IDProviders array
function sortIdentityProviders(&$IDProviders){
	$sortedIDProviders = Array();
	$sortedCategories = Array();
	
	foreach ($IDProviders as $entityId => $IDProvider){
		if (!is_array($IDProvider) || !isset($IDProvider['Name'])){
			// Remove any entries that are not arrays
			unset($IDProviders[$entityId]);
		} elseif ($IDProvider['Type'] == 'category'){
			$sortedCategories[$entityId] = $IDProvider;
		} else {
			$sortedIDProviders[$entityId] = $IDProvider;
		}
	}
	
	// Sort categories and IdPs
	if (count($sortedCategories) > 1){
		// Sort using index
		uasort($sortedCategories, 'sortUsingTypeIndexAndName');
	} else {
		// Sort alphabetically using the key of a category
		ksort($sortedCategories);
	}
	
	// Add category 'unknown' if not present
	if (!isset($IDProviders['unknown'])){
		$sortedCategories['unknown'] = array (
		'Name' => 'Unknown',
		'Type' => 'category',
		);
	}
	
	// Sort Identity Providers
	uasort($sortedIDProviders, 'sortUsingTypeIndexAndName');
	$IDProviders = Array();
	
	// Compose array
	$showUnknownCategory = false;
	while(list($categoryKey, $categoryValue) = each($sortedCategories)){
		$IDProviders[$categoryKey] = $categoryValue;
		
		// Loop through all IdPs
		foreach ($sortedIDProviders as $IDProvidersPKey => $IDProvidersValue){
			// Add IdP if its type matches the current category
			if ($IDProvidersValue['Type'] == $categoryKey){
				$IDProviders[$IDProvidersPKey] = $IDProvidersValue;
				unset($sortedIDProviders[$IDProvidersPKey]);
			}
			
			// Add IdP if its type is 'unknown' or if there doesnt exist a category for its type
			if ($categoryKey == 'unknown' || !isset($sortedCategories[$IDProvidersValue['Type']])){
				$IDProviders[$IDProvidersPKey] = $IDProvidersValue;
				unset($sortedIDProviders[$IDProvidersPKey]);
				$showUnknownCategory = true;
			}
			
		}
	}
	
	// Check if unkown category is needed
	if (!$showUnknownCategory){
		unset($IDProviders['unknown']);
	}
	
}

/******************************************************************************/
// Sorts two entries according to their Type, Index and (local) Name
function sortUsingTypeIndexAndName($a, $b){
	global $language;
	
	if ($a['Type'] != $b['Type']){
		return strcmp($b['Type'], $a['Type']);
	} elseif (isset($a['Index']) && isset($b['Index']) && $a['Index'] != $b['Index']){
		return strcmp($a['Index'], $b['Index']);
	} else {
		// Sort using locale names
		$localNameB = (isset($a[$language]['Name'])) ? $a[$language]['Name'] : $a['Name'];
		$localNameA = (isset($b[$language]['Name'])) ? $b[$language]['Name'] : $b['Name'];
		return strcmp($localNameB, $localNameA);
	}
}

?>
