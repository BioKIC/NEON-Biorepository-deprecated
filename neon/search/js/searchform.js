/**
 * GLOBAL VARIABLES
 */
const criteriaPanel = document.getElementById('criteria-panel');
// const testUrl = document.getElementById('test-url');
const allNeon = document.getElementById('all-neon-colls-quick');
const allSites = document.getElementById('all-sites');
const form = document.getElementById('params-form');
const formColls = document.getElementById('search-form-colls');
const formSites = document.getElementById('site-list');
const collsModal = document.getElementById('colls-modal');
let criterionSelected = 'taxonomic-cat';
let paramsArr = [];
//////////////////////////////////////////////////////////////////////////

/**
 * METHODS
 */

/**
 * Toggles tab selection for collection picking options in modal
 * Uses jQuery
 */
$('input[type="radio"]').click(function () {
  var inputValue = $(this).attr('value');
  var targetBox = $('#' + inputValue);
  $('.box').not(targetBox).hide();
  $(targetBox).show();
  $(this).parent().addClass('tab-active');
  $(this).parent().siblings().removeClass('tab-active');
});

/**
 * Opens modal with id selector
 * @param {String} elementid Selector for modal to be opened
 */
function openModal(elementid) {
  $(elementid).css('display', 'block');
  $(document.body).css('overflow: hidden');
}

/**
 * Closes modal with id selector
 * @param {String} elementid Selector for modal to be opened
 */
function closeModal(elementid) {
  $(elementid).css('display', 'none');
}

/**
 * Opens map helper
 * @param {String} mapMode Option from select in form
 * Function from `../../js/symb/collections.harvestparams.js`
 */
function openCoordAid(mapMode) {
  mapWindow = open(
    '../../collections/tools/mapcoordaid.php?mapmode=' + mapMode,
    'polygon',
    'resizable=0,width=900,height=630,left=20,top=20'
  );
  if (mapWindow.opener == null) mapWindow.opener = self;
  mapWindow.focus();
}

/**
 * Chips
 */

/**
 * Adds default chips
 * @param {HTMLObjectElement} element Input for which chips are going to be created by default
 */
function addChip(element) {
  let inputChip = document.createElement('span'),
    chipBtn = document.createElement('button');
  inputChip.classList.add('chip');
  inputChip.id = 'chip-' + element.id;
  chipBtn.setAttribute('type', 'button');
  chipBtn.classList.add('chip-remove-btn');
  chipBtn.onclick = function () {
    element.type === 'checkbox'
      ? (element.checked = false)
      : (element.value = element.defaultValue);
    element.dataset.formId ? uncheckAll(element) : '';
    removeChip(inputChip);
  };
  inputChip.textContent = element.dataset.chip;
  inputChip.appendChild(chipBtn);
  document.getElementById('chips').appendChild(inputChip);
}

/**
 * Removes chip
 * @param {HTMLObjectElement} chip Chip element
 */
function removeChip(chip) {
  chip != null ? chip.remove() : '';
}

/**
 * Updateds chips based on selected options
 * @param {Event} e
 */
function updateChip(e) {
  document.getElementById('chips').innerHTML = '';
  let inputs = document.querySelectorAll('input');
  inputs.forEach((item) => {
    if (item.type == 'text' && item.value != '') {
      addChip(item);
    } else if (
      item.type == 'checkbox' &&
      item.checked &&
      item.hasAttribute('data-chip')
    ) {
      addChip(item);
    }
  });
}
/////////

/**
 * Toggles state of checkboxes in nested lists when clicking an "all-selector" element
 * Uses jQuery
 */
function toggleAllSelector() {
  $(this)
    .siblings()
    .find('input[type=checkbox]:enabled')
    .prop('checked', this.checked)
    .attr('checked', this.checked);
}

/**
 * Triggers toggling of checked/unchecked boxes in nested lists
 * Default is all boxes are checked in HTML.
 * @param {String} e.data.element Selector for element containing
 * list, should be passed when binding function to element
 */
