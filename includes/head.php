<?php
/*
 * Customize css links below as needed to establish custom look-and-feel for portal
 */
//Customized styling: copy all /css/symb/*.css files into alternate directory (e.g. /css/symb/custom) and then modify $cssPathPrefix to point into that directory
//$cssPathPrefix = $CLIENT_ROOT.'/css/symb';
$cssPathPrefix = $CLIENT_ROOT.'/css/symb/custom';
?>
<meta name="viewport" content="initial-scale=1.0, user-scalable=yes" />
<?php
if($activateJQuery){
	//For an alternate jQuery UI styling, point link below to another css file
	echo '<link href="'.$CLIENT_ROOT.'/css/jquery-ui.css" type="text/css" rel="stylesheet">';
}
?>
<!-- FONT –––––––––––––––––––––––––––––––––––––––––––––––––– -->
<link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:200,300,400,600,700" rel="stylesheet" type="text/css">
<!-- UNIVERSAL CSS –––––––––––––––––––––––––––––––––––––––––––––––––– -->
<link rel="stylesheet" href="css/normalize.css">
<link rel="stylesheet" href="css/skeleton.css">
<link rel="stylesheet" href="<?php echo $CLIENT_ROOT; ?>/css/neon.css?ver=1">
<link href="<?php echo $CLIENT_ROOT; ?>/css/base.css?ver=1" type="text/css" rel="stylesheet">
<link href="<?php echo $CLIENT_ROOT; ?>/css/main.css?ver=1" type="text/css" rel="stylesheet">

<script type="text/javascript" src="<?php echo $CLIENT_ROOT; ?>/js/symb/base.js?ver=7"></script>
<script type="text/javascript">
	//Uncomment following line to support toggling of database content containing DIVs with lang classes in form of: <div class="lang en">Content in English</div><div class="lang es">Content in Spanish</div>
	//setLanguageDiv();
</script>
