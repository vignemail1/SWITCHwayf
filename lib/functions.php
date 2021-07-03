<?php

// Copyright (c) 2019, SWITCH

/*
******************************************************************************
This file contains common functions of the SWITCHwayf
******************************************************************************
*/

/**
 * Initializes default configuration options if they were not set already
 *
 * @return void
 */
function initConfigOptions()
{
    global $defaultLanguage;
    global $commonDomain;
    global $cookieNamePrefix;
    global $redirectCookieName;
    global $redirectStateCookieName;
    global $SAMLDomainCookieName;
    global $SPCookieName;
    global $cookieSecurity;
    global $cookieValidity;
    global $showPermanentSetting;
    global $useImprovedDropDownList;
    global $disableRemoteLogos;
    global $useSAML2Metadata;
    global $SAML2MetaOverLocalConf;
    global $includeLocalConfEntries;
    global $enableDSReturnParamCheck;
    global $useACURLsForReturnParamCheck;
    global $useKerberos;
    global $useReverseDNSLookup;
    global $useEmbeddedWAYF;
    global $useEmbeddedWAYFPrivacyProtection;
    global $useEmbeddedWAYFRefererForPrivacyProtection;
    global $useLogging;
    global $exportPreselectedIdP;
    global $federationName;
    global $supportContactEmail;
    global $federationURL;
    global $organizationURL;
    global $faqURL;
    global $helpURL;
    global $privacyURL;
    global $imageURL;
    global $javascriptURL;
    global $cssURL;
    global $logoURL;
    global $smallLogoURL;
    global $organizationLogoURL;
    global $customStrings;
    global $IDPConfigFile;
    global $backupIDPConfigFile;
    global $metadataFile;
    global $metadataIDPFile;
    global $metadataSPFile;
    global $metadataLockFile;
    global $WAYFLogFile;
    global $kerberosRedirectURL;
    global $instanceIdentifier;
    global $developmentMode;
    global $topLevelDir;
    global $useSelect2;
    global $select2PageSize;
    global $allowedCORSDomain;


    // Set independent default configuration options
    $defaults = array();
    $defaults['instanceIdentifier'] = 'SWITCHwayf';
    $defaults['defaultLanguage'] = 'en';
    $defaults['commonDomain'] = getTopLevelDomain($_SERVER['SERVER_NAME']);
    $defaults['cookieNamePrefix'] = '';
    $defaults['cookieSecurity'] = false;
    $defaults['cookieValidity'] = 100;
    $defaults['showPermanentSetting'] = false;
    $defaults['useImprovedDropDownList'] = true;
    $defaults['useSelect2'] = false;
    $defaults['select2PageSize'] = 100;
    $defaults['allowedCORSDomain'] = '*';
    $defaults['disableRemoteLogos'] = false;
    $defaults['useSAML2Metadata'] = false;
    $defaults['SAML2MetaOverLocalConf'] = false;
    $defaults['includeLocalConfEntries'] = true;
    $defaults['enableDSReturnParamCheck'] = true;
    $defaults['useACURLsForReturnParamCheck'] = false;
    $defaults['useKerberos'] = false;
    $defaults['useReverseDNSLookup'] = false;
    $defaults['useEmbeddedWAYF'] = false;
    $defaults['useEmbeddedWAYFPrivacyProtection'] = false;
    $defaults['useEmbeddedWAYFRefererForPrivacyProtection'] = false;
    $defaults['useLogging'] = true;
    $defaults['exportPreselectedIdP'] = false;
    $defaults['federationName'] = 'Identity Federation';
    $defaults['organizationURL'] = 'http://www.' . $defaults['commonDomain'];
    $defaults['federationURL'] = $defaults['organizationURL'] . '/aai';
    $defaults['faqURL'] = $defaults['federationURL'] . '/faq';
    $defaults['helpURL'] = $defaults['federationURL'] . '/help';
    $defaults['privacyURL'] = $defaults['federationURL'] . '/privacy';
    $defaults['supportContactEmail'] = 'support-contact@' . $defaults['commonDomain'];
    $defaults['imageURL'] = 'https://' . $_SERVER['SERVER_NAME'] . dirname($_SERVER['SCRIPT_NAME']) . '/images';
    $defaults['javascriptURL'] = 'https://' . $_SERVER['SERVER_NAME'] . dirname($_SERVER['SCRIPT_NAME']) . '/js';
    $defaults['cssURL'] = 'https://' . $_SERVER['SERVER_NAME'] . dirname($_SERVER['SCRIPT_NAME']) . '/css';
    $defaults['IDPConfigFile'] = 'IDProvider.conf.php';
    $defaults['backupIDPConfigFile'] = 'IDProvider.conf.php';
    $defaults['metadataFile'] = '/etc/shibboleth/metadata.switchaai.xml';
    $defaults['metadataIDPFile'] = 'IDProvider.metadata.php';
    $defaults['metadataSPFile'] = 'SProvider.metadata.php';
    $lockFileName = preg_replace('/[^-_\.a-zA-Z]/', '', $defaults['instanceIdentifier']);
    $defaults['metadataLockFile'] = (substr($_SERVER['PATH'], 0, 1) == '/') ? '/tmp/wayf_metadata-' . $lockFileName . '.lock' : 'C:\windows\TEMP\wayf_metadata-' . $lockFileName . '.lock';
    $defaults['WAYFLogFile'] = '/var/log/apache2/wayf.log';
    $defaults['kerberosRedirectURL'] = dirname($_SERVER['SCRIPT_NAME']) . 'kerberosRedirect.php';
    $defaults['developmentMode'] = false;
    $defaults['customStrings'] = array();

    // Initialize independent defaults
    foreach ($defaults as $key => $value) {
        if (!isset($$key)) {
            $$key = $value;
        }
    }

    // Set dependent default configuration options
    $defaults = array();
    $defaults['redirectCookieName'] = $cookieNamePrefix . '_redirect_user_idp';
    $defaults['redirectStateCookieName'] = $cookieNamePrefix . '_redirection_state';
    $defaults['SAMLDomainCookieName'] = $cookieNamePrefix . '_saml_idp';
    $defaults['SPCookieName'] = $cookieNamePrefix . '_saml_sp';
    $defaults['logoURL'] = $imageURL . '/federation-logo.png';
    $defaults['smallLogoURL'] = $imageURL . '/small-federation-logo.png';
    $defaults['organizationLogoURL'] = $imageURL . '/organization-logo.png';

    // Initialize dependent defaults
    foreach ($defaults as $key => $value) {
        if (!isset($$key)) {
            $$key = $value;
        }
    }

    // Turn relatives paths into absolute ones
    $files = array(
        'IDPConfigFile', 'backupIDPConfigFile', 'metadataFile',
        'metadataIDPFile', 'metadataSPFile', 'metadataLockFile'
    );
    foreach ($files as $file) {
        if (substr($$file, 0, 1) != '/') {
            $$file = $topLevelDir . '/etc/' . $$file;
        }
    }
}