function autoToggleSelector(e) {
  if (e.type == 'click' || e.type == 'change') {
    let isChild = e.target.classList.contains('child');
    if (isChild) {
      let nearParentNode = e.target.closest('ul').parentNode;
      let nearParentOpt = e.target
        .closest('ul')
        .parentNode.querySelector('.all-selector');
      let numOptions = nearParentNode.querySelectorAll(
        'ul > li input.child:not(.all-selector):enabled'
      ).length;
      let numOpChecked = nearParentNode.querySelectorAll(
        'ul > li input.child:not(.all-selector):checked'
      ).length;
      numOptions == numOpChecked
        ? (nearParentOpt.checked = true)
        : (nearParentOpt.checked = false);

      if (nearParentOpt.classList.contains('child')) {
        let parentAllNode = nearParentNode.closest('ul').parentNode;
        let parentAllOpt = parentAllNode.querySelector('.all-selector');
        let numOptionsAll = parentAllNode.querySelectorAll(
          'input.child:enabled'
        ).length;
        let numOpCheckedAll = parentAllNode.querySelectorAll(
          'input.child:checked'
        ).length;
        numOptionsAll == numOpCheckedAll
          ? (parentAllOpt.checked = true)
          : (parentAllOpt.checked = false);
      }
    }
  }
}

/**
 * Unchecks children of 'all-selector' checkboxes when chip is removed
 * Uses 'data-form-id' property in .php
 * @param {Object} element HTML Node Object
 */
function uncheckAll(element) {
  let isAllSel = element.classList.contains('all-selector');
  if (isAllSel) {
    let selChildren = document.querySelectorAll(
      '#' + element.dataset.formId + ' input[type=checkbox]:checked'
    );
    selChildren.forEach((item) => {
      item.checked = false;
    });
  }
}
/////////
/**
 * Finds all collections selected
 * Uses active tab in modal
 */
function getCollsSelected() {
  let criterionSelected = collsModal.querySelector(
    '.tab.tab-active input[type=radio]:checked'
  ).value;
  let query = '#' + criterionSelected + ' input[name="db"]:checked';
  let selectedInModal = Array.from(document.querySelectorAll(query));
  let selectedInForm = Array.from(
    document.querySelectorAll('#search-form-colls input[name="db"]:checked')
  );
  let collsArr = selectedInForm.concat(selectedInModal);
  return collsArr;
}

/**
 * Searches specified fields and capture values
 * @param {String} paramName Name of parameter to be looked for in form
 * Passes objects to `paramsArr`
 * Passes default objects
 */
function getParam(paramName) {
  //Default country
  // paramsArr['country'] = 'USA';
  const elements = document.getElementsByName(paramName);
  const firstEl = elements[0];
  const firstTag = firstEl.tagName;
  let elementValues = '';

  // for db and datasetid
  if (paramName === 'db') {
    let dbArr = [];
    let tempArr = getCollsSelected();
    console.log(tempArr);
    tempArr.forEach((item) => {
      dbArr.push(item.value);
    });
    console.log(dbArr);
    elementValues = dbArr;
  } else if (paramName === 'datasetid') {
    datasetArr = [];
    elements.forEach((el) => {
      if (el.checked) {
        let isSite = el.dataset.domain != undefined;
        if (isSite) {
          let isDomainSel = document.getElementById(el.dataset.domain).checked;
          isDomainSel ? '' : datasetArr.push(el.value);
        } else {
          datasetArr.push(el.value);
        }
      }
    });
    elementValues = datasetArr;
  } else {
    switch (firstTag) {
      case 'INPUT':
        (firstEl.type === 'checkbox' && firstEl.checked) ||
        (firstEl.type === 'text' && firstEl != '')
          ? (elementValues = firstEl.value)
          : '';
        break;
      case 'SELECT':
        elementValues = firstEl.options[firstEl.selectedIndex].value;
        break;
      case 'TEXTAREA':
        elementValues = firstEl.value;
        break;
    }
  }
  elementValues != '' ? (paramsArr[paramName] = elementValues) : '';
  console.log(paramsArr);
  return paramsArr;
}

/**
 * Creates search URL with parameters
 * Define parameters to be looked for in `paramNames` array
 */
