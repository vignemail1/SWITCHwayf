<?php // Copyright (c) 2013, SWITCH - Serving Swiss Universities

// Localized language strings for SWITCHwayf
// Make sure to use HTML entities instead of plain UTF-8 characters for 
// non-ASCII characters if you are using the Embedded WAYF. It could be that the
// Embedded WAYF is used on non-UTF8 web pages, which then could cause 
// encoding issues

// *********************************************************************************
// If you want locales in your own language here, please send them to aai@switch.ch
// *********************************************************************************

// English, default
$langStrings['en'] = array (

// To permanently customize locales such that they are not overwritten by updates
// of the SWITCHwayf, create a file 'custom-languages.php' and override any 
// individual locale in the $langStrings array. For example like this:
// 
// $langStrings['en']['about_federation'] = 'About Example Federation';
// $langStrings['en']['additional_info'] = 'My <b>sample HTML content</b>';
// 
//
// Set a locale to an empty string ('') in order to hide it
// Note that any string in custom-languages.php will survive updates

// In particular you might want to override these three locales or set the
// to an empty string in order to hide them if they are not needed.
'about_federation' => 'About AAI',  // This string can be hidden by setting it to ''
'about_organisation' => 'About SWITCH', // This string can be hidden by setting it to ''
'additional_info' => '<a href="http://www.switch.ch/" target="_blank">SWITCH</a> provides innovative, unique internet services for the Swiss universities and internet users.', // This string can be hidden by setting it to ''

// Generic strings
'faq' => 'FAQ', // This string can be hidden by setting it to ''
'help' => 'Help',// This string can be hidden by setting it to ''
'privacy' => 'Privacy', // This string can be hidden by setting it to ''
'title' => 'Home Organisation Selection',
'header' => 'Select your Home Organisation', 
'make_selection' => 'You must select a valid Home Organisation.',
'settings' => 'Default Home Organisation for this web browser',
'permanent_select_header' => 'Permanently set your Home Organisation',
'permanent_cookie' => 'On this page you can set a <strong>default Home Organisation</strong> for this web browser. Setting a default Home Organisation will henceforth redirect you directly to your Home Organisation when you access AAI services. Don\'t use this feature if you use several AAI accounts.',
'permanent_cookie_notice' => 'A default setting for your Home Organisation has the effect that you don\'t need to select your Home Organisation anymore when accessing AAI services with this web browser. The default setting is:',
'permanent_cookie_note' => 'You can reset the default setting by going to: %s',
'delete_permanent_cookie_button' => 'Reset',
'goto_sp' => 'Save and continue to your Home Organisation',
'permanently_remember_selection' => 'Remember selection permanently and bypass this step from now on.',
'confirm_permanent_selection' => 'Are you sure that you want to set the selected entry as your default Home Organisation? Don\'t proceed if you have user accounts at multiple organisations.',
'save_button' => 'Save',
'access_host' => 'In order to access a service on host <code>\'%s\'</code> please select or search the organisation you are affiliated with.',
'select_idp' => 'Select the organisation you are affiliated with',
'search_idp' => 'Type the name of the organisation you are affiliated with',
'remember_selection' => 'Remember selection for this web browser session.',
'invalid_user_idp' => 'There may be an error in the data you just submitted.<br>The value of your input <code>\'%s\'</code> is invalid.<br>Only the following values are allowed:',
'contact_assistance' => 'Please contact <a href="mailto:%s">%s</a> for assistance.',
'no_arguments' => 'No arguments received!',
'arguments_missing' => 'The web server received an invalid query because there are some arguments missing<br>The following arguments were received:',
'valid_request_description' => 'A valid request needs at least the arguments <code>shire</code> and <code>target</code> with valid values. Optionally the arguments <code>providerID</code>, <code>origin</code> and <code>redirect</code> can be supplied to automtically redirect the web browser to a Home Organisation and to do that automatically for the current web browser session',
'valid_saml2_request_description' => 'A valid SAML2 request needs at least the arguments <code>entityID</code> and <code>return</code> with valid values. Instead of the <code>return</code> argument, metadata for the Service Provider can include a <code>DiscoveryResponse</code> endpoint. Optionally the arguments <code>isPassive</code>, <code>policy</code> and <code>returnIDParam</code> can be supplied to automtically redirect the web browser to a Home Organisation and to do that automatically for the current web browser session',
'invalid_query' => 'Error: Invalid Query',
'select_button' => 'Select',
'login' => 'Login',
'login_with' => 'Login with:',
'other_federation' => 'From other federations',
'logged_in' => 'You are already authenticated. <a href=\"%s\">Proceed</a>.',
'most_used' => 'Most frequently used Home Organisations',
'invalid_return_url' => 'The return URL <code>\'%s\'</code> is not a valid URL.',
'unverified_return_url' => 'The return URL <code>\'%s\'</code> could not be verified for Service Provider <code>\'%s\'</code>.',
'unknown_sp' => 'The Service Provider <code>\'%s\'</code> could not be found in metadata and is therefore unknown.',
'no_idp_found' => 'No Home Organisation found for this search text',
'no_idp_available' => 'No Home Organisation available',

);