/**
 * Generates an array of IDPs using the cookie value
 *
 * @param  string $value
 * @return array
 */
function getIdPArrayFromValue($value)
{
    // Decodes and splits cookie value
    $CookieArray = preg_split('/ /', $value);
    $CookieArray = array_map('base64_decode', $CookieArray);

    return $CookieArray;
}

/**
 * Generate the value that is stored in the cookie using the list of IDPs
 *
 * @param  array $CookieArray
 * @return string
 */
function getValueFromIdPArray($CookieArray)
{
    // Merges cookie content and encodes it
    $CookieArray = array_map('base64_encode', $CookieArray);
    $value = implode(' ', $CookieArray);
    return $value;
}

/**
 * Append a value to the array of IDPs, ensure no more than 5
 * entries are in array
 *
 * @param  mixed $value
 * @param  array $CookieArray
 * @return array
 */
function appendValueToIdPArray($value, $CookieArray)
{
    // Remove value if it already existed in array
    foreach (array_keys($CookieArray) as $i) {
        if ($CookieArray[$i] == $value) {
            unset($CookieArray[$i]);
        }
    }

    // Add value to end of array
    $CookieArray[] = $value;

    // Shorten array from beginning as latest entry should
    // be at end according to SAML spec
    while (count($CookieArray) > 5) {
        array_shift($CookieArray);
    }

    return $CookieArray;
}

/**
 * Checks if the configuration file has changed. If it has, check the file
 * and change its timestamp.
 *
 * @param  string $IDPConfigFile file path
 * @param  string $backupIDPConfigFile file path
 * @return bool
 */
function checkConfig($IDPConfigFile, $backupIDPConfigFile)
{

    // Do files have the same modification time
    if (filemtime($IDPConfigFile) == filemtime($backupIDPConfigFile)) {
        return true;
    }

    // Availability check
    if (!file_exists($IDPConfigFile)) {
        return false;
    }

    // Readability check
    if (!is_readable($IDPConfigFile)) {
        return false;
    }

    // Size check
    if (filesize($IDPConfigFile) < 200) {
        return false;
    }

    // Make modification time the same
    // If that doesn't work we won't notice it
    touch($IDPConfigFile, filemtime($backupIDPConfigFile));

    return true;
}

/**
 * Checks if an IDP exists and returns true if it does, false otherwise
 *
 * @param  string $IDP
 * @return bool
 */
