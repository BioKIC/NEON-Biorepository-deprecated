<div class="header-wrapper">
	<header>
		<nav class="top-login">
			<?php
			if ($USER_DISPLAY_NAME) {
			?>
				<span>
					Welcome <?php echo $USER_DISPLAY_NAME; ?>!
				</span>
				<span class="button button-tertiary">
					<a href="<?php echo $CLIENT_ROOT; ?>/profile/viewprofile.php">My profile</a>
				</span>
				<span class="button button-secondary">
					<a href="<?php echo $CLIENT_ROOT; ?>/profile/index.php?submit=logout">Log out</a>
				</span>
			<?php
			} else {
			?>
				<span>
					<a href="#">
						Contact Us
					</a>
				</span>
				<span class="button button-secondary">
					<a href="<?php echo $CLIENT_ROOT . "/profile/index.php?refurl=" . $_SERVER['SCRIPT_NAME'] . "?" . htmlspecialchars($_SERVER['QUERY_STRING'], ENT_QUOTES); ?>">
						Log in
					</a>
				</span>
			<?php
			}
			?>

		</nav>
		<div class="top-brand">
			<a href="https://symbiota.org">
				<img src="<?php echo $CLIENT_ROOT; ?>/images/layout/logo_symbiota.png" alt="Symbiota logo" width="100%">
			</a>
			<div class="brand-name">
				<h1>Symbiota Brand New Portal</h1>
				<h2>Redesigned by the Symbiota Support Hub</h2>
			</div>
		</div>
		<div class="menu-wrapper">
			<!-- Hamburger icon -->
			<input class="side-menu" type="checkbox" id="side-menu" />
			<label class="hamb" for="side-menu"><span class="hamb-line"></span></label>
			<!-- Menu -->
			<nav class="top-menu">
				<ul class="menu">
					<li><a href="<?php echo $CLIENT_ROOT; ?>/index.php">Home</a></li>
					<li><a href="<?php echo $CLIENT_ROOT; ?>/collections/index.php">Search Collections</a></li>
					<li><a href="<?php echo $CLIENT_ROOT; ?>/collections/map/index.php" target="_blank">Map Search</a></li>
					<li><a href="<?php echo $CLIENT_ROOT; ?>/checklists/index.php">Checklists</a></li>
					<li><a href="<?php echo $CLIENT_ROOT; ?>/imagelib/search.php">Image Search</a></li>
					<li><a href="<?php echo $CLIENT_ROOT; ?>/includes/usagepolicy.php">Data Use</a></li>
					<li><a href="https://symbiota.org/" target="_blank">About Symbiota</a></li>
					<li><a href="https://biokic.github.io/symbiota-docs/" target="_blank">Help</a></li>
				</ul>
			</nav>
		</div>
	</header>
</div>