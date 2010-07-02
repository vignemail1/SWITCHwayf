<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<title><?php echo getLocalString('title') ?></title> 
	<meta http-equiv="content-type" content="text/html; charset=utf-8">
<style type="text/css">
<!--
	
	body
	{
		color: #000000;
		background-color: #EFF1F1;
		font-family: Verdana, Arial, Helvetica, sans-serif;
		font-size: 12px;
	}
	
	a 
	{
		color: #203781; 
		font-family: Verdana, Arial, Helvetica, sans-serif;
		font-size: 12px;
	}
	
	a:hover
	{
		color: #203781;
		text-decoration: underline;
	}
	
	h2.switchaai
	{
		font-family: Verdana, Arial, Helvetica, sans-serif;
		color: #000000;
		font-size: 17px;
	}
	
	h1
	{
		font-family: Verdana, Arial, Helvetica, sans-serif;
		color: #000000;
		font-size: 18px;
	}
	
	p
	{
		color: #000000;
		font-family: Verdana, Arial, Helvetica, sans-serif;
		line-height: 1.2;
		font-size: 12px;
	}
	
	b
	{
		color: #000000;
		font-family: Verdana, Arial, Helvetica, sans-serif;
		line-height: 1.2;
		font-size: 12px;
		font-weight: bold;
	}
	
	tt
	{
		line-height: 1.2;
		font-weight: bold;
	}
	
	span.switchaai
	{
		line-height: 30px;
	}
	
	input.switchaai
	{
		border-width: 1px;
		border-style: solid;
		border-color: #888888;
	}
	
	a.switchaai
	{
		font-family: Verdana, Arial, Helvetica, sans-serif;
		color: #000000;
		font-size: 12px;
	}
	
	.outer-box
	{
		margin-left:auto; margin-right:auto;
		border-style: solid;
		border-color: #00247D;
		border-width: 1px;
		padding: 10px;
		text-align: left;
		background-color: white;
	}
	
	.selectedIdP
	{
		font-family: Verdana, Arial, Helvetica, sans-serif;
		color: #000000;
		font-size: 12px;
		background-color: white;
		border-color: #203781;
		border-style: solid;
		margin: 2px;
		border-width: 1px;
		width: 400px;
		height: 25px;
		text-align: center;
		line-height: 25px;
	}
	
	.fullheight
	{
		height: 100%;
		min-height: 100%;
	}
	
	.inner-box
	{
		border-width: 1px;
		border-color: #203781;
		background-color: #979CE3;
		border-style: solid;
		padding: 3px;
	}

-->
</style>
</head>
<body bgcolor="#ffffff" onLoad="if (document.IdPList && document.IdPList.Select) document.IdPList.Select.focus()">
<script language="JavaScript" type="text/javascript">
<!--
function showConfirmation(){
	
	return confirm(unescape('<?php echo getLocalString('confirm_permanent_selection', 'js') ?>'));
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
<table border="0" cellpadding="0" cellspacing="0" style="width:100%; height:100%">
	<tr>
		<td align="center" valign="middle">
			<table border="0" cellpadding="0" cellspacing="0" width="600" class="outer-box">
				<tr>
					<td class="switchaai">
						<a href="http://www.switch.ch/aai/" target="_blank"><img src="<?php echo $logoURL ?>" border="0" class="switchaai" alt="Federation Logo"></a>
						<br>
						<a href="http://www.switch.ch/<?php echo $language ?>/aai/about/" class="switchaai-link"><?php echo getLocalString('about_aai'); ?></a>&nbsp;:&nbsp;<a href="http://www.switch.ch/<?php echo $language ?>/about/" class="switchaai-link"><?php echo getLocalString('about_switch'); ?></a>&nbsp;:&nbsp;<a href="http://www.switch.ch/<?php echo $language ?>/aai/faq/" class="switchaai-link"><?php echo getLocalString('faq') ?></a>&nbsp;:&nbsp;<a href="http://www.switch.ch/<?php echo $language ?>/aai/help/" class="switchaai-link"><?php echo getLocalString('help') ?></a>&nbsp;:&nbsp;<a href="http://www.switch.ch/<?php echo $language ?>/aai/privacy/" class="switchaai-link"><?php echo getLocalString('privacy') ?></a>
<!-- Body: Start -->