function checkIDP($IDP)
{
    global $IDProviders;

    if (isset($IDProviders[$IDP])) {
        return true;
    }
    return false;
}

/**
 * Checks if an IDP exists and returns true if it exists and prints an error
 * if it doesn't
 *
 * @param  mixed $IDP
 * @return mixed
 */
function checkIDPAndShowErrors($IDP)
{
    global $IDProviders;

    if (checkIDP($IDP)) {
        return true;
    }

    // Otherwise show an error
    $message = sprintf(getLocalString('invalid_user_idp'), htmlentities($IDP)) . "</p><p>\n<code>";
    foreach ($IDProviders as $key => $value) {
        if (isset($value['SSO'])) {
            $message .= $key . "<br>\n";
        }
    }
    $message .= "</code>\n";

    printError($message);
    exit;
}

/**
 * Validates the URL and returns it if it is valid or false otherwise
 *
 * @param  mixed $url
 * @return string|bool
 */
function getSanitizedURL($url)
{
    $components = parse_url($url);

    if ($components) {
        return $url;
    }
    return false;
}

/**
 * Parses the hostname out of a string and returns it
 *
 * @param  string $string
 * @return string
 */
function getHostNameFromURI($string)
{
    // Check if string is URN
    if (preg_match('/^urn:mace:/i', $string)) {
        // Return last component of URN
        $components = explode(':', $string);
        return end($components);
    }

    // Apparently we are dealing with something like a URL
    if (preg_match('/([a-zA-Z0-9\-\.]+\.[a-zA-Z0-9\-\.]{2,6})/', $string, $matches)) {
        return $matches[0];
    }
    return '';
}

/**
 * Parses the domain out of a string and returns it
 *
 * @param  string $string
 * @return string
 */
function getDomainNameFromURI($string)
{
    // Check if string is URN
    if (preg_match('/^urn:mace:/i', $string)) {
        // Return last component of URN
        $components = explode(':', $string);
        return getTopLevelDomain(end($components));
    }

    // Apparently we are dealing with something like a URL
    if (preg_match('/[a-zA-Z0-9\-\.]+\.([a-zA-Z0-9\-\.]{2,6})/', $string, $matches)) {
        return getTopLevelDomain($matches[0]);
    }
    return '';
}

/**
 * Returns top level domain name from a DNS name
 *
 * @param  string $string
 * @return string
 */
function getTopLevelDomain($string)
{
    $hostnameComponents = explode('.', $string);
    if (count($hostnameComponents) >= 2) {
        return $hostnameComponents[count($hostnameComponents) - 2] . '.' . $hostnameComponents[count($hostnameComponents) - 1];
    }
    return $string;
}

/**
 * Parses the reverse dns lookup hostname out of a string and returns domain
 *
 * @return string
 */
function getDomainNameFromURIHint()
{
    global $IDProviders;

    $clientHostname = gethostbyaddr($_SERVER['REMOTE_ADDR']);
    if ($clientHostname == $_SERVER['REMOTE_ADDR']) {
        return '-';
    }

    // Get domain name from client host name
    $clientDomainName = getDomainNameFromURI($clientHostname);
    if ($clientDomainName == '') {
        return '-';
    }

    // Return first matching IdP entityID that contains the client domain name
    foreach ($IDProviders as $key => $value) {
        if (
            preg_match('/^http.+' . $clientDomainName . '/', $key)
            || preg_match('/^urn:.+' . $clientDomainName . '$/', $key)
        ) {
            return $key;
        }
    }

    // No matching entityID was found
    return '-';
}


/**
 * Get the user's language using the accepted language http header
 *
 * @return string
 */
function determineLanguage()
{
    global $langStrings, $defaultLanguage;

    // Check if language is enforced by PATH-INFO argument
    if (isset($_SERVER['PATH_INFO']) && !empty($_SERVER['PATH_INFO'])) {
        foreach ($langStrings as $lang => $values) {
            if (preg_match('#/' . $lang . '($|/)#', $_SERVER['PATH_INFO'])) {
                return $lang;
            }
        }
    }

    // Check if there is a language GET argument
    if (isset($_GET['lang'])) {
        $localeComponents = decomposeLocale($_GET['lang']);
        if (
            $localeComponents !== false
            && isset($langStrings[$localeComponents[0]])
        ) {
            // Return language
            return $localeComponents[0];
        }
    }

    // Return default language if no headers are present otherwise
    if (!isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
        return $defaultLanguage;
    }

    // Inspect Accept-Language header which looks like:
    // Accept-Language: en,de-ch;q=0.8,fr;q=0.7,fr-ch;q=0.5,en-us;q=0.3,de;q=0.2
    $languages = explode(',', trim($_SERVER['HTTP_ACCEPT_LANGUAGE']));
    foreach ($languages as $language) {
        $languageParts = explode(';', $language);

        // Only treat art before the prioritization
        $localeComponents = decomposeLocale($languageParts[0]);
        if (
            $localeComponents !== false
            && isset($langStrings[$localeComponents[0]])
        ) {
            // Return language
            return $localeComponents[0];
        }
    }

    return $defaultLanguage;
}


