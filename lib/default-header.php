<?php // Copyright (c) 2019, SWITCH?>
<!DOCTYPE HTML>
<html>
<head>
	<title><?php echo getLocalString('title') ?></title>
	<!-- <meta http-equiv="content-type" content="text/html; charset=utf-8"> -->
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="keywords" content="Home Organisation, Discovery Service, WAYF, Shibboleth, Login, AAI">
	<meta name="description" content="Choose your home organisation to authenticate">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
	<link rel="stylesheet" href="<?php echo $_SERVER['SCRIPT_NAME'] ?>/styles.css" type="text/css">
	<script type="text/javascript" src="<?php echo $javascriptURL ?>/jquery.js"></script>
	<?php

    if ($useSelect2) {
        echo '<link rel="stylesheet" href="'. $_SERVER['SCRIPT_NAME'] .'/select2.css" type="text/css" >'.PHP_EOL;
        echo '<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>'.PHP_EOL;
        echo '<script type="text/javascript" src="'.$javascriptURL .'/select2Functions.js"></script>'.PHP_EOL;
    } elseif ($useImprovedDropDownList) {
        echo '<link rel="stylesheet" href="'. $_SERVER['SCRIPT_NAME'] .'/ImprovedDropDown.css" type="text/css">'.PHP_EOL;
        echo '<script type="text/javascript" src="'. $javascriptURL .'/improvedDropDown.js"></script>'.PHP_EOL;
    }

?>
	<script type="text/javascript">
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

	// Perform input validation on WAYF form
	function checkForm(){
		//console.log("checkForm ", document.IdPList.user_idp, "/", document.IdPList.user_idp.selectedIndex);
		if(
			document.IdPList.user_idp &&
			document.IdPList.user_idp.selectedIndex == 0
		){
			alert(unescape('<?php echo getLocalString('make_selection', 'js') ?>'));
			return false;
		} else {
			return true;
		}
	}

	// Perform input validation on WAYF form for select2
	function select2CheckForm() {
	  console.log("checkForm ", $('.userIdPSelection option:selected').text());
	  if (
	    document.IdPList.user_idp && (
				$('.userIdPSelection option:selected').text() == null ||
	    $('.userIdPSelection option:selected').text() == ''
		)
	  ) {
	    alert(unescape('<?php echo getLocalString('make_selection', 'js') ?>'));
	    return false;
	  } else {
	    return true;
	  }
	}

	// Init WAYF
	function init(){
		preventIframeEmbedding();

		setFocus();

		if (<?php echo ($useSelect2) ? 'true' : 'false' ?>){
			$('.userIdPSelection').select2({
			ajax: {
				url: <?php echo "'".$apiURL."/idps'" ?>,
				delay: 250,
				dataType: 'json',
				data: function (params) {
						var query = {
							search: params.term,
							page: params.page || 1
						}
						// Query parameters will be ?search=[term]&page=[page]
						return query;
					},
				error: function(jqxhr, status, exception) {
					console.error('Exception:', exception);
					<?php
            if ($developmentMode) {
                echo("alert('Exception:', exception);");
            }
          ?>
				}
			},
			placeholder: "<?php echo getLocalString('select_idp') ?>",
			allowClear: true,
			templateResult: formatList,
			templateSelection: formatRepoSelection,
			escapeMarkup: function (text) { return text; }
		});


		} else if (<?php echo ($useImprovedDropDownList) ? 'true' : 'false' ?>){

			var searchText = '<?php echo getLocalString('search_idp', 'js') ?>';
			$("#userIdPSelection:enabled option[value='-']").text(searchText);

			// Convert select element into improved drop down list
			$("#userIdPSelection:enabled").improveDropDown({
				iconPath:'<?php echo $imageURL ?>/drop_icon.png',
				noMatchesText: '<?php echo getLocalString('no_idp_found', 'js') ?>',
				noItemsText: '<?php echo getLocalString('no_idp_available', 'js') ?>',
				disableRemoteLogos: <?php echo ($disableRemoteLogos) ? 'true' : 'false' ?>
			});
		}
	}

	// Call init function when DOM is ready
	$(document).ready(init);

	-->
	</script>
</head>

<body>

<div id="container">
	<div class="box">
		<div id="header">
			<?php if (!empty($logoURL)) {
              ?>
			<a href="<?php echo sprintf($federationURL, $language) ?>"><img src="<?php echo $logoURL ?>" alt="Federation Logo" id="federationLogo"></a>
			<?php
          } ?>
			<?php if (!empty($organizationLogoURL)) {
              ?>
			<a href="<?php echo sprintf($organizationURL, $language) ?>"><img src="<?php echo $organizationLogoURL ?>" alt="Organization Logo" id="organisationLogo"></a>
			<?php
          } ?>
		</div>
			<div id="content">
				<ul class="menu">
					<?php if (!empty($federationURL) && getLocalString('about_federation') != '') {
              ?>
					<li><a href="<?php echo sprintf($federationURL, $language) ?>"><?php echo getLocalString('about_federation'); ?></a></li>
					<?php
          } ?>
					<?php if (!empty($faqURL) && getLocalString('faq') != '') {
              ?>
					<li class="last"><a href="<?php echo sprintf($faqURL, $language) ?>"><?php echo getLocalString('faq') ?></a></li>
					<?php
          } ?>
					<?php if (!empty($helpURL) && getLocalString('help') != '') {
              ?>
					<li class="last"><a href="<?php echo sprintf($helpURL, $language) ?>"><?php echo getLocalString('help') ?></a></li>
					<?php
          } ?>
					<?php if (!empty($privacyURL) && getLocalString('privacy') != '') {
              ?>
					<li class="last"><a href="<?php echo sprintf($privacyURL, $language) ?>"><?php echo getLocalString('privacy') ?></a></li>
					<?php
          } ?>
				</ul>
<!-- Body: Start -->
