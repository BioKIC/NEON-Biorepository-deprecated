<?php
 //error_reporting(E_ALL);
include_once('../../config/symbini.php');
if($LANG_TAG != 'en' && file_exists($SERVER_ROOT.'/content/lang/collections/editor/assocsppaid.'.$LANG_TAG.'.php')) include_once($SERVER_ROOT.'/content/lang/collections/editor/assocsppaid.'.$LANG_TAG.'.php');
else include_once($SERVER_ROOT.'/content/lang/collections/editor/assocsppaid.en.php');
header("Content-Type: text/html; charset=".$CHARSET);
 
?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $CHARSET; ?>">
	<title><?php echo $LANG['ASSOC_SPP_AID']; ?></title>
  <?php
    $activateJQuery = true;
    if(file_exists($SERVER_ROOT.'/includes/head.php')){
      include_once($SERVER_ROOT.'/includes/head.php');
    }
    else{
      echo '<link href="'.$CLIENT_ROOT.'/css/jquery-ui.css" type="text/css" rel="stylesheet" />';
      echo '<link href="'.$CLIENT_ROOT.'/css/base.css?ver=1" type="text/css" rel="stylesheet" />';
      echo '<link href="'.$CLIENT_ROOT.'/css/main.css?ver=1" type="text/css" rel="stylesheet" />';
    }
  ?>
	<script type="text/javascript" src="../../js/jquery.js"></script>
	<script type="text/javascript" src="../../js/jquery-ui.js"></script>
	<script type="text/javascript">

		$(document).ready(function() {
			$("#taxonname").autocomplete({ source: "rpc/getassocspp.php" },
			{ minLength: 4, autoFocus: true, delay: 200 });

			$("#taxonname").focus();
		});

		function addName(){
		    var nameElem = document.getElementById("taxonname");
		    if(nameElem.value){
		    	var asStr = opener.document.fullform.associatedtaxa.value;
		    	if(asStr) asStr = asStr + ", ";  
		    	opener.document.fullform.associatedtaxa.value = asStr + nameElem.value;
		    	nameElem.value = "";
		    	nameElem.focus();
		    }
	    }

	</script>
</head>

<body style="background-color:white">
	<!-- This is inner text! -->
	<div id="innertext" style="background-color:white;">
		<fieldset style="width:450px;">
			<legend><b><?php echo $LANG['ASSOC_SPP_AID']; ?></b></legend>
			<div style="">
				<?php echo $LANG['TAXON']; ?>: 
				<input id="taxonname" type="text" style="width:350px;" /><br/>
				<button id="transbutton" type="button" value="Add Name" onclick="addName();"><?php echo $LANG['ADD_NAME']; ?></button>
			</div>
		</fieldset>
	</div>
</body>
</html> 