/**
 * Splits up a string (relazed) according to
 * http://www.debian.org/doc/manuals/intro-i18n/ch-locale.en.html#s-localename
 * and returns an array with the four components
 *
 * @param  string $locale
 * @return string|bool|null
 */
function decomposeLocale($locale)
{

    // Locale name syntax:  language[_territory][.codeset][@modifier]
    if (!preg_match('/^([a-zA-Z]{2})([-_][a-zA-Z]{2})?(\.[^@]+)?(@.+)?$/', $locale, $matches)) {
        return false;
    } else {
        // Remove matched string in first position
        array_shift($matches);

        return $matches;
    }
}

/**
 * Gets a string in a specific language. Fallback to default language and
 * to English.
 *
 * @param  string $string
 * @param  string $encoding
 * @return string
 */
function getLocalString($string, $encoding = '')
{
    global $defaultLanguage, $langStrings, $language;

    $textString = '';
    if (isset($langStrings[$language][$string])) {
        $textString = $langStrings[$language][$string];
    } elseif (isset($langStrings[$defaultLanguage][$string])) {
        $textString = $langStrings[$defaultLanguage][$string];
    } else {
        $textString = $langStrings['en'][$string];
    }

    // Change encoding if necessary
    if ($encoding == 'js') {
        $textString = convertToJSString($textString);
    }

    return $textString;
}

/**
 * Converts string to a JavaScript format that can be used in JS alert
 *
 * @param  string $string
 * @return string
 */
function convertToJSString($string)
{
    return addslashes(html_entity_decode($string, ENT_COMPAT, 'UTF-8'));
}

/**
 * Replaces all newlines with spaces and then trims the string to get one line
 *
 * @param  string $string
 * @return string
 */
function trimToSingleLine($string)
{
    return trim(preg_replace("|\n|", ' ', $string));
}

/**
 * Checks if entityID hostname of a valid IdP exists in path info
 *
 * @return string
 */
function getIdPPathInfoHint()
{
    global $IDProviders;

    // Check if path info is available at all
    if (!isset($_SERVER['PATH_INFO']) || empty($_SERVER['PATH_INFO'])) {
        return '-';
    }

    // Check for entityID hostnames of all available IdPs
    foreach ($IDProviders as $key => $value) {
        // Only check actual IdPs
        if (
            isset($value['SSO'])
            && !empty($value['SSO'])
            && $value['Type'] != 'wayf'
            && isPartOfPathInfo(getHostNameFromURI($key))
        ) {
            return $key;
        }
    }

    // Check for entityID domain names of all available IdPs
    foreach ($IDProviders as $key => $value) {
        // Only check actual IdPs
        if (
            isset($value['SSO'])
            && !empty($value['SSO'])
            && $value['Type'] != 'wayf'
            && isPartOfPathInfo(getDomainNameFromURI($key))
        ) {
            return $key;
        }
    }

    return '-';
}

/**
 * Joins localized names and keywords of an IdP to a single string
 *
 * @param  string[][] $IdPValues
 * @return string
 */
function composeOptionData($IdPValues)
{
    $data = '';
    foreach ($IdPValues as $key => $value) {
        if (is_array($value) && isset($value['Name'])) {
            $data .= ' ' . $value['Name'];
        }

        if (is_array($value) && isset($value['Keywords'])) {
            $data .= ' ' . $value['Keywords'];
        }
    }

    return $data;
}

/**
 * Parses the Kerberos realm out of the string and returns it
 *
 * @param  mixed $string
 * @return string
 */
function getKerberosRealm($string)
{
    global $IDProviders;

    if ($string != '') {
        // Find a matching Kerberos realm
        foreach ($IDProviders as $key => $value) {
            if ($value['Realm'] == $string) {
                return $key;
            }
        }
    }

    return '-';
}


/**
 * Determines the IdP according to the IP address if possible
 *
 * @return string
 */
function getIPAdressHint()
{
    global $IDProviders;

    foreach ($IDProviders as $name => $idp) {
        if (is_array($idp) && array_key_exists("IP", $idp)) {
            $clientIP = $_SERVER["REMOTE_ADDR"];

            foreach ($idp["IP"] as $network) {
                if (isIPinCIDRBlock($network, $clientIP)) {
                    return $name;
                }
            }
        }
    }
    return '-';
}

