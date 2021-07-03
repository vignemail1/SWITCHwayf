<?php

// Copyright (c) 2019, SWITCH

/**
 * updateMetadata
 *
 * @return void
 */
function updateMetadata()
{
    global $metadataLockFile, $metadataIDPFile, $metadataSPFile;
    global $metadataFile, $defaultLanguage;
    global $SAML2MetaOverLocalConf, $includeLocalConfEntries;
    global $verbose, $IDProviders, $SProviders;

    // Open the metadata lock file.
    if (($lockFp = fopen($metadataLockFile, 'a+')) === false) {
        $errorMsg = 'Could not open lock file ' . $metadataLockFile;
        logError($errorMsg);
        return false;
    }

    // Check that $IDProviders exists
    if (!isset($IDProviders) or !is_array($IDProviders)) {
        $IDProviders = array();
    }

    if (!file_exists($metadataIDPFile) or filemtime($metadataFile) > filemtime($metadataIDPFile)) {
        // Get an exclusive lock to regenerate the IdP and SP files
        // from the metadata file.
        if (flock($lockFp, LOCK_EX) === false) {
            $errorMsg = 'Could not get exclusive lock on ' . $metadataLockFile;
            logError($errorMsg);
            fclose($lockFp);
            return false;
        }

        // parse metadata file
        list($metadataIDProviders, $metadataSProviders) = parseMetadata($metadataFile, $defaultLanguage);

        if ($metadataIDProviders != false && is_array($metadataIDProviders)) {
            dumpFile($metadataIDPFile, $metadataIDProviders, 'metadataIDProviders');
        }

        if ($metadataSProviders != false && is_array($metadataSProviders)) {
            dumpFile($metadataSPFile, $metadataSProviders, 'metadataSProviders');
        }

        // release the exclusive lock
        flock($lockFp, LOCK_UN);

        // Now merge IDPs from metadata and static file
        $IDProviders = mergeInfo($IDProviders, $metadataIDProviders, $SAML2MetaOverLocalConf, $includeLocalConfEntries);

        // There is no static file for SPs, use metadata only
        $SProviders = $metadataSProviders;
    } elseif (file_exists($metadataIDPFile)) {
        // Get a shared lock to read the IdP and SP files
        // generated from the metadata file.
        if (flock($lockFp, LOCK_SH) === false) {
            $errorMsg = 'Could not lock file ' . $metadataLockFile;
            logError($errorMsg);
            fclose($lockFp);
            return false;
        }

        // Read SP and IDP files generated with metadata
        require($metadataIDPFile);
        require($metadataSPFile);

        // Release the lock.
        flock($lockFp, LOCK_UN);

        // Now merge IDPs from metadata and static file
        $IDProviders = mergeInfo($IDProviders, $metadataIDProviders, $SAML2MetaOverLocalConf, $includeLocalConfEntries);

        // There is no static file for SPs, use metadata only
        $SProviders = $metadataSProviders;
    }

    // Close the metadata lock file.
    fclose($lockFp);
}

// Function parseMetadata, parses metadata file and returns Array($IdPs, SPs)  or
// Array(false, false) if error occurs while parsing metadata file
/**
 * parseMetadata
 *
 * @param  string $metadataFile
 * @param  string $defaultLanguage [unused variable]
 * @return (bool|array)[]
 */
