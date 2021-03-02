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
  'llbound',
  // 'upperlat',
  // 'upperlat_NS',
  // 'bottomlat',
  // 'bottomlat_NS',
  // 'leftlong',
  // 'leftlong_EW',
  // 'rightlong',
  // 'rightlong_EW',
  'footprintwkt',
  'llpoint',
  // 'pointlat',
  // 'pointlat_NS',
  // 'pointlong',
  // 'pointlong_EW',
  'radius',
  'radiusunits',
  'eventdate1',
  'eventdate2',
  'taxa',
  'usethes',
  'taxontype',
];
const uLat = document.getElementById('upperlat');
const uLatNs = document.getElementById('upperlat_NS');
const bLat = document.getElementById('bottomlat');
const bLatNs = document.getElementById('bottomlat_NS');
const lLng = document.getElementById('leftlong');
const lLngEw = document.getElementById('leftlong_EW');
const rLng = document.getElementById('rightlong');
const rLngEw = document.getElementById('rightlong_EW');

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
    let datasetArr = [];
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
  } else if (paramName === 'llbound') {
    // Only if inputs aren't empty
    if (
      uLat.value != '' &&
      bLat.value != '' &&
      lLng.value != '' &&
      rLng.value != ''
    ) {
      let uLatVal = uLatNs.value == 'S' ? uLat.value * -1 : uLat.value * 1;
      let bLatVal = bLatNs.value == 'S' ? bLat.value * -1 : bLat.value * 1;
      let lLngVal = lLngEw.value == 'W' ? lLng.value * -1 : lLng.value * 1;
      let rLngVal = rLngEw.value == 'W' ? rLng.value * -1 : rLng.value * 1;
      let llboundArr = `${uLatVal};${bLatVal};${lLngVal};${rLngVal}`;
      elementValues = llboundArr;
    }
  } else if (paramName === 'llpoint') {
    console.log('llpoint is for Point Lat');
  } else if (elements[0] != undefined) {
    switch (firstEl.tagName) {
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
  // testUrl.innerText = 'Click here';
  // testUrl.href = baseUrl.href;
  // testUrl.href = queryString;
  // return baseUrl.href;
  return baseUrl.href;
}

/**
 * Form validation functions
 */

// Enforces selection of at least 1 `db` parameter

function validateForm() {
  errors = [];
  // DB
  let anyCollsSelected = getCollsSelected();
  if (anyCollsSelected.length === 0) {
    errors.push({
      elId: 'search-form-colls',
      errorMsg: 'Please select at least one collection.',
    });
  }
  // HTML5 built-in validation
  let invalidInputs = document.querySelectorAll('input:invalid');
  if (invalidInputs.length > 0) {
    invalidInputs.forEach((inp) => {
      errors.push({
        elId: inp.id,
        errorMsg: 'Please check values in field ' + inp.dataset.chip,
      });
    });
  }
  // Bounding Box
  let bBoxNums = document.querySelectorAll(
    '#bounding-box-form input[type=number]'
  );
  let bBoxNumArr = [];
  bBoxNums.forEach((el) => {
    el.value != '' ? bBoxNumArr.push(el.value) : false;
  });
  let bBoxCardinals = document.querySelectorAll('#bounding-box-form select');
  selectedCardinals = [];
  bBoxCardinals.forEach((hItem) => {
    hItem.value != '' ? selectedCardinals.push(hItem.id) : false;
  });
  if (bBoxNumArr.length > 0 && bBoxNumArr.length < bBoxNums.length) {
    errors.push({
      elId: 'bounding-box-form',
      errorMsg:
        'Please make sure either all Lat/Long bounding box values contain a value, or all are empty.',
    });
  } else if (bBoxNumArr.length > 0 && selectedCardinals.length == 0) {
    errors.push({
      elId: 'bounding-box-form',
      errorMsg: 'Please select hemisphere values.',
    });
  } else if (bBoxNumArr.length > 0 && selectedCardinals.length > 0) {
    let uLatVal = uLat.value;
    let uLatNsVal = uLatNs.value;
    let bLatVal = bLat.value;
    let bLatNsVal = bLatNs.value;

    if (uLatNsVal == 'S' && bLatNsVal == 'S') {
      uLatVal = uLatVal * -1;
      bLatVal = bLatVal * -1;
      console.log(uLatVal, bLatVal);
      console.log(uLatVal < bLatVal);
      if (uLatVal < bLatVal) {
        errors.push({
          elId: 'bounding-box-form',
          errorMsg:
            'Your northern latitude value is less than your southern latitude value.',
        });
      }
    }

    let lLngVal = lLng.value;
    let lLngEwVal = lLngEw.value;
    let rLngVal = rLng.value;
    let rLngEwVal = rLngEw.value;

    if (lLngEw == 'W' && rLngEw == 'W') {
      lLng = lLng * -1;
      rLng = rLng * -1;
      if (lLng > rLng) {
        errors.push({
          elId: 'bounding-box-form',
          errorMsg:
            'Your western longitude value is greater than your eastern longitude value. Note that western hemisphere longitudes in the decimal format are negative.',
        });
      }
    }
  }
  return errors;
}

function handleValErrors(errors) {
  const errorDiv = document.getElementById('error-msgs');
  errorDiv.innerHTML = '';
  errors.map((err) => {
    let element = document.getElementById(err.elId);
    element.classList.add('invalid');
    errorDiv.classList.remove('visually-hidden');
    let errorP = document.createElement('p');
    errorP.classList.add('error');
    errorP.innerText = err.errorMsg;
    errorDiv.appendChild(errorP);
  });
}

/**
 * Calls methods to validate form and build URL that will redirect search
 */
function simpleSearch() {
  let errors = validateForm();
  let isValid = errors.length == 0;
  if (isValid) {
    let searchUrl = getSearchUrl();
    // window.location = searchUrl;
    console.log('search would be performed');
    console.log(searchUrl);
  } else {
    handleValErrors(errors);
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
