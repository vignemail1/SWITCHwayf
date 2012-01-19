<?php // Copyright (c) 2011, SWITCH - Serving Swiss Universities

//******************************************************************************
// This file contains the WAYF/DS configuration. Adapt the settings to reflect
// your environment and then do some testing before deploying the WAYF.
//******************************************************************************

// 1. Language settings
//*********************
$defaultLanguage = 'en'; 

// 2. Cookie settings
//*******************

// Domain within the WAYF cookei shall be readable. Must start with a .
$commonDomain = '.switch.ch';

// Optionnal cookie name prefix in case you run several 
// instances of the WAYF in the same domain. 
// Example: $cookieNamePrefix = '_mywayf';
$cookieNamePrefix = '';

// Names of the cookies where to store the settings to temporarily
// redirect users transparently to their last selected IdP
$redirectCookieName = $cookieNamePrefix.'_redirect_user_idp';
$redirectStateCookieName = $cookieNamePrefix.'_redirection_state';

// Stores last selected IdPs 
// This value shouldn't be changed because _saml_idp is the officilly
// defined name in the SAML specification
$SAMLDomainCookieName = $cookieNamePrefix.'_saml_idp';

// Stores last selected SP
// This value can be choosen as you like because it is something specific
// to this WAYF implementation. It can be used to display help/contact 
// information on a page in the same domain as $commonDomain by accessing
// the federation metadata and parsing out the contact information of the 
// selected IdP and SP using $SAMLDomainCookieName and $SPCookieName
$SPCookieName = $cookieNamePrefix.'_saml_sp';

// If enabled cookies are set/transmitted only via https connections
// and the http only option is set to prevent javascripts from reading the
// cookies
$cookieSecurity = false;

// Number of days longterm cookies shall be valid
$cookieValidity = 100;

// 3. Features and extensions
//***************************

// Whether to show the checkbox to permanently remember a setting
$showPermanentSetting = false;

// Whether or not to use the search-as-you-type feature of the drop down list
$userImprovedDropDownList = true;

// Set to true in order to enable reading the Identity Provider from a SAML2 
// metadata file defined below in $metadataFile
$useSAML2Metadata = true; 

  // If true parsed metadata shall have precedence if there are entries defined 
  // in metadata as well as the local IDProviders configuration file.
  // Requires $useSAML2Metadata to be true
  $SAML2MetaOverLocalConf = false;

  // If includeLocalConfEntries parameter is set to true, Identity Providers
  // not listed in metadata but defined in the local IDProviders file will also
  // be displayed in the drop down list. This is required if you need to add 
  // local exceptions over the federation metadata
  // Requires $useSAML2Metadata to be true
  $includeLocalConfEntries = true;

  // Whether the return parameter is checked against SAML2 metadata or not
  // The Discovery Service specification says the DS SHOULD check this in order
  // to mitigate phising problems.
  // The return parameter will only be checked if the Service Provider's metadata 
  // contains an <idpdisc:DiscoveryResponse> or if the assertion consumer url 
  // check below is enabled
  // Requires $useSAML2Metadata to be true
  $enableDSReturnParamCheck = true;

    // If true, the return parameter is checked for Service Providers that
    // don't have and <idpdisc:DiscoveryResponse> extension set. Instead of this
    // extension, the hostnames of the assertion consumer URLs are used to check 
    // the return parameter against. 
    // This feature is useful in case the Service Provider's metadata doesn't contain 
    // a <idpdisc:DiscoveryResponse> extension. It increases security for Service 
    // Provider's that don't have an <idpdisc:DiscoveryResponse> extensions.
    // Requires $useSAML2Metadata and $enableDSReturnParamCheck to be true
    $useACURLsForReturnParamCheck = false;

// Whether to turn on Kerberos support for Identity Provider preselection
$useKerberos = false;

// If enabled, the user's IP is used for a reverse DNS lookup whose resulting 
// domain name then is matched with the URN values of the Identity Providers
$useReverseDNSLookup = false;