function parseMetadata($metadataFile, $defaultLanguage)
{
    global $supportHideFromDiscoveryEntityCategory;

    if (!file_exists($metadataFile)) {
        $errorMsg = 'File ' . $metadataFile . " does not exist";
        if (isRunViaCLI()) {
            echo $errorMsg . "\n";
        } else {
            logError($errorMsg);
        }
        return array(false, false);
    }

    if (!is_readable($metadataFile)) {
        $errorMsg = 'File ' . $metadataFile . " cannot be read due to insufficient permissions";
        if (isRunViaCLI()) {
            echo $errorMsg . "\n";
        } else {
            logError($errorMsg);
        }
        return array(false, false);
    }

    $CurrentXMLReaderNode = new XMLReader();
    if (!$CurrentXMLReaderNode->open($metadataFile, null, LIBXML_PARSEHUGE | LIBXML_NOERROR | LIBXML_NOWARNING | 1)) {
        $errorMsg = 'Could not parse metadata file ' . $metadataFile;
        if (isRunViaCLI()) {
            echo $errorMsg . "\n";
        } else {
            logError($errorMsg);
        }
        return array(false, false);
    }

    // Go to first element and check it is named 'EntitiesDescriptor'
    // If not it's probably not a valid SAML metadata file
    $CurrentXMLReaderNode->read();

    // Often there are comments at the beginning of the metadata,
    // this will eat/skip a max #(3) of comments and then hit the
    // evaluation of the EntitiesDescriptor tweak if necessary
    $maxCommentCount = 3;
    $commentCount = 0;
    while ($CurrentXMLReaderNode->localName  == '#comment') {
        $CurrentXMLReaderNode->read();
        // Always have an way to punch out of a while loop & inform the user why
        if ($commentCount++ >= $maxCommentCount) {
            $errorMsg = 'This file has exceeded the max # comments of' . $maxCommentCount;
            $errorMsg .= ' XML comments before an EntityDescriptor. Are you sure this is a well formed Metadata file?';
            if (isRunViaCLI()) {
                echo $errorMsg . "\n";
            } else {
                logError($errorMsg);
            }
            return array(false, false);
        }
    }

    // If we arrive here, we have read the first node at very
    // least and if it's not a comment, it should be an
    // EntityDescriptor
    // If it WAS 1 or more comments, we will have eaten then up
    // and the NEXT read will have occurred
    // If this next read of the XML nodes is NOT an
    // EntitiesDescriptor, (note the plural), we should fail.
    // If the next read of the XML nodes IS an
    // EntitiesDescriptor, we should proceed stead of 'read again'
    if ($CurrentXMLReaderNode->localName  !== 'EntitiesDescriptor') {
        $errorMsg = 'Metadata file ' . $metadataFile . ' does not include a root node EntitiesDescriptor';
        if (isRunViaCLI()) {
            echo $errorMsg . "\n";
        } else {
            logError($errorMsg);
        }
        return array(false, false);
    }

    // Init variables
    $hiddenIdPs = 0;
    $metadataIDProviders = array();
    $metadataSProviders = array();

    // Process individual EntityDescriptors
    while ($CurrentXMLReaderNode->read()) {
        if ($CurrentXMLReaderNode->nodeType == XMLReader::ELEMENT && $CurrentXMLReaderNode->localName  === 'EntityDescriptor') {
            $entityID = $CurrentXMLReaderNode->getAttribute('entityID');
            $EntityDescriptorXML = $CurrentXMLReaderNode->readOuterXML();
            $EntityDescriptorDOM = new DOMDocument();
            $EntityDescriptorDOM->loadXML($EntityDescriptorXML);

            // Check role descriptors
            foreach ($EntityDescriptorDOM->documentElement->childNodes as $RoleDescriptor) {
                $nodeName = $RoleDescriptor->localName;
                switch ($nodeName) {
                    case 'IDPSSODescriptor':
                        $IDP = processIDPRoleDescriptor($RoleDescriptor);
                        if ($IDP) {
                            $metadataIDProviders[$entityID] = $IDP;
                        } else {
                            $hiddenIdPs++;
                        }
                        break;
                    case 'SPSSODescriptor':
                        $SP = processSPRoleDescriptor($RoleDescriptor);
                        if ($SP) {
                            $metadataSProviders[$entityID] = $SP;
                        } else {
                            $errorMsg = "Failed to load SP with entityID $entityID from metadata file $metadataFile";
                            if (isRunViaCLI()) {
                                echo $errorMsg . "\n";
                            } else {
                                logWarning($errorMsg);
                            }
                        }
                        break;
                    default:
                }
            }
        }
    }

    // log result when called by the web application
    if (!isRunViaCLI()) {
        $infoMsg = "Successfully parsed metadata file " . $metadataFile . " ";
        $infoMsg .= "(" . count($metadataIDProviders) . " IdPs, ";
        $infoMsg .= " " . count($metadataSProviders) . " SPs, ";
        $infoMsg .=  ($hiddenIdPs > 0) ? $hiddenIdPs . " IdPs are hidden)" : "no hidden IdPs)";
        logInfo($infoMsg);
    }

    return array($metadataIDProviders, $metadataSProviders);
}

