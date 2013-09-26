<?php // Copyright (c) 2013, SWITCH - Serving Swiss Universities

/*
******************************************************************************
This file contains the some functions that render HTML code.
******************************************************************************
*/

if(!isset($_SERVER['REMOTE_ADDR']) || basename($_SERVER['SCRIPT_NAME']) == 'templates.php'){
	exit('No direct script access allowed');
}

/*------------------------------------------------*/
// Functions containing HTML code
/*------------------------------------------------*/

function printHeader(){

	global $langStrings, $language, $imageURL, $javascriptURL, $cssURL, $logoURL, $useImprovedDropDownList, $organizationLogoURL;
	global $federationURL, $organizationURL, $faqURL, $helpURL, $privacyURL;
	
	// Check if custom header template exists
	if(file_exists('custom-header.php')){
		include('custom-header.php');
	} else {
		// Use default code
		include('default-header.php');
	}
}


/******************************************************************************/
// Presents the user the drop-down list with available IDPs
function printWAYF(){
	
	global $selectedIDP, $language, $IDProviders, $SProviders, $redirectCookieName, $imageURL, $redirectStateCookieName, $showPermanentSetting;
	
	if (!isset($showPermanentSetting)){
		$showPermanentSetting = false;
	}
	
	$promptMessage =  getLocalString('make_selection');
	$serviceName = '';
	$entityID = '';
	
	// Check if entityID is available
	if (isset($_GET['entityID'])){
		$entityID = $_GET['entityID'];
	} else if (isset($_GET['providerId'])){
		$entityID = $_GET['providerId'];
	}
	
	// Set service name if entityID has a description
	if (!empty($entityID) && isset($SProviders[$entityID]) ){
		$SP = $SProviders[$entityID];
		$serviceName = $SP['Name'];
		if (isset($SP[$language]['Name'])){
			$serviceName = $SP[$language]['Name'];
		}
	}
	
	// Fallback to hostname if no name was available
	if (empty($serviceName)){
		if (isset($_GET['return'])){
			$serviceName = getHostNameFromURI($_GET['return']);
		} else if (isset($_GET['shire'])){
			$serviceName = getHostNameFromURI($_GET['shire']);
		} else {
			$serviceName = 'unknown';
		}
		$serviceName = '<span class="hostName">'.$serviceName.'</span>';
	} else {
		$serviceName = '<span class="serviceName">'.$serviceName.'</span>';
	}
	
	// Compose strings
	$promptMessage =  sprintf(getLocalString('access_host'), $serviceName);
	$actionURL = $_SERVER['SCRIPT_NAME'].'?'.htmlentities($_SERVER['QUERY_STRING']);
	$defaultSelected = ($selectedIDP == '-') ? 'selected="selected"' : '';
	$rememberSelectionChecked = (isset($_COOKIE[$redirectStateCookieName])) ? 'checked="checked"' : '' ;
	
	// Check if custom header template exists
	if(file_exists('custom-body.php')){
		include('custom-body.php');
	} else {
		// Use default code
		include('default-body.php');
	}
}

/******************************************************************************/
// Presents the user a form to set a permanent cookie for their default IDP
function printSettings(){
	
	global $selectedIDP, $language, $IDProviders, $redirectCookieName;
	
	$actionURL = $_SERVER['SCRIPT_NAME'].'?'.htmlentities($_SERVER['QUERY_STRING']);
	$defaultSelected = ($selectedIDP == '-') ? 'selected="selected"' : '';
	
	// Check if custom header template exists
	if(file_exists('custom-settings.php')){
		include('custom-settings.php');
	} else {
		// Use default code
		include('default-settings.php');
	} 
}

/******************************************************************************/
// Prints the HTML drop down list including categories etc
function printDropDownList($IDProviders, $selectedIDP = ''){
	global $language;
	
	$counter = 0;
	$optgroup = '';
	foreach ($IDProviders as $key => $values){
		
		// Get IdP Name
		if (isset($values[$language]['Name'])){
			$IdPName = $values[$language]['Name'];
		} else {
			$IdPName = $values['Name'];
		}
		
		// Add additional information as data attribute to the entry
		$data = getDomainNameFromURI($key);
		$data .= composeOptionData($values);
		
		// Figure out if entry is valid or a category
		if (!isset($values['SSO'])){
			
			// Check if entry is a category
			if (isset($values['Type']) && $values['Type'] == 'category'){
				if (!empty($optgroup)){
					echo '
	</optgroup>';
				}
				
				// Add another category unless first (and  probably only) category is unknown
				if (!empty($optgroup) || $key != 'unknown'){
						echo '
<optgroup label="'.$IdPName.'">';
				$optgroup = $key;
				}
			}
			
			continue;
		}
		
		// Set selected attribute
		if ($selectedIDP == $key){
			$selected = ' selected="selected"';
		} else {
			$selected = '';
		}
		
		// Add logo (which is assumed to be 16x16px) to extension string
		$logo = '';
		if (isset($values['Logo'])){
			$logo = 'logo="'.$values['Logo']['URL']. '"';
		}
		
		echo '
		<option value="'.$key.'"'.$selected.' data="'.$data.'" '.$logo.'>'.$IdPName.'</option>';
		
		$counter++;
	}
	
	// Add last optgroup if that was used
	if (!empty($optgroup)){
		echo '
	</optgroup>';
	}
}

