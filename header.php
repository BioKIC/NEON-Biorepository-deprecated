<?php
include_once($SERVER_ROOT.'/content/lang/header.'.$LANG_TAG.'.php');
?>
<!-- CSS -->
<!-- <link href="https://fonts.googleapis.com/css?family=EB+Garamond|Playfair+Display+SC" rel="stylesheet" /> -->
<link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:200,300,400,600,700" rel="stylesheet" type="text/css">
<link href="<?php echo $CLIENT_ROOT; ?>/css/neon.css" type="text/css" rel="Stylesheet" />
<script type="text/javascript" src="<?php echo $CLIENT_ROOT; ?>/js/symb/base.js?ver=171023"></script>
<script type="text/javascript">
	//Uncomment following line to support toggling of database content containing DIVs with lang classes in form of: <div class="lang en">Content in English</div><div class="lang es">Content in Spanish</div>
	//setLanguageDiv();
</script>

		<!-- NEON Main Top Bar -->
		<div class="region-utility">
			<div id="block-menu-global-menu" class="block block-menu">

				<h2 class="block-title block-title">Global Menu</h2>

				<div class="block-content block-content">
					<ul class="nav">
						<li class="first leaf nav-item depth--1"><a href="https://biorepo.neonscience.org/portal/" title="NEON Biorepository at Arizona State University" class="nav-link active">Biorepository</a></li>
						<li class="last leaf nav-item depth--1"><a href="https://www.neonscience.org/" class="nav-link" title="NEON Science Main Portal">Neon Science</a></li>
						<li class="last leaf nav-item depth--1"><a href="https://data.neonscience.org/home" title="NEON Data Portal" class="nav-link">Data Portal</a></li>
	
					</ul>
				</div>
			</div>
		</div>

		<!-- Symbiota Main Header -->
		<header id="header">
			<ul id="biorepo-header">
				<!-- Main Site Logo -->
				<li id="branding">
					<!-- NSF/Neon logo -->
					<a href="/" title="Home" rel="home" class="branding-link active"><img src="https://www.neonscience.org/sites/all/themes/neon/img/logo-homepage.png" alt="NSF NEON | Open Data to Understand our Ecosystems"></a>
				</li>
				<li class="header-title"><h1>Biorepository Data Portal</h1></li>
			</ul>
			<!-- Symbiota Navigation -->
			<ul id="hor_dropdown">
				<li>
					<a href="<?php echo $CLIENT_ROOT; ?>/index.php" class="biorepo-home-icon"></a>
				</li>
				<li>
					<a href="#" ><?php echo (isset($LANG['H_SEARCH'])?$LANG['H_SEARCH']:'Search'); ?></a>
					<ul>
						<li>
							<a href="<?php echo $CLIENT_ROOT; ?>/collections/index.php" ><?php echo (isset($LANG['H_COLLECTIONS'])?$LANG['H_COLLECTIONS']:'Collections'); ?></a>
						</li>
						<li>
							<a href="<?php echo $CLIENT_ROOT; ?>/collections/map/index.php" target="_blank"><?php echo (isset($LANG['H_MAP'])?$LANG['H_MAP']:'Map Search'); ?></a>
						</li>
						<li>
							<a href="<?php echo $CLIENT_ROOT; ?>/checklists/dynamicmap.php?interface=checklist" ><?php echo (isset($LANG['H_DYN_LISTS'])?$LANG['H_DYN_LISTS']:'Dynamic Checklist'); ?></a>
						</li>
						<li>
							<a href="<?php echo $CLIENT_ROOT; ?>/taxa/taxonomy/taxonomydynamicdisplay.php" ><?php echo (isset($LANG['H_TAXONOMIC_EXPLORER'])?$LANG['H_TAXONOMIC_EXPLORER']:'Taxonomy Explorer'); ?></a>
						</li>
					</ul>
				</li>
				<li>
					<a href="#" ><?php echo (isset($LANG['H_IMAGES'])?$LANG['H_IMAGES']:'Images'); ?></a>
					<ul>
						<li>
							<a href="<?php echo $CLIENT_ROOT; ?>/imagelib/index.php" ><?php echo (isset($LANG['H_IMAGE_BROWSER'])?$LANG['H_IMAGE_BROWSER']:'Image Browser'); ?></a>
						</li>
						<li>
							<a href="<?php echo $CLIENT_ROOT; ?>/imagelib/search.php" ><?php echo (isset($LANG['H_IMAGE_SEARCH'])?$LANG['H_IMAGE_SEARCH']:'Search Images'); ?></a>
						</li>
					</ul>
				</li>
				<li>
					<a href="#">Species Lists</a>
					<ul>
						<li>
							<a href="<?php echo $CLIENT_ROOT; ?>/projects/index.php?pid=1">Research Sites</a>
						</li>
						<?php
						if($IS_ADMIN){
							?>
							<li>
								<a href="<?php echo $CLIENT_ROOT; ?>/projects/index.php?pid=2">In Progress (private)</a>
							</li>
							<?php
						}
						?>
					</ul>
				</li>
				<li>
					<a href="#" >Additional Information</a>
					<ul>
						<li>
							<a href="https://www.neonscience.org" target="_blank" >About NEON</a>
						</li>
						<li>
							<a href="https://www.neonscience.org/data/neon-data-portal" target="_blank" >NEON Data Portal</a>
						</li>
					</ul>
				</li>
				<?php
				if($IS_ADMIN){
					?>
					<li>
						<a href="#" >Management Tools</a>
						<ul>
							<li>
								<a href="<?php echo $CLIENT_ROOT; ?>/neon/shipment/manifestloader.php">Manifest Submission</a>
							</li>
							<li>
								<a href="<?php echo $CLIENT_ROOT; ?>/neon/shipment/manifestsearch.php">Manifest Browse &amp; Search</a>
							</li>
						</ul>
					</li>
					<?php
				}
				?>
			</ul>
		</header>



		<!-- Symbiota log in bar -->
		<div id="top_navbar">
			<div id="right_navbarlinks">
				<?php
				if($USER_DISPLAY_NAME){
					?>
					<span style="">
						<?php echo (isset($LANG['H_WELCOME'])?$LANG['H_WELCOME']:'Welcome').' '.$USER_DISPLAY_NAME; ?>!
					</span>
					<span style="margin-left:5px;">
						<a href="<?php echo $CLIENT_ROOT; ?>/profile/viewprofile.php"><?php echo (isset($LANG['H_MY_PROFILE'])?$LANG['H_MY_PROFILE']:'My Profile')?></a>
					</span>
					<span style="margin-left:5px;">
						<a href="<?php echo $CLIENT_ROOT; ?>/profile/index.php?submit=logout"><?php echo (isset($LANG['H_LOGOUT'])?$LANG['H_LOGOUT']:'Logout')?></a>
					</span>
					<?php
				}
				else{
					?>
					<span style="">
						<a href="<?php echo $CLIENT_ROOT."/profile/index.php?refurl=".$_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING']; ?>"><?php echo (isset($LANG['H_LOGIN'])?$LANG['H_LOGIN']:'Login')?></a>
					</span>
					<span style="margin-left:5px;">
						<a href="<?php echo $CLIENT_ROOT; ?>/profile/newprofile.php"><?php echo (isset($LANG['H_NEW_ACCOUNT'])?$LANG['H_NEW_ACCOUNT']:'New Account')?></a>
					</span>
					<?php
				}
				?>
				<span style="margin-left:5px;">
					<a href='<?php echo $CLIENT_ROOT; ?>/sitemap.php'><?php echo (isset($LANG['H_SITEMAP'])?$LANG['H_SITEMAP']:'Sitemap'); ?></a>
				</span>
			</div>
		</div>