// Processes an IDPRoleDescriptor XML node and returns an IDP entry or false if
// something went wrong
function processIDPRoleDescriptor($IDPRoleDescriptorNode)
{
    global $defaultLanguage, $supportHideFromDiscoveryEntityCategory, $filterEntityCategory;

    $IDP = array();
    $Profiles = array();

    // Skip Idp if it has the Hide-From-Discovery entity
    // category attribute
    if (!isset($supportHideFromDiscoveryEntityCategory) || $supportHideFromDiscoveryEntityCategory) {
        if (hasHideFromDiscoveryEntityCategory($IDPRoleDescriptorNode)) {
            return false;
        }
    }

    // Skip if IdPs should be filtered by entity category
    if (isset($filterEntityCategory) && $filterEntityCategory) {
        if (!hasSpecificEntityCategory($IDPRoleDescriptorNode, $filterEntityCategory)) {
            return false;
        }
    }

    // Get SSO URL
    $SSOServices = $IDPRoleDescriptorNode->getElementsByTagNameNS('urn:oasis:names:tc:SAML:2.0:metadata', 'SingleSignOnService');
    foreach ($SSOServices as $SSOService) {
        $Profiles[$SSOService->getAttribute('Binding')] = $SSOService->getAttribute('Location');
    }

    // Set SAML1 SSO URL
    if (isset($Profiles['urn:mace:shibboleth:1.0:profiles:AuthnRequest'])) {
        $IDP['SSO'] = $Profiles['urn:mace:shibboleth:1.0:profiles:AuthnRequest'];
    } else if (isset($Profiles['urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect'])) {
        $IDP['SSO'] = $Profiles['urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect'];
    } else {
        $IDP['SSO'] = 'https://no.saml1.or.saml2.sso.url.defined.com/error';
    }

    // First get MDUI name
    $MDUIDisplayNames = getMDUIDisplayNames($IDPRoleDescriptorNode);
    if (count($MDUIDisplayNames)) {
        $IDP['Name'] = current($MDUIDisplayNames);
    }
    foreach ($MDUIDisplayNames as $lang => $value) {
        $IDP[$lang]['Name'] = $value;
    }

    // Then try organization names
    if (empty($IDP['Name'])) {
        $OrgnizationNames = getOrganizationNames($IDPRoleDescriptorNode);
        $IDP['Name'] = current($OrgnizationNames);

        foreach ($OrgnizationNames as $lang => $value) {
            $IDP[$lang]['Name'] = $value;
        }
    }

    // As last resort, use entityID
    if (empty($IDP['Name'])) {
        $IDP['Name'] = $IDPRoleDescriptorNode->parentNode->getAttribute('entityID');
    }

    // Set default name
    if (isset($IDP[$defaultLanguage])) {
        $IDP['Name'] = $IDP[$defaultLanguage]['Name'];
    } elseif (isset($IDP['en'])) {
        $IDP['Name'] = $IDP['en']['Name'];
    }

    // Get supported protocols
    $protocols = $IDPRoleDescriptorNode->getAttribute('protocolSupportEnumeration');
    $IDP['Protocols'] = $protocols;

    // Get keywords
    $MDUIKeywords = getMDUIKeywords($IDPRoleDescriptorNode);
    foreach ($MDUIKeywords as $lang => $keywords) {
        $IDP[$lang]['Keywords'] = $keywords;
    }

    // Ensure there is a keyword entry for default language
    if (!isset($IDP[$defaultLanguage]['Keywords'])) {
        $IDP[$defaultLanguage]['Keywords'] = '';
    }

    // Also add entityID as keyword
    $IDP[$defaultLanguage]['Keywords'] .= $IDPRoleDescriptorNode->parentNode->getAttribute('entityID');

    // Get scopes
    $ShibScopes = getShibScopes($IDPRoleDescriptorNode);

    // Get domain hints
    $MDUIDomainHints = getMDUIDomainHints($IDPRoleDescriptorNode);

    // Add unique domains as keywords as well
    $IDP[$defaultLanguage]['Keywords'] .= ' ' . implode(' ', array_unique(array_merge($ShibScopes, $MDUIDomainHints)));

    // Get Logos
    $MDUILogos = getMDUILogos($IDPRoleDescriptorNode);
    foreach ($MDUILogos as $Logo) {
        // Skip non-favicon logos
        if ($Logo['Height'] != 16 || $Logo['Width'] != 16) {
            continue;
        }

        // Strip height and width
        unset($Logo['Height']);
        unset($Logo['Width']);

        if ($Logo['Lang'] == '') {
            unset($Logo['Lang']);
            $IDP['Logo'] = $Logo;
        } else {
            $lang = $Logo['Lang'];
            unset($Logo['Lang']);
            $IDP[$lang]['Logo'] = $Logo;
        }
    }

    // Get AttributeValue
    $SAMLAttributeValues = getSAMLAttributeValues($IDPRoleDescriptorNode);
    if ($SAMLAttributeValues) {
        $IDP['AttributeValue'] = $SAMLAttributeValues;
    }

    // Get IPHints
    $MDUIIPHints = getMDUIIPHints($IDPRoleDescriptorNode);
    if ($MDUIIPHints) {
        $IDP['IPHint'] = $MDUIIPHints;
    }

    // Get DomainHints
    if ($MDUIDomainHints) {
        $IDP['DomainHint'] = $MDUIDomainHints;
    }

    // Get GeolocationHints
    $MDUIGeolocationHints = getMDUIGeolocationHints($IDPRoleDescriptorNode);
    if ($MDUIGeolocationHints) {
        $IDP['GeolocationHint'] = $MDUIGeolocationHints;
    }

    return $IDP;
}