/**
 * Returns true if IP is in IPv4/IPv6 CIDR range
 * and returns false otherwise
 *
 * @param  string $cidr
 * @param  mixed $ip
 * @return bool
 */
function isIPinCIDRBlock($cidr, $ip)
{

    // Split CIDR notation
    list($net, $mask) = preg_split("|/|", $cidr);

    // Convert to binary string value of 1s and 0s
    $netAsBinary = convertIPtoBinaryForm($net);
    $ipAsBinary =  convertIPtoBinaryForm($ip);

    // Return false if netmask and ip are using different protocols
    if (strlen($netAsBinary) != strlen($ipAsBinary)) {
        return false;
    }

    // Compare the first $mask bits
    for ($i = 0; $i < $mask; $i++) {
        // Return false if bits don't match
        if ($netAsBinary[$i] != $ipAsBinary[$i]) {
            return false;
        }
    }

    // If we got here, ip matches net
    return true;
}

/**
 * Converts IP in human readable format to binary string
 *
 * @param  mixed $ip
 * @return string|bool false if not IPv4 of if not IPv6 address
 */
function convertIPtoBinaryForm($ip)
{

    //  Handle IPv4 IP
    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) !== false) {
        return base_convert(ip2long($ip), 10, 2);
    }

    // Return false if IP is neither IPv4 nor a IPv6 IP
    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) === false) {
        return false;
    }

    // Convert IP to binary structure and return false if this fails
    if (($ipAsBinStructure = inet_pton($ip)) === false) {
        return false;
    }

    $numOfBytes = 16;
    $ipAsBinaryString = '';

    // Convert IP to binary string
    while ($numOfBytes > 0) {
        // Convert current byte to decimal number
        $currentByte = ord($ipAsBinStructure[$numOfBytes - 1]);

        // Convert currently byte to string of 1 and 0
        $currentByteAsBinary = sprintf("%08b", $currentByte);

        // Prepend to rest of IP in binary string
        $ipAsBinaryString = $currentByteAsBinary . $ipAsBinaryString;

        // Decrease byte counter
        $numOfBytes--;
    }

    return $ipAsBinaryString;
}

/**
 * Returns URL without GET arguments
 *
 * @param  string $url
 * @return string
 */
function getURLWithoutArguments($url)
{
    return preg_replace('/\?.*/', '', $url);
}

/**
 * Returns true if URL could be verified or if no check is necessary, false otherwise
 *
 * @param  string $entityID
 * @param  string $returnURL
 * @return bool
 */
function verifyReturnURL($entityID, $returnURL)
{
    global $SProviders, $useACURLsForReturnParamCheck;

    // Prevent attacks with return URLs like https://ilias.unibe.ch@google.com
    $returnURL = preg_replace('|(https?://)(.+@)(.+)|', '\1\3', $returnURL);

    // If SP has a <idpdisc:DiscoveryResponse>, check return param
    if (isset($SProviders[$entityID]['DSURL'])) {
        $returnURLWithoutArguments = getURLWithoutArguments($returnURL);
        foreach ($SProviders[$entityID]['DSURL'] as $DSURL) {
            $DSURLWithoutArguments = getURLWithoutArguments($DSURL);
            if ($DSURLWithoutArguments == $returnURLWithoutArguments) {
                return true;
            }
        }

        // DS URLs did not match the return URL
        return false;
    }

    // Return true if SP has no <idpdisc:DiscoveryResponse>
    // and $useACURLsForReturnParamCheck is disabled (we don't check anything)
    if (!$useACURLsForReturnParamCheck) {
        return true;
    }

    // $useACURLsForReturnParamCheck is enabled, so
    // check return param against host name of assertion consumer URLs

    // Check hostnames
    $returnURLHostName = getHostNameFromURI($returnURL);
    foreach ($SProviders[$entityID]['ACURL'] as $ACURL) {
        if (getHostNameFromURI($ACURL) == $returnURLHostName) {
            return true;
        }
    }

    // We haven't found a matching assertion consumer URL, therefore we return false
    return false;
}

/**
 * Returns a reasonable value for returnIDParam
 *
 * @return string
 */
function getReturnIDParam()
{
    if (isset($_GET['returnIDParam']) && !empty($_GET['returnIDParam'])) {
        return $_GET['returnIDParam'];
    }
    return 'entityID';
}

/**
 * Returns true if valid Shibboleth 1.x request or Directory Service request
 *
 * @return bool
 */
function isValidShibRequest()
{
    return (isValidShib1Request() || isValidDSRequest());
}

/**
 * Returns true if valid Shibboleth request
 *
 * @return bool
 */
function isValidShib1Request()
{
    if (isset($_GET['shire']) && isset($_GET['target'])) {
        return true;
    }
    return false;
}

