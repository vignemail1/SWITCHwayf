<?php // Copyright (c) 2011, SWITCH - Serving Swiss Universities

// WAYF Identity Provider Configuration file

// Find below some example entries of Identity Providers, categories and 
// cascaded WAYFs
// The keys of $IDProviders must correspond to the entityId of the 
// Identity Providers or a unique value in case of a cascaded WAYF/DS or 
// a category. In the case of a category, the key must correspond to the the 
// Type value of Identity Provider entries.
// The sequence of IdPs and SPs play a role. No sorting is done.
// 
// Please read the file DOC for information on the format of the entries

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
		'en' => array ('Keywords' => 'Bristol South+West+England'),
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
		'en' => array (
			'Name' => 'Virtual Home Organisation',
			'Keywords','Zurich Switzerland',
			),
		'de' => array (
			'Name' => 'Virtuelle Home Organisation',
			'Keywords','Zürich Schweiz',
			),
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