/******************************************************************************/
// Prints the notice that tells the users their permanent IDP with an option
// to clear the permanent cookie.
function printNotice(){
	
	global $redirectCookieName, $IDProviders;
	
	$actionURL = $_SERVER['SCRIPT_NAME'].'?'.htmlentities($_SERVER['QUERY_STRING']);
	
	$hiddenUserIdPInput = '';
	$permanentUserIdP = '';
	$permanentUserIdPName = '';
	$permanentUserIdPLogo = '';
	
	
	if (
			isset($_POST['user_idp']) 
			&& checkIDPAndShowErrors($_POST['user_idp'])
		){
		$permanentUserIdP = $_POST['user_idp'];
	} elseif (
			isset($_COOKIE[$redirectCookieName]) 
			&& checkIDPAndShowErrors($_COOKIE[$redirectCookieName])
		){
		$permanentUserIdP = $_COOKIE[$redirectCookieName];
	}
	
	if ($permanentUserIdP != ''){
		$hiddenUserIdPInput = '<input type="hidden" name="user_idp" value="'.$permanentUserIdP.'">';
		$permanentUserIdPName = $IDProviders[$permanentUserIdP]['Name'];
		$permanentUserIdPLogo = $IDProviders[$permanentUserIdP]['Logo']['URL'];
	}
	
	// Check if footer template exists
	if(file_exists('custom-notice.php')){
		include('custom-notice.php');
	} else {
		// Use default code
		include('default-notice.php');
	}
}

/******************************************************************************/
// Prints end of HTML page
function printFooter(){
	
	// Check if footer template exists
	if(file_exists('custom-footer.php')){
		include('custom-footer.php');
	} else {
		// Use default code
		include('default-footer.php');
	}
}

/******************************************************************************/
// Prints an error message
function printError($message){
	
	global $langStrings, $language, $supportContactEmail;
	
	// Show Header
	printHeader();
	
	// Check if error template exists
	if(file_exists('custom-error.php')){
		include('custom-error.php');
	} else {
		// Use default code
		include('default-error.php');
	}
	
	// Show footer
	printFooter();
}