// Deutsch
$langStrings['de'] = array (
// Read note on line 16 how to properly customize locales so that they survive updates
'about_federation' => '&Uuml;ber AAI',  // This string can be hidden by setting it to ''
'about_organisation' => '&Uuml;ber SWITCH',  // This string can be hidden by setting it to ''
'additional_info' => '<a href="http://www.switch.ch/" target="_blank">SWITCH</a> erbringt innovative, einzigartige Internet-Dienstleistungen f&uuml;r die Schweizer Hochschulen und Internetbenutzer.',  // This string can be hidden by setting it to ''

// Generic strings
'faq' => 'FAQ',  // This string can be hidden by setting it to ''
'help' => 'Hilfe', // This string can be hidden by setting it to ''
'privacy' => 'Datenschutz', // This string can be hidden by setting it to ''
'title' => 'Auswahl der Home Organisation',
'header' => 'Home Organisation ausw&auml;hlen',
'make_selection' => 'Sie m&uuml;ssen eine g&uuml;ltige Home Organisation ausw&auml;hlen',
'settings' => 'Standard Home Organisation f&uuml;r diesen Webbrowser',
'permanent_select_header' => 'Home Organisation speichern',
'permanent_cookie' => 'Auf dieser Seite k&ouml;nnen Sie die <strong>Standardeinstellung Ihrer Home Organisation</strong> f&uuml;r diesen Webbrowser dauerhaft zu speichern. Sie werden darauf beim Zugriff auf AAI Dienste jedes Mal direkt zur Loginseite Ihrer Home Organisation weitergeleitet. Dies wird jedoch nicht empfohlen wenn sie mehrere AAI Benutzerkonnten verwenden.',
'permanent_cookie_notice' => 'Wenn Sie die folgende Home Organisation als Standardeinstellung speichern, werden Sie jedes Mal automatisch zu deren Login Seite weitergeleitet, wenn Sie auf AAI Dienste zugreifen. Die Einstellung lautet momentan:',
'permanent_cookie_note' => 'Sie k&ouml;nnen die Home Organisation Einstellung zur&uuml;cksetzen auf der Seite: %s',
'delete_permanent_cookie_button' => 'Zur&uuml;cksetzen',
'goto_sp' => 'Speichern und weiter zur Home Organisation',
'permanently_remember_selection' => 'Auswahl permanent speichern und diesen Schritt von jetzt an &uuml;berspringen.',
'confirm_permanent_selection' => 'Sind Sie sicher, dass Sie die Auswahl als Home Organisation Einstellung speichern wollen? Dies ist nicht empfehlenswert, wenn Sie Benutzerkonten bei mehreren Organisationen besitzen.',
'save_button' => 'Speichern',
'access_host' => 'Um auf einen Dienst auf dem Server <code>\'%s\'</code> zuzugreifen, w&auml;hlen oder suchen Sie bitte die Organisation, der Sie angeh&ouml;ren.',
'select_idp' => 'W&auml;hlen Sie die Organisation aus, der Sie angeh&ouml;ren',
'search_idp' => 'Tippen Sie den Namen der Organisation, der Sie angeh&ouml;ren',
'remember_selection' => 'Auswahl f&uuml;r die laufende Webbrowser Sitzung speichern.',
'invalid_user_idp' => 'M&ouml;glicherweise sind die &uuml;bermittelten Daten fehlerhaft.<br>Der Wert der Eingabe <code>\'%s\'</code> ist ung&uuml;ltig.<br>Es sind ausschliesslich die folgenden Wert erlaubt:',
'contact_assistance' => 'F&uuml;r Unterst&uuml;tzung und Hilfe, kontaktieren Sie bitte <a href="mailto:%s">%s</a>.',
'no_arguments' => 'Keine Argumente erhalten!',
'arguments_missing' => 'Der Webserver hat eine fehlerhafte Anfrage erhalten da einige Argumente in der Anfrage fehlen.<br>Folgende Argumente wurden empfangen:',
'valid_request_description' => 'Eine g&uuml;ltige Anfrage muss mindestens die Argumente <code>shire</code> und <code>target</code> enthalten. Zus&auml;tzlich k&ouml;nnen die Argumente <code>providerID</code>, <code>origin</code> und <code>redirect</code> benutzt werden um den Webbrowser automatisch an die Home Organisation weiter zu leiten und um sich die ausgew&auml;hlte Home Organisation f&uuml;r l&auml;ngere Zeit zu merken.',
'valid_saml2_request_description' => 'Eine g&uuml;ltige Anfrage muss mindestens die Argumente <code>entityID</code> und <code>return</code> enthalten. Anstatt dem Argument <code>return</code> k&ouml;nnen die Metadaten f&uuml;r den Service Provider einen <code>DiscoveryResponse</code> Endpunkt enthalten. Zus&auml;tzlich k&ouml;nnen die Argumente <code>isPassive</code>, <code>policy</code> und <code>returnIDParam</code> benutzt werden um den Webbrowser automatisch an die Home Organisation weiter zu leiten und um sich die ausgew&auml;hlte Home Organisation f&uuml;r l&auml;ngere Zeit zu merken.',
'invalid_query' => 'Error: Fehlerhafte Anfrage',
'select_button' => 'Ausw&auml;hlen',
'login' => 'Anmelden',
'login_with' => 'Anmelden &uuml;ber:',
'other_federation' => 'Von anderen F&ouml;derationen',
'logged_in' => 'Sie sind bereits angemeldet. <a href=\"%s\">Weiter</a>.',
'most_used' => 'Meist genutzte Home Organisationen',
'invalid_return_url' => 'Die return URL <code>\'%s\'</code> ist keine g&uuml;tige URL.',
'unverified_return_url' => 'Die return URL <code>\'%s\'</code> ist nicht g&uuml;tige f&uuml;r den Service Provider <code>\'%s\'</code>.',
'unknown_sp' => 'Der Service Provider <code>\'%s\'</code> konnte nicht in den Metadaten gefunden werden und ist deshalb unbekannt.',
'no_idp_found' => 'Keine Home Organisation gefunden f&uuml;r diesen Suchtext',
'no_idp_available' => 'Keine Home Organisation verf&uuml;gbar',
);


