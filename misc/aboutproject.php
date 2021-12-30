<?php
include_once('../config/symbini.php');
header("Content-Type: text/html; charset=".$CHARSET);
?>
<html>
	<head>
		<title><?php echo ($LANG_TAG=='en'?'About the Project':'Sobre el proyecto'); ?></title>
		<?php
		$activateJQuery = false;
		include_once($SERVER_ROOT.'/includes/head.php');
		?>
	</head>
	<body>
		<?php
		$displayLeftMenu = false;
		include($SERVER_ROOT.'/includes/header.php');
		?>
		<div class="navpath">
			<a href="../index.php"><?php echo ($LANG_TAG=='en'?'Home':'Inicio'); ?></a> &gt;&gt;
			<b><?php echo ($LANG_TAG=='en'?'About the Project':'Sobre el proyecto'); ?></b>
		</div>
		<!-- This is inner text! -->
		<div id="innertext" style="margin:10px 20px">
			<?php
			if($LANG_TAG=='en'){
				?>
				<h2>About the Project:</h2>

				<p>



				</p>

				<h2>Contacts:</h2>

				<p>



				</p>

				<?php
			}
			else{
				?>
				<h2>Sobre el proyecto:</h2>

				<p>
					Este portal denominado Red de Herbarios del Noroeste de México, es una iniciativa impulsada conjuntamente por la Universidad de Sonora y
					la Universidad Estatal de Arizona. Ha sido establecido sobre la plataforma de Symbiota que fue desarrollado junto con el portal
					SEINet Herbarium Network.
				</p>
				<p>
					Actualmente, cuenta con la participación y colaboración de los herbarios de las siguientes instituciones:
					Universidad Autónoma de Baja California (BCMEX), Centro de Investigaciones Biológicas del Noroeste (HCIB),
					Universidad de Sonora (USON), Universidad Autónoma de Sinaloa (UAS), Herbario Regional CIAD-Mazatlán (HCIAD),
					Centro Interdisciplinario de Investigación para el Desarrollo Integral Regional Unidad Durango (CIIDIR),
					así como otros herbarios del norte-centro de México que han ingresado recientemente como SLPM, JAAA, IBUG, QMEX, UAA, JES y WLM, entre otros.
				</p>

				<h2>Contactos:</h2>

				<p>




				</p>

				<div>
					<p style="text-align: center;">
						<a href="http://www.cibnor.mx/es/investigacion/colecciones-biologicas/herbario-hcib/entrada" target="_blank">
							<img alt="Herbario Anetta Mary Carter (HCIB)" src="<?php echo $CLIENT_ROOT; ?>/images/icons/hcib.jpg" style="width: 130px; height: 48px;" />
						</a>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
						<a href="http://bajaterraignota.webnode.mx/herbario-bcmex/" target="_blank">
							<img alt="Herbario de la Universidad Autónoma de Baja California (BCMEX)" src="<?php echo $CLIENT_ROOT; ?>/images/icons/bcmex.jpg" style="width: 89px; height: 80px;" />
						</a>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
						<a href="http://herbario.uson.mx/" target="_blank">
							<img alt="Herbario de la Universidad de Sonora (USON)" src="<?php echo $CLIENT_ROOT; ?>/images/icons/uson.png" style="width: 80px; height: 80px;" />
						</a>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
						<a href="http://www.ciidirdurango.ipn.mx/investigacion/Paginas/herbario.aspx" target="_blank">
							<img alt="Herbario del Instituto Politécnico Nacional-Unidad Durango (CIIDIR)" src="<?php echo $CLIENT_ROOT; ?>/images/icons/ciidir.jpg" style="width: 82px; height: 80px;" />
						</a>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
						<img alt="Herbario Jesús González Ortega (UAS)" src="<?php echo $CLIENT_ROOT; ?>/images/icons/uas.jpg" style="width: 59px; height: 80px;" />
						&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
						<a href="http://www.ciad.mx/herbario/" target="_blank">
							<img alt="Herbario Regional CIAD-Mazatlán (HCIAD)" src="<?php echo $CLIENT_ROOT; ?>/images/icons/hciad.jpg" style="width: 64px; height: 80px;" />
						</a>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
						<a href="http://slpm.uaslp.mx/" target="_blank">
							<img alt="Herbario Isidro Palacios (SLPM)" src="<?php echo $CLIENT_ROOT; ?>/images/icons/uaslp.jpg" style="width: 64px; height: 80px;" />
						</a>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
						<a href="/portal/collections/misc/collprofiles.php?collid=397" target="_blank" rel="noopener">
							<img style="width: 124px; height: 80px;" src="<?php echo $CLIENT_ROOT; ?>/images/icons/jaaa.jpg" alt="Herbario Jorge Arturo Alba Avila (UJED-JAAA)">
						</a>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
						<a href="/portal/collections/misc/collprofiles.php?collid=396" target="_blank" rel="noopener">
							<img style="width: 63px; height: 100px;" src="<?php echo $CLIENT_ROOT; ?>/images/icons/qmex.jpg" alt="Herbario de la Universidad Autónoma de Querétaro (QMEX)">
						</a>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
						<a href="/portal/collections/misc/collprofiles.php?collid=400" target="_blank" rel="noopener">
							<img style="width: 244px; height: 65px;" src="<?php echo $CLIENT_ROOT; ?>/images/icons/hjbc.jpg" alt="Herbario del Jardín Botánico Culiacán (HJBC)">
						</a>
					</p>
				</div>
				<?php
			}
			?>
		</div>
		<?php
		include($SERVER_ROOT.'/includes/footer.php');
		?>
	</body>
</html>