function getSearchUrl() {
  const harvestUrl = location.href.slice(0, location.href.indexOf('/neon'));
  console.log(harvestUrl);
  const baseUrl = new URL(harvestUrl + '/collections/list.php');

  // Clears array temporarily to avoid redundancy
  paramsArr = [];

  const paramNames = [
    'db',
    'datasetid',
    'catnum',
    'includeothercatnum',
    'hasimages',
    'hasgenetic',
    'state',
    'county',
    'local',
    'elevlow',
    'elevhigh',
    'upperlat',
    'upperlat_NS',
    'bottomlat',
    'bottomlat_NS',
    'leftlong',
    'leftlong_EW',
    'rightlong',
    'rightlong_EW',
    'footprintwkt',
    'pointlat',
    'pointlat_NS',
    'pointlong',
    'pointlong_EW',
    'radius',
    'radiusunits',
    'eventdate1',
    'eventdate2',
    'taxa',
    'usethes',
    'taxontype',
  ];
  // Grabs params from form for each param name
  paramNames.forEach((param, i) => {
    return getParam(paramNames[i]);
  });

  // Appends each key value for each param in search url
  let queryString = Object.keys(paramsArr).map((key) => {
    //   return encodeURIComponent(key) + '=' + encodeURIComponent(paramsArr[key])
    // }).join('&');
    // console.log(baseURL + queryString);
    baseUrl.searchParams.append(key, paramsArr[key]);
  });
  // console.log(paramsArr);
  // console.log(baseUrl.href);
  // Appends URL to `testUrl` link
  // testUrl.innerHTML = baseUrl.href;
  testUrl.innerText = 'Click here';
  testUrl.href = baseUrl.href;
  // testUrl.href = queryString;
  // return baseUrl.href;
  return baseUrl.href;
}

/**
 * Form validation functions
 */

// Enforces selection of at least 1 `db` parameter

function validateForm() {
  let anyCollsSelected = document.querySelectorAll(
    '#search-form-colls input[type="checkbox"]:checked'
  ).length;
  // let anyCollSelected =

  if (anyCollsSelected === 0) {
    window.alert('Please select at least one collection');
    return false;
  }
}

function checkHarvestParamsForm(frm) {
  //make sure they have filled out at least one field.
  if (
    frm.taxa.value.trim() == '' &&
    frm.country.value.trim() == '' &&
    frm.state.value.trim() == '' &&
    frm.county.value.trim() == '' &&
    frm.local.value.trim() == '' &&
    frm.elevlow.value.trim() == '' &&
    frm.upperlat.value.trim() == '' &&
    frm.footprintwkt.value.trim() == '' &&
    frm.pointlat.value.trim() == '' &&
    frm.collector.value.trim() == '' &&
    frm.collnum.value.trim() == '' &&
    frm.eventdate1.value.trim() == '' &&
    frm.catnum.value.trim() == '' &&
    frm.typestatus.checked == false &&
    frm.hasimages.checked == false &&
    frm.hasgenetic.checked == false
  ) {
    alert('Please fill in at least one search parameter!');
    return false;
  }

  if (
    frm.upperlat.value != '' ||
    frm.bottomlat.value != '' ||
    frm.leftlong.value != '' ||
    frm.rightlong.value != ''
  ) {
    // if Lat/Long field is filled in, they all should have a value!
    if (
      frm.upperlat.value == '' ||
      frm.bottomlat.value == '' ||
      frm.leftlong.value == '' ||
      frm.rightlong.value == ''
    ) {
      alert(
        'Error: Please make all Lat/Long bounding box values contain a value or all are empty'
      );
      return false;
    }

    // Check to make sure lat/longs are valid.
    if (
      Math.abs(frm.upperlat.value) > 90 ||
      Math.abs(frm.bottomlat.value) > 90 ||
      Math.abs(frm.pointlat.value) > 90
    ) {
      alert('Latitude values can not be greater than 90 or less than -90.');
      return false;
    }
    if (
      Math.abs(frm.leftlong.value) > 180 ||
      Math.abs(frm.rightlong.value) > 180 ||
      Math.abs(frm.pointlong.value) > 180
    ) {
      alert('Longitude values can not be greater than 180 or less than -180.');
      return false;
    }
    var uLat = frm.upperlat.value;
    if (frm.upperlat_NS.value == 'S') uLat = uLat * -1;
    var bLat = frm.bottomlat.value;
    if (frm.bottomlat_NS.value == 'S') bLat = bLat * -1;
    if (uLat < bLat) {
      alert(
        'Your northern latitude value is less then your southern latitude value. Please correct this.'
      );
      return false;
    }
    var lLng = frm.leftlong.value;
    if (frm.leftlong_EW.value == 'W') lLng = lLng * -1;
    var rLng = frm.rightlong.value;
    if (frm.rightlong_EW.value == 'W') rLng = rLng * -1;
    if (lLng > rLng) {
      alert(
        'Your western longitude value is greater then your eastern longitude value. Please correct this. Note that western hemisphere longitudes in the decimal format are negitive.'
      );
      return false;
    }
  }

  //Same with point radius fields
  if (
    frm.pointlat.value != '' ||
    frm.pointlong.value != '' ||
    frm.radius.value != ''
  ) {
    if (
      frm.pointlat.value == '' ||
      frm.pointlong.value == '' ||
      frm.radius.value == ''
    ) {
      alert(
        'Error: Please make all Lat/Long point-radius values contain a value or all are empty'
      );
      return false;
    }
  }

  return true;
}