/**
 * Processes an SPRoleDescriptor XML node and returns an SP entry or false if
 * something went wrong
 *
 * @param  mixed $SPRoleDescriptorNode
 * @return array|bool
 */
function processSPRoleDescriptor($SPRoleDescriptorNode)
{
    global $defaultLanguage;

    $SP = array();

    // Get <idpdisc:DiscoveryResponse> extensions
    $DResponses = $SPRoleDescriptorNode->getElementsByTagNameNS('urn:oasis:names:tc:SAML:profiles:SSO:idp-discovery-protocol', 'DiscoveryResponse');
    foreach ($DResponses as $DResponse) {
        if ($DResponse->getAttribute('Binding') == 'urn:oasis:names:tc:SAML:profiles:SSO:idp-discovery-protocol') {
            $SP['DSURL'][] =  $DResponse->getAttribute('Location');
        }
    }

    // First get MDUI name
    $MDUIDisplayNames = getMDUIDisplayNames($SPRoleDescriptorNode);
    if (count($MDUIDisplayNames)) {
        $SP['Name'] = current($MDUIDisplayNames);
    }
    foreach ($MDUIDisplayNames as $lang => $value) {
        $SP[$lang]['Name'] = $value;
    }

    // Then try attribute consuming service
    if (empty($SP['Name'])) {
        $ConsumingServiceNames = getAttributeConsumingServiceNames($SPRoleDescriptorNode);
        $SP['Name'] = current($ConsumingServiceNames);

        foreach ($ConsumingServiceNames as $lang => $value) {
            $SP[$lang]['Name'] = $value;
        }
    }

    // As last resort, use entityID
    if (empty($SP['Name'])) {
        $SP['Name'] = $SPRoleDescriptorNode->parentNode->getAttribute('entityID');
    }

    // Set default name
    if (isset($SP[$defaultLanguage])) {
        $SP['Name'] = $SP[$defaultLanguage]['Name'];
    } elseif (isset($SP['en'])) {
        $SP['Name'] = $SP['en']['Name'];
    }

    // Get Assertion Consumer Services and store their hostnames
    $ACServices = $SPRoleDescriptorNode->getElementsByTagNameNS('urn:oasis:names:tc:SAML:2.0:metadata', 'AssertionConsumerService');
    foreach ($ACServices as $ACService) {
        $SP['ACURL'][] =  $ACService->getAttribute('Location');
    }

    // Get supported protocols
    $protocols = $SPRoleDescriptorNode->getAttribute('protocolSupportEnumeration');
    $SP['Protocols'] = $protocols;

    // Get keywords
    $MDUIKeywords = getMDUIKeywords($SPRoleDescriptorNode);
    foreach ($MDUIKeywords as $lang => $keywords) {
        $SP[$lang]['Keywords'] = $keywords;
    }

    return $SP;
}

/**
 * Dump variable to a file
 *
 * @param  string $dumpFile
 * @param  mixed $providers
 * @param  mixed $variableName
 * @return void
 */