// Francais
$langStrings['fr'] =  array (
// Read note on line 16 how to properly customize locales so that they survive updates
'about_federation' => '&Agrave; propos de l\'AAI', // This string can be hidden by setting it to ''
'about_organisation' => '&Agrave; propos de SWITCH', // This string can be hidden by setting it to ''
'additional_info' => '<a href="http://www.switch.ch/" target="_blank">SWITCH</a> fournit des prestations innovantes et uniques pour les hautes &eacute;coles suisses et les utilisateurs d\'Internet.', // This string can be hidden by setting it to ''

// Generic strings
'faq' => 'FAQ', // This string can be hidden by setting it to ''
'help' => 'Aide',// This string can be hidden by setting it to ''
'privacy' => 'Protection des donn&eacute;es',// This string can be hidden by setting it to ''
'title' => 'S&eacute;lection de votre Home Organisation',
'header' => 'S&eacute;lectionnez votre Home Organisation',
'make_selection' => 'Vous devez s&eacute;lectionner une Home Organisation valide.',
'settings' => 'Home Organisation par d&eacute;faut pour ce navigateur',
'permanent_select_header' => 'S&eacute;lection d\'une Home Organisation de fa&ccedil;on permanente',
'permanent_cookie' => 'Sur cette page vous pouvez d&eacute;finir une <strong>Home Organisation par d&eacute;faut</strong> pour ce navigateur. En d&eacute;finissant une Home Organisation par d&eacute;faut, vous serez automatiquement redirig&eacute; vers cette Home Organisation lorsque vous acc&eacute;dez &agrave; une ressource AAI. N\'utilisez pas cette fonction si vous avez plusieurs identit&eacute;s AAI.',
'permanent_cookie_notice' => 'En choisissant une Home Organisation par d&eacute;faut, vous ne devez plus s&eacute;lectionner votre Home Organisation dans la liste lorsque vous acc&eacute;dez &agrave; une ressource AAI avec ce navigateur. D&eacute;faut : ',
'permanent_cookie_note' => 'Vous pouvez r&eacute;initialiser la propri&eacute;t&eacute; par d&eacute;faut en allant &agrave; l\'adresse: %s',
'delete_permanent_cookie_button' => 'R&eacute;initialiser',
'goto_sp' => 'Sauver et continuez vers votre Home Organisation',
'permanently_remember_selection' => 'Se souvenir de mon choix d&eacute;finitivement et contourner cette &eacute;tape &agrave; partir de maintenant.',
'confirm_permanent_selection' => '&Ecirc;tes-vous s&ucirc; de vouloir d&eacute;finir votre s&eacute;lection comme votre Home Organisation par d&eacute;faut ? N\'utilisez pas cette fonction si vous avez plusieurs identit&eacute;s AAI.',
'save_button' => 'Sauver',
'access_host' => 'Pour acc&eacute;der au service <code>\'%s\'</code> s&eacute;lectionnez ou cherchez l\'&eacute;tablissement auquel vous &ecirc;tes rattach&eacute;.',
'select_idp' => 'Veuillez s&eacute;lectionner l\'organisation &agrave; laquelle vous appartenez.',
'search_idp' => 'Veuillez taper le nom de l\'organisation &agrave; laquelle vous appartenez.',
'remember_selection' => 'Se souvenir de mon choix pour cette session.',
'invalid_user_idp' => 'Une erreur s\'est produite.<br>La valeur de votre donn&eacute;e <code>\'%s\'</code> n\'est pas valide.<br>Seules ces valeurs sont admises :',
'contact_assistance' => 'Contactez le support <a href="mailto:%s">%s</a> si l\'erreur persiste.',
'no_arguments' => 'Pas de param&egrave;tre re&ccedil;u !',
'arguments_missing' => 'La requ&ecirc;te n\'est pas valide, certains param&egrave;tres sont manquants.<br>Les param&egrave;tres suivants ont &eacute;t&eacute; re&ccedil;us :',
'valid_request_description' => 'Une requ&ecirc;te valide doit contenir au moins les param&egrave;tres <code>shire</code> et <code>target</code>. Les param&egrave;tres optionnels <code>providerID</code>, <code>origin</code> et <code>redirect</code> peuvent &ecirc;tre utilis&eacute;s pour rediriger automatiquement le navigateur vers une Home Organisation.',
'valid_saml2_request_description' => 'Une requ&ecirc;te valide doit contenir au moins les param&egrave;tres <code>entityID</code> et <code>return</code>. Au lieu de param&egrave;tre <code>return</code>, metadata pour ce Service Provider peut contenir un URL pour le <code>DiscoveryResponse</code>. Les param&egrave;tres optionnel <code>isPassive</code>, <code>policy</code> et <code>returnIDParam</code> peuvent &ecirc;tre utilis&eacute;s pour rediriger automatiquement le navigateur vers une Home Organisation.',
'invalid_query' => 'Erreur : La requ&ecirc;te n\'est pas valide',
'select_button' => 'S&eacute;lection',
'login' => 'Connexion',
'login_with' => 'Se connecter avec:',
'other_federation' => 'D\'autres f&eacute;derations',
'logged_in' => 'Vous &ecirc;tes d&eacute;j&agrave; authentifi&eacute;. <a href=\"%s\">Continuez</a>.',
'most_used' => 'Home Organisations les plus utilis&eacute;es',
);


