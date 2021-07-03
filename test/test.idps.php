<?php

// Copyright (c) 2019 Geoffroy Arnoud, Guillaume Rousse, and SWITCHwayf contributors

$metadataIDProviders = array(
  'http://idp-condorcet.dev.entrouvert.org/idp/saml2/metadata' =>
  array(
    'SSO' => 'http://idp-condorcet.dev.entrouvert.org/idp/saml2/sso',
    'Name' => 'Campus Condorcet - POC RICCO Project',
    'en' =>
    array(
      'Name' => 'Campus Condorcet - POC RICCO Project',
    ),
    'fr' =>
    array(
      'Name' => 'Campus Condorcet - POC Projet RICCO',
    ),
    'Protocols' => 'urn:oasis:names:tc:SAML:2.0:protocol',
  ),
  'http://idp-pre.math.cnrs.fr/idp/shibboleth' =>
  array(
    'SSO' => 'http://idp-pre.math.cnrs.fr/idp/profile/Shibboleth/SSO',
    'Name' => 'idp de test mathrice-plm-team-bdx-novembre-2016',
    'en' =>
    array(
      'Name' => 'idp de test mathrice-plm-team-bdx-novembre-2016',
    ),
    'fr' =>
    array(
      'Name' => 'idp de test mathrice-plm-team-bdx-novembre-2016',
    ),
    'Protocols' => 'urn:oasis:names:tc:SAML:1.1:protocol urn:mace:shibboleth:1.0 urn:oasis:names:tc:SAML:2.0:protocol',
  ),'https://195.220.94.102/idp/shibboleth' =>
    array(
        'SSO' => 'https://195.220.94.102/idp/profile/SAML2/Redirect/SSO',
        'Name' => 'Idp test G.Arnoud',
        'en' =>
        array(
          'Name' => 'Idp test G.Arnoud EN',
        ),
        'fr' =>
        array(
          'Name' => 'Idp test G.Arnoud FR',
        ),
        'Protocols' => 'urn:oasis:names:tc:SAML:2.0:protocol',
        'Logo' =>
          array(
              'URL' => 'data:image/gif;base64,R0lGODlhEAAQADUMACH5BAEAAAwAIfkEAQAADAAsAAAAABAAEACDREJExLqk/OZ07ObM7L5c1JIs/Pbs5Na8XFpc/PKM/Npk/P78////AAAAAAAAAAAABHCQMURrlXguszrvSIZ83DcMRighaOca8KAyQICaQ6DPtD1sOUIBcKEVbLCAQgj4LUKAAsGnZAIATyNBETAklkOaTKsQeMEAMVTKzaFph/VWEPhaD3GyIGEf4lU1bHxCAQCGGAB4BwSMimkZNIYWhxkRACH+DmF1dG9tYXR0aWNfaW5jADs=',
            ),
          )
);