/**
 * Calls methods to validate form and build URL that will redirect search
 */
function simpleSearch() {
  let isValid = validateForm();
  if (isValid) {
    let searchUrl = getSearchUrl();
    window.location = searchUrl;
  }
}

//////////////////////////////////////////////////////////////////////////

/**
 * EVENT LISTENERS/INITIALIZERS
 */

// Test button gets params
// $("#teste-btn").click(getSearchParams);
// $('#teste-btn').click(function (event) {
//   console.log('here');
//   event.preventDefault();
//   valColls();
//   getSearchUrl();
// });

// Search button
document
  .getElementById('search-btn')
  .addEventListener('click', function (event) {
    event.preventDefault();
    simpleSearch();
  });

// Listen for open modal click
document
  .getElementById('neon-modal-open')
  .addEventListener('click', function (event) {
    event.preventDefault();
    openModal('#biorepo-collections-list');
  });

// When checking "all neon collections" box, toggle checkboxes in modal
$('#all-neon-colls-quick').click(function () {
  let isChecked = $(this).prop('checked');
  $('.all-neon-colls').prop('checked', isChecked);
  $('.all-neon-colls').siblings().find('.child').prop('checked', isChecked);
  // validateForm();
});

// When checking any 'all-selector', toggle children checkboxes
$('.all-selector').click(toggleAllSelector);

formColls.addEventListener('click', autoToggleSelector, false);
formColls.addEventListener('change', autoToggleSelector, false);

formSites.addEventListener('click', autoToggleSelector, false);
formSites.addEventListener('change', autoToggleSelector, false);

collsModal.addEventListener('click', autoToggleSelector, false);
collsModal.addEventListener('change', autoToggleSelector, false);

// Listen for close modal click and passes value of selected colls to main form
document
  .getElementById('neon-modal-close')
  .addEventListener('click', function (event) {
    event.preventDefault();
    closeModal('#biorepo-collections-list');
    let criterionSelected = collsModal.querySelector(
      '.tab.tab-active input[type=radio]:checked'
    ).value;
    let tabSelected = document.getElementById(criterionSelected);
    let isAllSelected = tabSelected.getElementsByClassName('all-neon-colls')[0]
      .checked;
    allNeon.checked = isAllSelected;
    isAllSelected
      ? addChip(allNeon)
      : removeChip(document.getElementById('chip-' + allNeon.id));
  });

//////// Binds Update chip on event change
form.addEventListener('change', updateChip);

// on default (on document load): All Neon Collections, All Domains & Sites
document.addEventListener('DOMContentLoaded', updateChip);

// Binds expansion function to plus and minus icons in selectors, uses jQuery
$('.expansion-icon').click(function () {
  if ($(this).siblings('ul').hasClass('collapsed')) {
    $(this)
      .html('indeterminate_check_box')
      .siblings('ul')
      .removeClass('collapsed');
  } else {
    $(this).html('add_box').siblings('ul').addClass('collapsed');
  }
});

//////////////////////////////////////////////////////////////////////////