function dumpFile($dumpFile, $providers, $variableName)
{

    if (($fp = fopen($dumpFile, 'w')) !== false) {
        fwrite($fp, "<?php\n\n");
        fwrite($fp, "// This file was automatically generated by readMetadata.php\n");
        fwrite($fp, "// Don't edit!\n\n");

        fwrite($fp, '$' . $variableName . ' = ');
        fwrite($fp, var_export($providers, true));

        fwrite($fp, "\n?>");

        fclose($fp);
    } else {
        $errorMsg = 'Could not open file ' . $dumpFile . ' for writing';
        if (isRunViaCLI()) {
            echo $errorMsg . "\n";
        } else {
            logInfo($errorMsg);
        }
    }
}

/**
 * Function mergeInfo is used to create the effective $IDProviders array.
 * For each IDP found in the metadata, merge the values from IDProvider.conf.php.
 * If an IDP is found in IDProvider.conf as well as in metadata, use metadata
 * information if $SAML2MetaOverLocalConf is true or else use IDProvider.conf data
 *
 * @param  array $IDProviders
 * @param  array $metadataIDProviders
 * @param  bool $SAML2MetaOverLocalConf
 * @param  bool $includeLocalConfEntries
 * @return array
 */
function mergeInfo($IDProviders, $metadataIDProviders, $SAML2MetaOverLocalConf, $includeLocalConfEntries)
{

    // If $includeLocalConfEntries parameter is set to true, mergeInfo() will also consider IDPs
    // not listed in metadataIDProviders but defined in IDProviders file
    // This is required if you need to add local exceptions over the federation metadata
    $allIDPS = $metadataIDProviders;
    $mergedArray = array();
    if ($includeLocalConfEntries) {
        $allIDPS = array_merge($metadataIDProviders, $IDProviders);
    }

    foreach ($allIDPS as $allIDPsKey => $allIDPsEntry) {
        if (isset($IDProviders[$allIDPsKey])) {
            // Entry exists also in local IDProviders.conf.php
            if (isset($metadataIDProviders[$allIDPsKey]) && is_array($metadataIDProviders[$allIDPsKey])) {
                // Remove IdP if there is a removal rule in local IDProviders.conf.php
                if (!is_array($IDProviders[$allIDPsKey])) {
                    unset($metadataIDProviders[$allIDPsKey]);
                    continue;
                }

                // Entry exists in both IDProviders sources and is an array
                if ($SAML2MetaOverLocalConf) {
                    // Metadata entry overwrite local conf
                    $mergedArray[$allIDPsKey] = array_merge($IDProviders[$allIDPsKey], $metadataIDProviders[$allIDPsKey]);
                } else {
                    // Local conf overwrites metadata entry
                    $mergedArray[$allIDPsKey] = array_merge($metadataIDProviders[$allIDPsKey], $IDProviders[$allIDPsKey]);
                }
            } else {
                // Entry only exists in local IDProviders file
                $mergedArray[$allIDPsKey] = $IDProviders[$allIDPsKey];
            }
        } else {
            // Entry doesn't exist in in local IDProviders.conf.php
            $mergedArray[$allIDPsKey] = $metadataIDProviders[$allIDPsKey];
        }
    }

    return $mergedArray;
}

/**
 * Get MD Display Names from RoleDescriptor
 *
 * @param  mixed $RoleDescriptorNode
 * @return array
 */
function getMDUIDisplayNames($RoleDescriptorNode)
{
    $Entity = array();

    $MDUIDisplayNames = $RoleDescriptorNode->getElementsByTagNameNS('urn:oasis:names:tc:SAML:metadata:ui', 'DisplayName');
    foreach ($MDUIDisplayNames as $MDUIDisplayName) {
        $lang = $MDUIDisplayName->getAttributeNodeNS('http://www.w3.org/XML/1998/namespace', 'lang')->nodeValue;
        $Entity[$lang] = trimToSingleLine($MDUIDisplayName->nodeValue);
    }

    return $Entity;
}

/**
 * Get MD Keywords from RoleDescriptor
 *
 * @param  mixed $RoleDescriptorNode
 * @return array
 */
