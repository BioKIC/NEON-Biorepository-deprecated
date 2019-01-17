<?php
include_once($SERVER_ROOT.'/content/lang/header.'.$LANG_TAG.'.php');
?>
<link href="https://fonts.googleapis.com/css?family=EB+Garamond|Playfair+Display+SC" rel="stylesheet" />
<style>
	.header1 { font-family: 'EB Garamond', serif; font-size: 32px; font-style: italic; margin: 15px 10px 0px 70px; }
	.header2 { font-family: 'Playfair Display SC', serif; font-size: 24px; margin: 0px 10px 10px 30px; }
</style>
<script type="text/javascript" src="<?php echo $CLIENT_ROOT; ?>/js/symb/base.js?ver=171023"></script>
<script type="text/javascript">
	//Uncomment following line to support toggling of database content containing DIVs with lang classes in form of: <div class="lang en">Content in English</div><div class="lang es">Content in Spanish</div>
	//setLanguageDiv();
</script>
<table id="maintable" cellspacing="0">
	<tr>
		<td id="header" colspan="3">
			<div id="header-div">
				<div id="header-text">
					<div style="float:left;">
						<img src="<?php echo $CLIENT_ROOT; ?>/images/layout/neon_logo.png" />
					</div>
					<div id="header-text2" style="float:left;">
						<div class="header1">NEON Biorepository</div>

					</div>
				</div>
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
					<ul id="hor_dropdown">
						<li>
							<a href="<?php echo $CLIENT_ROOT; ?>/index.php" ><?php echo (isset($LANG['H_HOME'])?$LANG['H_HOME']:'Home'); ?></a>
						</li>
						<li>
							<a href="#" ><?php echo (isset($LANG['H_SEARCH'])?$LANG['H_SEARCH']:'Search'); ?></a>
							<ul>
								<li>
									<a href="<?php echo $CLIENT_ROOT; ?>/collections/index.php" ><?php echo (isset($LANG['H_COLLECTIONS'])?$LANG['H_COLLECTIONS']:'Collections'); ?></a>
								</li>
								<li>
									<a href="<?php echo $CLIENT_ROOT; ?>/collections/map/index.php" target="_blank"><?php echo (isset($LANG['H_MAP'])?$LANG['H_MAP']:'Map'); ?></a>
								</li>
								<li>
									<a href="<?php echo $CLIENT_ROOT; ?>/checklists/dynamicmap.php?interface=checklist" ><?php echo (isset($LANG['H_DYN_LISTS'])?$LANG['H_DYN_LISTS']:'Dynamic Species List'); ?></a>
								</li>
								<li>
									<a href="<?php echo $CLIENT_ROOT; ?>/taxa/taxonomy/taxonomydynamicdisplay.php" ><?php echo (isset($LANG['H_TAXONOMIC_EXPLORER'])?$LANG['H_TAXONOMIC_EXPLORER']:'Taxonomic Explorer'); ?></a>
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
							<a href="#">NEON Sites Species Lists</a>
							<ul>
								<li>
									<a href="<?php echo $CLIENT_ROOT; ?>/projects/index.php?pid=1">NEON Plant Lists</a>
								</li>
								<?php
								if($IS_ADMIN){
									?>
									<li>
										<a href="<?php echo $CLIENT_ROOT; ?>/projects/index.php?pid=2">NEON Plant Lists not yet processed (private)</a>
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
										<a href="<?php echo $CLIENT_ROOT; ?>/neon/shipment/manifestloader.php" target="_blank">Manifest Submission</a>
									</li>
									<li>
										<a href="<?php echo $CLIENT_ROOT; ?>/neon/shipment/manifestviewer.php" target="_blank">Manifest Processing</a>
									</li>
								</ul>
							</li>
							<?php
						}
						?>
					</ul>
				</div>
			</div>
		</td>
	</tr>
	<tr>
		<td id='middlecenter'  colspan="3">
