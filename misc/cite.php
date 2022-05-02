<?php
include_once('../config/symbini.php');
header("Content-Type: text/html; charset=".$CHARSET);
?>
<html>
	<head>
		<title>How to Cite</title>
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
        margin: 2rem 0;
      }
      .anchor {
        padding-top: 50px;
      }
    </style>
	</head>
	<body>
		<?php
		$displayLeftMenu = true;
		include($SERVER_ROOT.'/includes/header.php');
		?>
		<div class="navpath">
			<a href="<?php echo $CLIENT_ROOT; ?>/index.php">Home</a> >>
			<b>How to Cite</b>
		</div>
		<!-- This is inner text! -->
		<div id="innertext">
      <h1 style="text-align: center;">How to Cite</h1>
      <h2 style="text-align: center;">Ways to Acknowledge and Cite the Use of the NEON Biorepository</h2>
      <p>The following provides guidelines for acknowledging and citing the use of NEON Biorepository resources, including samples and data, in your research publications.</p>
      <!-- Table of Contents -->
      <h2 class="anchor" id="dataset-publishing-toc">Table of Contents</h2>

      <ol>
        <li>
          <a href="#h.1">Acknowledging the NEON Biorepository as a used resource in scientific publications</a>
          <ol type="A">
            <li><a href="#h.1.a">Generic <i>acknowledgment</i> of the NEON Biorepository as a resource</a></li>
            <li><a href="#h.1.b">Generic <i>citation</i> of the NEON Biorepository as a resource</a></li>
          </ol>
        </li>
          <li>
            <a href="#h.2">Citing the use of the NEON Biorepository data portal</a>
            <ol type="A">
              <li><a href="#h.2.a">Citing the NEON Biorepository portal generally</a></li>
              <li><a href="#h.2.b">Citing particular NEON Biorepository <i>occurrence records</i></a></li>
              <li><a href="#h.2.c">Citing particular NEON Biorepository <i>collections</i> as sources for occurrence data</a></li>
              <li><a href="#h.2.d">Citing a NEON Biorepository <i>published research</i> or <i>special collections dataset</i></a></li>
            </ol>
          </li>
          <li><a href="#h.3">Acknowledging and citing NEON data generally</a></li>
      </ol>

      <!-- End of Table of Contents -->
      <article>
        <h3 class="anchor" id="h.1">1. Acknowledging the NEON Biorepository as a used resource in scientific publications</h3>
        <h4 class="anchor" id="h.1.a">1A. Generic <i>acknowledgment</i> of the NEON Biorepository as a resource</h4>
        <p>You can promote use of NEON Biorepository resources with the following statement in the acknowledgement section of your relevant publications:</p>
        <blockquote>"The National Ecological Observatory Network Biorepository at Arizona State University provided samples and data collected as part of the NEON Program."</blockquote>
        <h4 class="anchor" id="h.1.b">1B. Generic <i>citation</i> of the NEON Biorepository as a resource</h4>
        <p>If the sampling scheme and design of the NEON Biorepository has been integral to facilitating your research, we encourage you to also cite the following publication that outlines its conceptualization and implementation:</p>
        <blockquote>Kelsey M Yule, Edward E Gilbert, Azhar P Husain, M Andrew Johnston, Laura Rocha Prado, Laura Steger, & Nico M Franz. (2020). Designing Biorepositories to Monitor Ecological and Evolutionary Responses to Change (Version 1). Zenodo. <a href="https://doi.org/10.5281/zenodo.3880411" target="_blank" rel="noopener noreferrer">https://doi.org/10.5281/zenodo.3880411</a></blockquote>
        <button><a href="#dataset-publishing-toc">Go back to TOC</a></button>
      </article>
      <article>
        <h3 class="anchor" id="h.2">2. Citing the use of the NEON Biorepository <i>data</i> portal</h3>
        <h4 class="anchor" id="h.2.a">2A. Citing the NEON Biorepository portal generally</h4>
        <p> When your work relies on occurrence data published by the NEON Biorepository, cite the following:
          <blockquote>Biodiversity occurrence data published by: NEON (National Ecological Observatory Network) Biorepository, Arizona State University Biodiversity Knowledge Integration Center (Accessed through the NEON Biorepository Data Portal, <a href="http//:biorepo.neonscience.org/" target="_blank" rel="noopener noreferrer">http//:biorepo.neonscience.org/</a>, 2022-03-11)</blockquote>
        </p>
        <h4 class="anchor" id="h.2.b">2B. Citing particular NEON Biorepository <i>occurrence records</i></h4>
        <h4 class="anchor" id="h.2.c">2C. Citing particular NEON Biorepository <i>collections</i> as sources for occurrence data</h4>
        <p>When your work relies on occurrence data from particular NEON Biorepository collections, use the preferred citation format published on the relevant collection details page. For example, to cite the <a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=20" target="_blank" rel="noopener noreferrer">NEON Biorepository fish voucher collection</a>, include the following in your publication:
          <blockquote>NEON Biorepository Fish Collection (Vouchers). Occurrence dataset (ID: 42e0872f-6223-4f8d-83f8-cd2f10e4b3c0) <a href="https://biorepo.neonscience.org/portal/content/dwca/NEON-FISC-V_DwC-A.zip" target="_blank" rel="noopener noreferrer">https://biorepo.neonscience.org/portal/content/dwca/NEON-FISC-V_DwC-A.zip</a> accessed via the NEON Biorepository Data Portal, <a href="http//:biorepo.neonscience.org/" target="_blank" rel="noopener noreferrer">http//:biorepo.neonscience.org/</a>, 2022-03-11.</blockquote>
        </p>
        <h4 class="anchor" id="h.2.d">2D. Citing a NEON Biorepository <i>published research</i> or <i>special collections dataset</i></h4>
        <p>To cite the use of occurrence records from an <a href="https://biorepo.neonscience.org/portal/collections/datasets/publiclist.php" target="_blank" rel="noopener noreferrer">existing published research or special collections dataset</a>, include the citations available from the relevant dataset page. When this dataset is associated with a prior publication, include the citation to the original publication, as well. For example, to cite the occurrence records associated with <a href="https://biorepo.neonscience.org/portal/collections/datasets/public.php?datasetid=157" target="_blank" rel="noopener noreferrer">Ayres 2019</a> include the following references:
          <blockquote>Biodiversity occurrence data published by: NEON (National Ecological Observatory Network) Biorepository, Arizona State University, Biodiversity Knowledge Integration Center. Ayres 2019: Quantitative Guidelines for Establishing and Operating Soil Archives (ID: {DATASET ID}) https://biorepo.neonscience.org/portal/collections/list.php?datasetid={ID} accessed via the NEON Biorepository Data Portal, <a href="http//:biorepo.neonscience.org/" target="_blank" rel="noopener noreferrer">http//:biorepo.neonscience.org/</a>, 2022-03-11).</blockquote>
        </p>
        <p>In many cases, you should also cite the original publication associated with the dataset, which is also available on the dataset description page. Eg.:
          <blockquote>Ayres, E. 2019. Quantitative Guidelines for Establishing and Operating Soil Archives. Soil Science Society of America Journal, 83(4): 973-981. <a href="https://doi.org/10.2136/sssaj2019.02.0050" target="_blank" rel="noopener noreferrer">https://doi.org/10.2136/sssaj2019.02.0050</a></blockquote>
        </p>
        <button><a href="#dataset-publishing-toc">Go back to TOC</a></button>
      </article>
      <article>
        <h3 class="anchor" id="h.3">3. Acknowledging and citing NEON data generally</h3>
        <p>Research outputs using other NEON data and samples should also follow NEON <a href="https://www.neonscience.org/data-samples/guidelines-policies/citing" target="_blank" rel="noopener noreferrer">citation policies</a> and <a href="http://https://www.neonscience.org/data-samples/guidelines-policies/publishing-research-outputs" target="_blank" rel="noopener noreferrer">guidelines for publishing research output</a>.</p>
        <button><a href="#dataset-publishing-toc">Go back to TOC</a>
      </button>
		</div>
		<?php
			include($SERVER_ROOT.'/includes/footer.php');
		?>
	</body>
</html>
