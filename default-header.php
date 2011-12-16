<?php // Copyright (c) 2011, SWITCH - Serving Swiss Universities ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<title><?php echo getLocalString('title') ?></title> 
	<meta http-equiv="content-type" content="text/html; charset=utf-8">
	<meta name="keywords" content="Home Organisation, Discovery Service, WAYF, Shibboleth, Login, AAI">
	<meta name="description" content="Choose your home organization to authenticate">
	<script type="text/javascript" src="<?php echo $javascriptURL ?>/jquery.js"></script>
	<script type="text/javascript" src="<?php echo $javascriptURL ?>/improvedDropDown.js"></script>
	<script language="JavaScript" type="text/javascript">
	<!--
	
	// Prevent that WAYF is loaded in an IFRAME
	function preventIframeEmbedding(){
		if (top != self) {
			top.location = self.location;
		}
	}
	
	// Set focus to submit button or drop down list
	function setFocus(){
		// Skip this if we cannot access the form elements
		if (
			!document.IdPList || 
			!document.IdPList.Select
			){
			return;
		}
		
		if (
				document.IdPList.user_idp && 
				document.IdPList.user_idp.selectedIndex == 0
			){
			// Set focus to select
			document.IdPList.user_idp.focus();
		} else {
			// Set focus to submit button
			document.IdPList.Select.focus();
		}
	}
	
	// Confirm action
	function showConfirmation(){
		
		return confirm(unescape('<?php echo getLocalString('confirm_permanent_selection', 'js') ?>'));
	}
	
	// Confirm permanent selection
	function showPermanentConfirmation(){
		
		var message = unescape('<?php echo getLocalString('permanent_cookie_note', 'js') ?>');
		message = message.replace('%s', window.location.href.replace(/\?.+/, ''));
		return alert(message);
	}
	
	// Perform input validation on WAYF form
	function checkForm(){
		if(
			document.IdPList.user_idp && 
			document.IdPList.user_idp.selectedIndex == 0
		){
			alert(unescape('<?php echo getLocalString('make_selection', 'js') ?>'));
			return false;
		} else {
			if (document.IdPList.permanent && document.IdPList.permanent.checked){
				return showConfirmation();
			} else {
				return true;
			}
		}
	}
	
	// Init WAYF
	function init(){
		preventIframeEmbedding();
		
		setFocus();
		
		if (<?php echo ($userImprovedDropDownList) ? 'true' : 'false' ?>){
			
			var searchText = '<?php echo getLocalString('search_idp', 'js') ?>';
			$("#userIdPSelection:enabled option[value='-']").text(searchText);
			
			// Convert select element into improved drop down list
			$("#userIdPSelection:enabled").improveDropDown({
				iconPath:'<?php echo $imageURL ?>/drop_icon.png',
				noMatchesText: '<?php echo getLocalString('no_idp_found', 'js') ?>',
				noItemsText: '<?php echo getLocalString('no_idp_available', 'js') ?>'
			});
		}
	}
	
	// Call init function when DOM is ready
	$(document).ready(init);
	
	-->
	</script>
	<style type="text/css">
	<!--
	<? printCSS() ?>
	-->
	</style>
</head>

<body>

<div id="container">
	<div class="box">
		<div id="header">
			<a href="http://www.switch.ch/aai"><img src="<?php echo $logoURL ?>" alt="SWITCHaai" id="federationLogo"></a>
			<a href="http://www.switch.ch/"><img src="<?php echo $imageURL ?>/switch-logo.png" alt="SWITCH" id="organisationLogo"></a>
		</div>
			<div id="content">
				<ul class="menu">
				  <li><a href="http://www.switch.ch/<?php echo $language ?>/aai/about/"><?php echo getLocalString('about_federation'); ?></a></li>
				  <li class="last"><a href="http://www.switch.ch/<?php echo $language ?>/aai/faq/"><?php echo getLocalString('faq') ?></a></li>
				  <li class="last"><a href="http://www.switch.ch/<?php echo $language ?>/aai/help/"><?php echo getLocalString('help') ?></a></li>
				  <li class="last"><a href="http://www.switch.ch/<?php echo $language ?>/aai/privacy/"><?php echo getLocalString('privacy') ?></a></li>
				</ul>
<!-- Body: Start -->