// Whether the JavaScript required for embedding the WAYF
// on a remote site shall be generated or not
// Lowers security against phising!
// If this value is set to true, any web page in the world can 
// (with some efforts) find out with a high probability from which 
// organization a user is from. This could be misused for phishing attacks. 
// Therefore, only enable this feature if you know what you are doing!
$useEmbeddedWAYF = false;

  // If enabled the Embedded WAYF will prevent releasing information
  // about the user's preselected Identity Provider 
  // While this is benefical to the data protection of the user, it will also
  // prevent preselecting the user's Identity Provider. Thus, users will have
  // to preselect their IdP each and every time
  // Requires $useEmbeddedWAYF to be true
  $useEmbeddedWAYFPrivacyProtection = false;

  // If enabled, the referer hostname of the request must match tan assertion 
  // consumer URL or a discovery URL of a Service Provider in $metadataSPFile
  // in order to let the Embedded WAYF preselect an Identity Provider.
  // Therefore, this option is a good compromise between data protection and
  // userfriendlyness.
  // Requires $useSAML2Metadata to be true and $useEmbeddedWAYFPrivacyProtection
  // to be false
  $useEmbeddedWAYFRefererForPrivacyProtection = false;

// Whether or not to add the entityID of the preselected IdP to the
// exported JSON/Text/PHP Code
// Lowers security against phising!
// If this value is set to true, any web page
// in the world can easily find out with a high probability from which 
// organization a user is from. This could be misused for phishing attacks. 
// Therefore, only enable this feature if you know what you are doing!
$exportPreselectedIdP = false;

// Whether to enable logging of WAYF/DS requests
// If turned on make sure to also configure $WAYFLogFile
$useLogging = true; 


// 4. Appearance settings
//**************************

// Name of the federation
$federationName = 'SWITCHaai Federation';

// URL to send user to when clicking on federation logo
$federationURL = 'http://www.switch.ch/aai/';

// Use an absolute URL in case you want to use the embedded WAYF
$imageURL = 'https://'.$_SERVER['SERVER_NAME'].dirname($_SERVER['SCRIPT_NAME']).'/images';

// Absolute URL to point to css directory
$cssURL = 'https://'.$_SERVER['SERVER_NAME'].dirname($_SERVER['SCRIPT_NAME']).'/css';

// Absolute URL to point to javascript directory
$javascriptURL = 'https://'.$_SERVER['SERVER_NAME'].dirname($_SERVER['SCRIPT_NAME']).'/js';

// Absolute URL to the logo that shall be displayed in the Embedded WAYF
$logoURL = $imageURL.'/switch-aai-transparent.png'; 

// Absolute URL to the small logo that shall be displayed in the 
// embedded WAYF if dimensions must be small
$smallLogoURL = $imageURL.'/switch-aai-transparent-small.png';


// 5. Files and path settings
//***************************

// Set both config files to the same value if you don't want to use the 
// the WAYF to read a (potential) automatically generated file that undergoes
// some plausability checks before being used
$IDPConfigFile = 'IDProvider.conf.php';
$backupIDPConfigFile = 'IDProvider.conf.php';

// Use $metadataFile as source federation's metadata.
$metadataFile = '/etc/shibboleth/metadata.switchaai.xml';

// File to store the parsed IdP list
// Will be updated automatically if the metadataFile modification time
// is more recent than this file's
// The user running the script must have permission to create $metadataIdpFile
$metadataIDPFile = 'IDProvider.metadata.php';

// File to store the parsed SP list.
// Will be updated automatically if the metadataFile modification time
// is more recent than this file's
// The user running the script must have permission to create $metadataIdpFile
$metadataSPFile = 'SProvider.metadata.php';

// File to use as the lock file for writing the parsed IdP and SP lists.
// The user running the script must have permission to write $metadataLockFile
$metadataLockFile = '/tmp/wayf_metadata.lock';

// Where to log the access
// Make sure the web server user has write access to this file!
$WAYFLogFile = '/var/log/apache2/wayf.log'; 


// 6. Other settings
//******************

// A Kerboros-protected soft link back to this script!
$kerberosRedirectURL = '/SWITCHaai/kerberosRedirect.php';


// Development mode settings
//**************************
// If the development mode is activated, PHP errors and warnings will be displayed
$developmentMode = false;

?>