/**
 * Returns true if request is a valid Directory Service request
 *
 * @return bool
 */
function isValidDSRequest()
{
    global $SProviders;

    // If entityID is not present, request is invalid
    if (!isset($_GET['entityID'])) {
        return false;
    }

    // If entityID and return parameters are present, request is valid
    if (isset($_GET['return'])) {
        return true;
    }

    // If no return parameter and no Discovery Service endpoint is available
    // for SP, request is invalid
    if (!isset($SProviders[$_GET['entityID']]['DSURL'])) {
        return false;
    }

    if (count($SProviders[$_GET['entityID']]['DSURL']) < 1) {
        return false;
    }

    // EntityID is available and there is at least one DiscoveryService
    // endpoint defined. Therefore, the request is valid
    return true;
}

/**
 * Sets the Location header to redirect the user's web browser
 *
 * @param  string $url
 * @return void
 */
function redirectTo($url)
{
    header('Location: ' . $url);
}

/**
 * Sets the Location that is used for redirect the web browser back to the SP
 *
 * @param  string $url
 * @param  string $IdP
 * @return void
 */
function redirectToSP($url, $IdP)
{
    if (preg_match('/\?/', $url) > 0) {
        redirectTo($url . '&' . getReturnIDParam() . '=' . urlencode($IdP));
    } else {
        redirectTo($url . '?' . getReturnIDParam() . '=' . urlencode($IdP));
    }
}

/**
 * Logs all events where users were redirected to their IdP or back to an SP
 * The log then can be used to approximately detect how many users were served
 * by the SWITCHwayf
 *
 * @param  mixed $protocol
 * @param  mixed $type
 * @param  mixed $sp
 * @param  mixed $idp
 * @param  mixed $return
 * @return void
 */
function logAccessEntry($protocol, $type, $sp, $idp, $return)
{
    global $WAYFLogFile, $useLogging;

    // Return if logging deactivated
    if (!$useLogging) {
        return;
    }

    // Create log file if it does not exist yet
    if (!file_exists($WAYFLogFile) && !touch($WAYFLogFile)) {
        // File does not exist and cannot be written to
        logFatalErrorAndExit('WAYF log file ' . $WAYFLogFile . ' does not exist and could not be created.');
    }

    // Ensure that the file exists and is writable
    if (!is_writable($WAYFLogFile)) {
        logFatalErrorAndExit('Current file permission do not allow WAYF to write to its log file ' . $WAYFLogFile . '.');
    }

    // Compose log entry
    $entry = date('Y-m-d H:i:s') . ' ' . $_SERVER['REMOTE_ADDR'] . ' ' . $protocol . ' ' . $type . ' ' . $idp . ' ' . $return . ' ' . $sp . "\n";

    // Open file in append mode
    if (!$handle = fopen($WAYFLogFile, 'a')) {
        logFatalErrorAndExit('Could not open file ' . $WAYFLogFile . ' for appending log entries.');
    }

    // Try getting the lock
    while (!flock($handle, LOCK_EX)) {
        usleep(rand(10, 100));
    }

    // Write entry
    fwrite($handle, $entry);

    // Release the lock
    flock($handle, LOCK_UN);

    // Close file handle
    fclose($handle);
}

/**
 * Init connection to system logger
 *
 * @return void
 */
function initLogger()
{
    global $instanceIdentifier;

    openlog($instanceIdentifier, LOG_NDELAY, LOG_USER);
}

/**
 * Logs a debug message
 *
 * @param  mixed $infoMsg
 * @return void
 */
function logDebug($infoMsg)
{
    wayfLog("DEBUG", $infoMsg);
}

/**
 * Logs an info message
 *
 * @param  mixed $infoMsg
 * @return void
 */
function logInfo($infoMsg)
{
    wayfLog("INFO", $infoMsg);
}

/**
 * Logs an warning message
 *
 * @param  mixed $warnMsg
 * @return void
 */
function logWarning($warnMsg)
{
    wayfLog("WARN", $warnMsg);
}

/**
 * Logs an error message
 *
 * @param  mixed $errorMsg
 * @return void
 */
function logError($errorMsg)
{
    wayfLog("ERROR", $errorMsg);
}

/**
 * Logs an fatal error message
 *
 * @param  mixed $errorMsg
 * @return void
 */
function logFatalErrorAndExit($errorMsg)
{
    logError($errorMsg);
    exit;
}

/**
 * Logs a message to errorLog
 *
 * @param  string $level
 * @param  string $errorMsg
 * @return void
 */