// Italian
$langStrings['it'] = array (
// Read note on line 16 how to properly customize locales so that they survive updates
'about_federation' => 'Informazioni su AAI', // This string can be hidden by setting it to ''
'about_organisation' => 'Informazioni su SWITCH', // This string can be hidden by setting it to ''
'additional_info' => '<a href="http://www.switch.ch/" target="_blank">SWITCH</a> eroga servizi Internet innovativi e unici per le scuole universitarie svizzere e per gli utenti di Internet.', // This string can be hidden by setting it to ''

// Generic strings
'faq' => 'FAQ', // This string can be hidden by setting it to ''
'help' => 'Aiuto', // This string can be hidden by setting it to ''
'privacy' => 'Protezione dei dati', // This string can be hidden by setting it to ''
'title' => 'Selezione della vostra Home Organisation',
'header' => 'Selezioni la sua Home Organisation',
'make_selection' => 'Per favore, scelga una valida Home Organisation.',
'settings' => 'Home Organisation predefinita per questo web browser.',
'permanent_select_header' => 'Salvare la Home Organisation.',
'permanent_cookie' => 'In questa pagina pu&ograve; impostare la <strong>Home Organisation predefinita</strong> per questo web browser. Impostare una Home Organisation predefinita consentir&agrave; al suo web browser di venir reindirizzato alla sua Home Organisation automaticamente ogni qual volta lei tenter&agrave; di accedere a risorse AAI per le quali necessita un\'autentificazione. Non &egrave; da impostare se lei possiede e usa correntemente differenti account AAI.',
'permanent_cookie_notice' => 'Se sceglie di impostare una Home Organisation predefinita, la sua scelta verr&agrave; ricordata e non dovr&agrave; pi&ugrave; preoccuparsene quando acceder&agrave; a risorse AAI con questo web browser. L\'impostazione predefinita &egrave;:',
'permanent_cookie_note' => 'Pu&ograve; cambiare la sua impostazione predefinita sulla pagina: %s',
'delete_permanent_cookie_button' => 'Cancella',
'goto_sp' => 'Salvare e proseguire verso la Home Organisation',
'permanently_remember_selection' => 'Salvare la scelta permanentemente e non passare pi&ugrave; per il WAYF.',
'confirm_permanent_selection' => 'E\' sicuro di voler impostare la Home Organisation selezionata come sua Home Organisation predefinita? Non &egrave; da impostare se usa regolarmente diversi account AAI.',
'save_button' => 'Salva',
'access_host' => 'Per poter accedere alla risorsa sull\' host <code>\'%s\'</code> per favore selezioni o cerchi l\'organizzazione con la quale &egrave; affiliato.',
'select_idp' => 'Selezioni l\'organizzazione con la quale &egrave; affiliato.',
'search_idp' => 'Digitare il nome dell\'organizzazione con cui e\' affiliato.',
'remember_selection' => 'Ricorda la selezione per questa sessione.',
'invalid_user_idp' => 'Errore nei parametri pervenuti.<br>Il valore del parametro <code>\'%s\'</code> non &#143; valido.<br>Solo i seguenti valori sono ammessi:',
'contact_assistance' => 'Se l\' errore persiste, si prega di contattare <a href="mailto:%s">%s</a>.',
'no_arguments' => 'Parametri non pervenuti!',
'arguments_missing' => 'La richiesta non &egrave; valida per la mancanza di alcuni parametri. <br>I seguenti parametri sono stati ricevuti:',
'valid_request_description' => 'Una richiesta valida &egrave; deve contenere almeno i parametri <code>shire</code> e <code>target</code>. I parametri opzionali <code>providerID</code>, <code>origin</code> e <code>redirect</code> possono essere utilizzati per ridirigere automaticamente il browser web verso una Home Organisation.',
'valid_saml2_request_description' => 'Una richiesta valida &egrave; deve contenere almeno i parametri <code>entityID</code> e <code>return</code>. I parametri opzionali <code>isPassive</code>, <code>policy</code> e <code>returnIDParam</code> possono essere utilizzati per ridirigere automaticamente il browser web verso una Home Organisation.',
'invalid_query' => 'Errore: Richiesta non Valida',
'select_button' => 'Seleziona',
'login' => 'Login',
'login_with' => 'Login con:',
'other_federation' => 'Di altra federaziones',
'logged_in' => 'Lei &egrave; gi&agrave; autenticato. <a href=\"%s\">Proseguire</a>.',
'most_used' => 'Home Organisations utilizzate pi&ugrave; spesso',
);