function getMDUIKeywords($RoleDescriptorNode)
{
    $Entity = array();

    $MDUIKeywords = $RoleDescriptorNode->getElementsByTagNameNS('urn:oasis:names:tc:SAML:metadata:ui', 'Keywords');
    foreach ($MDUIKeywords as $MDUIKeywordEntry) {
        $lang = $MDUIKeywordEntry->getAttributeNodeNS('http://www.w3.org/XML/1998/namespace', 'lang')->nodeValue;
        $keywordEntry = $MDUIKeywordEntry->nodeValue;
        $keywordEntry = preg_replace('/\+/', ' ', $keywordEntry);
        $Entity[$lang] = trimToSingleLine($keywordEntry);
    }

    return $Entity;
}

/**
 * Get Shib Scopes from RoleDescriptor
 *
 * @param  mixed $RoleDescriptorNode
 * @return array
 */
function getShibScopes($RoleDescriptorNode)
{
    $Scopes = array();

    $ShibScopes = $RoleDescriptorNode->getElementsByTagNameNS('urn:mace:shibboleth:metadata:1.0', 'Scope');
    foreach ($ShibScopes as $ShibScopeEntry) {
        // Ignore regular expression scopes
        if ($ShibScopeEntry->getAttribute('regexp') && $ShibScopeEntry->getAttribute('regexp') == 'true') {
            continue;
        }

        $Scopes[] = trim($ShibScopeEntry->nodeValue);
    }

    return $Scopes;
}

/**
 * Get MD Logos from RoleDescriptor. Prefer the favicon logos
 *
 * @param  mixed $RoleDescriptorNode
 * @return array
 */
function getMDUILogos($RoleDescriptorNode)
{
    $Logos = array();
    $MDUILogos = $RoleDescriptorNode->getElementsByTagNameNS('urn:oasis:names:tc:SAML:metadata:ui', 'Logo');
    foreach ($MDUILogos as $MDUILogoEntry) {
        $Logo = array();
        $Logo['URL'] = trimToSingleLine($MDUILogoEntry->nodeValue);
        $Logo['Height'] = ($MDUILogoEntry->getAttribute('height') != '') ? trimToSingleLine($MDUILogoEntry->getAttribute('height')) : '16';
        $Logo['Width'] = ($MDUILogoEntry->getAttribute('width') != '') ? trimToSingleLine($MDUILogoEntry->getAttribute('width')) : '16';
        $Logo['Lang'] = ($MDUILogoEntry->getAttribute('lang') != '') ? trimToSingleLine($MDUILogoEntry->getAttribute('lang')) : '';
        $Logos[] = $Logo;
    }

    return $Logos;
}

/**
 * Get MD Attribute Value(kind) from RoleDescriptor
 *
 * @param  mixed $RoleDescriptorNode
 * @return array
 */
function getSAMLAttributeValues($RoleDescriptorNode)
{
    $Entity = array();

    $SAMLAttributeValues = $RoleDescriptorNode->getElementsByTagNameNS('urn:oasis:names:tc:SAML:2.0:assertion', 'AttributeValue');
    foreach ($SAMLAttributeValues as $SAMLAttributeValuesEntry) {
        $Entity[] = trimToSingleLine($SAMLAttributeValuesEntry->nodeValue);
    }

    return $Entity;
}

/**
 * Get MD IP Address Hints from RoleDescriptor
 *
 * @param  mixed $RoleDescriptorNode
 * @return array
 */
function getMDUIIPHints($RoleDescriptorNode)
{
    $Entity = array();

    $MDUIIPHints = $RoleDescriptorNode->getElementsByTagNameNS('urn:oasis:names:tc:SAML:metadata:ui', 'IPHint');
    foreach ($MDUIIPHints as $MDUIIPHintEntry) {
        if (preg_match("/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\/[0-9]{1,2}$/", trimToSingleLine($MDUIIPHintEntry->nodeValue), $splitIP)) {
            $Entity[] = trimToSingleLine($splitIP[0]);
        } elseif (preg_match("/^.*\:.*\/[0-9]{1,2}$/", trimToSingleLine($MDUIIPHintEntry->nodeValue), $splitIP)) {
            $Entity[] = trimToSingleLine($splitIP[0]);
        }
    }

    return $Entity;
}

/**
 * Get MD Domain Hints from RoleDescriptor
 *
 * @param  mixed $RoleDescriptorNode
 * @return array
 */
function getMDUIDomainHints($RoleDescriptorNode)
{
    $Entity = array();

    $MDUIDomainHints = $RoleDescriptorNode->getElementsByTagNameNS('urn:oasis:names:tc:SAML:metadata:ui', 'DomainHint');
    foreach ($MDUIDomainHints as $MDUIDomainHintEntry) {
        $Entity[] = trimToSingleLine($MDUIDomainHintEntry->nodeValue);
    }

    return $Entity;
}

