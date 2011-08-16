<?php // Copyright (c) 2011, SWITCH - Serving Swiss Universities ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html><head>
	<title><?php echo getLocalString('title') ?></title> 
	<meta http-equiv="content-type" content="text/html; charset=utf-8">
	<meta name="keywords" content="Discovery Service, WAYF, Shibboleth">
	<meta name="description" content="Choose your home organization to authenticate">
	<style type="text/css"><!--
	<? printCSS() ?>
-->
</style>
</head>

<body onLoad="preventIframeEmbedding(); setFocus();">
<script language="JavaScript" type="text/javascript">
<!--

function preventIframeEmbedding(){
	if (top != self) {
		top.location = self.location;
	}
	
}

function setFocus(){
	// Skip this if we cannot access the form elements
	if (!document.IdPList || !document.IdPList.Select){
		return;
	}
	
	// Set focus to submit button unless autofocus is supported
	if (!("autofocus" in document.createElement("input"))) {
		document.IdPList.Select.focus();
	}
}

function showConfirmation(){
	
	return confirm(unescape('<?php echo getLocalString('confirm_permanent_selection', 'js') ?>'));
}

function showPermanentConfirmation(){
	
	return alert(unescape('<?php echo getLocalString('permanent_cookie_note', 'js') ?>'));
}

function checkForm(){
	if(document.IdPList.user_idp && document.IdPList.user_idp.selectedIndex == 0){
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

-->
</script>

<div class="outer_container">
	<div class="wayf_box">
		<div class="header">
			<a href="http://www.switch.ch/aai"><img src="<?php echo $imageURL ?>/switchaai-logo.gif" alt="SWITCHaai" class="aai_logo"><a href="http://www.switch.ch/"><img src="<?php echo $imageURL ?>/switch-logo.gif" alt="SWITCH" class="switch_logo" align="right"></a>
		</div>
			<div class="content">
			<ul class="menu">
			  <li class="last"><a href="http://www.switch.ch/<?php echo $language ?>/aai/about/"><?php echo getLocalString('about_aai'); ?></a></li>
			  <li class="last"><a href="http://www.switch.ch/<?php echo $language ?>/aai/faq/"><?php echo getLocalString('faq') ?></a></li>
			  <li class="last"><a href="http://www.switch.ch/<?php echo $language ?>/aai/help/"><?php echo getLocalString('help') ?></a></li>
			  <li class="last"><a href="http://www.switch.ch/<?php echo $language ?>/aai/privacy/"><?php echo getLocalString('privacy') ?></a></li>
			</ul>
<!-- Body: Start -->
