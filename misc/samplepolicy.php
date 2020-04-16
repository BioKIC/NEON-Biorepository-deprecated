<?php
include_once('../config/symbini.php');
header("Content-Type: text/html; charset=".$CHARSET);
?>
<html>
	<head>
		<title>Sample Use Policy</title>
		<?php
		$activateJQuery = false;
		include_once($SERVER_ROOT.'/includes/head.php');
		?>
	</head>
	<body>
		<?php
		$displayLeftMenu = true;
		include($SERVER_ROOT.'/includes/header.php');
		?>
		<div class="navpath">
			<a href="<?php echo $CLIENT_ROOT; ?>/index.php">Home</a> &gt;&gt;
			<b>Sample Use Policy</b>
		</div>
		<!-- This is inner text! -->
		<div id="innertext" style="margin-bottom: 3rem;">
      <h1 style="text-align: center;">Sample Use Policy</h1>
      <p>NEON, the National Ecological Observatory Network, aims to provide "open data to understand how our aquatic and terrestrial ecosystems are changing". Therefore, the <a href="NEON-Sample-Use-Policy_20181217.pdf" target=_blank>NEON Sample Use Policy</a> reflects the need to provide researchers with access to NEON samples for a wide-variety of purposes while preserving the future research potential of those samples.</p>
        <ul>
          <li><p><a href="mailto:biorepo@asu.edu">Contact us</a> well in advance of any grant proposal deadlines in order to optimally integrate sample uses into project narratives, data management plans, and budgets.</p></li>
          <li><p>The NEON Biorepository data portal is capable of hosting or linking to many forms of sample-associated data. Researchers using samples are strongly encouraged to become portal managers in order to disseminate their data to the public.</p></li>
          <li><p>The NEON Biorepository Advisory Committee, consisting of Biorepository and NEON staff as well as the external Biorepository Technical Working Group, may be consulted prior to approval of any sample use request.</p></li>
          <li><p>Sample uses can be non-invasive, invasive, consumptive, or destructive. Uses that reduce the future research potential of a sample will likely require stronger justification and a plan to disseminate all sample-associated data.</p></li>
          <li><p>Researchers are responsible for proper handling of all samples, adhering to all aspects of the sample use agreement, and following all NEON and National Science Foundation data reporting and citation policies.</p></li>
        </ul>
      <p>Please read the <a href="NEON-Sample-Use-Policy_20181217.pdf" target=_blank>full policy</a> for more details and <a href="mailto:biorepo@asu.edu">contact us</a> for more information.</p>
		</div>
		<?php
			include($SERVER_ROOT.'/includes/footer.php');
		?>
	</body>
</html>
