<?php
include_once('../config/symbini.php');
header("Content-Type: text/html; charset=".$CHARSET);
?>
<html>
	<head>
		<title><?php echo $DEFAULT_TITLE; ?> Data Usage Guidelines</title>
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
		<!-- This is inner text! -->
		<div id="innertext">
			<?php
			if($LANG_TAG=='en'){
				?>
				<h1>Guidelines for Acceptable Use of Data</h1><br />

				<h2>Recommended Citation Formats</h2>
				<div style="margin:10px">
					Use one of the following formats to cite data retrieved from the <?php echo $DEFAULT_TITLE; ?> network:
					<div style="font-weight:bold;margin-top:10px;">
						General Citation:
					</div>
					<div style="margin:10px;">
						<?php
						$basePath = "http://";
						if((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443) $basePath = "https://";
						$basePath .= $_SERVER["SERVER_NAME"];
						if($_SERVER["SERVER_PORT"] && $_SERVER["SERVER_PORT"] != 80 && $_SERVER["SERVER_PORT"] != 443) $basePath .= ':'.$_SERVER["SERVER_PORT"];
						$basePath .= $CLIENT_ROOT.(substr($CLIENT_ROOT,-1)=='/'?'':'/');
						echo $DEFAULT_TITLE.'. '.date('Y').'. ';
						echo $basePath.'index.php. ';
						echo 'Accessed on '.date('F d').'. ';
						?>
					</div>

					<div style="font-weight:bold;margin-top:10px;">
						Usage of occurrence data from specific institutions:
					</div>
					<div style="margin:10px;">
						Biodiversity occurrence data published by: &lt;List of Collections&gt;
						(Accessed through <?php echo $DEFAULT_TITLE; ?> Data Portal,
						<?php echo $basePath.'index.php'; ?>, YYYY-MM-DD)<br/><br/>
						<b>For example:</b><br/>
						Biodiversity occurrence data published by:
						Field Museum of Natural History, Museum of Vertebrate Zoology, and New York Botanical Garden
						(Accessed through <?php echo $DEFAULT_TITLE; ?> Data Portal,
						<?php echo $basePath.'index.php, '.date('Y-m-d').')'; ?>
					</div>
				</div>
				<div>
				</div>

				<a name="occurrences"></a>
				<h2>Occurrence Record Use Policy</h2>
			    <div style="margin:10px;">
					<ul>
						<li>
							While <?php echo $DEFAULT_TITLE; ?> will make every effort possible to control and document the quality
							of the data it publishes, the data are made available "as is". Any report of errors in the data should be
							directed to the appropriate curators and/or collections managers.
						</li>
						<li>
							<?php echo $DEFAULT_TITLE; ?> cannot assume responsibility for damages resulting from mis-use or
							mis-interpretation of datasets or from errors or omissions that may exist in the data.
						</li>
						<li>
							It is considered a matter of professional ethics to cite and acknowledge the work of other scientists that
							has resulted in data used in subsequent research. We encourages users to
							contact the original investigator responsible for the data that they are accessing.
						</li>
						<li>
							<?php echo $DEFAULT_TITLE; ?> asks that users not redistribute data obtained from this site without permission for data owners.
							However, links or references to this site may be freely posted.
						</li>
					</ul>
			    </div>

				<a name="images"></a>
				<h2>Images</h2>
			    <div style="margin:15px;">
			    	Images within this website have been generously contributed by their owners to
			    	promote education and research. These contributors retain the full copyright for
			    	their images. Unless stated otherwise, images are made available under the Creative Commons
			    	Attribution-ShareAlike (<a href="http://creativecommons.org/licenses/by-sa/3.0/" target="_blank">CC BY-SA</a>).
					Users are allowed to copy, transmit, reuse, and/or adapt content, as long as attribution
					regarding the source of the content is made. If the content is altered, transformed, or enhanced,
					it may be re-distributed only under the same or similar license by which it was acquired.
			    </div>

				<h2>Notes on Specimen Records and Images</h2>
			    <div style="margin:15px;">
					Specimens are used for scientific research and because of skilled preparation and
					careful use they may last for hundreds of years. Some collections have specimens
					that were collected over 100 years ago that are no longer occur within the area.
					By making these specimens available on the web as images, their availability and
					value improves without an increase in inadvertent damage caused by use. Note that
					if you are considering making specimens, remember collecting normally requires
					permission of the landowner and, in the case of rare and endangered plants,
					additional permits may be required. It is best to coordinate such efforts with a
					regional institution that manages a publically accessible collection.
				</div>

			    <div style="margin:15px;">
					<b>Disclaimer:</b> This data portal may contain specimens and historical records that are culturally sensitive.
					The collections include specimens dating back over 200 years collected from all around the world.
					Some records may also include offensive language. These records do not reflect the portal community's current viewpoint
					but rather the social attitudes and circumstances of the time period when specimens were collected or cataloged.
				</div>
				<?php
			}
			else{
				?>

				<h1>Directrices para el uso aceptable de los datos</h1>

				<h2>Formatos de citas recomendadas</h2>
				<div style="margin:10px">
					Utilice uno de los siguientes formatos para citar los datos recuperados de la red <?php echo $DEFAULT_TITLE; ?>:
					<div style="font-weight:bold;margin-top:10px;">
						Cita general:
					</div>
					<div style="margin:10px;">
						<?php
						echo $DEFAULT_TITLE.'. '.date('Y').'. ';
						echo 'http//:'.$_SERVER['HTTP_HOST'].$CLIENT_ROOT.(substr($CLIENT_ROOT,-1)=='/'?'':'/').'index.php. ';
						echo 'Accessed on '.date('F d').'. ';
						?>
					</div>
					<div style="font-weight:bold;margin-top:10px;">
						Uso de los datos provenientes de instituciones espec&iacute;ficas:
					</div>
					<div style="margin:10px;">
						Datos sobre biodiversidad publicado por: &lt;Lista de Colecciones&gt;
						(Accedido a trav&eacute;s del portal de datos <?php echo $DEFAULT_TITLE; ?>,
						<?php echo 'http//:'.$_SERVER['HTTP_HOST'].$CLIENT_ROOT.(substr($CLIENT_ROOT,-1)=='/'?'':'/').'index.php'; ?>, DD-MM-AAAA)<br/><br/>
						<b>Por ejemplo:</b><br/>
						Datos sobre biodiversidad publicado por:
						Field Museum of Natural History, Museum of Vertebrate Zoology, and New York Botanical Garden
						(Accedido a trav&eacute;s del portal de datos <?php echo $DEFAULT_TITLE; ?>,
						<?php echo 'http//:'.$_SERVER['HTTP_HOST'].$CLIENT_ROOT.(substr($CLIENT_ROOT,-1)=='/'?'':'/').'index.php, '.date('Y-m-d').')'; ?>
					</div>
				</div>
				<div>
				</div>

				<a name="occurrences"></a>
				<h2>Pol&iacute;tica de Uso de Registros</h2>
			    <div>
					<ul>
						<li>
							Mientras que la <?php echo $DEFAULT_TITLE; ?> har&aacute; todos los esfuerzos posibles para controlar y documentar la calidad de los datos que publica, los 								datos se ponen a disposici&oacute;n "tal cual". Cualquier informe de errores en los datos debe ser dirigida a los curadores apropiados y / o administradores 						de colecciones.
						</li>
						<li>
							<?php echo $DEFAULT_TITLE; ?> no puede asumir la responsabilidad por da&ntilde;os resultantes del mal uso o mala interpretaci&oacute;n de conjuntos de 								datos o de los errores u omisiones que puedan existir en los datos.
						</li>
						<li>
							Se considera una cuesti&oacute;n de &eacute;tica profesional el citar y reconocer el trabajo de otros cient&iacute;ficos que ha dado lugar a los datos 								utilizados en investigaciones posteriores. Recomendamos a los usuarios ponerse en contacto con el investigador original responsable de los datos que 						est&aacute;n accediendo.
						</li>
						<li>
							<?php echo $DEFAULT_TITLE; ?> solicita que los usuarios no distribuyan los datos obtenidos de este sitio sin el permiso de los propietarios de datos.
							Sin embargo, los enlaces o referencias a este sitio pueden ser publicados libremente.
						</li>
					</ul>
			    </div>
				<a name="images"></a>
				<h2>Im&aacute;genes</h2>
			    <div style="margin:15px;">
			    	Las im&aacute;genes dentro de este sitio web han sido generosamente aportados por sus propietarios para promover la educaci&oacute;n y la investigaci&oacute;n. Estos contribuyentes 			conservan todos los derechos de sus im&aacute;genes. A menos que se indique lo contrario, las im&aacute;genes est&aacute;n disponibles bajo la licencia Creative Commons 						Attribution-ShareAlike (CC BY-SA). Los usuarios pueden copiar, transmitir, reutilizar, y/o adaptar el contenido, siempre y cuando se reconozca el origen del contenido. Si el contenido es 					alterado, transformado, o mejorado, este se puede volver a distribuir s&oacute;lo bajo la misma licencia o una similar con la cual fue adquirido.
			    </div>

				<h2>Notas sobre los registros de espec&iacute;menes e im&aacute;genes</h2>
			    <div style="margin:15px;">
					Los ejemplares son generalmente utilizados para la investigaci&oacute;n cient&iacute;fica y debido a su preparaci&oacute;n especializada y uso cuidadoso podr&iacute;an durar 						cientos de a&ntilde;os. Algunas colecciones tienen muestras que fueron recolectadas hace m&aacute;s de 100 a&ntilde;os, que incluso ya no se producen dentro de la zona. Al 						hacer estos ejemplares disponibles en la web como im&aacute;genes, su disponibilidad y valor mejora sin un aumento en el da&ntilde;o inadvertido provocado por el uso. Tenga 				en cuenta que si est&aacute; considerando recolectar muestras, recuerde que ello requiere normalmente del permiso del propietario del terreno y, en el caso de plantas raras y 						en peligro de extinci&oacute;n, se pueden requerir permisos adicionales. Lo mejor es coordinar estos esfuerzos con una instituci&oacute;n regional que maneja una 							colecci&oacute;n accesible p&uacute;blicamente, como por ejemplo, los herbarios que conforman la <b>Red de Herbarios del Noroeste de M&eacute;xico</b>.
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
