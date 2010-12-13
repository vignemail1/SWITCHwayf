<?php // Copyright (c) 2010, SWITCH - Serving Swiss Universities

// WAYF Identity Provider Configuration file

// Find below some example entries of Identity Providers, categories and 
// cascaded WAYFs
// The keys of $IDProviders must correspond to the entityId of the 
// Identity Providers or a unique value in case of a cascaded WAYF/DS or 
// a category. In the case of a category, the key must correspond to the the 
// Type value of Identity Provider entries.
// The sequence of IdPs and SPs play a role. No sorting is done.

// A general entry for an IdP can consist of the form:
// Type:   [Optional]    Type of the entry. Default type will 
//                       be 'unknown' if not specified.
//                       Categories should have the type 'category'
//                       An entry for a cascaded WAYF that the user shall be
//                        redirected to should have the type 'wayf'
// Name:   [Mandatory]   Default name to display in drop-down list
// [en|it|fr||de|pt][Name]: [Optional] Display name in other languages
// SSO:    [Mandatory]   Should be the SAML1 SSO endpoint of the IdP
// Realm:  [Optional]    Kerberos Realm
// IP[]:   [Optional]    IP ranges of that organizations that can be used to guess
//                       a user's Identity Provider
// Index:  [Optional]    An alphanumerical value that is used for sorting 
//                       categories and Identity Provider in ascending order 
//                       if the Identity Providers are parsed from metadata.
//                       This is only relevant if 
//                       $includeLocalConfEntries = true

// A category entry can be used to group multiple IdP entries into a optgroup
// The category entries should look like:
// Name:   [Mandatory]   Default name to display in drop-down list
// [en|it|fr||de|pt][Name]: [Optional] Display name in other languages
// Type:   'category'    Category type 
// As stated above, the sequence of entries is important. So, one is completely
// flexible when it comes to ordering the category and IdP entries.
// 

// Category
$IDProviders['university'] = array (
		'Type' => 'category',
		'Name' => 'Universities',
);


// Example of a Kerberos-enabled Identity Provider
$IDProviders['bristol.ac.uk'] = array (
		'Type' => 'university',
		'Name' => 'University of Bristol',
		'SSO' => 'https://sso.bris.ac.uk/sso/index.jsp',
		'Realm' => 'ADS.BRIS.AC.UK',
);

// Example with optional network blocks that can be used as an 
// additional IdP preselection hint
$IDProviders['aitta.funet.fi'] = array (
		'Type' => 'university',
		'Name' => 'Tampere University of Technology',
		'SSO' => 'https://idp.tut.fi/shibboleth-idp/SSO',
		'IP' => array ('193.166.2.0/24','130.233.0.0/16'),
);


// Category
$IDProviders['vho'] = array (
		'Type' => 'category',
		'Name' => 'Virtual Home Organizations',
);

// An example of a configuration with multiple network blocks and multiple languages 
$IDProviders['urn:mace:switch.ch:SWITCHaai:vho-switchaai.ch'] = array (
		'Type' => 'vho',
		'Name' => 'Virtual Home Organisation',
		'de' => array ('Name' => 'Virtuelle Home Organisation'),
		'fr' => array ('Name' => 'Home Organisation Virtuelle'),
		'it' => array ('Name' => 'Virtuale Home Organisation'),
		'IP' => array ('130.59.6.0/16','127.0.0.0/24'),
		'SSO' => 'https://aai.vho-switchaai.ch/shibboleth-idp/SSO',
);


// Example of a WAYF entry that would redirect the user to this cascaded WAYF
// For SAML2 authentication requests, you must set the type to 'wayf' so that
// The user is not returned back to the Service Provider but forwarded to this
// additional Discovery Service
$IDProviders['urn:mace:switch.ch:SWITCHaai:edugain.net'] = array (
		'SSO' => 'https://maclh.switch.ch/ShiBE-R/ShiBEWebSSORequester',
		'Name' => 'Login via eduGAIN (testing)',
		'Type' => 'wayf',
);

$IDProviders['urn:geant:edugain:component:be:switch:development.switch.ch'] = array (
		'Type' => 'other',
		'Name' => 'Login via eduGAIN (development)',
		'SSO' => 'https://maclh.switch.ch/ShiBE-H/WebSSORequestListener',
);

// Example of an IDP you want not to be displayed when IDPs are parsed from
// a metadata file and SAML2MetaOverLocalConf is set to false
//$IDProviders['urn:mace:switch.ch:SWITCHaai:invisibleidp'] = '-';


// Category
$IDProviders['other'] = array (
		'Type' => 'category',
		'Name' => 'Others',
		'de' => array ('Name' => 'Andere'),
		'fr' => array ('Name' => 'Autres'),
		'it' => array ('Name' => 'Altri'),
);


// Standard example with a Type that could be used to hide certain
// Identity Providers in the list of an embedded WAYF according to their type
$IDProviders['https://aai-logon.switch.ch/idp/shibboleth'] = array(
		'Type' => 'other',
		'Name' => 'SWITCH - Serving Swiss Universities',
		'SSO' => 'https://aai-logon.switch.ch/idp/profile/Shibboleth/SSO',
);

?>
