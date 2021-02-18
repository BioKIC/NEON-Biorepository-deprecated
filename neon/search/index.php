<?php
include_once('../../config/symbini.php');
include_once('../../content/lang/index.'.$LANG_TAG.'.php');
include_once($SERVER_ROOT.'/neon/classes/CollectionMetadata.php');
include_once($SERVER_ROOT.'/neon/classes/DatasetsMetadata.php');
header("Content-Type: text/html; charset=".$CHARSET);

$collData = new CollectionMetadata();
$siteData = new DatasetsMetadata();
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
              <div><input type="checkbox" name="usethes" id="usethes" data-chip="Include Synonyms" value="1" checked>Include Synonyms</div>
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
                <!-- Open NEON Collections modal -->
                <div><input id="all-neon-colls-quick" data-chip="All NEON Collections" class="all-selector" type="checkbox" checked="" data-form-id="biorepo-collections-list"><span id="neon-modal-open" class="material-icons expansion-icon">add_box</span><span>All NEON Biorepository Collections</span></div>
                <!-- External Collections -->
                <div>
                  <ul id="neonext-collections-list">
                    <li><input id="all-neon-ext" data-chip="All Add NEON Colls" type="checkbox" class="all-selector" data-form-id='neonext-collections-list'><span class="material-icons expansion-icon">add_box</span><span>All Additional NEON Collections</span>
                    <?php if($collsArr = $collData->getCollMetaByCat('Additional NEON Collections')){
                    echo '<ul class="collapsed">';
                      foreach($collsArr as $result) {
                        echo "<li><input type='checkbox' name='db' value='{$result["collid"]}' class='child'><span><a href='../../collections/misc/collprofiles.php?collid={$result["collid"]}' target='_blank' rel='noopener noreferrer'>{$result["collectionname"]}</span></a></li>";
                      }
                      echo '</ul>';
                    } ;?>
                    </li>
                  </ul>
                  <ul id="ext-collections-list">
                    <li><input id="all-ext" data-chip="All Ext Colls" type="checkbox" class="all-selector" data-form-id='ext-collections-list'><span class="material-icons expansion-icon">add_box</span><span>All Other Collections from NEON sites</span>
                    <?php if($collsArr = $collData->getCollMetaByCat('Other Collections from NEON sites')){
                    echo '<ul class="collapsed">';
                      foreach($collsArr as $result) {
                        echo "<li><input type='checkbox' name='db' value='{$result["collid"]}' class='child'><span><a href='../../collections/misc/collprofiles.php?collid={$result["collid"]}' target='_blank' rel='noopener noreferrer'>{$result["collectionname"]}</span></a></li>";
                      }
                      echo '</ul>';
                    } ;?>
                    </li>
                  </ul>
                </div>

              </div>
            </div>
            <!-- NEON Biorepository Collections Modal -->
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
                  <!-- By Taxonomic Group -->
                  <div id="taxonomic-cat" class="box" style="display: block;">
                    <h2>Select Collections by Taxonomic Group</h2>
                    <?php if($groupsArr = $collData->getBiorepoGroups('highertaxon')){
                      echo '<ul id="collections-list1"><li><input type="checkbox" name="db" class="all-selector" checked><span class="material-icons expansion-icon">indeterminate_check_box</span><span>All NEON Biorepository Collections</span>';
                      foreach($groupsArr as $result) {                  
                        if($result['highertaxon']){
                          echo "<ul><li><input type='checkbox' class='all-selector child'  checked><span class='material-icons expansion-icon'>add_box</span><span>{$result["highertaxon"]}</span><ul class='collapsed'>";
                          $collsArr = $collData->getBiorepoColls('highertaxon', $result['highertaxon']);
                          if($collsArr){
                            foreach($collsArr as $row) {
                              echo "<li>";
                              // IF AVAILABLE
                              if ($row['available'] == 'TRUE'){
                                echo "<input type='checkbox' name='db' value='{$row["collid"]}' class='child' checked><a href='../../collections/misc/collprofiles.php?collid={$row["collid"]}' target='_blank'>{$row["collectionname"]}</a>";
                              } elseif ($row["available"] == 'FALSE'){
                                echo "<input type='checkbox' name='db' value='{$row['collid']}' class='child' disabled=''><span style='color: gray'>{$row['collectionname']} - Samples Unavailable</span> <a href='../../collections/misc/collprofiles.php?collid={$row['collid']}' target='_blank'>More Info</a>";
                              }
                              echo "</li>";   
                            }
                          }
                          echo '</ul></li></ul>';
                        }
                      }
                      echo '</li></ul>';
                    }
                    ;?>
                  </div>
                  <div id="neon-theme" class="box">
                    <h2>Select Collections by NEON Theme</h2>
                    <?php if($groupsArr = $collData->getBiorepoGroups('neontheme')){
                      echo '<ul id="collections-list2"><li><input type="checkbox" name="db" class="all-selector" checked><span class="material-icons expansion-icon">indeterminate_check_box</span><span>All NEON Biorepository Collections</span>';
                      foreach($groupsArr as $result) {                  
                        if($result['neontheme']){
                          echo "<ul><li><input type='checkbox' class='all-selector child' checked><span class='material-icons expansion-icon'>add_box</span><span>{$result["neontheme"]}</span><ul class='collapsed'>";
                          $collsArr = $collData->getBiorepoColls('neontheme', $result['neontheme']);
                          if($collsArr){
                            foreach($collsArr as $row) {
                              echo "<li>";
                              // IF AVAILABLE
                              if ($row['available'] == 'TRUE'){
                                echo "<input type='checkbox' name='db' value='{$row["collid"]}' class='child' checked><a href='../../collections/misc/collprofiles.php?collid={$row["collid"]}' target='_blank'>{$row["collectionname"]}</a>";
                              } elseif ($row["available"] == 'FALSE'){
                                echo "<input type='checkbox' name='db' value='{$row['collid']}' class='child' disabled=''><span style='color: gray'>{$row['collectionname']} - Samples Unavailable</span> <a href='../../collections/misc/collprofiles.php?collid={$row['collid']}' target='_blank'>More Info</a>";
                              }
                              echo "</li>";   
                            }
                          }
                          echo '</ul></li></ul>';
                        }
                      }
                      echo '</li></ul>';
                    }
                    ;?>                    
                  </div>
                  <div id="sample-type" class="box">
                    <h2>Select Collections by Sample Type</h2>
                    <?php if($groupsArr = $collData->getBiorepoGroups('sampletype')){
                      echo '<ul id="collections-list3"><li><input type="checkbox" name="db" class="all-selector" checked><span class="material-icons expansion-icon">indeterminate_check_box</span><span>All NEON Biorepository Collections</span>';
                      foreach($groupsArr as $result) {                  
                        if($result['sampletype']){
                          echo "<ul><li><input type='checkbox' class='all-selector child' checked><span class='material-icons expansion-icon'>add_box</span><span>{$result["sampletype"]}</span><ul class='collapsed'>";
                          $collsArr = $collData->getBiorepoColls('sampletype', $result['sampletype']);
                          if($collsArr){
                            foreach($collsArr as $row) {
                              echo "<li>";
                              // IF AVAILABLE
                              if ($row['available'] == 'TRUE'){
                                echo "<input type='checkbox' name='db' value='{$row["collid"]}' class='child' checked><a href='../../collections/misc/collprofiles.php?collid={$row["collid"]}' target='_blank'>{$row["collectionname"]}</a>";
                              } elseif ($row["available"] == 'FALSE'){
                                echo "<input type='checkbox' name='db' value='{$row['collid']}' class='child' disabled=''><span style='color: gray'>{$row['collectionname']} - Samples Unavailable</span> <a href='../../collections/misc/collprofiles.php?collid={$row['collid']}' target='_blank'>More Info</a>";
                              }
                              echo "</li>";   
                            }
                          }
                          echo '</ul></li></ul>';
                        }
                      }
                      echo '</li></ul>';
                    }
                    ;?>
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
                    <input type="checkbox" name="includeothercatnum" id="includeothercatnum" value="1" data-chip="Include other IDs" checked>
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
              <ul id="site-list"><input id="all-sites" name="datasetid" data-chip="All Domains & Sites" type="checkbox" class="all-selector" checked="" data-form-id='search-form-locality'><span class="material-icons expansion-icon">indeterminate_check_box</span><span>All NEON Domains and Sites</span>
                  <?php if($domainsArr = $siteData->getNeonDomains()){
                  echo '<ul>';
                    foreach($domainsArr as $result) {
                      echo "<li><input type='checkbox' class='all-selector child' checked=''><span class='material-icons expansion-icon'>add_box</span><span>{$result["domainnumber"]} - {$result["domainname"]}</span>";
                      // ECHO SITES PER DOMAINS
                      echo "</li>";
                    }
                    echo '</ul>';
                  } ;?>
              </ul>
              <!-- <ul id="site-list">
                <li><input id="all-sites" name="datasetid" data-chip="All Domains & Sites" type="checkbox" class="all-selector" checked="" data-form-id='search-form-locality'><span class="material-icons expansion-icon">indeterminate_check_box</span><span>All NEON Domains and Sites</span>
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
              </ul> -->
              <div>
                <div>
                  <div class="input-text-container">
                    <label for="" class="input-text--outlined">
                  <input type="text" name="state" id="state">
                  <span data-label="State"></span></label>
                    <span class="assistive-text">Separate multiple with commas.</span>
                  </div>
                  <div class="input-text-container">
                    <label for="" class="input-text--outlined">
                  <input type="text" name="county" id="county">
                  <span data-label="County"></span></label>
                    <span class="assistive-text">Separate multiple with commas.</span>
                  </div>
                  <div class="input-text-container">
                    <label for="" class="input-text--outlined">
                  <input type="text" name="local" id="local">
                  <span data-label="Locality"></span></label>
                    <span class="assistive-text">Separate multiple with commas.</span>
                  </div>
                </div>
                <div class="grid grid--half">
                  <div class="input-text-container">
                    <label for="" class="input-text--outlined">
                        <input type="text" name="elevlow" id="elevlow">
                        <span data-label="Minimum Elevation"></span></label>
                    <span class="assistive-text">Only numbers.</span>
                  </div>
                  <div class="input-text-container">
                    <label for="" class="input-text--outlined">
                        <input type="text" name="elevhigh"  id="elevhigh">
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
                    <input type="text" id="upperlat" name="upperlat">
                    <select id="upperlat_NS" name="upperlat_NS">
                      <option id="ulN" value="N">N</option>
                      <option id="ulS" value="S">S</option>
                    </select>
                    <span data-label="Northern Latitude"></span></label>
                    <span class="assistive-text">Assistive text.</span>
                  </div>
                  <div class="input-text-container">
                    <label for="bottomlat" class="input-text--outlined">
                    <input type="text" id="bottomlat" name="bottomlat">
                    <select id="bottomlat_NS" name="bottomlat_NS">
                      <option id="blN" value="N">N</option>
                      <option id="blS" value="S">S</option>
                    </select>
                    <span data-label="Southern Latitude"></span></label>
                    <span class="assistive-text">Assistive text.</span>
                  </div>
                  <div class="input-text-container">
                    <label for="leftlong" class="input-text--outlined">
                    <input type="text" id="leftlong" name="leftlong">
                    <select id="leftlong_EW" name="leftlong_EW">
                      <option id="llW" value="W">W</option>
                      <option id="llE" value="E">E</option>
                    </select>
                    <span data-label="Western Longitude"></span></label>
                    <span class="assistive-text">Assistive text.</span>
                  </div>
                  <div class="input-text-container">
                    <label for="rightlong" class="input-text--outlined">
                    <input type="text" id="rightlong" name="rightlong">
                    <select id="rightlong_EW" name="rightlong_EW">
                      <option id="rlW" value="W">W</option>
                      <option id="rlE" value="E">E</option>
                    </select>
                    <span data-label="Eastern Longitude"></span></label>
                    <span class="assistive-text">Assistive text.</span>
                  </div>
                </div>
                <div>
                  <h3>Polygon (WKT footprint)</h3>
                  <button onclick="openCoordAid('polygon');return false;">Select in map</button>
                  <div class="text-area-container">
                    <label for="footprintwkt" class="text-area--outlined">
                    <textarea id="footprintwkt" name="footprintwkt" wrap="off" cols="30%" rows="5"></textarea>
                    <span data-label="Polygon"></span></label>
                    <span class="assistive-text">Assistive text.</span>
                  </div>
                </div>
                <div>
                  <h3>Point-Radius</h3>
                  <button onclick="openCoordAid('circle');return false;">Select in map</button>
                  <div class="input-text-container">
                    <label for="pointlat" class="input-text--outlined">
                    <input type="text" id="pointlat" name="pointlat">
                    <select id="pointlat_NS" name="pointlat_NS">
                      <option id="N" value="N">N</option>
                      <option id="S" value="S">S</option>
                    </select>
                    <span data-label="Latitude"></span></label>
                    <span class="assistive-text">Assistive text.</span>
                  </div>
                  <div class="input-text-container">
                    <label for="pointlong" class="input-text--outlined">
                    <input type="text" id="pointlong" name="pointlong">
                    <select id="pointlong_EW" name="pointlong_EW">
                      <option id="W" value="W">W</option>
                      <option id="E" value="E">E</option>
                    </select>
                    <span data-label="Longitude"></span></label>
                    <span class="assistive-text">Assistive text.</span>
                  </div>
                  <div class="input-text-container">
                    <label for="radius" class="input-text--outlined">
                    <input type="text" id="radius" name="radius">
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
          <div id="chips"></div>
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