// Portuguese
$langStrings['pt'] = array (
// Read note on line 16 how to properly customize locales so that they survive updates
'about_federation' => 'Sobre AAI', // This string can be hidden by setting it to ''
'about_organisation' => 'Sobre a SWITCH', // This string can be hidden by setting it to ''
'additional_info' => 'A SWITCH foundation &eacute; uma institui&ccedil;&atilde;o gere e opera a rede de investiga&ccedil;&atilde;o e ensino sui&ccedil;a por forma a garantir conectividade de alto desempenho &agrave; Internet e a redes de I&amp;D globais para o beneficio de uma educa&ccedil;&atilde;o superior na sui&ccedil;a', // This string can be hidden by setting it to ''

// Generic strings
'faq' => 'FAQ', // This string can be hidden by setting it to ''
'help' => 'Ajuda', // This string can be hidden by setting it to ''
'privacy' => 'Privacidade', // This string can be hidden by setting it to ''
'title' => 'Selec&ccedil;&atilde;o de Institui&ccedil;&atilde;o de Origem',
'header' => 'Seleccione a sua Institui&ccedil;&atilde;o de Origem',
'make_selection' => 'Dever&aacute; seleccionar uma Institui&ccedil;&atilde;o de Origem V&aacute;lida',
'settings' => 'Institui&ccedil;&atilde;o de Origem por defeito para este web browser',
'permanent_select_header' => 'Defina permanentemente a sua Institui&ccedil;&atilde;o de Origem',
'permanent_cookie' => 'Nesta p&aacute;gina poder&aacute; definir a sua <strong>Institui&ccedil;&atilde;o de Origem</strong> para este web browser. Defenir uma Institui&ccedil;&atilde;o de Origem levar&aacute; a que seja redireccionado directamente para a sua Institui&ccedil;&atilde;o de Origem aquando do acesso de recursos-AAI. N&atilde;o use esta funcionalidade se possuir v&aacute;rias contas de AAI.',
'permanent_cookie_notice' => 'Por omiss&atilde;o a configura&ccedil;&atilde;o da sua institui&ccedil;&atilde;o de origem ter&acute; a funcionalidade de n&atilde;o ser necess&acute;rio seleccionar novamente recursos federados. A configura&ccedil;&atilde;o &ecute;:',
'permanent_cookie_note' => 'Poder&aacute; efectuar um reset &agrave;s configura&ccedil;&otilde;es no url %s',
'delete_permanent_cookie_button' => 'Reset',
'goto_sp' => 'Salve e continue para a sua Institui&ccedil;&atilde;o de Origem',
'permanently_remember_selection' => 'Memorize a sua selec&ccedil;&atilde;o permanentemente e passe o mecanismo WAYF apartir de agora.',
'confirm_permanent_selection' => 'Tem a certeza que pretende seleccionar a op&ccedil;&atilde;o escolhida como a sua institui&ccedil;&atilde;o de origem? N&atilde;o seleccione se possui v&aacute;rias contas AAI.',
'save_button' => 'Guarde',
'access_host' => 'No sentido de aceder ao recurso em <code>\'%s\'</code> dever&aacute; autenticar-se.',
'select_idp' => 'Seleccione a sua Institui&ccedil;&atilde;o de Origem',
'remember_selection' => 'Memorize a selec&ccedil;&atilde;o para esta sess&atilde;o.',
'invalid_user_idp' => 'Poder&aacute; existir um erro nos dados que enviou.<br>Os valores enviados <code>\'%s\'</code> s&atilde;o inv&aacute;lidos.<br>Apenas os valores seguintes s&atilde;o permitidos:',
'contact_assistance' => 'Contacte <a href="mailto:%s">%s</a> para assistencia.',
'no_arguments' => 'Nenhum argumento recebido!',
'arguments_missing' => 'O servidor web recebeu uma query inv&acute;lida devido &agrave; falta de alguns argumentos. Foram recebidos os seguintes argumentos:',
'valid_request_description' => 'Um pedido v&acute;lido necessita de pelo menos dos atributos <code>shire</code> e <code>target</code> com valores v&acute;lidos. Opcionalmente os argumentos <code>providerID</code>, <code>origin</code> e <code>redirect</code> podem ser fornecidos para de uma forma autom&acute;tica redireccionar o browser do utilizador.',
'invalid_query' => 'Erro: Query Invalida',
'select_button' => 'Seleccione',
'login' => 'Autenticar',
'login_with' => 'Autenticar em:',
'other_federation' => 'Outra Federa&ccedil;Atilde;o',
'logged_in' => 'J&aacute; se encontra autenticado. <a href=\"%s\">Continue</a>.',
'most_used' => 'Institui&ccedil;&atilde;o de Origem mais utilizada',
);


