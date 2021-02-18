/**
 * GLOBAL VARIABLES
 */
const criteriaPanel = document.getElementById('criteria-panel');
const testURL = document.getElementById('test-url');
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
  // console.log(element);
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
//// BROKEN FOR TEXT INPUTS?
// Não está fazendo sentido nenhum.
function updateChip(e) {
  // REMOVER TODOS OS CHIPS EXISTENTES
  document.getElementById('chips').innerHTML = '';
  // CAMINHAR PELA PÁGINA PROCURANDO OS CRITERIOS SELECIONADOS
  let inputs = document.querySelectorAll('input');
  // console.log(inputs.length);
  inputs.forEach((item) => {
    // console.log(item.type);
    if (item.type == 'text' && item.value != '') {
      // create chip for text value
      // console.log(item.value);
      addChip(item);
    } else if (
      item.type == 'checkbox' &&
      item.checked &&
      item.hasAttribute('data-chip')
    ) {
      // create chip for checkboxes
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
  // WHEN CLICKING CHILD ELEMENT, UPDATE STATUS OF PARENTS ALL
  if (e.type == 'click' || e.type == 'change') {
    let isChild = e.target.classList.contains('child');
    // console.log('is child: ', isChild);
    // if isChild , grab nearest parent
    if (isChild) {
      // console.log(e.target);
      // console.log(e.target.parentNode.closest('.all-selector'));
      // console.log(e.target.closest('ul').parentNode);
      let nearParentNode = e.target.closest('ul').parentNode;
      // console.log(nearParentNode);
      let nearParentOpt = e.target
        .closest('ul')
        .parentNode.querySelector('.all-selector');
      let numOptions = nearParentNode.querySelectorAll(
        'ul > li input.child:not(.all-selector):enabled'
      ).length;
      let numOpChecked = nearParentNode.querySelectorAll(
        'ul > li input.child:not(.all-selector):checked'
      ).length;
      // console.log(numOptions, ' ', numOpChecked);
      // console.log(
      //   nearParentNode.querySelectorAll(
      //     'ul > li input.child:not(.all-selector):enabled'
      //   )
      // );
      numOptions == numOpChecked
        ? (nearParentOpt.checked = true)
        : (nearParentOpt.checked = false);

      // CHECK
      // IF PARENT ALSO HAS A CHILD CLASS
      // console.log(nearParentOpt.classList.contains('child'));
      // console.log(nearParentNode.closest('ul').parentNode);
      if (nearParentOpt.classList.contains('child')) {
        // console.log('parent is also a child');
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
      // TOGGLE CHECK OF PARENT
    }
  }
}

/**
 * Unchecks children of 'all-selector' checkboxes when chip is removed
 * Uses 'data-form-id' property in .php
 * @param {Object} element HTML Node Object
 */
function uncheckAll(element) {
  // console.log(element);
  // console.log('here');
  let isAllSel = element.classList.contains('all-selector');
  if (isAllSel) {
    // Find all children
    let selChildren = document.querySelectorAll(
      '#' + element.dataset.formId + ' input[type=checkbox]:checked'
    );
    // Uncheck all children
    selChildren.forEach((item) => {
      item.checked = false;
    });
  }
}
/////////

/**
 * Searches specified fields and capture values
 * @param {String} paramName Name of parameter to be looked for in form
 * Passes objects to `paramsArr`
 * Passes default objects
 */
function getParam(paramName) {
  //Default country
  paramsArr['country'] = 'USA';
  // If parameter is 'db', go only through currently selected radio option in modal plus external collections in form
  // console.log(criterionSelected);
  // console.log(paramName);
  let element = '';
  if (paramName === 'db') {
    let query = '#' + criterionSelected + ' input[name="db"]';
    let selectedInModal = Array.from(document.querySelectorAll(query));
    let selectedInForm = Array.from(
      document.querySelectorAll('#search-form-colls input[name="db"]')
    );
    // console.log('Selected in Form: ', selectedInForm);
    // console.log('Selected in Modal: ', selectedInModal);
    // console.log(typeof selectedInModal);
    element = selectedInForm.concat(selectedInModal);
    // console.log(typeof element);
  } else element = document.getElementsByName(paramName);

  // Deals with dropdown options
  // const answer = element[0].tagName === "SELECT" ? "it's a dropdown" : "it's not a dropdown";
  // console.log(answer);
  // console.log(element[0].tagName);

  // Deals with inputs
  // console.log(element.length);
  if (element[0].tagName === 'INPUT') {
    // Deals with checkboxes
    if (element[0].getAttribute('type') === 'checkbox') {
      // let i = 0;
      let itemsArr = [];
      for (let i = 0; i < element.length; ++i) {
        element[i].checked ? itemsArr.push(element[i].value) : '';
        // : 'console.log('not checked')';
      }
      // paramsArr.push({
      //   [paramName]: itemsArr
      // });
      paramsArr[paramName] = itemsArr;
    } else {
      // Deals with text
      let elementValue = element[0].value;
      if (elementValue) {
        // paramsArr.push({
        //   [paramName]: elementValue
        // });
        paramsArr[paramName] = elementValue;
      }
    }
  } else if (element[0].tagName === 'SELECT') {
    let elementValue = element[0].options[element[0].selectedIndex].value;
    // paramsArr.push({
    //   [paramName]: elementValue
    // });
    paramsArr[paramName] = elementValue;
    // Deals with textarea
  } else if (element[0].tagName === 'TEXTAREA') {
    let elementValue = element[0].value;
    if (elementValue) {
      // paramsArr.push({
      //   [paramName]: elementValue
      // });
      paramsArr[paramName] = elementValue;
    }
  }
  return paramsArr;
}

/**
 * Creates search URL with parameters
 * Define parameters to be looked for in `paramNames` array
 */
function getSearchUrl() {
  // const baseURL = new URL(
  //   'https://biorepo.neonscience.org/portal/collections/list.php'
  //  http://biokic4.rc.asu.edu/biorepo/collections/list.php
  // );
  const baseURL = new URL(
    'http://github.localhost:8080/NEON-Biorepository/collections/list.php'
  );

  // console.log(baseURL);
  // Clears array temporarily to avoid redundancy
  paramsArr = [];

  const paramNames = [
    'db',
    'datasetid',
    'catnum',
    'includeothercatnum',
    'hasimages',
    'hasgenetic',
    // 'collector',
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

  // Deals with absent taxa
  if (!('taxa' in paramsArr)) {
    delete paramsArr.usethes;
    delete paramsArr.taxontype;
  }

  // Deals with absent catalog number
  if (!('catnum' in paramsArr)) {
    delete paramsArr.includeothercatnum;
  }

  // for (const [key, value] of Object.entries(paramsArr)) {
  //   console.log(`${key}: ${value}`);
  // }

  for (const [key, value] of Object.entries(paramsArr)) {
    // If value is null or empty, don't pass to url
    value.length === 0 ? delete paramsArr[key] : false;
    // paramsArr[key].length === 0 ? delete paramsArr.key : false;
  }

  // Appends each key value for each param in search url
  let queryString = Object.keys(paramsArr).map((key) => {
    //   return encodeURIComponent(key) + '=' + encodeURIComponent(paramsArr[key])
    // }).join('&');
    // console.log(baseURL + queryString);
    baseURL.searchParams.append(key, paramsArr[key]);
  });
  console.log(paramsArr);
  console.log(baseURL.href);
  // Appends URL to `testURL` link
  testURL.innerHTML = baseURL.href;
  testURL.href = baseURL.href;
}

/**
 * Form validation functions
 */

/**
 * Enforces selection of at least 1 `db` parameter
 *
 */
function valColls() {
  let allSelectedForm = document.querySelectorAll(
    '#search-form-colls input[type="checkbox"]:checked'
  ).length;
  if (!allSelectedForm) {
    window.alert('Please select at least one collection');
    return false;
  }
}

//////////////////////////////////////////////////////////////////////////

/**
 * EVENT LISTENERS/INITIALIZERS
 */

// Test button gets params
// $("#teste-btn").click(getSearchParams);
$('#teste-btn').click(function (event) {
  event.preventDefault();
  valColls();
  getSearchUrl();
});

// Listen for open modal click
$('#neon-modal-open').click(function (event) {
  event.preventDefault();
  openModal('#biorepo-collections-list');
});

// When checking "all neon collections" box, toggle checkboxes in modal
$('#all-neon-colls-quick').click(function () {
  let isChecked = $(this).prop('checked');
  $('.all-neon-colls').prop('checked', isChecked);
  $('.all-neon-colls').siblings().find('.child').prop('checked', isChecked);
  valColls();
});

// When checking any 'all-selector', toggle children checkboxes
$('.all-selector').click(toggleAllSelector);

const allNeon = document.getElementById('all-neon-colls-quick');
const allSites = document.getElementById('all-sites');

const formColls = document.getElementById('search-form-colls');
formColls.addEventListener('click', autoToggleSelector, false);
formColls.addEventListener('change', autoToggleSelector, false);

const formSites = document.getElementById('site-list');
formSites.addEventListener('click', autoToggleSelector, false);
formSites.addEventListener('change', autoToggleSelector, false);

const collsModal = document.getElementById('testing-modal');
collsModal.addEventListener('click', autoToggleSelector, false);
collsModal.addEventListener('change', autoToggleSelector, false);

// When unchecking children checkboxes, uncheck 'all-selector'
// $('.child').bind(
//   'click',
//   { element: $('.child').siblings().find('.all-selector') },
//   autoToggleSelector
// );
// $('#all-sites')
//   .siblings()
//   .find('.child')
//   .bind('click', { element: '#all-sites' }, autoToggleSelector);
// $('#collections-list1')
//   .find('.child')
//   .bind('click', { element: '#collections-list1' }, autoToggleSelector);
// $('#collections-list2')
//   .find('.child')
//   .bind('click', { element: '#collections-list2' }, autoToggleSelector);
// $('#collections-list3')
//   .find('.child')
//   .bind('click', { element: '#collections-list3' }, autoToggleSelector);

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
    console.log(isAllSelected);
  });

//////// Binds Update chip on event change
document.querySelector('#params-form').addEventListener('change', updateChip);
// const taxaInput = document.getElementById('taxa-search');
// taxaInput.addEventListener('change', updateChip);
// taxaInput.addEventListener('change', updateChip);
// const catNumInput = document.getElementById('taxa-search');
// allNeon.addEventListener('change', updateChip);
// const allNeonExt = document.getElementById('all-neon-ext');
// allNeonExt.addEventListener('change', updateChip);
// const allExt = document.getElementById('all-ext');
// allExt.addEventListener('change', updateChip);
// allSites.addEventListener('change', updateChip);

// const locInput = document.getElementById('')

// on default (on document load): All Neon Collections, All Domains & Sites
document.addEventListener('DOMContentLoaded', updateChip);

// Binds expansion function to plus and minus icons in selectors
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