/******************************************************************************/
// Prints the JavaScript that renders the Embedded WAYF
function printEmbeddedWAYFScript(){

	global $langStrings, $language, $imageURL, $javascriptURL, $cssURL, $logoURL, $smallLogoURL, $federationURL;
	global $selectedIDP, $IDProviders, $SAMLDomainCookieName, $redirectCookieName, $redirectStateCookieName, $federationName;
	
	// Get some values that are used in the script
	$loginWithString = getLocalString('login_with');
	$makeSelectionString = getLocalString('make_selection', 'js');
	$loggedInString =  getLocalString('logged_in');
	$configurationScriptUrl = preg_replace('/embedded-wayf.js/', 'embedded-wayf.js/snippet.html', 'https://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
	$utcTime = time();
	$checkedBool = (isset($_COOKIE[$redirectStateCookieName]) && !empty($_COOKIE[$redirectStateCookieName])) ? 'checked="checked"' : '' ;
	$rememberSelectionText = addslashes(getLocalString('remember_selection'));
	$loginString = addslashes(getLocalString('login'));
	$selectIdPString = addslashes(getLocalString('select_idp'));
	$otherFederationString = addslashes(getLocalString('other_federation'));
	$mostUsedIdPsString = addslashes(getLocalString('most_used'));
	
	// Generate list of Identity Providers
	$JSONIdPArray = array();
	$JSONCategoryArray = array();
	foreach ($IDProviders as $key => $IDProvider){
		
		// Get IdP Name
		if (isset($IDProvider[$language]['Name'])){
			$IdPName = addslashes($IDProvider[$language]['Name']);
		} else {
			$IdPName = addslashes($IDProvider['Name']);
		}
		
		// Set selected attribute
		$selected = ($selectedIDP == $key) ? ' selected:"true",' : '' ;
		$IdPType = isset($IDProviders[$key]['Type']) ? $IDProviders[$key]['Type'] : '';
		
		// SSO
		if (isset($IDProvider['SSO'])){
			$IdPSSO = $IDProvider['SSO'];
		} else {
			$IdPSSO = '';
		}
		
		// Logo URL
		if (isset($IDProvider['Logo']['URL'])){
			$IdPLogoURL = $IDProvider['Logo']['URL'];
		} else {
			$IdPLogoURL = '';
		}
		
		// Add other information to find IdP
		$IdPData = getDomainNameFromURI($key);
		$IdPData .= composeOptionData($IDProvider);
		
		// Skip non-IdP entries
		if ($IdPType == ''){
			continue;
		}
		
		// Fill category and IdP buckets
		if ($IdPType == 'category'){
			$JSONCategoryArray[] = <<<ENTRY

"{$key}":{
	type:"{$IdPType}",
	name:"{$IdPName}"
}

ENTRY;
		} else {
			$JSONIdPArray[] = <<<ENTRY

"{$key}":{ {$selected}
	type:"{$IdPType}",
	name:"{$IdPName}",
	logoURL:"{$IdPLogoURL}",
	data:"{$IdPData}"
}
ENTRY;
		}
	}
	$JSONIdPList = join(',', $JSONIdPArray);
	$JSONCategoryList = join(',', $JSONCategoryArray);
	
	// Text for javascript
	$searchText = getLocalString('search_idp', 'js');
	$noIdPFoundText =  getLocalString('no_idp_found', 'js');
	$noIdPAvailableText = getLocalString('no_idp_available', 'js');
	
	echo <<<SCRIPT

// To use this JavaScript, please access:
// {$configurationScriptUrl}
// and copy/paste the resulting HTML snippet to an unprotected web page that 
// you want the embedded WAYF to be displayed

// ############################################################################

// Declare all variables
var wayf_sp_entityID;
var wayf_URL;
var wayf_return_url;
var wayf_sp_handlerURL;

var wayf_use_discovery_service;
var wayf_use_improved_drop_down_list;
var wayf_use_small_logo;
var wayf_width;
var wayf_height;
var wayf_background_color;
var wayf_border_color;
var wayf_font_color;
var wayf_font_size;
var wayf_hide_logo;
var wayf_auto_login;
var wayf_logged_in_messsage;
var wayf_hide_after_login;
var wayf_most_used_idps;
var wayf_show_categories;
var wayf_hide_categories;
var wayf_hide_idps;
var wayf_unhide_idps;
var wayf_show_remember_checkbox;
var wayf_force_remember_for_session;
var wayf_additional_idps;
var wayf_sp_samlDSURL;
var wayf_sp_samlACURL;
var wayf_use_disco_feed;
var wayf_discofeed_url;
var wayf_html = "";
var wayf_categories = { {$JSONCategoryList}};
var wayf_idps = { {$JSONIdPList} };

// Define functions
function submitForm(){
	
	if (document.IdPList.user_idp && document.IdPList.user_idp.selectedIndex == 0){
		alert('{$makeSelectionString}');
		return false;
	}
	
	// Set local cookie
	var selectedIdP = document.IdPList.user_idp[document.IdPList.user_idp.selectedIndex].value;
	
	// User chose non-federation IdP
	if (
		wayf_additional_idps.length > 0 
		&& document.IdPList.user_idp
		&& document.IdPList.user_idp.selectedIndex >= (document.IdPList.user_idp.options.length - wayf_additional_idps.length)){
		
		var NonFedEntityID = wayf_additional_idps[selectedIdP].entityID;
		setDomainSAMLDomainCookie(NonFedEntityID);
		
		var redirect_url;
		// Redirect user to SP handler
		var redirect_url;
		if (wayf_use_discovery_service){
			
			var entityIDGETParam = getGETArgument("entityID");
			var returnGETParam = getGETArgument("return");
			if (entityIDGETParam != "" && returnGETParam != ""){
				redirect_url = returnGETParam;
			} else {
				redirect_url = wayf_sp_samlDSURL;
				redirect_url += getGETArgumentSeparator(redirect_url) + 'target=' + encodeURIComponent(wayf_return_url);
			}
			
			// Append selected Identity Provider
			redirect_url += '&entityID=' + encodeURIComponent(NonFedEntityID);
			
			// Make sure the redirect always is being executed in parent window
			if (window.parent){
				window.parent.location = redirect_url;
			} else {
				window.location = redirect_url;
			}
			
		} else {
			redirect_url = wayf_sp_handlerURL + '?providerId=' 
			+ encodeURIComponent(NonFedEntityID)
			+ '&target=' + encodeURIComponent(wayf_return_url);
			
			// Make sure the redirect always is being done in parent window
			if (window.parent){
				window.parent.location = redirect_url;
			} else {
				window.location = redirect_url;
			}
		}
		
		// If input type button is used for submit, we must return false
		return false;
	} else {
		var FedEntityID = selectedIdP;
		setDomainSAMLDomainCookie(FedEntityID);
		
		// User chose federation IdP entry
		document.IdPList.submit();
	}
	
	return false;
}

function writeHTML(a){
	wayf_html += a;
}

function isAllowedType(IdP, type){
	for ( var i=0; i<= wayf_hide_categories.length; i++){
		
		if (wayf_hide_categories[i] == type || wayf_hide_categories[i] == "all" ){
			
			for ( var i=0; i <= wayf_unhide_idps.length; i++){
				// Show IdP if it has to be unhidden
				if (wayf_unhide_idps[i] == IdP){
					return true;
				}
			}
			// If IdP is not unhidden, the default applies
			return false;
		}
	}
	
	// Category was not hidden
	return true;
}

function isAllowedCategory(category){
	
	if (!category || category == ''){
		return true;
	}
	
	for ( var i=0; i<= wayf_hide_categories.length; i++){
		
		if (wayf_hide_categories[i] == category || wayf_hide_categories[i] == "all" ){
			return false;
		}
	}
	
	// Category was not hidden
	return true;
}

function isAllowedIdP(IdP){
	
	for ( var i=0; i <= wayf_hide_idps.length; i++){
		if (wayf_hide_idps[i] == IdP){
			return false;
		}
	}
	// IdP was not hidden
	return true;
}

function createEntry(entityID, IdP){
	var data = '';
	var logo = '';
	var selected = '';
	
	if (IdP.data){
		data = ' data="' + IdP.data + '"';
	}
	
	if (IdP.logoURL){
		logo = ' logo="' + IdP.logoURL + '"';
	}
	
	if (IdP.selected){
		selected = ' selected="selected"';
	}
	
	writeHTML('<option value="' + entityID + '"' + data + logo + selected + '>' + IdP.name + '</option>');
}

function setDomainSAMLDomainCookie(entityID){
	// Create and store SAML domain cookie on host where WAYF is embedded
	var currentDomainCookie = getCookie('_saml_idp');
	var encodedEntityID = encodeBase64(entityID);
	
	if (currentDomainCookie == null){
		currentDomainCookie = '';
	}
	
	var oldIdPs = currentDomainCookie.split(' ');
	var newCookie = '';
	for (var i = 0; i < oldIdPs.length; i++) {
		if (oldIdPs[i] != encodedEntityID && oldIdPs[i] != ''){
			newCookie += oldIdPs[i] + ' ';
		}
	}
	newCookie += encodedEntityID;
	setCookie('{$SAMLDomainCookieName}', newCookie , 100);
}

function setCookie(c_name, value, expiredays){
	var exdate = new Date();
	exdate.setDate(exdate.getDate() + expiredays);
	document.cookie=c_name + "=" + escape(value) +
	((expiredays==null) ? "" : "; expires=" + exdate.toGMTString());
}

function getCookie(check_name){
	// First we split the cookie up into name/value pairs
	// Note: document.cookie only returns name=value, not the other components
	var a_all_cookies = document.cookie.split( ';' );
	var a_temp_cookie = '';
	var cookie_name = '';
	var cookie_value = '';
	
	for ( var i = 0; i < a_all_cookies.length; i++ ){
		// now we'll split apart each name=value pair
		a_temp_cookie = a_all_cookies[i].split( '=' );
		
		
		// and trim left/right whitespace while we're at it
		cookie_name = a_temp_cookie[0].replace(/^\s+|\s+$/g, '');
	
		// if the extracted name matches passed check_name
		if ( cookie_name == check_name )
		{
			// We need to handle case where cookie has no value but exists (no = sign, that is):
			if ( a_temp_cookie.length > 1 )
			{
				cookie_value = unescape( a_temp_cookie[1].replace(/^\s+|\s+$/g, '') );
			}
			// note that in cases where cookie is initialized but no value, null is returned
			return cookie_value;
			break;
		}
		a_temp_cookie = null;
		cookie_name = '';
	}
	
	return null;
}

// Query Shibboleth Session handler and process response afterwards
// This method has to be used because HttpOnly prevents reading 
// the shib session cookies via JavaScript
function isShibbolethSession(url){
	var xmlhttp;
	if (window.XMLHttpRequest){
		xmlhttp = new XMLHttpRequest();
	}  else {
		xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	}
	
	// Send request
	try {
		xmlhttp.open("GET", url, false);
		xmlhttp.send();
	} catch (e) {
		// Something went wrong, send back false
		return false;
	} 
	
	// Check response code
	if (xmlhttp.readyState != 4 || xmlhttp.status != 200 ){
		return false;
	}
	
	// Return true if session handler shows valid session
	if (
		xmlhttp.responseText.search(/Authentication Time/i) > 0){
		return true;
	}
	
	return false;
}

// Loads Identity Provider from DiscoFeed and adds them to additional IdPs
function loadDiscoFeedIdPs(){
	var xmlhttp;
	
	if (window.XMLHttpRequest){
		xmlhttp = new XMLHttpRequest();
	}  else {
		xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	}
	
	// Send request
	try {
		xmlhttp.open("GET", wayf_discofeed_url, false);
		xmlhttp.send();
	} catch (e) {
		// Something went wrong, send back false
		return;
	} 
	
	// Check response code
	if (xmlhttp.readyState != 4 || xmlhttp.status != 200 ){
		return;
	}
	
	// Load JSON
	var IdPs = eval("(" + xmlhttp.responseText + ")");
	
	return IdPs;

} 

// Adds unknown IdPs to wayf_additional_idps and hides IdPs that are not
// contained in the Discovery Feed
function processDiscoFeedIdPs(IdPs){
	
	if (typeof(IdPs) == "undefined"){
		return;
	}
	
	// Hide IdPs that are not in the Discovery Feed
	for (var entityID in wayf_idps){
		var foundIdP = false;
		for ( var i = 0; i < IdPs.length; i++) {
			if (IdPs[i].entityID == entityID){
				foundIdP = true;
			}
		}
		
		if (foundIdP == false){
			wayf_hide_idps.push(entityID);
		}
	}
	
	
	// Add unkown IdPs to wayf_additional_idps
	for ( var i = 0; i < IdPs.length; i++) {
		// Skip IdPs that are in same federation
		if (wayf_idps[IdPs[i].entityID]){
			continue;
		}
		
		// Skip hidden IdPs
		if (!isAllowedIdP(IdPs[i].entityID)){
			continue;
		}
		
		var newIdP;
		if (IdPs[i].DisplayNames){
			newIdP = {"name": IdPs[i].DisplayNames[0].value, "entityID":IdPs[i].entityID, "SAML1SSOurl":"https://www.example.org/test"};
		} else {
			newIdP = {"name":IdPs[i].entityID, "entityID":IdPs[i].entityID, "SAML1SSOurl":"https://www.example.org/test"};
		}
		wayf_additional_idps.push(newIdP);
	}
}

// Sorts Discovery feed entries 
function sortEntities(a, b){
	var nameA = a.name.toLowerCase();
	var nameB = b.name.toLowerCase();
	
	if (nameA < nameB){
		return -1;
	}
	
	if (nameA > nameB){
		return 1;
	}
	
	return 0;
}

// Returns true if user is logged in
function isUserLoggedIn(){
	
	if (
		   typeof(wayf_check_login_state_function) != "undefined"
		&& typeof(wayf_check_login_state_function) == "function" ){
		
		// Use custom function
		return wayf_check_login_state_function();
	
	} else {
		// Check Shibboleth session handler
		return isShibbolethSession(wayf_sp_handlerURL + '/Session');
	}
}

function encodeBase64(input) {
	var base64chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";
	var output = "", c1, c2, c3, e1, e2, e3, e4;
	
	for ( var i = 0; i < input.length; ) {
		c1 = input.charCodeAt(i++);
		c2 = input.charCodeAt(i++);
		c3 = input.charCodeAt(i++);
		e1 = c1 >> 2;
		e2 = ((c1 & 3) << 4) + (c2 >> 4);
		e3 = ((c2 & 15) << 2) + (c3 >> 6);
		e4 = c3 & 63;
		if (isNaN(c2)){
			e3 = e4 = 64;
		} else if (isNaN(c3)){
			e4 = 64;
		}
		output += base64chars.charAt(e1) + base64chars.charAt(e2) + base64chars.charAt(e3) + base64chars.charAt(e4);
	}
	
	return output;
}

function decodeBase64(input) {
	var base64chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";
	var output = "", chr1, chr2, chr3, enc1, enc2, enc3, enc4;
	var i = 0;

	// Remove all characters that are not A-Z, a-z, 0-9, +, /, or =
	var base64test = /[^A-Za-z0-9\+\/\=]/g;
	input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");
	
	do {
		enc1 = base64chars.indexOf(input.charAt(i++));
		enc2 = base64chars.indexOf(input.charAt(i++));
		enc3 = base64chars.indexOf(input.charAt(i++));
		enc4 = base64chars.indexOf(input.charAt(i++));

		chr1 = (enc1 << 2) | (enc2 >> 4);
		chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
		chr3 = ((enc3 & 3) << 6) | enc4;

		output = output + String.fromCharCode(chr1);

		if (enc3 != 64) {
			output = output + String.fromCharCode(chr2);
		}
		if (enc4 != 64) {
			output = output + String.fromCharCode(chr3);
		}
		
		chr1 = chr2 = chr3 = "";
		enc1 = enc2 = enc3 = enc4 = "";
		
	} while (i < input.length);
	
	return output;
}

function getGETArgument(name){
	name = name.replace(/[\[]/,"\\\[").replace(/[\]]/,"\\\]");
	var regexString = "[\\?&]"+name+"=([^&#]*)";
	var regex = new RegExp(regexString);
	var results = regex.exec(window.location.href);
	
	if( results == null ){
		return "";
	} else {
		return decodeURIComponent(results[1]);
	}
}

function getGETArgumentSeparator(url){
	if (url.indexOf('?') >=0 ){
		return '&';
	} else {
		return '?';
	}
}

function ieLoadBugFix(scriptElement, callback){
	if (scriptElement.readyState=='loaded' || scriptElement.readyState=='completed'){
		callback();
	 } else {
		setTimeout(function() {
			ieLoadBugFix(scriptElement, callback); 
		}, 100);
	 }
}

function loadJQuery() {
	
	var head = document.getElementsByTagName('head')[0];
	var script = document.createElement('script');
	script.src = '{$javascriptURL}/jquery.js';
	script.type = 'text/javascript';
	script.onload = function() {
		loadImprovedDropDown();
	};
	
	// Fix for IE Browsers
	ieLoadBugFix(script, function(){
		loadImprovedDropDown();
	});
	
	head.appendChild(script);
}

function loadImprovedDropDown(){
	
	// Load CSS
	$('head').append('<link rel="stylesheet" type="text/css" href="{$cssURL}/default-ImprovedDropDown.css">');
	
	// Load Improved Drop Down Javascript
	$.getScript( '{$javascriptURL}/improvedDropDown.js', function( ) {
		var searchText = '{$searchText}';
		$("#user_idp:enabled option[value='-']").text(searchText);
		
		// Convert select element into improved drop down list
		$("#user_idp:enabled").improveDropDown({
			iconPath:'{$imageURL}/drop_icon.png',
			noMatchesText: '{$noIdPFoundText}',
			noItemsText: '{$noIdPAvailableText}'
		});
	 
	});
}

(function() {
	
	var config_ok = true; 
	
	// Get GET parameters that maybe are set by Shibboleth
	var returnGETParam = getGETArgument("return");
	var entityIDGETParam = getGETArgument("entityID");
	
	// First lets make sure properties are available
	if(
		typeof(wayf_use_discovery_service)  == "undefined"  
		|| typeof(wayf_use_discovery_service) != "boolean"
	){
		wayf_use_discovery_service = true;
	}
	
	if(
		typeof(wayf_use_improved_drop_down_list)  == "undefined"  
		|| typeof(wayf_use_improved_drop_down_list) != "boolean"
	){
		wayf_use_improved_drop_down_list = false;
	}
	
	// Overwrite entityID with GET argument if present
	var entityIDGETParam = getGETArgument("entityID");
	if (entityIDGETParam != ""){
		wayf_sp_entityID = entityIDGETParam;
	}
	
	if(
		typeof(wayf_sp_entityID) == "undefined"
		|| typeof(wayf_sp_entityID) != "string"
		){
		alert('The mandatory parameter \'wayf_sp_entityID\' is missing. Please add it as a javascript variable on this page.');
		config_ok = false;
	}
	
	if(
		typeof(wayf_URL) == "undefined"
		|| typeof(wayf_URL) != "string"
		){
		alert('The mandatory parameter \'wayf_URL\' is missing. Please add it as a javascript variable on this page.');
		config_ok = false;
	}
	
	if(
		typeof(wayf_return_url) == "undefined"
		|| typeof(wayf_return_url) != "string"
		){
		alert('The mandatory parameter \'wayf_return_url\' is missing. Please add it as a javascript variable on this page.');
		config_ok = false;
	}
	
	if(
		wayf_use_discovery_service == false 
		&& typeof(wayf_sp_handlerURL) == "undefined"
		){
		alert('The mandatory parameter \'wayf_sp_handlerURL\' is missing. Please add it as a javascript variable on this page.');
		config_ok = false;
	}
	
	if(
		wayf_use_discovery_service == true 
		&& typeof(wayf_sp_samlDSURL) == "undefined"
		){
		// Set to default DS handler
		wayf_sp_samlDSURL = wayf_sp_handlerURL + "/Login";
	}
	
	if (
		typeof(wayf_sp_samlACURL) == "undefined"
		|| typeof(wayf_sp_samlACURL) != "string"
		){
		wayf_sp_samlACURL = wayf_sp_handlerURL + '/SAML/POST';
	}
	
	if(
		typeof(wayf_font_color) == "undefined"
		|| typeof(wayf_font_color) != "string"
		){
		wayf_font_color = 'black';
	}
	
	if(
		typeof(wayf_font_size) == "undefined"
		|| typeof(wayf_font_size) != "number"
		){
		wayf_font_size = 12;
	}
	
	if(
		typeof(wayf_border_color) == "undefined"
		|| typeof(wayf_border_color) != "string"
		){
		wayf_border_color = '#848484';
	}
	
	if(
		typeof(wayf_background_color) == "undefined"
		|| typeof(wayf_background_color) != "string"
		){
		wayf_background_color = '#F0F0F0';
	}
	
	if(
		typeof(wayf_use_small_logo) == "undefined" 
		|| typeof(wayf_use_small_logo) != "boolean"
		){
		wayf_use_small_logo = true;
	}
	
	if(
		typeof(wayf_hide_logo) == "undefined" 
		|| typeof(wayf_use_small_logo) != "boolean"
		){
		wayf_hide_logo = false;
	}
	
	if(
		typeof(wayf_width) == "undefined" 
		|| typeof(wayf_width) != "number"
	){
		wayf_width = "auto";
	} else {
		wayf_width += 'px';
	}
	
	if(
		typeof(wayf_height) == "undefined" 
		|| typeof(wayf_height) != "number"
		){
		wayf_height = "auto";
	} else {
		wayf_height += "px";
	}
	
	if(
		typeof(wayf_show_remember_checkbox) == "undefined"
		|| typeof(wayf_show_remember_checkbox) != "boolean"
		){
		wayf_show_remember_checkbox = true;
	}
	
	if(
		typeof(wayf_force_remember_for_session) == "undefined"
		|| typeof(wayf_force_remember_for_session) != "boolean"
		){
		wayf_force_remember_for_session = false;
	}
	
	if(
		typeof(wayf_auto_login) == "undefined"
		|| typeof(wayf_auto_login) != "boolean"
		){
		wayf_auto_login = true;
	}
	
	if(
		typeof(wayf_hide_after_login) == "undefined"
		|| typeof(wayf_hide_after_login) != "boolean"
		){
		wayf_hide_after_login = true;
	}
	
	if(
		typeof(wayf_logged_in_messsage) == "undefined"
		|| typeof(wayf_logged_in_messsage) != "string"
		){
		wayf_logged_in_messsage = "{$loggedInString}".replace(/%s/, wayf_return_url);
	}
	
	if(
		typeof(wayf_most_used_idps) == "undefined"
		|| typeof(wayf_most_used_idps) != "object"
		){
		wayf_most_used_idps = new Array();
	}
	
	if(
		typeof(wayf_show_categories) == "undefined"
		|| typeof(wayf_show_categories) != "boolean"
		){
		wayf_show_categories = true;
	}
	
	if(
		typeof(wayf_hide_categories) == "undefined"
		|| typeof(wayf_hide_categories) != "object"
		){
		wayf_hide_categories = new Array();
	}
	
	if(
		typeof(wayf_unhide_idps) == "undefined"
		||  typeof(wayf_unhide_idps) != "object"
	){
		wayf_unhide_idps = new Array();
	}
	
	// Disable categories if IdPs are unhidden from hidden categories
	if (wayf_unhide_idps.length > 0){
		wayf_show_categories = false;
	}
	
	if(
		typeof(wayf_hide_idps) == "undefined"
		|| typeof(wayf_hide_idps) != "object"
		){
		wayf_hide_idps = new Array();
	}
	
	if(
		typeof(wayf_additional_idps) == "undefined"
		|| typeof(wayf_additional_idps) != "object"
		){
		wayf_additional_idps = [];
	}
	
	if(
		typeof(wayf_use_disco_feed) == "undefined"
		|| typeof(wayf_use_disco_feed) != "boolean"
		){
		wayf_use_disco_feed = false;
	}
	
	if(
		typeof(wayf_discofeed_url) == "undefined"
		|| typeof(wayf_discofeed_url) != "string"
		){
		wayf_discofeed_url = "/Shibboleth.sso/DiscoFeed";
	}
	
	// Exit without outputting html if config is not ok
	if (config_ok != true){
		return;
	}
	
	// Check if user is logged in already:
	var user_logged_in = isUserLoggedIn();
	
	// Check if user is authenticated already and 
	// whether something has to be drawn
	if (
		wayf_hide_after_login 
		&& user_logged_in 
		&& wayf_logged_in_messsage == ''
	){
		
		// Exit script without drawing
		return;
	}
	
	// Now start generating the HTML for outer box
	if(
		wayf_hide_after_login 
		&& user_logged_in
	){
		writeHTML('<div id="wayf_div" style="background:' + wayf_background_color + ';border-style: solid;border-color: ' + wayf_border_color + ';border-width: 1px;padding: 10px; height: auto;width: ' + wayf_width + ';text-align: left;overflow: hidden;">');
	} else {
		writeHTML('<div id="wayf_div" style="background:' + wayf_background_color + ';border-style: solid;border-color: ' + wayf_border_color + ';border-width: 1px;padding: 10px; height: ' + wayf_height + ';width: ' + wayf_width + ';text-align: left;overflow: hidden;">');
	}
	
	// Do we have to display the logo
	if (wayf_hide_logo != true){
		
		// Write header of logo div
		writeHTML('<div id="wayf_logo_div" style="float: right;"><a href="{$federationURL}" target="_blank" style="border:0px; margin-bottom: 4px;">');
		
		// Which size of the logo should we display
		var embeddedLogoURL = '';
		if (wayf_use_small_logo){
			embeddedLogoURL = "{$smallLogoURL}";
		} else {
			embeddedLogoURL = "{$logoURL}";
		}
		
		// Only show logo if it is not empty
		if (embeddedLogoURL != ''){
			writeHTML('<img id="wayf_logo" src="' + embeddedLogoURL +  '" alt="Federation Logo" style="border:0px; margin-bottom: 4px;">');
		}
		
		// Write footer of logo div
		writeHTML('</a></div>');
	}
	
	// Start login check
	// If session exists, we only draw the logged_in_message
	if(
		wayf_hide_after_login 
		&& user_logged_in
	){
		writeHTML('<p id="wayf_intro_div" style="float:left;font-size:' + wayf_font_size + 'px;color:' + wayf_font_color + ';">' + wayf_logged_in_messsage + '</p>');
		
	} else {
	// Else draw embedded WAYF
		
		//Do we have to draw custom text? or any text at all?
		if(typeof(wayf_overwrite_intro_text) == "undefined"){
			writeHTML('<label for="user_idp" id="wayf_intro_label" style="float:left; min-width:80px; font-size:' + wayf_font_size + 'px;color:' + wayf_font_color + ';">{$loginWithString}</label>');
		} else if (wayf_overwrite_intro_text != "") {
			writeHTML('<label for="user_idp" id="wayf_intro_label" style="float:left; min-width:80px; font-size:' + wayf_font_size + 'px;color:' + wayf_font_color + ';">' + wayf_overwrite_intro_text + '</label>');
		}
		
		var wayf_authReq_URL = '';
		var form_start = '';
		
		if (wayf_use_discovery_service == true){
			// New SAML Discovery Service protocol
			
			wayf_authReq_URL = wayf_URL;
			
			// Use GET arguments or use configuration parameters
			if (entityIDGETParam != "" && returnGETParam != ""){
				wayf_authReq_URL += '?entityID=' + encodeURIComponent(entityIDGETParam);
				wayf_authReq_URL += '&amp;return=' + encodeURIComponent(returnGETParam);
			} else {
				var return_url = wayf_sp_samlDSURL + getGETArgumentSeparator(wayf_sp_samlDSURL);
				return_url += 'SAMLDS=1&target=' + encodeURIComponent(wayf_return_url);
				wayf_authReq_URL += '?entityID=' + encodeURIComponent(wayf_sp_entityID);
				wayf_authReq_URL += '&amp;return=' + encodeURIComponent(return_url);
			}
		} else {
			// Old Shibboleth WAYF protocol
			wayf_authReq_URL = wayf_URL;
			wayf_authReq_URL += '?providerId=' + encodeURIComponent(wayf_sp_entityID);
			wayf_authReq_URL += '&amp;target=' + encodeURIComponent(wayf_return_url);
			wayf_authReq_URL += '&amp;shire=' + encodeURIComponent(wayf_sp_samlACURL);
			wayf_authReq_URL += '&amp;time={$utcTime}';
		}
		
		// Add form element
		form_start = '<form id="IdPList" name="IdPList" method="post" target="_parent" action="' + wayf_authReq_URL + '">';
		
SCRIPT;
	
	// Create redirect links in case the checkbox is checked
	if (isset($_COOKIE[$redirectCookieName]) && !empty($_COOKIE[$redirectCookieName])){
		// Redirect user to WAYF automatically
		echo <<<SCRIPT
		
		// Do auto login in case this option is set
		if (wayf_auto_login){
		
			// Redirect user automatically to WAYF
			var redirect_url = wayf_authReq_URL.replace(/&amp;/g, '&');
			
			// Make sure the redirect is always being done in the parent window
			if (window.parent){
				window.parent.location = redirect_url;
			} else {
				window.location = redirect_url;
			}
			
			// Return here and stop writing HTML
			return;
		}
		
SCRIPT;
		
	}
	
	echo <<<SCRIPT
		
		// Load additional IdPs from DiscoFeed if feature is enabled
		if (wayf_use_disco_feed){
			discoFeedIdPs = loadDiscoFeedIdPs();
			
			// Hide IdPs for which SP doesnt have metadata and add unknown IdPs 
			processDiscoFeedIdPs(discoFeedIdPs);
		}
		
		writeHTML(form_start);
		writeHTML('<input name="request_type" type="hidden" value="embedded">');
		writeHTML('<select id="user_idp" name="user_idp" style="margin-top: 15px;margin-bottom: 10px; width: 100%;">');
		
		// Get local cookie
		var saml_idp_cookie = getCookie('_saml_idp');
		var last_idp = '';
		var last_idps = new Array();
		
		// Get last used IdP from local host cookie
		if (saml_idp_cookie && saml_idp_cookie.length > 0){
			last_idps = saml_idp_cookie.split(' ');
			if (last_idps.length > 0){
				last_idp = last_idps[(last_idps.length - 1)];
				if (last_idp.length > 0){
					last_idp = decodeBase64(last_idp);
				}
			}
		}
		
		// Add first entry: "Select your IdP..."
		writeHTML('<option value="-">{$selectIdPString} ...</option>');
		
		// Favourites
		if (wayf_most_used_idps.length > 0){
			if(typeof(wayf_overwrite_most_used_idps_text) == "undefined"){
				writeHTML('<optgroup label="{$mostUsedIdPsString}">');
			} else {
				writeHTML('<optgroup label="' + wayf_overwrite_most_used_idps_text + '">');
			}
			
			// Show most used IdPs in the order they are defined
			for ( var i=0; i < wayf_most_used_idps.length; i++){
				if (wayf_idps[wayf_most_used_idps[i]]){
					writeHTML('<option value="' + wayf_most_used_idps[i] + '" logo="' + wayf_idps[wayf_most_used_idps[i]].logoURL + '" logo="' + wayf_idps[wayf_most_used_idps[i]].data + '">' + wayf_idps[wayf_most_used_idps[i]].name + '</option>');
				}
			}
			
			writeHTML('</optgroup>');
		}
		
		// Draw drop down list
		var category = '';
		for(var entityID in wayf_idps){
			
			var idp_type = wayf_idps[entityID].type;
			
			if (wayf_show_categories == true && category != idp_type){
				
				// Finish category if a new one starts that exists
				if (category != '' && wayf_categories[idp_type]){
					writeHTML('</optgroup>');
				}
				
				// Skip category if it is not allowed
				if (!isAllowedCategory(idp_type)){
					continue;
				} else if (wayf_categories[idp_type]) {
					// Start new category if there exists a description
					writeHTML('<optgroup label="' + wayf_categories[idp_type].name + '">');
				}
			}
			
			if (isAllowedType(entityID, idp_type) && isAllowedIdP(entityID)){
				createEntry(entityID, wayf_idps[entityID]);
			}
			
			category =  idp_type;
		}
		
		// Close category if categories are enabled
		if (wayf_show_categories == true){
			writeHTML('</optgroup>');
		}
		
		if (wayf_additional_idps.length > 0){
			
			if (wayf_show_categories == true){
				writeHTML('<optgroup label="{$otherFederationString}">');
			}
			
			// Sort Array
			wayf_additional_idps.sort(sortEntities)
			
			// Show additional IdPs
			for ( var i=0; i < wayf_additional_idps.length ; i++){
				
				if (!wayf_additional_idps[i]){
					continue;
				}
				
				writeHTML('<option ');
				
				// Last used IdP is known because of local _saml_idp cookie
				if (
					wayf_additional_idps[i].name
					&& wayf_additional_idps[i].entityID == last_idp
					){
					writeHTML(' selected="selected" ');
				} 
				// If no IdP is known but the default IdP matches, use this entry
				else if (
					wayf_additional_idps[i].name
					&& typeof(wayf_default_idp) != "undefined" 
					&& wayf_additional_idps[i].entityID == wayf_default_idp
					){
					writeHTML(' selected="selected" ');
				}
				
				writeHTML(' value="' + i + '" data="' + wayf_additional_idps[i].name + '"');
				
				// Add logo if available
				if (wayf_additional_idps[i].logoURL){
					writeHTML(' logo="' + wayf_additional_idps[i].logoURL + '"');
				}
				
				writeHTML('>' + wayf_additional_idps[i].name  + '</option>');
			}
			
			if (wayf_show_categories == true){
				writeHTML('</optgroup>');
			}
		}
		
		writeHTML('</select>');
		
		// Draw checkbox
		writeHTML('<div id="wayf_remember_checkbox_div" style="float: left;margin-top: 0px;margin-bottom:10px;">');
		
		// Do we have to show the remember settings checkbox?
		if (wayf_show_remember_checkbox){
			// Is the checkbox forced to be checked
			if (wayf_force_remember_for_session){
				// First draw the dummy checkbox ...
				writeHTML('<input id="wayf_remember_checkbox" type="checkbox" name="session_dummy" value="true" checked="checked" disabled="disabled" >&nbsp;');
				// ... and now the real but hidden checkbox
				writeHTML('<input type="hidden" name="session" value="true">&nbsp;');
			} else {
				writeHTML('<input id="wayf_remember_checkbox" type="checkbox" name="session" value="true" {$checkedBool}>&nbsp;');
			}
			
			// Do we have to display custom text?
			if(typeof(wayf_overwrite_checkbox_label_text) == "undefined"){
				writeHTML('<label for="wayf_remember_checkbox" id="wayf_remember_checkbox_label" style="min-width:80px; font-size:' + wayf_font_size + 'px;color:' + wayf_font_color + ';">{$rememberSelectionText}</label>');
				
			} else if (wayf_overwrite_checkbox_label_text != "")  {
				writeHTML('<label for="wayf_remember_checkbox" id="wayf_remember_checkbox_label" style="min-width:80px; font-size:' + wayf_font_size + 'px;color:' + wayf_font_color + ';">' + wayf_overwrite_checkbox_label_text + '</label>');
			}
		} else if (wayf_force_remember_for_session){
			// Is the checkbox forced to be checked but hidden
			writeHTML('<input id="wayf_remember_checkbox" type="hidden" name="session" value="true">&nbsp;');
		}
		
		writeHTML('</div>');
		
		// Do we have to display custom text?
		if(typeof(wayf_overwrite_submit_button_text) == "undefined"){
			writeHTML('<input id="wayf_submit_button" type="submit" name="Login" accesskey="s" value="{$loginString}" style="float: right;" onClick="javascript:return submitForm();">');
		} else {
			writeHTML('<input id="wayf_submit_button" type="submit" name="Login" accesskey="s" value="' + wayf_overwrite_submit_button_text + '" style="float: right;" onClick="javascript:return submitForm();">');
		}
		
		// Close form
		writeHTML('</form>');
		
	}  // End login check
	
	// Close box
	writeHTML('</div>');
	
	// Now output HTML all at once
	document.write(wayf_html);
	
	if (wayf_use_improved_drop_down_list){
		// Check if jQuery is alread loaded
		if (typeof jQuery == 'undefined') {
			loadJQuery();
		} else {
			loadImprovedDropDown();
		}
	}
})()

SCRIPT;
}

/******************************************************************************/
// Print sample configuration script used for Embedded WAYF
function printEmbeddedConfigurationScript(){
	global $IDProviders;
	
	$types = array();
	foreach ($IDProviders as $IDProvider){
		if (isset($IDProvider['Type']) && $IDProvider['Type'] != 'category'){
			$types[$IDProvider['Type']] = $IDProvider['Type'];
		}
	}
	
	$host = $_SERVER['SERVER_NAME'];
	$path = $_SERVER['SCRIPT_NAME'];
	$types = '"'.implode('","',$types).'"';
	
	header('Content-type: text/plain;charset="utf-8"');
	
	if(file_exists('custom-embedded-wayf.php')){
		include('custom-embedded-wayf.php');
	} else {
		// Use default code
		include('default-embedded-wayf.php');
	}
}

/******************************************************************************/
// Print sample configuration script used for Embedded WAYF
function printCSS($file){
	
	global $imageURL;
	
	if ($file != 'ImprovedDropDown.css'){
		$file= 'styles.css';
	}
	
	$defaultCSSFile =  'css/default-'.$file;
	$cssContent = file_get_contents($defaultCSSFile);

	// Read custom CSS if available
	if (file_exists('css/custom-'.$file)){
		$customCSSFile =  'css/custom-'.$file;
		$cssContent .= file_get_contents($customCSSFile);
	}
	
	// Read CSS and substitute content
	$cssContent = preg_replace('/{?\$imageURL}?/',$imageURL, $cssContent);
	
	echo $cssContent;
}
?>