// Japanese
$langStrings['ja'] = array (
// Read note on line 16 how to properly customize locales so that they survive updates
'about_federation' => 'フェデレーションとは', // This string can be hidden by setting it to ''
'about_organisation' => '学認とは', // This string can be hidden by setting it to ''
'additional_info' => '<a href="http://www.gakunin.jp/" target="_blank">GakuNin</a>は，学術認証フェデレーションの略です．', // This string can be hidden by setting it to ''

// Generic strings
'faq' => 'FAQ', // This string can be hidden by setting it to ''
'help' => 'ヘルプ', // This string can be hidden by setting it to ''
'privacy' => 'プライバシー', // This string can be hidden by setting it to ''
'title' => '所属機関選択',
'header' => '所属機関の選択',
'make_selection' => '正しい所属機関を選んで下さい',
'settings' => 'このブラウザで利用するデフォルト所属機関',
'permanent_select_header' => '所属機関情報の保存',
'permanent_cookie' => 'このブラウザで利用する<strong>デフォルト所属機関</strong>を保存できます．この設定により，サービスで機関認証を選択した場合に，再び所属機関のIdPを選択することなく，直接機関のIdPにリダイレクトされます．いくつかのアカウントを使い分けている場合には，この機能を利用しないで下さい．',
'permanent_cookie_notice' => 'デフォルトの所属機関を選択することで，このブラウザで他のサービスにアクセスした場合に，IdPの選択画面をスキップすることができます．<br>現在セット中のデフォルト所属機関は:',
'permanent_cookie_note' => '次のURLにアクセスすることで，デフォルトセッティングをリセットできます: %s',
'delete_permanent_cookie_button' => 'リセット',
'goto_sp' => '所属機関を保存して次へ',
'permanently_remember_selection' => '選択した所属機関を保存して今後IdPの選択画面をスキップする',
'confirm_permanent_selection' => '選択した機関をデフォルト所属機関として保存してもよいですか？　いくつかのアカウントを使い分けている場合にはこの機能を利用しないで下さい．',
'save_button' => '保存',
'access_host' => 'サービス<tt>\'%s\'</tt>を利用するために認証が必要です',
'select_idp' => '所属している機関を選択',
'search_idp' => '所属している機関を入力',
'remember_selection' => 'ブラウザ起動中は自動ログイン',
'invalid_user_idp' => '入力したIdPの情報（<tt>\'%s\'</tt>）に誤りがあります<br>以下の値のみが入力可能です:',
'contact_assistance' => '問い合わせ先：<a href="mailto:%s">%s</a>',
'no_arguments' => '引数が送られてきませんでした',
'arguments_missing' => 'ブラウザが無効なクエリを受付ました．いくつかの必要な引数が不足しています．<br>以下の引数を受けつけました．:',
'valid_request_description' => '有効なリクエストでは少なくとも，<tt>shire</tt>と<tt>target</tt>の適正な値を必要とします．オプショナルな引数である<tt>providerID</tt>，<tt>origin</tt>や<tt>redirect</tt>を送信することにより，ウェブブラウザを所属機関にIdPに自動的にリダイレクトさせることができます．',
'valid_saml2_request_description' => '有効なSAML2のリクエストでは少なくとも，<tt>entityID</tt>と<tt>return</tt>の適正な値を必要とします．オプショナルな引数である<tt>isPassive</tt>, <tt>policy</tt>や<tt>returnIDParam</tt>を送信することにより，ウェブブラウザを所属機関にIdPに自動的にリダイレクトさせることができます．',
'invalid_query' => 'エラー: 無効なクエリです',
'select_button' => '選択',
'login' => '選択',
'login_with' => '所属機関:',
'other_federation' => '他のフェデレーションから',
'logged_in' => '認証済 <a href=\"%s\">進む</a>.',
'no_idp_found' => 'この検索キーでは機関が見つかりません',
'no_idp_available' => '使用できる機関がありません',
);
?>