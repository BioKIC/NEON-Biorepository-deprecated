<?php
include_once('../../config/symbini.php');
header("Content-Type: text/html; charset=".$CHARSET);
?>
<html>
	<head>
		<title>Label Content Format Visual Editor</title>
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
    <link rel="stylesheet" href="../../css/symb/labelhelpers.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
  <style>
    body {
      font-size: 14px;
      line-height: 24px;
      background-color: white;
      min-width: 200px;
      width: 100%;
      margin: 0 auto;
    }
    
    main {
      max-width: 960px;
      margin: 0 auto;
      display: grid;
      grid-template-columns: 240px 420px 200px;
      grid-gap: 1em;
      align-items: start;
    }

    @media only screen and (max-width: 780px) {
      main {
        grid-template-columns: 100%;
      }
    }

    main li {
      list-style-type: none;
      display: inline-block;
    }


    select.control {
      display: block;
      margin: 0.25em 0;
      width: 100%;
    }

    input {
      width: 100%;
    }

    h2,
    h3,
    h4 {
      text-transform: uppercase;
      font-weight: 400;
      color: #909090;
      letter-spacing: 2px;
    }

    h4,
    h5 {
      margin: 0.5em 0;
    }

    #fields-filter {
      margin-bottom: 0.25em;
      width: 100%;
    }

    #field-options>div {
      display: inline-block;
      margin: 1em 0;
    }

    #fields-list {
      overflow-y: scroll;
      height: 65vh;
    }

    #build-label {
      background-color: #acacac;
      /* min-height: 300px; */
      grid-column: 1/2;
      padding: 0.5em;
      margin-top: 1em;
    }

    #build-label .delimiter {
      height: 1em;
      /* background-color: white; */
      /* color: black; */
    }

    #label-middle {
      border: 1px solid white;
    }

    #label-middle>.field-block {
      border: 1px dashed white;
      min-height: 2em;
    }

    .field-block.container.selected {
      background-color: black;
    }

    #preview-label {
      border: 1px solid gray;
      min-height: 100px;
      padding: 0.5em;
    }

    #preview-label>.field-block>div {
      display: inline;
    }

    #field-block-options {
      margin-top: 2em;
    }

    /** Button stuff **/
    button,
    input,
    optgroup,
    select,
    textarea {
      font-family: inherit;
      /* 1 */
      font-size: 100%;
      /* 1 */
      line-height: 1.15;
      /* 1 */
      margin: 0;
      /* 2 */
    }

    li {
      color: white;
      font-weight: 600;
      font-size: 0.8rem !important;
      /* background: #a972cb;
      border: 2px solid #a972cb; */
      border-radius: 2px;
      font: inherit;
      line-height: 1;
      margin: 0.5em;
      padding: 0.25em 0.5em;
    }

      button.btn {
        /* width: 220px; */
        /* // background-color: darkslategrey; */
        border-color: transparent;
        border-radius: 2px;
        /* color: white; */
        text-transform: uppercase;
        padding: 0.5em;
        margin-top: 0.25em;
      }

      button.btn:hover {
        background-color: rgb(168, 167, 167);
      }

      button.btn:focus {
        outline: none;
      }

      button.control:disabled {
        background: lightgray;
      }

      button.control:disabled,
      button.control[disabled],
      button.control:disabled:hover {
        background: lightgray;
      }

      button.control {
        color: white;
        background: darkslategrey;
        border: none;
        border-right: 1px solid lightslategray;
        /* border-radius: 2px; */
        font: inherit;
        line-height: 1;
        padding: 0.5em;
        outline: none;
        /* width: 40px; */
        height: 40px;
        cursor: pointer;
      }

      button.control:hover {
        background: rgb(61, 102, 102);
        border: none;
        line-height: 1;
        border-right: 1px solid lightslategrey;
        outline: none;
      }

      button .material-icons {
        width: 24px;
      }

      button>.material-icons {
        pointer-events: none;
      }

      span.material-icons:hover {
        background-color: rgb(61, 102, 102);
      }

      .draggable.selected {
        background-color: black;
        border: 1px solid white;
      }

      .drag-icon {
        background-color: white;
        cursor: move;
      }

      [data-category='specimen'] {
        background: #0da827;
        border: 2px solid #0da827;
      }

      [data-category='collection'] {
        background: #0da827;
        border: 2px solid #0da827;
      }

      [data-category='taxon'] {
        background: #077eb6;
        border: 2px solid #077eb6;
      }

      [data-category='determination'] {
        background: #1c4eda;
        border: 2px solid #1c4eda;
      }

      [data-category='event'] {
        background: #ee7bc8;
        border: 2px solid #ee7bc8;
      }

      [data-category='locality'] {
        background: #952ed1;
        border: 2px solid#952ed1;
      }

      button.control.selected,
      button.control.selected:hover {
        background-color: black;
      }

      .draggable.dragging {
        opacity: 1;
      }


  </style>
	</head>
	<body>
    <main>
      <div>
        <div id="fields">
          <h4>Fields Available</h4>
          <label for="fields-filter">Filter fields by category:</label>
          <select name="fields-filter" id="fields-filter">
            <option value="all">All</option>
            <option value="specimen">Specimen</option>
            <option value="collection">Collection</option>
            <option value="taxon">Taxon</option>
            <option value="determination">Determination</option>
            <option value="event">Event</option>
            <option value="locality">Locality</option>
          </select>
          <div id="fields-list" class="container"></div>
        </div>
      </div>
      <div>
        <div id="build">
          <!-- <h4>Label Format</h4> -->
          <div id="build-label">
            <h4 style="color: #212529">Label Content Area</h4>
            <h5>drag, drop & reorder fields here; click fields or lines to apply formats; toggle select/deselect by clicking once; reorder lines clicking on arrows</h5>
            <!-- <div id="label-header">
            <h5 class='area-title'>Label Heading</h5>
          </div> -->
            <div id="label-middle">
              <div class="field-block container" draggable="true">
                <span class="material-icons">keyboard_arrow_up</span><span class="material-icons">keyboard_arrow_down</span></div>
            </div>
            <button class="btn" onClick="addLine()">Add line (fieldBlock)</button>
            <!-- <div id="label-footer">
            <h5 class='area-title'>Label Footer</h5>
          </div> -->
          </div>
        </div>
        <div id="preview">
          <h4>Label preview</h4>
          <h5>content automatically displayed below</h5>
          <div id="preview-label"></div>
          <button class="btn" onclick="printJson()">Display JSON</button>
          <button class="btn" id='copyBtn' onclick="copyJson()" style="display: none;">Copy JSON to clipboard</button>
          <textarea id="dummy" style="display: none;"></textarea>
        </div>
      </div>
      <div>
        <div id="controls">
          <div id="field-options">
            <h4>Field Options</h4>
            <div>
              <div>
                <label for="prefix">Prefix:</label>
                <input type="text" name="prefix" id="prefix" class="control" disabled="true" data-group="field">
              </div>
              <div>
                <label for="suffix">Suffix:</label>
                <input type="text" name="suffix" id="suffix" class="control" disabled="true" data-group="field">
              </div>
            </div>
          </div>
          <div id="field-block-options">
            <h4>Line Options</h4>
            <div>
              <label for="delimiter">Fields Delimiter:</label>
              <input type="text" name="delimiter" id="delimiter" class="control" disabled="true" data-group="field-block">
            </div>
          </div>
        </div>
      </div>
    </main>
  </body>
  <script src="../../js/symb/collections.labeljsongui.js"></script>
</html>
