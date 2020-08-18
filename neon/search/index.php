<?php
include_once('../../config/symbini.php');
include_once('../../content/lang/index.'.$LANG_TAG.'.php');
header("Content-Type: text/html; charset=".$CHARSET);
?>
<html>
  <head>
    <title><?php echo $DEFAULT_TITLE; ?>New Sample Search</title>
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
    <script src="js/jquery-3.2.1.min.js" type="text/javascript"></script>
    <!-- <script src="js/jquery-ui-1.12.1/jquery-ui.min.js" type="text/javascript"></script> -->
    <!-- <script src="js/symb/api.taxonomy.taxasuggest.js" type="text/javascript"></script> -->
    <script type="text/javascript">
      <?php include_once($SERVER_ROOT.'/includes/googleanalytics.php'); ?>
    </script>
    <!-- Search-specific styles -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="../css/app.css">
  </head>
  <body>
    <?php
    include($SERVER_ROOT.'/includes/header.php');
    ?>
    <!-- This is inner text! -->
    <div id="innertext">

      <h1>Sample Search</h1>
      <form id="params-form">
        <!-- Criteria forms -->
        <div class="accordions">
          <!-- Taxonomy -->
          <section>
            <!-- Accordion selector -->
            <input type="checkbox" id="taxonomy" class="accordion-selector" checked=true />

            <!-- Accordion header -->
            <label for="taxonomy" class="accordion-header">Taxonomy</label>

            <!-- Taxonomy -->
            <div id="search-form-taxonomy" class="content">
              <div id="taxa-text" class="input-text-container">
                <label for="taxa" class="input-text--outlined">
                  <input type="text" name="taxa" id="taxa-search" data-chip="Taxa" list="match-list">
                  <!-- <div id="match-list" class="suggestion-box hide" ></div> -->
                  <div id="match-list-container"></div>
                  <span data-label="Taxon"></span></label>
                <span class="assistive-text">Separate multiple with commas.</span>
              </div>
              <div class="select-container">
                <!-- <label for="taxon-type">Taxon Type</label> -->
                <select name="taxontype">
                    <option value="1">Any name</option>
                    <option value="2">Scientific name</option>
                    <option value="3">Family</option>
                    <option value="4">Taxonomic group</option>
                    <option value="5">Common name</option>
                  </select>
                <span class="assistive-text">Taxon type.</span>
              </div>
              <div><input type="checkbox" name="usethes" value="1" checked>Include Synonyms</div>
            </div>
          </section>
          <!-- Colections -->
          <section>
            <!-- Accordion selector -->
            <input type="checkbox" id="collections" class="accordion-selector" checked=true />
            <!-- Accordion header -->
            <label for="collections" class="accordion-header">Collections</label>
            <!-- Accordion content -->
            <div class="content">
              <div id="search-form-colls">
                <!-- <ul id="collections-list"></ul> -->
                <!-- Open NEON Collections modal -->
                <div><input id="all-neon-colls-quick" data-chip="All NEON Collections" class="all-selector" type="checkbox" checked=""><span id="neon-modal-open" class="material-icons expansion-icon">add_box</span><span>All NEON Collections</span></div>
                <!-- External Collections -->
                <div>
                  <ul id="neonext-collections-list">
                    <li><input id="allNeonExtCollections" data-chip="All NEON External Collections" type="checkbox" class="all-selector"><span class="material-icons expansion-icon">add_box</span><span>All NEON External Collections</span>
                      <ul class="collapsed">
                        <li><input type="checkbox" name="db" value="44" class="child"><span>Essig Museum of Entomology</span></li>
                        <li><input type="checkbox" name="db" value="74" class="child"><span>Museum of Southwestern Biology - Mammal specimens</span></li>
                      </ul>
                    </li>
                  </ul>
                  <ul id="ext-collections-list">
                    <li><input id="allExternalCollections" type="checkbox" class="all-selector"><span class="material-icons expansion-icon">add_box</span><span>All non-NEON External Collections</span>
                      <ul class="collapsed">
                        <li><input type="checkbox" name="db" value="43" class="child"><span>Consortium of Small Vertebrate Collections</span></li>
                        <li><input type="checkbox" name="db" value="37" class="child"><span>SCAN Portal Network Arthropod Specimens</span></li>
                        <li><input type="checkbox" name="db" value="2" class="child"><span>SEINet Portal Network Botanical Specimens</span></li>
                      </ul>
                    </li>
                  </ul>
                </div>

              </div>
            </div>
            <!-- NEON COllections Modal -->
            <div class="modal" id="biorepo-collections-list">
              <div class="modal-content">
                <button id="neon-modal-close" class="btn">Accept and close</button>
                <div id="testing-modal">
                  <div>
                    <h3>Pick a criterion to filter collections</h3>
                    <label class="tab tab-active"><input type="radio" name="collChoice" value="taxonomic-cat" checked="true"> Taxonomic Group</label>
                    <label class="tab"><input type="radio" name="collChoice" value="neon-theme"> NEON Theme</label>
                    <label class="tab"><input type="radio" name="collChoice" value="sample-type"> Sample Type</label>
                  </div>

                  <div id="taxonomic-cat" class="box" style="display: block;">
                    <h2>Select Collections by Taxonomic Group</h2>
                    <ul id="collections-list1">
                      <li><input type="checkbox" class="all-selector all-neon-colls" checked=""><span class="material-icons expansion-icon">indeterminate_check_box</span><span>All NEON Collections</span>
                        <ul>
                          <li><input type="checkbox" class="all-selector child" checked=""><span class="material-icons expansion-icon">add_box</span><span>Aquatic Invertebrates</span>
                            <ul class="collapsed">
                              <li><input type="checkbox" value="22" class="child" disabled=""><span style="color: gray">Benthic Macroinvertebrate Collection (Chironomids) - Samples Unavailable</span> <a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=22"
                                  target="_blank">More Info</a></li>
                              <li><input type="checkbox" value="61" class="child" disabled=""><span style="color: gray">Benthic Macroinvertebrate Collection (DNA Extracts) - Samples Unavailable</span> <a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=61"
                                  target="_blank">More Info</a></li>
                              <li><input type="checkbox" value="53" class="child" disabled=""><span style="color: gray">Benthic Macroinvertebrate Collection (Microscope Slides) - Samples Unavailable</span> <a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=53"
                                  target="_blank">More Info</a></li>
                              <li><input type="checkbox" value="52" class="child" disabled=""><span style="color: gray">Benthic Macroinvertebrate Collection (Oligochaetes) - Samples Unavailable</span> <a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=52"
                                  target="_blank">More Info</a></li>
                              <li><input type="checkbox" value="48" class="child" disabled=""><span style="color: gray">Benthic Macroinvertebrate Collection (Taxonomy Reference Collection) - Samples Unavailable</span> <a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=48"
                                  target="_blank">More Info</a></li>
                              <li><input type="checkbox" value="57" class="child" disabled=""><span style="color: gray">Benthic Macroinvertebrate Collection (Taxonomy Subsample) - Samples Unavailable</span> <a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=57"
                                  target="_blank">More Info</a></li>
                              <li><input type="checkbox" name="db" value="21" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=21" target="_blank">Benthic Macroinvertebrate Collection (Unsorted Bulk Sample)</a></span></li>
                              <li><input type="checkbox" name="db" value="62" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=62" target="_blank">Zooplankton Collection (DNA Extracts)</a></span></li>
                              <li><input type="checkbox" name="db" value="45" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=45" target="_blank">Zooplankton Collection (Remaining Bulk Taxonomy Sample)</a></span></li>
                              <li><input type="checkbox" name="db" value="55" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=55" target="_blank">Zooplankton Collection (Taxonomy Reference Collection)</a></span></li>
                              <li><input type="checkbox" name="db" value="60" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=60" target="_blank">Zooplankton Collection (Unsorted Bulk Sample)</a></span></li>
                            </ul>
                          </li>
                        </ul>
                        <ul>
                          <li><input type="checkbox" class="all-selector child" checked=""><span class="material-icons expansion-icon">add_box</span><span>Aquatic plants, bryophytes, lichens, and macroalgae</span>
                            <ul class="collapsed">
                              <li><input type="checkbox" name="db" value="50" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=50" target="_blank">Aquatic Macroalgae Collection (Chemical Preservation [Clip Harvests])</a></span></li>
                              <li><input type="checkbox" name="db" value="73" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=73" target="_blank">Aquatic Macroalgae Collection (Chemical Preservation [Point Counts])</a></span></li>
                              <li><input type="checkbox" name="db" value="9" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=9" target="_blank">Aquatic Plant, Bryophyte, and Lichen Collection (Herbarium Vouchers [Clip Harvests])</a></span></li>
                              <li><input type="checkbox" name="db" value="7" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=7" target="_blank">Aquatic Plant, Bryophyte, and Lichen Collection (Herbarium Vouchers [Point Counts])</a></span></li>
                              <li><input type="checkbox" name="db" value="8" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=8" target="_blank">Aquatic Plant, Bryophyte, and Lichen Collection (Herbarium Vouchers [Standard Sampling])</a></span></li>
                            </ul>
                          </li>
                        </ul>
                        <ul>
                          <li><input type="checkbox" class="all-selector child" checked=""><span class="material-icons expansion-icon">add_box</span><span>Environmental</span>
                            <ul class="collapsed">
                              <li><input type="checkbox" name="db" value="41" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=41" target="_blank">Particulate Mass Filter Collection</a></span></li>
                              <li><input type="checkbox" name="db" value="30" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=30" target="_blank">Soil Collection</a></span></li>
                              <li><input type="checkbox" name="db" value="76" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=76" target="_blank">Terrestrial Plant Collection (Belowground Biomass [Megapit])</a></span></li>
                              <li><input type="checkbox" name="db" value="10" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=10" target="_blank">Terrestrial Plant Collection (Belowground Biomass [Standard Sampling])</a></span></li>
                              <li><input type="checkbox" name="db" value="42" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=42" target="_blank">Wet Deposition Collection</a></span></li>
                            </ul>
                          </li>
                        </ul>
                        <ul>
                          <li><input type="checkbox" class="all-selector child" checked=""><span class="material-icons expansion-icon">add_box</span><span>Microbes</span>
                            <ul class="collapsed">
                              <li><input type="checkbox" name="db" value="67" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=67" target="_blank">Benthic Microbe Collection (DNA Extracts)</a></span></li>
                              <li><input type="checkbox" name="db" value="5" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=5" target="_blank">Benthic Microbe Collection (Sterivex Filters)</a></span></li>
                              <li><input type="checkbox" name="db" value="31" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=31" target="_blank">Soil Microbe Collection (Bulk Subsamples)</a></span></li>
                              <li><input type="checkbox" name="db" value="69" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=69" target="_blank">Soil Microbe Collection (DNA Extracts )</a></span></li>
                              <li><input type="checkbox" name="db" value="68" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=68" target="_blank">Surface Water Microbe Collection (DNA Extracts)</a></span></li>
                              <li><input type="checkbox" name="db" value="6" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=6" target="_blank">Surface Water Microbe Collection (Sterivex Filters)</a></span></li>
                            </ul>
                          </li>
                        </ul>
                        <ul>
                          <li><input type="checkbox" class="all-selector child" checked=""><span class="material-icons expansion-icon">add_box</span><span>Periphyton, seston, and phytoplankton</span>
                            <ul class="collapsed">
                              <li><input type="checkbox" name="db" value="47" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=47" target="_blank">Aquatic Microalgae Collection (Chemical Preservation)</a></span></li>
                              <li><input type="checkbox" name="db" value="46" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=46" target="_blank">Aquatic Microalgae Collection (Freeze-dried)</a></span></li>
                              <li><input type="checkbox" name="db" value="49" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=49" target="_blank">Aquatic Microalgae Collection (Microscope Slides)</a></span></li>
                            </ul>
                          </li>
                        </ul>
                        <ul>
                          <li><input type="checkbox" class="all-selector child" checked=""><span class="material-icons expansion-icon">add_box</span><span>Terrestrial Invertebrates</span>
                            <ul class="collapsed">
                              <li><input type="checkbox" name="db" value="11" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=11" target="_blank">Carabid Collection (Archive Pooling)</a></span></li>
                              <li><input type="checkbox" name="db" value="63" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=63" target="_blank">Carabid Collection (DNA Extracts)</a></span></li>
                              <li><input type="checkbox" name="db" value="39" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=39" target="_blank">Carabid Collection (Pinned Vouchers)</a></span></li>
                              <li><input type="checkbox" name="db" value="14" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=14" target="_blank">Carabid Collection (Trap Sorting)</a></span></li>
                              <li><input type="checkbox" name="db" value="13" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=13" target="_blank">Invertebrate Bycatch Collection (Archive Pooling)</a></span></li>
                              <li><input type="checkbox" name="db" value="16" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=16" target="_blank">Invertebrate Bycatch Collection (Trap Sorting)</a></span></li>
                              <li><input type="checkbox" name="db" value="56" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=56" target="_blank">Mosquito Collection (Bulk Identified)</a></span></li>
                              <li><input type="checkbox" name="db" value="58" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=58" target="_blank">Mosquito Collection (Bulk Unidentified)</a></span></li>
                              <li><input type="checkbox" name="db" value="65" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=65" target="_blank">Mosquito Collection (DNA Extracts)</a></span></li>
                              <li><input type="checkbox" name="db" value="59" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=59" target="_blank">Mosquito Collection (Pathogen Extracts)</a></span></li>
                              <li><input type="checkbox" name="db" value="29" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=29" target="_blank">Mosquito Collection (Pinned Vouchers)</a></span></li>
                              <li><input type="checkbox" name="db" value="4" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=4" target="_blank">NEON Biorepository Invertebrate Collections at Arizona State University</a></span></li>
                              <li><input type="checkbox" name="db" value="75" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=75" target="_blank">Tick Collection (Pathogen Extracts)</a></span></li>
                            </ul>
                          </li>
                        </ul>
                        <ul>
                          <li><input type="checkbox" class="all-selector child" checked=""><span class="material-icons expansion-icon">add_box</span><span>Terrestrial Plants</span>
                            <ul class="collapsed">
                              <li><input type="checkbox" name="db" value="18" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=18" target="_blank">Terrestrial Plant Collection (Canopy Foliage)</a></span></li>
                              <li><input type="checkbox" name="db" value="54" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=54" target="_blank">Terrestrial Plant Collection (Herbarium Vouchers)</a></span></li>
                              <li><input type="checkbox" name="db" value="40" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=40" target="_blank">Terrestrial Plant Collection (Leaf Tissue)</a></span></li>
                              <li><input type="checkbox" name="db" value="23" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=23" target="_blank">Terrestrial Plant Collection (Litterfall)</a></span></li>
                            </ul>
                          </li>
                        </ul>
                        <ul>
                          <li><input type="checkbox" class="all-selector child" checked=""><span class="material-icons expansion-icon">add_box</span><span>Vertebrates</span>
                            <ul class="collapsed">
                              <li><input type="checkbox" name="db" value="66" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=66" target="_blank">Fish Collection (DNA Extracts)</a></span></li>
                              <li><input type="checkbox" name="db" value="20" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=20" target="_blank">Fish Collection (Vouchers)</a></span></li>
                              <li><input type="checkbox" name="db" value="12" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=12" target="_blank">Herptile Voucher Collection (Ground Beetle Sampling Bycatch Archive Pooling)</a></span></li>
                              <li><input type="checkbox" name="db" value="15" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=15" target="_blank">Herptile Voucher Collection (Ground Beetle Sampling Bycatch Trap Sorting)</a></span></li>
                              <li><input type="checkbox" name="db" value="70" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=70" target="_blank">Herptile Voucher Collection (Small Mammal Sampling Bycatch)</a></span></li>
                              <li><input type="checkbox" name="db" value="24" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=24" target="_blank">Mammal Collection (Blood Samples)</a></span></li>
                              <li><input type="checkbox" name="db" value="71" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=71" target="_blank">Mammal Collection (DNA Extracts)</a></span></li>
                              <li><input type="checkbox" name="db" value="25" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=25" target="_blank">Mammal Collection (Ear Tissue)</a></span></li>
                              <li><input type="checkbox" name="db" value="26" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=26" target="_blank">Mammal Collection (Fecal Samples)</a></span></li>
                              <li><input type="checkbox" name="db" value="27" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=27" target="_blank">Mammal Collection (Hair Samples)</a></span></li>
                              <li><input type="checkbox" name="db" value="64" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=64" target="_blank">Mammal Collection (Vouchers [Ground Beetle Sampling Bycatch Archive Pooling])</a></span></li>
                              <li><input type="checkbox" name="db" value="17" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=17" target="_blank">Mammal Collection (Vouchers [Ground Beetle Sampling Bycatch Trap Sorting])</a></span></li>
                              <li><input type="checkbox" name="db" value="19" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=19" target="_blank">Mammal Collection (Vouchers [Standard Sampling at Diversity Plots])</a></span></li>
                              <li><input type="checkbox" name="db" value="28" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=28" target="_blank">Mammal Collection (Vouchers [Standard Sampling at Pathogen Plots])</a></span></li>
                            </ul>
                          </li>
                        </ul>
                      </li>
                    </ul>

                  </div>
                  <div id="neon-theme" class="box">
                    <h2>Select Collections by NEON Theme</h2>
                    <ul id="collections-list2">
                      <li><input name="db" type="checkbox" class="all-selector all-neon-colls" checked=""><span class="material-icons expansion-icon">indeterminate_check_box</span><span>All NEON Collections</span>
                        <ul>
                          <li><input type="checkbox" class="all-selector child" checked=""><span class="material-icons expansion-icon">add_box</span><span>Atmosphere</span>
                            <ul class="collapsed">
                              <li><input type="checkbox" name="db" value="41" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=41" target="_blank">Particulate Mass Filter Collection</a></span></li>
                              <li><input type="checkbox" name="db" value="42" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=42" target="_blank">Wet Deposition Collection</a></span></li>
                            </ul>
                          </li>
                        </ul>
                        <ul>
                          <li><input type="checkbox" class="all-selector child" checked=""><span class="material-icons expansion-icon">add_box</span><span>Biogeochemistry</span>
                            <ul class="collapsed">
                              <li><input type="checkbox" name="db" value="67" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=67" target="_blank">Benthic Microbe Collection (DNA Extracts)</a></span></li>
                              <li><input type="checkbox" name="db" value="5" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=5" target="_blank">Benthic Microbe Collection (Sterivex Filters)</a></span></li>
                              <li><input type="checkbox" name="db" value="30" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=30" target="_blank">Soil Collection</a></span></li>
                              <li><input type="checkbox" name="db" value="31" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=31" target="_blank">Soil Microbe Collection (Bulk Subsamples)</a></span></li>
                              <li><input type="checkbox" name="db" value="69" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=69" target="_blank">Soil Microbe Collection (DNA Extracts )</a></span></li>
                              <li><input type="checkbox" name="db" value="68" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=68" target="_blank">Surface Water Microbe Collection (DNA Extracts)</a></span></li>
                              <li><input type="checkbox" name="db" value="6" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=6" target="_blank">Surface Water Microbe Collection (Sterivex Filters)</a></span></li>
                              <li><input type="checkbox" name="db" value="18" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=18" target="_blank">Terrestrial Plant Collection (Canopy Foliage)</a></span></li>
                            </ul>
                          </li>
                        </ul>
                        <ul>
                          <li><input type="checkbox" class="all-selector child" checked=""><span class="material-icons expansion-icon">add_box</span><span>Land Cover &amp; Processes</span>
                            <ul class="collapsed">
                              <li><input type="checkbox" name="db" value="41" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=41" target="_blank">Particulate Mass Filter Collection</a></span></li>
                              <li><input type="checkbox" name="db" value="30" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=30" target="_blank">Soil Collection</a></span></li>
                              <li><input type="checkbox" name="db" value="18" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=18" target="_blank">Terrestrial Plant Collection (Canopy Foliage)</a></span></li>
                            </ul>
                          </li>
                        </ul>
                        <ul>
                          <li><input type="checkbox" class="all-selector child" checked=""><span class="material-icons expansion-icon">add_box</span><span>Organisms, Populations, and Communities</span>
                            <ul class="collapsed">
                              <li><input type="checkbox" name="db" value="50" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=50" target="_blank">Aquatic Macroalgae Collection (Chemical Preservation [Clip Harvests])</a></span></li>
                              <li><input type="checkbox" name="db" value="73" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=73" target="_blank">Aquatic Macroalgae Collection (Chemical Preservation [Point Counts])</a></span></li>
                              <li><input type="checkbox" name="db" value="47" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=47" target="_blank">Aquatic Microalgae Collection (Chemical Preservation)</a></span></li>
                              <li><input type="checkbox" name="db" value="46" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=46" target="_blank">Aquatic Microalgae Collection (Freeze-dried)</a></span></li>
                              <li><input type="checkbox" name="db" value="49" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=49" target="_blank">Aquatic Microalgae Collection (Microscope Slides)</a></span></li>
                              <li><input type="checkbox" name="db" value="9" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=9" target="_blank">Aquatic Plant, Bryophyte, and Lichen Collection (Herbarium Vouchers [Clip Harvests])</a></span></li>
                              <li><input type="checkbox" name="db" value="7" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=7" target="_blank">Aquatic Plant, Bryophyte, and Lichen Collection (Herbarium Vouchers [Point Counts])</a></span></li>
                              <li><input type="checkbox" name="db" value="8" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=8" target="_blank">Aquatic Plant, Bryophyte, and Lichen Collection (Herbarium Vouchers [Standard Sampling])</a></span></li>
                              <li><input type="checkbox" value="22" class="child" disabled=""><span style="color: gray">Benthic Macroinvertebrate Collection (Chironomids) - Samples Unavailable</span> <a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=22"
                                  target="_blank">More Info</a></li>
                              <li><input type="checkbox" value="61" class="child" disabled=""><span style="color: gray">Benthic Macroinvertebrate Collection (DNA Extracts) - Samples Unavailable</span> <a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=61"
                                  target="_blank">More Info</a></li>
                              <li><input type="checkbox" value="53" class="child" disabled=""><span style="color: gray">Benthic Macroinvertebrate Collection (Microscope Slides) - Samples Unavailable</span> <a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=53"
                                  target="_blank">More Info</a></li>
                              <li><input type="checkbox" value="52" class="child" disabled=""><span style="color: gray">Benthic Macroinvertebrate Collection (Oligochaetes) - Samples Unavailable</span> <a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=52"
                                  target="_blank">More Info</a></li>
                              <li><input type="checkbox" value="48" class="child" disabled=""><span style="color: gray">Benthic Macroinvertebrate Collection (Taxonomy Reference Collection) - Samples Unavailable</span> <a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=48"
                                  target="_blank">More Info</a></li>
                              <li><input type="checkbox" value="57" class="child" disabled=""><span style="color: gray">Benthic Macroinvertebrate Collection (Taxonomy Subsample) - Samples Unavailable</span> <a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=57"
                                  target="_blank">More Info</a></li>
                              <li><input type="checkbox" name="db" value="21" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=21" target="_blank">Benthic Macroinvertebrate Collection (Unsorted Bulk Sample)</a></span></li>
                              <li><input type="checkbox" name="db" value="67" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=67" target="_blank">Benthic Microbe Collection (DNA Extracts)</a></span></li>
                              <li><input type="checkbox" name="db" value="5" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=5" target="_blank">Benthic Microbe Collection (Sterivex Filters)</a></span></li>
                              <li><input type="checkbox" name="db" value="11" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=11" target="_blank">Carabid Collection (Archive Pooling)</a></span></li>
                              <li><input type="checkbox" name="db" value="63" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=63" target="_blank">Carabid Collection (DNA Extracts)</a></span></li>
                              <li><input type="checkbox" name="db" value="39" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=39" target="_blank">Carabid Collection (Pinned Vouchers)</a></span></li>
                              <li><input type="checkbox" name="db" value="14" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=14" target="_blank">Carabid Collection (Trap Sorting)</a></span></li>
                              <li><input type="checkbox" name="db" value="66" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=66" target="_blank">Fish Collection (DNA Extracts)</a></span></li>
                              <li><input type="checkbox" name="db" value="20" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=20" target="_blank">Fish Collection (Vouchers)</a></span></li>
                              <li><input type="checkbox" name="db" value="12" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=12" target="_blank">Herptile Voucher Collection (Ground Beetle Sampling Bycatch Archive Pooling)</a></span></li>
                              <li><input type="checkbox" name="db" value="15" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=15" target="_blank">Herptile Voucher Collection (Ground Beetle Sampling Bycatch Trap Sorting)</a></span></li>
                              <li><input type="checkbox" name="db" value="70" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=70" target="_blank">Herptile Voucher Collection (Small Mammal Sampling Bycatch)</a></span></li>
                              <li><input type="checkbox" name="db" value="13" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=13" target="_blank">Invertebrate Bycatch Collection (Archive Pooling)</a></span></li>
                              <li><input type="checkbox" name="db" value="16" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=16" target="_blank">Invertebrate Bycatch Collection (Trap Sorting)</a></span></li>
                              <li><input type="checkbox" name="db" value="24" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=24" target="_blank">Mammal Collection (Blood Samples)</a></span></li>
                              <li><input type="checkbox" name="db" value="71" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=71" target="_blank">Mammal Collection (DNA Extracts)</a></span></li>
                              <li><input type="checkbox" name="db" value="25" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=25" target="_blank">Mammal Collection (Ear Tissue)</a></span></li>
                              <li><input type="checkbox" name="db" value="26" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=26" target="_blank">Mammal Collection (Fecal Samples)</a></span></li>
                              <li><input type="checkbox" name="db" value="27" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=27" target="_blank">Mammal Collection (Hair Samples)</a></span></li>
                              <li><input type="checkbox" name="db" value="64" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=64" target="_blank">Mammal Collection (Vouchers [Ground Beetle Sampling Bycatch Archive Pooling])</a></span></li>
                              <li><input type="checkbox" name="db" value="17" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=17" target="_blank">Mammal Collection (Vouchers [Ground Beetle Sampling Bycatch Trap Sorting])</a></span></li>
                              <li><input type="checkbox" name="db" value="19" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=19" target="_blank">Mammal Collection (Vouchers [Standard Sampling at Diversity Plots])</a></span></li>
                              <li><input type="checkbox" name="db" value="28" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=28" target="_blank">Mammal Collection (Vouchers [Standard Sampling at Pathogen Plots])</a></span></li>
                              <li><input type="checkbox" name="db" value="56" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=56" target="_blank">Mosquito Collection (Bulk Identified)</a></span></li>
                              <li><input type="checkbox" name="db" value="58" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=58" target="_blank">Mosquito Collection (Bulk Unidentified)</a></span></li>
                              <li><input type="checkbox" name="db" value="65" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=65" target="_blank">Mosquito Collection (DNA Extracts)</a></span></li>
                              <li><input type="checkbox" name="db" value="59" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=59" target="_blank">Mosquito Collection (Pathogen Extracts)</a></span></li>
                              <li><input type="checkbox" name="db" value="29" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=29" target="_blank">Mosquito Collection (Pinned Vouchers)</a></span></li>
                              <li><input type="checkbox" name="db" value="4" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=4" target="_blank">NEON Biorepository Invertebrate Collections at Arizona State University</a></span></li>
                              <li><input type="checkbox" name="db" value="31" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=31" target="_blank">Soil Microbe Collection (Bulk Subsamples)</a></span></li>
                              <li><input type="checkbox" name="db" value="69" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=69" target="_blank">Soil Microbe Collection (DNA Extracts )</a></span></li>
                              <li><input type="checkbox" name="db" value="68" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=68" target="_blank">Surface Water Microbe Collection (DNA Extracts)</a></span></li>
                              <li><input type="checkbox" name="db" value="6" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=6" target="_blank">Surface Water Microbe Collection (Sterivex Filters)</a></span></li>
                              <li><input type="checkbox" name="db" value="76" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=76" target="_blank">Terrestrial Plant Collection (Belowground Biomass [Megapit])</a></span></li>
                              <li><input type="checkbox" name="db" value="10" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=10" target="_blank">Terrestrial Plant Collection (Belowground Biomass [Standard Sampling])</a></span></li>
                              <li><input type="checkbox" name="db" value="54" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=54" target="_blank">Terrestrial Plant Collection (Herbarium Vouchers)</a></span></li>
                              <li><input type="checkbox" name="db" value="40" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=40" target="_blank">Terrestrial Plant Collection (Leaf Tissue)</a></span></li>
                              <li><input type="checkbox" name="db" value="23" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=23" target="_blank">Terrestrial Plant Collection (Litterfall)</a></span></li>
                              <li><input type="checkbox" name="db" value="75" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=75" target="_blank">Tick Collection (Pathogen Extracts)</a></span></li>
                              <li><input type="checkbox" name="db" value="62" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=62" target="_blank">Zooplankton Collection (DNA Extracts)</a></span></li>
                              <li><input type="checkbox" name="db" value="45" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=45" target="_blank">Zooplankton Collection (Remaining Bulk Taxonomy Sample)</a></span></li>
                              <li><input type="checkbox" name="db" value="55" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=55" target="_blank">Zooplankton Collection (Taxonomy Reference Collection)</a></span></li>
                              <li><input type="checkbox" name="db" value="60" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=60" target="_blank">Zooplankton Collection (Unsorted Bulk Sample)</a></span></li>
                            </ul>
                          </li>
                        </ul>
                      </li>
                    </ul>

                  </div>
                  <div id="sample-type" class="box">
                    <h2>Select Collections by Sample Type</h2>
                    <ul id="collections-list3">
                      <li><input name="db" type="checkbox" class="all-selector all-neon-colls" checked=""><span class="material-icons expansion-icon">indeterminate_check_box</span><span>All NEON Collections</span>
                        <ul>
                          <li><input type="checkbox" class="all-selector child" checked=""><span class="material-icons expansion-icon">add_box</span><span>DNA Sample</span>
                            <ul class="collapsed">
                              <li><input type="checkbox" value="61" class="child" disabled=""><span style="color: gray">Benthic Macroinvertebrate Collection (DNA Extracts) - Samples Unavailable</span> <a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=61"
                                  target="_blank">More Info</a></li>
                              <li><input type="checkbox" name="db" value="67" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=67" target="_blank">Benthic Microbe Collection (DNA Extracts)</a></span></li>
                              <li><input type="checkbox" name="db" value="63" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=63" target="_blank">Carabid Collection (DNA Extracts)</a></span></li>
                              <li><input type="checkbox" name="db" value="66" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=66" target="_blank">Fish Collection (DNA Extracts)</a></span></li>
                              <li><input type="checkbox" name="db" value="71" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=71" target="_blank">Mammal Collection (DNA Extracts)</a></span></li>
                              <li><input type="checkbox" name="db" value="65" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=65" target="_blank">Mosquito Collection (DNA Extracts)</a></span></li>
                              <li><input type="checkbox" name="db" value="59" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=59" target="_blank">Mosquito Collection (Pathogen Extracts)</a></span></li>
                              <li><input type="checkbox" name="db" value="69" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=69" target="_blank">Soil Microbe Collection (DNA Extracts )</a></span></li>
                              <li><input type="checkbox" name="db" value="68" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=68" target="_blank">Surface Water Microbe Collection (DNA Extracts)</a></span></li>
                              <li><input type="checkbox" name="db" value="75" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=75" target="_blank">Tick Collection (Pathogen Extracts)</a></span></li>
                              <li><input type="checkbox" name="db" value="62" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=62" target="_blank">Zooplankton Collection (DNA Extracts)</a></span></li>
                            </ul>
                          </li>
                        </ul>
                        <ul>
                          <li><input type="checkbox" class="all-selector child" checked=""><span class="material-icons expansion-icon">add_box</span><span>Dry Sample</span>
                            <ul class="collapsed">
                              <li><input type="checkbox" name="db" value="46" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=46" target="_blank">Aquatic Microalgae Collection (Freeze-dried)</a></span></li>
                              <li><input type="checkbox" name="db" value="49" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=49" target="_blank">Aquatic Microalgae Collection (Microscope Slides)</a></span></li>
                              <li><input type="checkbox" name="db" value="9" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=9" target="_blank">Aquatic Plant, Bryophyte, and Lichen Collection (Herbarium Vouchers [Clip Harvests])</a></span></li>
                              <li><input type="checkbox" name="db" value="7" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=7" target="_blank">Aquatic Plant, Bryophyte, and Lichen Collection (Herbarium Vouchers [Point Counts])</a></span></li>
                              <li><input type="checkbox" name="db" value="8" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=8" target="_blank">Aquatic Plant, Bryophyte, and Lichen Collection (Herbarium Vouchers [Standard Sampling])</a></span></li>
                              <li><input type="checkbox" name="db" value="39" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=39" target="_blank">Carabid Collection (Pinned Vouchers)</a></span></li>
                              <li><input type="checkbox" name="db" value="29" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=29" target="_blank">Mosquito Collection (Pinned Vouchers)</a></span></li>
                              <li><input type="checkbox" name="db" value="4" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=4" target="_blank">NEON Biorepository Invertebrate Collections at Arizona State University</a></span></li>
                              <li><input type="checkbox" name="db" value="54" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=54" target="_blank">Terrestrial Plant Collection (Herbarium Vouchers)</a></span></li>
                            </ul>
                          </li>
                        </ul>
                        <ul>
                          <li><input type="checkbox" class="all-selector child" checked=""><span class="material-icons expansion-icon">add_box</span><span>Environmental Sample</span>
                            <ul class="collapsed">
                              <li><input type="checkbox" name="db" value="41" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=41" target="_blank">Particulate Mass Filter Collection</a></span></li>
                              <li><input type="checkbox" name="db" value="30" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=30" target="_blank">Soil Collection</a></span></li>
                              <li><input type="checkbox" name="db" value="76" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=76" target="_blank">Terrestrial Plant Collection (Belowground Biomass [Megapit])</a></span></li>
                              <li><input type="checkbox" name="db" value="10" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=10" target="_blank">Terrestrial Plant Collection (Belowground Biomass [Standard Sampling])</a></span></li>
                              <li><input type="checkbox" name="db" value="18" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=18" target="_blank">Terrestrial Plant Collection (Canopy Foliage)</a></span></li>
                              <li><input type="checkbox" name="db" value="23" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=23" target="_blank">Terrestrial Plant Collection (Litterfall)</a></span></li>
                              <li><input type="checkbox" name="db" value="42" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=42" target="_blank">Wet Deposition Collection</a></span></li>
                            </ul>
                          </li>
                        </ul>
                        <ul>
                          <li><input type="checkbox" class="all-selector child" checked=""><span class="material-icons expansion-icon">add_box</span><span>Fluid-Preserved Sample</span>
                            <ul class="collapsed">
                              <li><input type="checkbox" name="db" value="50" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=50" target="_blank">Aquatic Macroalgae Collection (Chemical Preservation [Clip Harvests])</a></span></li>
                              <li><input type="checkbox" name="db" value="73" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=73" target="_blank">Aquatic Macroalgae Collection (Chemical Preservation [Point Counts])</a></span></li>
                              <li><input type="checkbox" name="db" value="47" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=47" target="_blank">Aquatic Microalgae Collection (Chemical Preservation)</a></span></li>
                              <li><input type="checkbox" value="22" class="child" disabled=""><span style="color: gray">Benthic Macroinvertebrate Collection (Chironomids) - Samples Unavailable</span> <a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=22"
                                  target="_blank">More Info</a></li>
                              <li><input type="checkbox" value="52" class="child" disabled=""><span style="color: gray">Benthic Macroinvertebrate Collection (Oligochaetes) - Samples Unavailable</span> <a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=52"
                                  target="_blank">More Info</a></li>
                              <li><input type="checkbox" value="48" class="child" disabled=""><span style="color: gray">Benthic Macroinvertebrate Collection (Taxonomy Reference Collection) - Samples Unavailable</span> <a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=48"
                                  target="_blank">More Info</a></li>
                              <li><input type="checkbox" value="57" class="child" disabled=""><span style="color: gray">Benthic Macroinvertebrate Collection (Taxonomy Subsample) - Samples Unavailable</span> <a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=57"
                                  target="_blank">More Info</a></li>
                              <li><input type="checkbox" name="db" value="21" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=21" target="_blank">Benthic Macroinvertebrate Collection (Unsorted Bulk Sample)</a></span></li>
                              <li><input type="checkbox" name="db" value="11" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=11" target="_blank">Carabid Collection (Archive Pooling)</a></span></li>
                              <li><input type="checkbox" name="db" value="14" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=14" target="_blank">Carabid Collection (Trap Sorting)</a></span></li>
                              <li><input type="checkbox" name="db" value="20" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=20" target="_blank">Fish Collection (Vouchers)</a></span></li>
                              <li><input type="checkbox" name="db" value="12" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=12" target="_blank">Herptile Voucher Collection (Ground Beetle Sampling Bycatch Archive Pooling)</a></span></li>
                              <li><input type="checkbox" name="db" value="15" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=15" target="_blank">Herptile Voucher Collection (Ground Beetle Sampling Bycatch Trap Sorting)</a></span></li>
                              <li><input type="checkbox" name="db" value="70" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=70" target="_blank">Herptile Voucher Collection (Small Mammal Sampling Bycatch)</a></span></li>
                              <li><input type="checkbox" name="db" value="13" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=13" target="_blank">Invertebrate Bycatch Collection (Archive Pooling)</a></span></li>
                              <li><input type="checkbox" name="db" value="16" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=16" target="_blank">Invertebrate Bycatch Collection (Trap Sorting)</a></span></li>
                              <li><input type="checkbox" name="db" value="64" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=64" target="_blank">Mammal Collection (Vouchers [Ground Beetle Sampling Bycatch Archive Pooling])</a></span></li>
                              <li><input type="checkbox" name="db" value="17" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=17" target="_blank">Mammal Collection (Vouchers [Ground Beetle Sampling Bycatch Trap Sorting])</a></span></li>
                              <li><input type="checkbox" name="db" value="45" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=45" target="_blank">Zooplankton Collection (Remaining Bulk Taxonomy Sample)</a></span></li>
                              <li><input type="checkbox" name="db" value="55" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=55" target="_blank">Zooplankton Collection (Taxonomy Reference Collection)</a></span></li>
                              <li><input type="checkbox" name="db" value="60" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=60" target="_blank">Zooplankton Collection (Unsorted Bulk Sample)</a></span></li>
                            </ul>
                          </li>
                        </ul>
                        <ul>
                          <li><input type="checkbox" class="all-selector child" checked=""><span class="material-icons expansion-icon">add_box</span><span>Frozen Sample</span>
                            <ul class="collapsed">
                              <li><input type="checkbox" name="db" value="5" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=5" target="_blank">Benthic Microbe Collection (Sterivex Filters)</a></span></li>
                              <li><input type="checkbox" name="db" value="19" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=19" target="_blank">Mammal Collection (Vouchers [Standard Sampling at Diversity Plots])</a></span></li>
                              <li><input type="checkbox" name="db" value="28" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=28" target="_blank">Mammal Collection (Vouchers [Standard Sampling at Pathogen Plots])</a></span></li>
                              <li><input type="checkbox" name="db" value="56" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=56" target="_blank">Mosquito Collection (Bulk Identified)</a></span></li>
                              <li><input type="checkbox" name="db" value="58" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=58" target="_blank">Mosquito Collection (Bulk Unidentified)</a></span></li>
                              <li><input type="checkbox" name="db" value="31" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=31" target="_blank">Soil Microbe Collection (Bulk Subsamples)</a></span></li>
                              <li><input type="checkbox" name="db" value="6" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=6" target="_blank">Surface Water Microbe Collection (Sterivex Filters)</a></span></li>
                            </ul>
                          </li>
                        </ul>
                        <ul>
                          <li><input type="checkbox" class="all-selector child" checked=""><span class="material-icons expansion-icon">add_box</span><span>Slide-Mounted Sample</span>
                            <ul class="collapsed">
                              <li><input type="checkbox" value="53" class="child" disabled=""><span style="color: gray">Benthic Macroinvertebrate Collection (Microscope Slides) - Samples Unavailable</span> <a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=53"
                                  target="_blank">More Info</a></li>
                            </ul>
                          </li>
                        </ul>
                        <ul>
                          <li><input type="checkbox" class="all-selector child" checked=""><span class="material-icons expansion-icon">add_box</span><span>Tissue or Fecal Sample</span>
                            <ul class="collapsed">
                              <li><input type="checkbox" name="db" value="24" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=24" target="_blank">Mammal Collection (Blood Samples)</a></span></li>
                              <li><input type="checkbox" name="db" value="25" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=25" target="_blank">Mammal Collection (Ear Tissue)</a></span></li>
                              <li><input type="checkbox" name="db" value="26" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=26" target="_blank">Mammal Collection (Fecal Samples)</a></span></li>
                              <li><input type="checkbox" name="db" value="27" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=27" target="_blank">Mammal Collection (Hair Samples)</a></span></li>
                              <li><input type="checkbox" name="db" value="40" class="child" checked=""><span><a href="https://biorepo.neonscience.org/portal/collections/misc/collprofiles.php?collid=40" target="_blank">Terrestrial Plant Collection (Leaf Tissue)</a></span></li>
                            </ul>
                          </li>
                        </ul>
                      </li>
                    </ul>

                  </div>
                </div>
              </div>
            </div>
          </section>
          <!-- Sample Properties -->
          <section>
            <!-- Accordion selector -->
            <input type="checkbox" id="sample" class="accordion-selector" checked=true />
            <!-- Accordion header -->
            <label for="sample" class="accordion-header">Sample Properties</label>
            <!-- Accordion content -->
            <div class="content">
              <div id="search-form-sample">
                <div>
                  <div>
                    <input type="checkbox" name="includeothercatnum" value="1" checked>
                    <label for="includeothercatnum">Include other catalogue numbers and GUIds</label>
                  </div>
                  <div class="input-text-container">
                    <label for="" class="input-text--outlined">
                    <input type="text" name="catnum">
                    <span data-label="Catalog Number"></span></label>
                    <span class="assistive-text">Separate multiple with commas.</span>
                  </div>
                </div>
                <div>
                  <div>
                    <input type="checkbox" name="hasimages" value=1>
                    <label for="hasimages">Limit to specimens with images</label>
                  </div>
                  <div>
                    <input type="checkbox" name="hasgenetic" value=1>
                    <label for="hasgenetic">Limit to specimens with genetic data</label>
                  </div>
                </div>
              </div>
            </div>
          </section>
          <!-- Locality -->
          <section>
            <!-- Accordion selector -->
            <input type="checkbox" id="locality" class="accordion-selector" />
            <!-- Accordion header -->
            <label for="locality" class="accordion-header">Locality</label>
            <!-- Accordion content -->
            <div class="content">
              <div id="search-form-locality">
              <!-- <ul id="site-list"></ul> -->
              <ul id="site-list">
                <li><input id="allSites" name="datasetid" data-chip="All Domains & Sites" type="checkbox" class="all-selector" checked=""><span class="material-icons expansion-icon">indeterminate_check_box</span><span>All NEON Domains and Sites</span>
                  <ul>
                    <li><input type="checkbox" class="all-selector child" checked=""><span class="material-icons expansion-icon">add_box</span><span>D01 - Northeast</span>
                      <ul class="collapsed">
                        <li><input type="checkbox" name="datasetid" value="BART" class="child" checked=""><span>(BART) Bartlett Experimental Forest</span></li>
                        <li><input type="checkbox" name="datasetid" value="HARV" class="child" checked=""><span>(HARV) Harvard Forest</span></li>
                        <li><input type="checkbox" name="datasetid" value="HOPB" class="child" checked=""><span>(HOPB) Lower Hop Brook</span></li>
                      </ul>
                    </li>
                  </ul>
                  <ul>
                    <li><input type="checkbox" class="all-selector child" checked=""><span class="material-icons expansion-icon">add_box</span><span>D02 - Mid-Atlantic</span>
                      <ul class="collapsed">
                        <li><input type="checkbox" name="datasetid" value="BLAN" class="child" checked=""><span>(BLAN) Blandy Experimental Farm</span></li>
                        <li><input type="checkbox" name="datasetid" value="LEWI" class="child" checked=""><span>(LEWI) Lewis Run</span></li>
                        <li><input type="checkbox" name="datasetid" value="POSE" class="child" checked=""><span>(POSE) Posey Creek</span></li>
                        <li><input type="checkbox" name="datasetid" value="SCBI" class="child" checked=""><span>(SCBI) Smithsonian Conservation Biology Institute</span></li>
                        <li><input type="checkbox" name="datasetid" value="SERC" class="child" checked=""><span>(SERC) Smithsonian Environmental Research Center</span></li>
                      </ul>
                    </li>
                  </ul>
                  <ul>
                    <li><input type="checkbox" class="all-selector child" checked=""><span class="material-icons expansion-icon">add_box</span><span>D03 - Southeast</span>
                      <ul class="collapsed">
                        <li><input type="checkbox" name="datasetid" value="BARC" class="child" checked=""><span>(BARC) Ordway-Swisher Biological Station - Barco Lake</span></li>
                        <li><input type="checkbox" name="datasetid" value="DSNY" class="child" checked="" checked=""><span>(DSNY) Disney Wilderness Preserve</span></li>
                        <li><input type="checkbox" name="datasetid" value="FLNT" class="child" checked="" checked=""><span>(FLNT) Flint River</span></li>
                        <li><input type="checkbox" name="datasetid" value="JERC" class="child" checked="" checked=""><span>(JERC) Jones Ecological Research Center</span></li>
                        <li><input type="checkbox" name="datasetid" value="OSBS" class="child" checked="" checked=""><span>(OSBS) Ordway-Swisher Biological Station</span></li>
                        <li><input type="checkbox" name="datasetid" value="SUGG" class="child" checked="" checked=""><span>(SUGG) Ordway-Swisher Biological Station - Suggs Lake</span></li>
                      </ul>
                    </li>
                  </ul>
                  <ul>
                    <li><input type="checkbox" class="all-selector child" checked=""><span class="material-icons expansion-icon">add_box</span><span>D04 - Atlantic Neotropical</span>
                      <ul class="collapsed">
                        <li><input type="checkbox" name="datasetid" value="CUPE" class="child" checked=""><span>(CUPE) Rio Cupeyes</span></li>
                        <li><input type="checkbox" name="datasetid" value="GUAN" class="child" checked=""><span>(GUAN) Guanica Forest</span></li>
                        <li><input type="checkbox" name="datasetid" value="GUIL" class="child" checked=""><span>(GUIL) Rio Guilarte</span></li>
                        <li><input type="checkbox" name="datasetid" value="LAJA" class="child" checked=""><span>(LAJA) Lajas Experimental Station</span></li>
                      </ul>
                    </li>
                  </ul>
                  <ul>
                    <li><input type="checkbox" class="all-selector child" checked=""><span class="material-icons expansion-icon">add_box</span><span>D05 - Great Lakes</span>
                      <ul class="collapsed">
                        <li><input type="checkbox" name="datasetid" value="CRAM" class="child" checked=""><span>(CRAM) Crampton Lake</span></li>
                        <li><input type="checkbox" name="datasetid" value="LIRO" class="child" checked=""><span>(LIRO) Little Rock Lake</span></li>
                        <li><input type="checkbox" name="datasetid" value="STEI" class="child" checked=""><span>(STEI) Steigerwaldt Land Services</span></li>
                        <li><input type="checkbox" name="datasetid" value="TREE" class="child" checked=""><span>(TREE) Treehaven</span></li>
                        <li><input type="checkbox" name="datasetid" value="UNDE" class="child" checked=""><span>(UNDE) UNDERC</span></li>
                      </ul>
                    </li>
                  </ul>
                  <ul>
                    <li><input type="checkbox" class="all-selector child" checked=""><span class="material-icons expansion-icon">add_box</span><span>D06 - Prairie Peninsula</span>
                      <ul class="collapsed">
                        <li><input type="checkbox" name="datasetid" value="KING" class="child" checked=""><span>(KING) Kings Creek</span></li>
                        <li><input type="checkbox" name="datasetid" value="KONA" class="child" checked=""><span>(KONA) Konza Prairie Biological Station - Relocatable</span></li>
                        <li><input type="checkbox" name="datasetid" value="KONZ" class="child" checked=""><span>(KONZ) Konza Prairie Biological Station</span></li>
                        <li><input type="checkbox" name="datasetid" value="MCDI" class="child" checked=""><span>(MCDI) McDiffett Creek</span></li>
                        <li><input type="checkbox" name="datasetid" value="UKFS" class="child" checked=""><span>(UKFS) The University of Kansas Field Station</span></li>
                      </ul>
                    </li>
                  </ul>
                  <ul>
                    <li><input type="checkbox" class="all-selector child" checked=""><span class="material-icons expansion-icon">add_box</span><span>D07 - Appalachians &amp; Cumberland Plateau</span>
                      <ul class="collapsed">
                        <li><input type="checkbox" name="datasetid" value="GRSM" class="child" checked=""><span>(GRSM) Great Smoky Mountains National Park, Twin Creeks</span></li>
                        <li><input type="checkbox" name="datasetid" value="LECO" class="child" checked=""><span>(LECO) LeConte Creek</span></li>
                        <li><input type="checkbox" name="datasetid" value="MLBS" class="child" checked=""><span>(MLBS) Mountain Lake Biological Station</span></li>
                        <li><input type="checkbox" name="datasetid" value="ORNL" class="child" checked=""><span>(ORNL) Oak Ridge</span></li>
                        <li><input type="checkbox" name="datasetid" value="WALK" class="child" checked=""><span>(WALK) Walker Branch</span></li>
                      </ul>
                    </li>
                  </ul>
                  <ul>
                    <li><input type="checkbox" class="all-selector child" checked=""><span class="material-icons expansion-icon">add_box</span><span>D08 - Ozarks Complex</span>
                      <ul class="collapsed">
                        <li><input type="checkbox" name="datasetid" value="BLWA" class="child" checked=""><span>(BLWA) Black Warrior River near Dead Lake</span></li>
                        <li><input type="checkbox" name="datasetid" value="DELA" class="child" checked=""><span>(DELA) Dead Lake</span></li>
                        <li><input type="checkbox" name="datasetid" value="LENO" class="child" checked=""><span>(LENO) Lenoir Landing</span></li>
                        <li><input type="checkbox" name="datasetid" value="MAYF" class="child" checked=""><span>(MAYF) Mayfield Creek</span></li>
                        <li><input type="checkbox" name="datasetid" value="TALL" class="child" checked=""><span>(TALL) Talladega National Forest</span></li>
                        <li><input type="checkbox" name="datasetid" value="TOMB" class="child" checked=""><span>(TOMB) Lower Tombigbee River at Choctaw Refuge</span></li>
                      </ul>
                    </li>
                  </ul>
                  <ul>
                    <li><input type="checkbox" class="all-selector child" checked=""><span class="material-icons expansion-icon">add_box</span><span>D09 - Northern Plains</span>
                      <ul class="collapsed">
                        <li><input type="checkbox" name="datasetid" value="DCFS" class="child" checked=""><span>(DCFS) Dakota Coteau Field School</span></li>
                        <li><input type="checkbox" name="datasetid" value="NOGP" class="child" checked=""><span>(NOGP) Northern Great Plains Research Laboratory</span></li>
                        <li><input type="checkbox" name="datasetid" value="PRLA" class="child" checked=""><span>(PRLA) Prairie Lake at Dakota Coteau Field School</span></li>
                        <li><input type="checkbox" name="datasetid" value="PRPO" class="child" checked=""><span>(PRPO) Prairie Pothole </span></li>
                        <li><input type="checkbox" name="datasetid" value="WOOD" class="child" checked=""><span>(WOOD) Woodworth</span></li>
                      </ul>
                    </li>
                  </ul>
                  <ul>
                    <li><input type="checkbox" class="all-selector child" checked=""><span class="material-icons expansion-icon">add_box</span><span>D10 - Central Plains</span>
                      <ul class="collapsed">
                        <li><input type="checkbox" name="datasetid" value="ARIK" class="child" checked=""><span>(ARIK) Arikaree River</span></li>
                        <li><input type="checkbox" name="datasetid" value="CPER" class="child" checked=""><span>(CPER) Central Plains Experimental Range</span></li>
                        <li><input type="checkbox" name="datasetid" value="RMNP" class="child" checked=""><span>(RMNP) Rocky Mountain National Park, CASTNET</span></li>
                        <li><input type="checkbox" name="datasetid" value="STER" class="child" checked=""><span>(STER) North Sterling, CO</span></li>
                      </ul>
                    </li>
                  </ul>
                  <ul>
                    <li><input type="checkbox" class="all-selector child" checked=""><span class="material-icons expansion-icon">add_box</span><span>D11 - Southern Plains</span>
                      <ul class="collapsed">
                        <li><input type="checkbox" name="datasetid" value="BLUE" class="child" checked=""><span>(BLUE) Blue River</span></li>
                        <li><input type="checkbox" name="datasetid" value="CLBJ" class="child" checked=""><span>(CLBJ) LBJ National Grassland </span></li>
                        <li><input type="checkbox" name="datasetid" value="OAES" class="child" checked=""><span>(OAES) Klemme Range Research Station</span></li>
                        <li><input type="checkbox" name="datasetid" value="PRIN" class="child" checked=""><span>(PRIN) Pringle Creek</span></li>
                      </ul>
                    </li>
                  </ul>
                  <ul>
                    <li><input type="checkbox" class="all-selector child" checked=""><span class="material-icons expansion-icon">add_box</span><span>D12 - Northern Rockies</span>
                      <ul class="collapsed">
                        <li><input type="checkbox" name="datasetid" value="BLDE" class="child" checked=""><span>(BLDE) Blacktail Deer Creek</span></li>
                        <li><input type="checkbox" name="datasetid" value="YELL" class="child" checked=""><span>(YELL) Yellowstone Northern Range (Frog Rock)</span></li>
                      </ul>
                    </li>
                  </ul>
                  <ul>
                    <li><input type="checkbox" class="all-selector child" checked=""><span class="material-icons expansion-icon">add_box</span><span>D13 - Southern Rockies &amp; Colorado Plateau</span>
                      <ul class="collapsed">
                        <li><input type="checkbox" name="datasetid" value="COMO" class="child" checked=""><span>(COMO) Como Creek</span></li>
                        <li><input type="checkbox" name="datasetid" value="MOAB" class="child" checked=""><span>(MOAB) Moab</span></li>
                        <li><input type="checkbox" name="datasetid" value="NIWO" class="child" checked=""><span>(NIWO) Niwot Ridge Mountain Research Station</span></li>
                        <li><input type="checkbox" name="datasetid" value="WLOU" class="child" checked=""><span>(WLOU) West St Louis Creek</span></li>
                      </ul>
                    </li>
                  </ul>
                  <ul>
                    <li><input type="checkbox" class="all-selector child" checked=""><span class="material-icons expansion-icon">add_box</span><span>D14 - Desert Southwest</span>
                      <ul class="collapsed">
                        <li><input type="checkbox" name="datasetid" value="JORN" class="child" checked=""><span>(JORN) Jornada LTER</span></li>
                        <li><input type="checkbox" name="datasetid" value="SRER" class="child" checked=""><span>(SRER) Santa Rita Experimental Range</span></li>
                        <li><input type="checkbox" name="datasetid" value="SYCA" class="child" checked=""><span>(SYCA) Sycamore Creek</span></li>
                      </ul>
                    </li>
                  </ul>
                  <ul>
                    <li><input type="checkbox" class="all-selector child" checked=""><span class="material-icons expansion-icon">add_box</span><span>D15 - Great Basin</span>
                      <ul class="collapsed">
                        <li><input type="checkbox" name="datasetid" value="ONAQ" class="child" checked=""><span>(ONAQ) Onaqui</span></li>
                        <li><input type="checkbox" name="datasetid" value="REDB" class="child" checked=""><span>(REDB) Red Butte Creek</span></li>
                      </ul>
                    </li>
                  </ul>
                  <ul>
                    <li><input type="checkbox" class="all-selector child" checked=""><span class="material-icons expansion-icon">add_box</span><span>D16 - Pacific Northwest</span>
                      <ul class="collapsed">
                        <li><input type="checkbox" name="datasetid" value="ABBY" class="child" checked=""><span>(ABBY) Abby Road</span></li>
                        <li><input type="checkbox" name="datasetid" value="MART" class="child" checked=""><span>(MART) Martha Creek</span></li>
                        <li><input type="checkbox" name="datasetid" value="MCRA" class="child" checked=""><span>(MCRA) McRae Creek</span></li>
                        <li><input type="checkbox" name="datasetid" value="WREF" class="child" checked=""><span>(WREF) Wind River Experimental Forest</span></li>
                      </ul>
                    </li>
                  </ul>
                  <ul>
                    <li><input type="checkbox" class="all-selector child" checked=""><span class="material-icons expansion-icon">add_box</span><span>D17 - Pacific Southwest</span>
                      <ul class="collapsed">
                        <li><input type="checkbox" name="datasetid" value="BIGC" class="child" checked=""><span>(BIGC) Upper Big Creek</span></li>
                        <li><input type="checkbox" name="datasetid" value="SJER" class="child" checked=""><span>(SJER) San Joaquin Experimental Range</span></li>
                        <li><input type="checkbox" name="datasetid" value="SOAP" class="child" checked=""><span>(SOAP) Soaproot Saddle</span></li>
                        <li><input type="checkbox" name="datasetid" value="TEAK" class="child" checked=""><span>(TEAK) Lower Teakettle</span></li>
                        <li><input type="checkbox" name="datasetid" value="TECR" class="child" checked=""><span>(TECR) Teakettle 2 Creek</span></li>
                      </ul>
                    </li>
                  </ul>
                  <ul>
                    <li><input type="checkbox" class="all-selector child" checked=""><span class="material-icons expansion-icon">add_box</span><span>D18 - Tundra</span>
                      <ul class="collapsed">
                        <li><input type="checkbox" name="datasetid" value="BARR" class="child" checked=""><span>(BARR) Barrow Environmental Observatory</span></li>
                        <li><input type="checkbox" name="datasetid" value="OKSR" class="child" checked=""><span>(OKSR) Oksrukuyik Creek</span></li>
                        <li><input type="checkbox" name="datasetid" value="TOOK" class="child" checked=""><span>(TOOK) Toolik Lake</span></li>
                        <li><input type="checkbox" name="datasetid" value="TOOL" class="child" checked=""><span>(TOOL) Toolik</span></li>
                      </ul>
                    </li>
                  </ul>
                  <ul>
                    <li><input type="checkbox" class="all-selector child" checked=""><span class="material-icons expansion-icon">add_box</span><span>D19 - Taiga</span>
                      <ul class="collapsed">
                        <li><input type="checkbox" name="datasetid" value="BONA" class="child" checked=""><span>(BONA) Caribou-Poker Creeks Research Watershed</span></li>
                        <li><input type="checkbox" name="datasetid" value="CARI" class="child" checked=""><span>(CARI) Caribou Creek, Caribou-Poker Creeks Research Watershed</span></li>
                        <li><input type="checkbox" name="datasetid" value="DEJU" class="child" checked=""><span>(DEJU) Delta Junction</span></li>
                        <li><input type="checkbox" name="datasetid" value="HEAL" class="child" checked=""><span>(HEAL) Healy</span></li>
                      </ul>
                    </li>
                  </ul>
                  <ul>
                    <li><input type="checkbox" class="all-selector child" checked=""><span class="material-icons expansion-icon">add_box</span><span>D20 - Pacific Tropical</span>
                      <ul class="collapsed">
                        <li><input type="checkbox" name="datasetid" value="PUUM" class="child" checked=""><span>(PUUM) Pu'u Maka'ala Natural Area Reserve</span></li>
                      </ul>
                    </li>
                  </ul>
                </li>
              </ul>
              <div>
                <div>
                  <div class="input-text-container">
                    <label for="" class="input-text--outlined">
                  <input type="text" name="state">
                  <span data-label="State"></span></label>
                    <span class="assistive-text">Separate multiple with commas.</span>
                  </div>
                  <div class="input-text-container">
                    <label for="" class="input-text--outlined">
                  <input type="text" name="county">
                  <span data-label="County"></span></label>
                    <span class="assistive-text">Separate multiple with commas.</span>
                  </div>
                  <div class="input-text-container">
                    <label for="" class="input-text--outlined">
                  <input type="text" name="local">
                  <span data-label="Locality"></span></label>
                    <span class="assistive-text">Separate multiple with commas.</span>
                  </div>
                </div>
                <div class="grid grid--half">
                  <div class="input-text-container">
                    <label for="" class="input-text--outlined">
                        <input type="text" name="elevlow">
                        <span data-label="Minimum Elevation"></span></label>
                    <span class="assistive-text">Only numbers.</span>
                  </div>
                  <div class="input-text-container">
                    <label for="" class="input-text--outlined">
                        <input type="text" name="elevhigh" >
                        <span data-label="Maximum Elevation"></span></label>
                    <span class="assistive-text">Only numbers.</span>
                  </div>
                </div>
              </div>
            </div>
            </div>
          </section>
          <!-- Latitude & Longitude -->
          <section>
            <!-- Accordion selector -->
            <input type="checkbox" id="lat-long" class="accordion-selector" />
            <!-- Accordion header -->
            <label for="lat-long" class="accordion-header">Latitude & Longitude</label>
            <!-- Accordion content -->
            <div class="content">
              <div id="search-form-latlong">
                <div>
                  <h3>Bounding Box</h3>
                  <button onclick="openCoordAid('rectangle');return false;">Select in map</button>
                  <div class="input-text-container">
                    <label for="upperlat" class="input-text--outlined">
                    <input type="text" name="upperlat">
                    <select id="upperlat_NS" name="upperlat_NS">
                      <option id="ulN" value="N">N</option>
                      <option id="ulS" value="S">S</option>
                    </select>
                    <span data-label="Northern Latitude"></span></label>
                    <span class="assistive-text">Assistive text.</span>
                  </div>
                  <div class="input-text-container">
                    <label for="bottomlat" class="input-text--outlined">
                    <input type="text" name="bottomlat">
                    <select id="bottomlat_NS" name="bottomlat_NS">
                      <option id="blN" value="N">N</option>
                      <option id="blS" value="S">S</option>
                    </select>
                    <span data-label="Southern Latitude"></span></label>
                    <span class="assistive-text">Assistive text.</span>
                  </div>
                  <div class="input-text-container">
                    <label for="leftlong" class="input-text--outlined">
                    <input type="text" name="leftlong">
                    <select id="leftlong_EW" name="leftlong_EW">
                      <option id="llW" value="W">W</option>
                      <option id="llE" value="E">E</option>
                    </select>
                    <span data-label="Western Longitude"></span></label>
                    <span class="assistive-text">Assistive text.</span>
                  </div>
                  <div class="input-text-container">
                    <label for="rightlong" class="input-text--outlined">
                    <input type="text" name="rightlong">
                    <select id="rightlong_EW" name="rightlong_EW">
                      <option id="rlW" value="W">W</option>
                      <option id="rlE" value="E">E</option>
                    </select>
                    <span data-label="Eastern Longitude"></span></label>
                    <span class="assistive-text">Assistive text.</span>
                  </div>
                </div>
                <div>
                  <h3>Polygon (WKT footpring)</h3>
                  <button onclick="openCoordAid('polygon');return false;">Select in map</button>
                  <div class="text-area-container">
                    <label for="footpringwkt" class="text-area--outlined">
                    <textarea name="footprintwkt" wrap="off" cols="30%" rows="5"></textarea>
                    <span data-label="Polygon"></span></label>
                    <span class="assistive-text">Assistive text.</span>
                  </div>
                </div>
                <div>
                  <h3>Point-Radius</h3>
                  <button onclick="openCoordAid('circle');return false;">Select in map</button>
                  <div class="input-text-container">
                    <label for="pointlat" class="input-text--outlined">
                    <input type="text" name="pointlat">
                    <select id="pointlat_NS" name="pointlat_NS">
                      <option id="N" value="N">N</option>
                      <option id="S" value="S">S</option>
                    </select>
                    <span data-label="Latitude"></span></label>
                    <span class="assistive-text">Assistive text.</span>
                  </div>
                  <div class="input-text-container">
                    <label for="pointlong" class="input-text--outlined">
                    <input type="text" name="pointlong">
                    <select id="pointlong_EW" name="pointlong_EW">
                      <option id="W" value="W">W</option>
                      <option id="E" value="E">E</option>
                    </select>
                    <span data-label="Longitude"></span></label>
                    <span class="assistive-text">Assistive text.</span>
                  </div>
                  <div class="input-text-container">
                    <label for="radius" class="input-text--outlined">
                    <input type="text" name="radius">
                    <select id="radiusunits" name="radiusunits">
                      <option value="km">Kilometers</option>
                      <option value="mi">Miles</option>
                    </select>
                    <span data-label="Radius"></span></label>
                    <span class="assistive-text">Assistive text.</span>
                  </div>
                </div>
              </div>
            </div>
          </section>
          <!-- Collecting Event -->
          <section>
            <!-- Accordion selector -->
            <input type="checkbox" id="coll-event" class="accordion-selector" />
            <!-- Accordion header -->
            <label for="coll-event" class="accordion-header">Collecting Event</label>
            <!-- Accordion content -->
            <div class="content">
              <div id="search-form-coll-event">
                <div class="input-text-container">
                  <label for="eventdate1" class="input-text--outlined">
                  <input type="text" name="eventdate1" >
                  <span data-label="Collection Start Date"></span></label>
                  <span class="assistive-text">Single date or start date of range.</span>
                </div>
                <div class="input-text-container">
                  <label for="eventdate2" class="input-text--outlined">
                    <input type="text" name="eventdate2" >
                    <span data-label="Collection End Date"></span></label>
                  <span class="assistive-text">Single date or start date of range.</span>
                </div>
              </div>
            </div>
          </section>
        </div>
        <!-- Criteria panel -->
        <div id="criteria-panel" style="position: sticky; top: 0; height: 100vh">
          <h2>Criteria</h2>
          <!-- <button>Clear</button>
          <button>Search</button> -->
          <button id="teste-btn">Get all params</button>
          <p><a href="#" id="test-url" target="_blank" style="max-width: 50px">Search URL</a></p>
          <button type="reset">Reset Form</button>
        </div>
      </form>

    </div>
    <?php
    include($SERVER_ROOT.'/includes/footer.php');
    ?>
  </body>
  <script src="js/searchform.js"></script>
  <script src="js/taxasearch.js"></script>
</html>