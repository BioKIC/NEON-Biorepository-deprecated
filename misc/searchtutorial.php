<?php
include_once('../config/symbini.php');
header("Content-Type: text/html; charset=".$CHARSET);
?>
<html>
	<head>
		<title>Conduct a Sample Search</title>
    <?php
      $activateJQuery = false;
      if(file_exists($SERVER_ROOT.'/includes/head.php')){
        include_once($SERVER_ROOT.'/includes/head.php');
      }
      else{
        echo '<link href="'.$CLIENT_ROOT.'/css/jquery-ui.css" type="text/css" rel="stylesheet" />';
        echo '<link href="'.$CLIENT_ROOT.'/css/base.css?ver=1" type="text/css" rel="stylesheet" />';
        echo '<link href="'.$CLIENT_ROOT.'/css/main.css?ver=1" type="text/css" rel="stylesheet" />';
      }
    ?>
    <style>
      article {
        background-color: #ededed;
        padding: 10px;
        margin: 10px 0;
        border-radius: 6px;
      }
      figcaption {
        background-color: #002d74;
        color: #ffffff;
        padding: 0 1em;
      }
      figure {
        border: 1px solid #002d74;
        -webkit-box-shadow: 9px 10px 12px -10px rgba(0,0,0,0.5);
        -moz-box-shadow: 9px 10px 12px -10px rgba(0,0,0,0.5);
        box-shadow: 9px 10px 12px -10px rgba(0,0,0,0.5);
      }
    </style>
	</head>
	<body>
		<?php
		$displayLeftMenu = true;
		include($SERVER_ROOT.'/includes/header.php');
		?>
		<div class="navpath">
      <a href="<?php echo $CLIENT_ROOT; ?>/index.php">Home</a> &gt;&gt;
      <a href="<?php echo $CLIENT_ROOT; ?>/misc/tutorials.php">Tutorials</a> &gt;&gt;   
			<b>Conduct a Sample Search</b>
		</div>
		<!-- This is inner text! -->
		<div id="innertext">
      <h1 style="text-align: center;">Conduct a Sample Search</h1>
      <p>Use the Sample Search feature of the NEON Biorepository data portal to search for, download, and map available NEON samples based on collection, taxon, location, and more.</p>
      <p>To do so, follow the following steps (or download the tutorial <a href="<?php echo $CLIENT_ROOT; ?>/misc/20200427_portalTutorial.pdf">here</a>):</p>

      <article>
        <p>Navigate to <a href="https://biorepo.neonscience.org/portal/collections/index.php&amp;sa=D&amp;ust=1590701809706000">Sample Search</a> under "Search" in the main menu.</p>
        <figure>
          <img src="../misc/images/tutorial_11.jpg" alt="">
          <figcaption>Navigate to <a href="https://biorepo.neonscience.org/portal/collections/index.php&amp;sa=D&amp;ust=1590701809706000">Sample Search</a> under "Search" in the main menu.</figcaption>
        </figure>
      </article>

      <article>
        <p>Note the disclaimer at the top of the search form:</p>
        <figure>
          <img src="../misc/images/tutorial_25.jpg" alt="Note the disclaimer at the top of the search form">
          <figcaption>Note the disclaimer at the top of the search form</figcaption>
        </figure>

      <article>
        <p>External Collections are of two types:
          <ol>
            <li>Collections of NEON samples not held at the NEON Biorepository (e.g. Essig and the Museum of Southwestern Biology). These samples are generally legacy samples collected before the initiation of the NEON Biorepository in late-2018.</li>
            <li>Collections of non-NEON samples that were collected at what are now NEON sites. These samples are not part of the NEON Biorepository and are generally not held at Arizona State University. Search these collections to better understand background measures of diversity at NEON sites.</li>
          </ol>
          <p>Make sure to deselect these collections at the bottom of the page if you only wish to explore NEON Biorepository samples.</p>
        <figure>
          <img src="../misc/images/tutorial_35.jpg" alt="Note the disclaimer at the top of the search form">
          <figcaption>Note the disclaimer at the top of the search form</figcaption>
        </figure>

		</div>
		<?php
			include($SERVER_ROOT.'/includes/footer.php');
		?>
	</body>
</html>