function wayfLog($level, $errorMsg)
{
    global $developmentMode;

    // If developmentMode => Log to errorLog
    if ($developmentMode) {
        error_log(sprintf("[%s] %s", $level, $errorMsg));
        // Legacy logging
        //echo $errorMsg;
    }

    $syslogPriority = LOG_INFO;
    if ($level == "ERROR") {
        $syslogPriority = LOG_ERR;
    }
    if ($level == "WARN") {
        $syslogPriority = LOG_WARNING;
    }

    if ($level != "DEBUG") {
        // Syslog Logging
        initLogger();

        syslog($syslogPriority, $errorMsg);
    }
}

/**
 * Returns true if PATH info indicates a request of type $type
 *
 * @param  string $type
 * @return bool
 */
function isRequestType($type)
{
    // Make sure the type is checked at end of path info
    return isPartOfPathInfo($type . '$');
}

/**
 * Checks for substrings in Path Info and returns true if match was found
 *
 * @param  string $needle
 * @return bool
 */
function isPartOfPathInfo($needle)
{
    if (
        isset($_SERVER['PATH_INFO'])
        && !empty($_SERVER['PATH_INFO'])
        && preg_match('|/' . $needle . '|', $_SERVER['PATH_INFO'])
    ) {
        return true;
    }
    return false;
}

/**
 * Converts to the unified data structure that the Shibboleth DS will be using
 *
 * @param  array $IDProviders
 * @return array
 */
function convertToShibDSStructure($IDProviders)
{
    $ShibDSIDProviders = array();

    foreach ($IDProviders as $key => $value) {
        // Skip unknown and category entries
        if (
            !isset($value['Type'])
            || $value['Type'] == 'category'
            || $value['Type'] == 'wayf'
        ) {
            continue;
        }

        // Init and fill IdP data
        $identityProvider = array();
        $identityProvider['entityID'] = $key;
        $identityProvider['DisplayNames'][] = array('lang' => 'en', 'value' => $value['Name']);

        // Add DisplayNames in other languages
        foreach ($value as $lang => $name) {
            if (
                $lang == 'Name'
                || $lang == 'SSO'
                || $lang == 'Realm'
                || $lang == 'Type'
                || $lang == 'IP'
            ) {
                continue;
            }

            if (isset($name['Name'])) {
                $identityProvider['DisplayNames'][] = array('lang' => $lang, 'value' => $name['Name']);
            }
        }

        // Add data to ShibDSIDProviders
        $ShibDSIDProviders[] = $identityProvider;
    }

    return $ShibDSIDProviders;
}

/**
 * Sorts the IDProviders array
 *
 * @param  array $IDProviders
 * @return void
 */
function sortIdentityProviders(&$IDProviders)
{
    $orderedCategories = array();

    // Create array with categories and IdPs in categories
    $unknownCategory = array();
    foreach ($IDProviders as $entityId => $IDProvider) {
        // Add categories
        if ($IDProvider['Type'] == 'category') {
            $orderedCategories[$entityId]['data'] = $IDProvider;
        }
    }

    // Add category 'unknown' if not present
    if (!isset($orderedCategories['unknown'])) {
        $orderedCategories['unknown']['data'] = array(
            'Name' => 'Unknown',
            'Type' => 'category',
        );
    }

    foreach ($IDProviders as $entityId => $IDProvider) {
        // Skip categories
        if ($IDProvider['Type'] == 'category') {
            continue;
        }

        // Skip incomplete descriptions
        if (!is_array($IDProvider) || !isset($IDProvider['Name'])) {
            continue;
        }

        // Sanitize category
        if (!isset($IDProvider['Type'])) {
            $IDProvider['Type'] = 'unknown';
        }

        // Add IdP
        $orderedCategories[$IDProvider['Type']]['IdPs'][$entityId] = $IDProvider;
    }

    // Relocate all IdPs for which no category with a name was defined
    $toremoveCategories = array();
    foreach ($orderedCategories as $category => $object) {
        if (!isset($object['data'])) {
            foreach ($object['IdPs'] as $entityId => $IDProvider) {
                $unknownCategory[$entityId] = $IDProvider;
            }
            $toremoveCategories[] = $category;
        }
    }

    // Remove categories without descriptions
    foreach ($toremoveCategories as $category) {
        unset($orderedCategories[$category]);
    }

    // Recompose $IDProviders
    $IDProviders = array();
    foreach ($orderedCategories as $category => $object) {
        // Skip category if it contains no IdPs
        if (!isset($object['IdPs']) || count($object['IdPs']) < 1) {
            continue;
        }

        // Add category
        $IDProviders[$category] = $object['data'];

        // Sort IdPs in category
        uasort($object['IdPs'], 'sortUsingTypeIndexAndName');

        // Add IdPs
        foreach ($object['IdPs'] as $entityId => $IDProvider) {
            $IDProviders[$entityId] = $IDProvider;
        }
    }
}


