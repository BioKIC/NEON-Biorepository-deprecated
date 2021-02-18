<?php
if(isset($GOOGLE_ANALYTICS_KEY) && $GOOGLE_ANALYTICS_KEY) {
	if(substr($GOOGLE_ANALYTICS_KEY,3) == 'UA'){
		?>
		<script type="text/javascript">
			(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
			  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
			  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
			  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

			  ga('create', '<?php echo $GOOGLE_ANALYTICS_KEY; ?>', '<?php echo $_SERVER["SERVER_NAME"]; ?>');
			  ga('send', 'pageview');
		</script>
		<?php
	}
	else{
		?>
		<!-- Global site tag (gtag.js) - Google Analytics -->
		<script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo $GOOGLE_ANALYTICS_KEY; ?>"></script>
		<script>
		window.dataLayer = window.dataLayer || [];
		function gtag(){dataLayer.push(arguments);}
		gtag('js', new Date());

		gtag('config', '<?php echo $GOOGLE_ANALYTICS_KEY; ?>');
		</script>
		<?php
	}
}
?>