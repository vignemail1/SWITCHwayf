Copyright (c) 2019, SWITCH
See LICENSE file for details.

-------------------------------------------------------------------------------

SWITCHwayf
==========

This document contains important information for this release of SWITCHwayf, 
including the installation and update instructions.

* Project web site: <https://forge.switch.ch/redmine/projects/wayf>
* Bug reports/feature requests: <https://forge.switch.ch/redmine/projects/wayf/issues>
* Contact: aai@switch.ch or go to <http://www.switch.ch/aai/wayf>


-------------------------------------------------------------------------------

Requirements
------------
- PHP 5.3 or newer, PHP 7
- PHP XML Parser extension is required for parsing SAML2 metadata
  (Debian/Ubuntu: 'apt install php-xml', CentOS/RedHat: yum install php-xml)
- The web server users must have write permissions to some files including: 
  * $backupIDPConfigFile (default 'IDProvider.conf.php')
  * $metadataIDPFile (default 'IDProvider.metadata.conf.php')
  * $metadataSPFile (default 'SProvider.metadata.conf.php')
  * $metadataLockFile (default '/tmp/wayf_metadata.lock')
  * $WAYFLogFile (default '/var/log/apache2/wayf.log')

-------------------------------------------------------------------------------

Download
--------
The latest release can be downloaded from:
<https://forge.switch.ch/redmine/projects/wayf/files>

-------------------------------------------------------------------------------

Installation
------------
1. Unpack the SWITCHwayf_binary ${VERSION}_${DATE}.zip ZIP archive into a 
   directory that is *not* accessible via the web server.

2. Make a copy of the *.dist.php files:
   - Copy the file SWITCHwayf/etc/config.dist.php and name it 
     SWITCHwayf/etc/config.php 
     This is the main configuration file of the SWITCHwayf
   - Copy the file etc/IDProvider.conf.dist.php and name it 
     SWITCHwayf/etc/IDProvider.conf.php
     This file contains the list of Identity Providers that that can be  
     configured by hand

3. Adapt the SWITCHwayf configuration in SWITCHwayf/etc/config.php. 
   There are comments in that file that should help you make 
   suitable choices for your use case.
   If you are relying on metadata for SP/IdP information, 
   initialize the 
   IDProvider.metadata.php//SProvider.metadata.php files with a 
   command like
   
        php bin/update-metadata.php --metadata-file #PATH-TO-SAML2-METADATA#/metadata.xml --metadata-idp-file etc/IDProvider.metadata.php --metadata-sp-file etc/SProvider.metadata.php --verbose