/**
 * Sorts two entries according to their Type, Index and (local) Name
 *
 * @param  string[] $a
 * @param  string[] $b
 * @return int
 */
function sortUsingTypeIndexAndName($a, $b)
{
    global $language;

    if ($a['Type'] != $b['Type']) {
        return strcasecmp(removeAccents($a['Type']), removeAccents($b['Type']));
    } elseif (isset($a['Index']) && isset($b['Index']) && $a['Index'] != $b['Index']) {
        return strcasecmp(removeAccents($a['Index']), removeAccents($b['Index']));
    } else {
        // Sort using locale names
        $localNameB = (isset($a[$language]['Name'])) ? $a[$language]['Name'] : $a['Name'];
        $localNameA = (isset($b[$language]['Name'])) ? $b[$language]['Name'] : $b['Name'];
        return strcasecmp(removeAccents($localNameB), removeAccents($localNameA));
    }
}


/**
 * Return given String without accents
 * 
 * @param string $string input value
 * 
 * @return string
 */
function removeAccents($string)
{
    $accents =    array("À", "Á", "Â", "Ã", "Ä", "Å", "à", "á", "â", "ã", "ä", "å", "Ò", "Ó", "Ô", "Õ", "Ö", "Ø", "ò", "ó", "ô", "õ", "ö", "ø", "È", "É", "Ê", "Ë", "è", "é", "ê", "ë", "Ç", "ç", "Ì", "Í", "Î", "Ï", "ì", "í", "î", "ï", "Ù", "Ú", "Û", "Ü", "ù", "ú", "û", "ü", "ÿ", "Ñ", "ñ");
    $nonAccents = array("a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "e", "e", "e", "e", "e", "e", "e", "e", "c", "c", "i", "i", "i", "i", "i", "i", "i", "i", "u", "u", "u", "u", "u", "u", "u", "u", "y", "n", "n");
    return str_replace(
        $accents,
        $nonAccents,
        $string
    );
}


/**
 * Returns true if the referer of the current request is matching an assertion
 * consumer or discovery service URL of a Service Provider
 *
 * @return bool
 */
function isRequestRefererMatchingSPHost()
{
    global $SProviders;

    // If referer is not available return false
    if (!isset($_SERVER["HTTP_REFERER"]) || $_SERVER["HTTP_REFERER"] == '') {
        return false;
    }

    if (!isset($SProviders) || !is_array($SProviders)) {
        return false;
    }

    $refererHostname = getHostNameFromURI($_SERVER["HTTP_REFERER"]);
    foreach ($SProviders as $key => $SProvider) {
        // Check referer against entityID
        $spHostname = getHostNameFromURI($key);
        if ($refererHostname == $spHostname) {
            return true;
        }

        // Check referer against Discovery Response URL(DSURL)
        if (isset($SProvider['DSURL'])) {
            foreach ($SProvider['DSURL'] as $url) {
                $spHostname = getHostNameFromURI($url);
                if ($refererHostname == $spHostname) {
                    return true;
                }
            }
        }

        // Check referer against Assertion Consumer Service URL(ACURL)
        if (isset($SProvider['ACURL'])) {
            foreach ($SProvider['ACURL'] as $url) {
                $spHostname = getHostNameFromURI($url);
                if ($refererHostname == $spHostname) {
                    return true;
                }
            }
        }
    }

    return false;
}

/**
 * Is this script run in CLI mode
 *
 * @return bool
 */
function isRunViaCLI()
{
    return !isset($_SERVER['REMOTE_ADDR']);
}

/**
 * Is this script run in CLI mode
 *
 * @return bool
 */
function isRunViaInclude()
{
    return basename($_SERVER['SCRIPT_NAME']) != 'readMetadata.php';
}

/**
 * printSubmitAction
 *
 * @return string
 */
function printSubmitAction()
{
    if (isUseSelect2()) {
        return "return select2CheckForm()";
    }
    return "return checkForm()";
}

/******************************************************************************/
// Getter for useSelect2: we can't only rely on config.php::$useSelect2
// because of embeddedWAYF.
// If SP want's to use Select2, it has to add ?useSelect2=true
function isUseSelect2()
{
    global $useSelect2;

    if (!isset($_GET["useSelect2"])) {
        return $useSelect2;
    }

    return $_GET["useSelect2"];
}

function getSelect2PageSize()
{
    global $select2PageSize;

    if (!isset($_GET["select2PageSize"])) {
        return $select2PageSize;
    }

    return $_GET["select2PageSize"];
}

function buildIdpData($IDProvider, $key)
{
    $data = getDomainNameFromURI($key);
    $data .= composeOptionData($IDProvider);
    return $data;
}