/**
 * Get MD Geolocation Hints from RoleDescriptor
 *
 * @param  mixed $RoleDescriptorNode
 * @return array
 */
function getMDUIGeolocationHints($RoleDescriptorNode)
{
    $Entity = array();

    $MDUIGeolocationHints = $RoleDescriptorNode->getElementsByTagNameNS('urn:oasis:names:tc:SAML:metadata:ui', 'GeolocationHint');
    foreach ($MDUIGeolocationHints as $MDUIGeolocationHintEntry) {
        if (preg_match("/^geo:([0-9]+\.{0,1}[0-9]*,[0-9]+\.{0,1}[0-9]*)$/", trimToSingleLine($MDUIGeolocationHintEntry->nodeValue), $splitGeo)) {
            $Entity[] = trimToSingleLine($splitGeo[1]);
        }
    }

    return $Entity;
}

/**
 * Get Organization Names from RoleDescriptor
 *
 * @param  mixed $RoleDescriptorNode
 * @return array
 */
function getOrganizationNames($RoleDescriptorNode)
{
    $Entity = array();

    $Organization = $RoleDescriptorNode->parentNode->getElementsByTagNameNS('urn:oasis:names:tc:SAML:2.0:metadata', 'Organization')->item(0);
    if ($Organization) {
        $DisplayNames = $Organization->getElementsByTagNameNS('urn:oasis:names:tc:SAML:2.0:metadata', 'OrganizationDisplayName');
        foreach ($DisplayNames as $DisplayName) {
            $lang = $DisplayName->getAttributeNodeNS('http://www.w3.org/XML/1998/namespace', 'lang')->nodeValue;
            $Entity[$lang] = trimToSingleLine($DisplayName->nodeValue);
        }
    }

    return $Entity;
}

/**
 * Get Attribute Consuming Service
 *
 * @param  mixed $RoleDescriptorNode
 * @return array
 */
function getAttributeConsumingServiceNames($RoleDescriptorNode)
{
    $Entity = array();

    $ServiceNames = $RoleDescriptorNode->getElementsByTagNameNS('urn:oasis:names:tc:SAML:2.0:metadata', 'ServiceName');
    foreach ($ServiceNames as $ServiceName) {
        $lang = $ServiceName->getAttributeNodeNS('http://www.w3.org/XML/1998/namespace', 'lang')->nodeValue;
        $Entity[$lang] = trimToSingleLine($ServiceName->nodeValue);
    }

    return $Entity;
}

/**
 * Returns true if IdP has Hide-From-Discovery entity category attribute
 *
 * @param  mixed $IDPRoleDescriptorNode
 * @return bool
 */
function hasHideFromDiscoveryEntityCategory($IDPRoleDescriptorNode)
{
    // Get SAML Attributes for this entity
    $AttributeValues = $IDPRoleDescriptorNode->parentNode->getElementsByTagNameNS('urn:oasis:names:tc:SAML:2.0:assertion', 'AttributeValue');

    if (!$AttributeValues || $AttributeValues->length < 1) {
        return false;
    }

    foreach ($AttributeValues as $AttributeValue) {
        if (trim($AttributeValue->nodeValue) == 'http://refeds.org/category/hide-from-discovery') {
            return true;
        }
    }

    return false;
}

/**
 * Returns true if IdP has specific entity category attribute
 *
 * @param  mixed $IDPRoleDescriptorNode
 * @param  string $filterEntityCategory
 * @return bool
 */
function hasSpecificEntityCategory($IDPRoleDescriptorNode, $filterEntityCategory)
{
    // Get SAML Attributes for this entity
    $AttributeValues = $IDPRoleDescriptorNode->parentNode->getElementsByTagNameNS('urn:oasis:names:tc:SAML:2.0:assertion', 'AttributeValue');

    if (!$AttributeValues || $AttributeValues->length < 1) {
        return false;
    }

    $entityCategories = explode(' ', $filterEntityCategory);
    foreach ($AttributeValues as $AttributeValue) {
        $thisCategory = trim($AttributeValue->nodeValue);
        if (in_array($thisCategory, $entityCategories)) {
            return true;
        }
    }

    return false;
}