4. Ensure that permissions for the files:
     - SWITCHwayf/etc/SProvider.metadata.php (configured in $metadataSPFile)
     - SWITCHwayf/etc/IDProvider.metadata.php  (configured in $metadataIDPFile)
     - /tmp/metadata.lock (configured in $metadataLockFile)
     - /var/log/apache2/wayf.log (configured in $WAYFLogFile)
   
   are set such that the web server user (e.g. www-data, www or httpd) has write
   permissions for them. E.g. with a command like:
   
        chown www-data etc/*metadata.php

5. If Apache 2 is used, add the following statement to the Apache configuration:

        Alias /#SOME_PATH# /#YOUR-PATH-TO#/SWITCHwayf/www
        <Directory /#YOUR-PATH-TO#/SWITCHwayf/www>
            Options Indexes MultiViews
            AllowOverride None
            Order allow,deny
            Allow from all
            
          <Files WAYF>
              SetHandler php7-script
              AcceptPathInfo On
          </Files>
        </Directory>

   Beware, only the www subdirectory should be exposed, but 
   not the whole top-level directory (SWITCHwayf).

   Alternatively, one also could rename the file 'WAYF' to 
   'WAYF.php' to avoid setting the PHP handler explicitly on 
   this file.

6. When using the embedded WAYF feature it might be necessary to add a line to 
   the Apache configuration like below in order to prevent certain web browsers 
   from not displaying the Embedded WAYF or parts of it:

        Header set P3P "CP=\"NOI CUR DEVa OUR IND COM NAV PRE\""

   For that to work, the Apache header extension must also be enabled
   with a command like:


        a2enmod headers
        /etc/init.d/apache2 reload

   See <http://www.w3.org/P3P/> for more details on P3P.

7. Test access by calling the WAYF with a URL like:
   
   <https://your.host.com/#SOME_PATH#/WAYF>
   
   Use this URL as Location for your Shibboleth configuration. The WAYF
   will automatically be able to detect whether it receives a Shibboleth 
   authentication request or a Discovery Service request.

8. Ensure to set the mode of the SWITCHwayf from developmentMode
   to production by setting
   '$developmentMode = false;'
   in SWITCHwayf/etc/config.php
   This will prevent some internal errors from being shown
   to the client web browser.

-------------------------------------------------------------------------------

Git Access
-----------------
Check out the latest SWITHCHwayf code with:

    git clone https://gitlab.switch.ch/aai/SWITCHwayf.git

Although the code in the GIT repository should always be 
executable, it should be considered unstable and not be used for 
production environments without prior testing.

-------------------------------------------------------------------------------

General Update Instructions
---------------------------
1. Make a backup of the directory where the currently active version of the 
   SWITCHwayf is installed, e.g. with 'cp -a SWITCHwayf SWITCHwayf.bak'

2. Get the ZIP archive of the new version and move it into the same 
   directory as the WAYF script of the currently deployed version.
   Download from <https://forge.switch.ch/redmine/projects/wayf/files>

3. Unzip the archive, e.g. with the command:
   
        unzip -d #DD# SWITCHwayf_x.y_YYYYMMDD.zip
   
   This step will overwrite all files except those whose names start 
   with 'custom-'.
   Alternatively, create a new directory, move the ZIP archive in that directory,
   unzip it and then copy the config.php and all custom-.* files from the 
   current SWITCHwayf installation over to the new directory.

4. Have a look at config.dist.php and compare this file with your current
   config.php in order to identify new configuration options.
   
   > Since version 1.18 the script 'update-config.php' can be used to 
   > merge an existing configuration (from config.php) with the default
   > configuration (config.dist.php) into a new configuration file 
   > (config.new.php). This allows easily getting a clean configuration file
   > while keeping the current settings. 
   > Run the script with: `php update-config.php`
   > Ensure that the user has the necessary write privileges to create the
   > file config.new.php. Also note that all comments you might have
   > added in the current.php will not be copied over.
   
   Also compare the custom-.* files to the default-.* files that might have
   changed. Some features like the improved drop-down list require the WAYF
   to load additional javascripts. If a custom header file is missing them,
   the feature will not work.

5. Ensure that permissions for the files:
     - SProvider.metadata.php
     - IDProvider.metadata.php 
     - metadata.lock
     - $WAYFLogFile (typically /var/log/apache2/wayf.log)
   are set such that the web server user (e.g. www-data, www or httpd) has write
   permissions for them.


6. If SAML2 metadata is used by SWITCHwayf, you might have to run the following
   command to bootstrap the metadata reading process again:
   
        php bin/update-metadata.php --metadata-file #PATH-TO-SAML2-METADATA#/metadata.xml --metadata-idp-file etc/IDProvider.metadata.php --metadata-sp-file etc/SProvider.metadata.php --verbose

It's also possible to retrieve the latest code directly from the GIT 
repository, which is located here: 

    git clone https://gitlab.switch.ch/aai/SWITCHwayf.git

-------------------------------------------------------------------------------

Specific Update Instructions
----------------------------

* Updates from versions before 2.0
  It's best to install version 2.0 or newer from scratch and 
  then copy over the following files from the pre 2.0 deployment
  to the new deployement:
  - IDProvider.conf.php -> SWITCHwayf/etc/
  - IDProvider.conf.php.bak -> SWITCHwayf/etc/
  - IDProvider.metadata.php -> SWITCHwayf/etc/
  - SProvider.metadata.php -> SWITCHwayf/etc/
  - config.php -> SWITCHwayf/etc/
  - custom-languages.php -> SWITCHwayf/lib/
  - css/custom-* -> SWITCHwayf/www/css/  
  
  You then might run php SWITCHwayf/bin/update-config.php to
  create a new configuration file based on previous settings.


* Updates from versions before 1.18
  The following new configuration options were introduced:
  
  - $supportContactEmail
  - $organizationLogoURL
  - $organizationURL
  - $faqURL
  - $helpURL
  - $privacyURL
  
  Have a look at config.dist.php in section "5. Appearance Settings" for a 
  description on these settings. Then make sure to add them to config.php
  with your own values (or empty strings to ignore them). Otherwise, default 
  values will be set.
  The default behaviour for the Embedded WAYF setting
  wayf_use_small_logo was changed from false to true as most instances
  of the Embedded WAYF seem to prefer the small logo. All non-mandatory
  settings of the Embedded WAYF are now commented out in the default 
  template that is generated for the Embedded WAYF. This implies that
  if there are Service Providers using your Embedded WAYF feature, they might
  have to review their Embedded WAYF settings if they still want to use the
  larger logo.


* Updates from versions before 1.15
  The keys of the following languages strings were renamed and should be  
  adapted in the custom-languages.php file if it exists.
  - 'about_aai' was renamed to 'about_federation'
  - 'about_switch' was renamed to 'about_organisation'
  - 'switch_description' was renamed to 'additional_info'


* Update from versions before 1.14.3:
  The new setting '$metadataLockFile' was introduced in config.php. It allows
  configuring the location of the lock file. When the SWITCHwayf is used in a 
  Windows environment, the path to this file probably has to be adapted.


* Update from versions before 1.8:
  This version has a slightly different structure than previous versions. 
  Therefore, it is recommended to start with a clean installation. 
  However, you should be able to take over most of your old config.php 
  functions and use them in the new template.php file again to keep your 
  customized look and feel.

-------------------------------------------------------------------------------

Security Notes
--------------
The Discovery Service protocol as defined in 
<http://docs.oasis-open.org/security/saml/Post2.0/sstc-saml-idp-discovery.pdf>
states that the protocol creates opportunities for phishing attacks as do all
SSO protocols that make use of redirection. The specification states that an 
implementation "SHOULD" examine the 'return' parameter used in a Discovery 
Service request and match it against the <idpdisc:DiscoveryResponse> 
extension in SAML metadata. Since version 1.14 the SWITCHwayf supports this 
feature. In order to activate it, the SWITCHwayf has to use the SAML 2 metadata
parsing features by using

    $useSAML2Metadata = true;

and set the options:

    enableDSReturnParamCheck = true;

and potentially

    $useACURLsForReturnParamCheck = true;

in case the metadata loaded by SWITCHwayf does not include DiscoveryResponse 
elements for many Service Providers.


-------------------------------------------------------------------------------

Troubleshooting
---------------
Generally, if there is an error or an exception, the WAYF will log it to syslog. 
In case there is a problem and only a white page without any output is displayed, 
open config.php in a text editor, go to the bottom of the file and set:

    $developmentMode = true;

This should output PHP warning messages which are otherwise supressed.

-------------------------------------------------------------------------------

Documentation
-------------
Consult the DOC file in the same directly as this file for further information 
on configuring and customizing the SWITCHwayf.
