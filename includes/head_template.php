<?php
/*
 * Customize css links below as needed to establish custom look-and-feel for portal
 */
//Customized styling: copy all /css/symb/*.css files into alternate directory (e.g. /css/symb/custom) and then modify $cssPathPrefix to point into that directory
$cssPathPrefix = $CLIENT_ROOT.'/css/symb';
//$cssPathPrefix = $CLIENT_ROOT.'/css/symb/custom';
?>
<meta name="viewport" content="initial-scale=1.0, user-scalable=yes" />
<?php
if($activateJQuery){
	//For an alternate jQuery UI styling, point link below to another css file
	echo '<link href="<?php echo $CLIENT_ROOT; ?>/css/jquery-ui.css" type="text/css" rel="stylesheet">'."/n";
}
?>
<link href="<?php echo $CLIENT_ROOT; ?>/css/base.css?ver=1" type="text/css" rel="stylesheet">
