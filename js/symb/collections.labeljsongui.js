/**
 *  Symbiota Label Builder Functions
 *  Author: Laura Rocha Prado
 *  Version: 2020
 */

/** Creating Page Elements/Controls
 ******************************
 */

// Defines formattable items in label (also used to create preview elements)
// Replace with constructor based in query?
const fieldProps = [
  {
    block: 'labelBlock',
    name: 'Occurrence ID',
    id: 'occid',
    group: 'specimen',
  },
  {
    block: 'labelBlock',
    name: 'Collection ID',
    id: 'collid',
    group: 'collection',
  },
  {
    block: 'labelBlock',
    name: 'Catalog Number',
    id: 'catalogNumber',
    group: 'specimen',
  },
  {
    block: 'labelBlock',
    name: 'Other Catalog Numbers',
    id: 'otherCatalogNumbers',
    group: 'specimen',
  },
  { block: 'labelBlock', name: 'Family', id: 'family', group: 'taxon' },
  {
    block: 'labelBlock',
    name: 'Scientific Name',
    id: 'scientificName',
    group: 'taxon',
  },
  { block: 'labelBlock', name: 'Taxon Rank', id: 'taxonRank', group: 'taxon' },
  {
    block: 'labelBlock',
    name: 'Infraspecific Epithet',
    id: 'infraSpecificEpithet',
    group: 'taxon',
  },
  {
    block: 'labelBlock',
    name: 'Scientific Name Authorship',
    id: 'scientificNameAuthorship',
    group: 'taxon',
  },
  {
    block: 'labelBlock',
    name: 'Parent Author',
    id: 'parentAuthor',
    group: 'taxon',
  },
  {
    block: 'labelBlock',
    name: 'Identified By',
    id: 'identifiedBy',
    group: 'determination',
  },
  {
    block: 'labelBlock',
    name: 'Date Identified',
    id: 'dateIdentified',
    group: 'determination',
  },
  {
    block: 'labelBlock',
    name: 'Identification References',
    id: 'identificationReferences',
    group: 'determination',
  },
  {
    block: 'labelBlock',
    name: 'Identification Remarks',
    id: 'identificationRemarks',
    group: 'determination',
  },
  {
    block: 'labelBlock',
    name: 'Taxon Remarks',
    id: 'taxonRemarks',
    group: 'determination',
  },
  {
    block: 'labelBlock',
    name: 'Identification Qualifier',
    id: 'identificationQualifier',
    group: 'determination',
  },
  {
    block: 'labelBlock',
    name: 'Type Status',
    id: 'typeStatus',
    group: 'specimen',
  },
  {
    block: 'labelBlock',
    name: 'Recorded By',
    id: 'recordedBy',
    group: 'event',
  },
  {
    block: 'labelBlock',
    name: 'Record Number',
    id: 'recordNumber',
    group: 'event',
  },
  {
    block: 'labelBlock',
    name: 'Associated Collectors',
    id: 'associatedCollectors',
    group: 'event',
  },
  { block: 'labelBlock', name: 'Event Date', id: 'eventDate', group: 'event' },
  { block: 'labelBlock', name: 'Year', id: 'year', group: 'event' },
  { block: 'labelBlock', name: 'Month', id: 'month', group: 'event' },
  { block: 'labelBlock', name: 'Month Name', id: 'monthName', group: 'event' },
  { block: 'labelBlock', name: 'Day', id: 'day', group: 'event' },
  {
    block: 'labelBlock',
    name: 'Verbatim Event Date',
    id: 'verbatimEventDate',
    group: 'event',
  },
  { block: 'labelBlock', name: 'Habitat', id: 'habitat', group: 'event' },
  { block: 'labelBlock', name: 'Substrate', id: 'substrate', group: 'event' },
  {
    block: 'labelBlock',
    name: 'Occurrence Remarks',
    id: 'occurrenceRemarks',
    group: 'specimen',
  },
  {
    block: 'labelBlock',
    name: 'Associated Taxa',
    id: 'associatedTaxa',
    group: 'taxon',
  },
  // { block: 'labelBlock', name: 'Dynamic Properties', id: 'dynamicProperties' },
  {
    block: 'labelBlock',
    name: 'Verbatim Attributes',
    id: 'verbatimAttributes',
    group: 'event',
  },
  { block: 'labelBlock', name: 'Behavior', id: 'behavior', group: 'specimen' },
  {
    block: 'labelBlock',
    name: 'Reproductive Condition',
    id: 'reproductiveCondition',
    group: 'specimen',
  },
  {
    block: 'labelBlock',
    name: 'Cultivation Status',
    id: 'cultivationStatus',
    group: 'specimen',
  },
  {
    block: 'labelBlock',
    name: 'Establishment Means',
    id: 'establishmentMeans',
    group: 'specimen',
  },
  {
    block: 'labelBlock',
    name: 'Life Stage',
    id: 'lifeStage',
    group: 'specimen',
  },
  { block: 'labelBlock', name: 'Sex', id: 'sex', group: 'specimen' },
  {
    block: 'labelBlock',
    name: 'Individual Count',
    id: 'individualCount',
    group: 'specimen',
  },
  {
    block: 'labelBlock',
    name: 'Sampling Protocol',
    id: 'samplingProtocol',
    group: 'specimen',
  },
  {
    block: 'labelBlock',
    name: 'Preparations',
    id: 'preparations',
    group: 'specimen',
  },
  { block: 'labelBlock', name: 'Country', id: 'country', group: 'locality' },
  {
    block: 'labelBlock',
    name: 'State/Province',
    id: 'stateProvince',
    group: 'locality',
  },
  { block: 'labelBlock', name: 'County', id: 'county', group: 'locality' },
  {
    block: 'labelBlock',
    name: 'Municipality',
    id: 'municipality',
    group: 'locality',
  },
  { block: 'labelBlock', name: 'Locality', id: 'locality', group: 'locality' },
  {
    block: 'labelBlock',
    name: 'Decimal Latitude',
    id: 'decimalLatitude',
    group: 'locality',
  },
  {
    block: 'labelBlock',
    name: 'Decimal Longitude',
    id: 'decimalLongitude',
    group: 'locality',
  },
  {
    block: 'labelBlock',
    name: 'Geodetic Datum',
    id: 'geodeticDatum',
    group: 'locality',
  },
  {
    block: 'labelBlock',
    name: 'Coordinate Uncertainty In Meters',
    id: 'coordinateUncertaintyInMeters',
    group: 'locality',
  },
  {
    block: 'labelBlock',
    name: 'Verbatim Coordinates',
    id: 'verbatimCoordinates',
    group: 'locality',
  },
  {
    block: 'labelBlock',
    name: 'Elevation In Meters',
    id: 'elevationInMeters',
    group: 'locality',
  },
  {
    block: 'labelBlock',
    name: 'Verbatim Elevation',
    id: 'verbatimElevation',
    group: 'locality',
  },
  {
    block: 'labelBlock',
    name: 'Minimum Depth In Meters',
    id: 'minimumDepthInMeters',
    group: 'locality',
  },
  {
    block: 'labelBlock',
    name: 'Maximum Depth In Meters',
    id: 'maximumDepthInMeters',
    group: 'locality',
  },
  {
    block: 'labelBlock',
    name: 'Verbatim Depth',
    id: 'verbatimDepth',
    group: 'locality',
  },
  {
    block: 'labelBlock',
    name: 'Disposition',
    id: 'disposition',
    group: 'locality',
  },
  {
    block: 'labelBlock',
    name: 'Storage Location',
    id: 'storageLocation',
    group: 'locality',
  },
  {
    block: 'labelBlock',
    name: 'Duplicate Quantity',
    id: 'duplicateQuantity',
    group: 'locality',
  },
  {
    block: 'labelBlock',
    name: 'Date Last Modified',
    id: 'dateLastModified',
    group: 'event',
  },
];

// Defines formatting buttons
const formatsArr = [
  { group: 'field', func: 'font-bold', icon: 'format_bold' },
  { group: 'field', func: 'italic', icon: 'format_italic' },
  { group: 'field', func: 'underline', icon: 'format_underlined' },
  { group: 'field', func: 'uppercase', icon: 'format_size' },
  { group: 'field-block', func: 'bar', icon: '', name: 'Add Bar' },
];

// Defines dropdown style groups
const dropdownsArr = [
  {
    id: 'text',
    name: 'font-size',
    group: 'field',
    options: [
      { value: '', text: 'Font Size' },
      { value: 'text-xs', text: 'X-Small' },
      { value: 'text-sm', text: 'Small' },
      { value: 'text-base', text: 'Normal' },
      { value: 'text-lg', text: 'Large' },
      { value: 'text-xl', text: 'X-Large' },
      { value: 'text-2xl', text: '2X-Large' },
      { value: 'text-3xl', text: '3X-Large' },
      { value: 'text-4xl', text: '4X-Large' },
      { value: 'text-5xl', text: '5X-Large' },
      { value: 'text-6xl', text: '6X-Large' },
    ],
  },
  {
    id: 'font-type',
    name: 'font-type',
    group: 'field',
    options: [
      { value: '', text: 'Font Type' },
      { value: 'font-type-sans', text: 'System Sans Serif' },
      { value: 'font-type-serif', text: 'System Serif' },
      { value: 'font-type-mono', text: 'System Mono' },
    ],
  },
  {
    id: 'text-align',
    name: 'text-align',
    group: 'field-block',
    options: [
      { value: '', text: 'Text Align' },
      { value: 'text-align-center', text: 'Center' },
      { value: 'text-align-right', text: 'Right' },
      { value: 'text-align-justify', text: 'Justify' },
    ],
  },
  {
    id: 'mt',
    name: 'spacing-top',
    group: 'field-block',
    options: [
      { value: '', text: 'Line Spacing Top' },
      { value: 'mt-0', text: 'Top: 0' },
      { value: 'mt-1', text: 'Top: 1' },
      { value: 'mt-2', text: 'Top: 2' },
      { value: 'mt-3', text: 'Top: 3' },
      { value: 'mt-4', text: 'Top: 4' },
      { value: 'mt-5', text: 'Top: 5' },
      { value: 'mt-6', text: 'Top: 6' },
      { value: 'mt-8', text: 'Top: 8' },
      { value: 'mt-10', text: 'Top: 10' },
      { value: 'mt-12', text: 'Top: 12' },
    ],
  },
  {
    id: 'mb',
    name: 'spacing-bottom',
    group: 'field-block',
    options: [
      { value: '', text: 'Line Spacing Bottom' },
      { value: 'mb-0', text: 'Bottom: 0' },
      { value: 'mb-1', text: 'Bottom: 1' },
      { value: 'mb-2', text: 'Bottom: 2' },
      { value: 'mb-3', text: 'Bottom: 3' },
      { value: 'mb-4', text: 'Bottom: 4' },
      { value: 'mb-5', text: 'Bottom: 5' },
      { value: 'mb-6', text: 'Bottom: 6' },
      { value: 'mb-8', text: 'Bottom: 8' },
      { value: 'mb-10', text: 'Bottom: 10' },
      { value: 'mb-12', text: 'Bottom: 12' },
    ],
  },
];

const fieldDiv = document.getElementById('fields');
const fieldListDiv = document.getElementById('fields-list');
const controlDiv = document.getElementById('controls');
const fieldsFilter = document.getElementById('fields-filter');
const labelMid = document.getElementById('label-middle');

// Initially creates all fields
createFields(fieldProps);

// Creates formatting (button) controls in page
formatsArr.forEach((format) => {
  let targetDiv = document.getElementById(`${format.group}-options`);
  let btn = document.createElement('button');
  btn.classList.add('control');
  btn.disabled = true;
  btn.dataset.func = format.func;
  btn.dataset.group = format.group;
  if (format.icon !== '') {
    let icon = document.createElement('span');
    icon.classList.add('material-icons');
    icon.innerText = format.icon;
    btn.appendChild(icon);
  } else {
    btn.innerText = format.name;
  }
  targetDiv.appendChild(btn);
});

// Creates formatting (dropdown) controls in page
dropdownsArr.forEach((dropObj) => {
  let targetDiv = document.getElementById(`${dropObj.group}-options`);
  let slct = document.createElement('select');
  slct.dataset.group = dropObj.group;
  slct.classList.add('control');
  slct.name = dropObj.name;
  slct.id = dropObj.id;
  slct.disabled = true;
  dropObj.options.forEach((choice) => {
    let opt = document.createElement('option');
    opt.value = choice.value;
    opt.innerText = choice.text;
    slct.appendChild(opt);
  });
  targetDiv.appendChild(slct);
});

// Grabs elements
const containers = document.querySelectorAll('.container');
const draggables = document.querySelectorAll('.draggable');
const build = document.getElementById('build-label');
const preview = document.getElementById('preview-label');
const controls = document.querySelectorAll('.control');
const inputs = document.querySelectorAll('input');

/** Methods
 ******************************
 */

/**
 * Displays user instructions overlay
 */
const overlay = document.getElementById('instructions');
function openOverlay() {
  overlay.classList.remove('hidden');
}

/**
 * Hides user instructions overlay
 */
function closeOverlay() {
  overlay.classList.add('hidden');
}

/**
 * Filters array based on desired property
 * @param {Array} arr Array to be filtered
 * @param {Object} criteria Pair or pairs of property and criterion
 */
function filterObject(arr, criteria) {
  return arr.filter(function (obj) {
    return Object.keys(criteria).every(function (c) {
      return obj[c] == criteria[c];
    });
  });
}

/**
 * Removes object from array based on desired property
 * @param {Array} arr Array to be cleaned
 * @param {Object} criteria Pair or pairs of property and criterion
 */
function removeObject(arr, criteria) {
  return arr.filter(function (obj) {
    return Object.keys(criteria).every(function (c) {
      return obj[c] !== criteria[c];
    });
  });
}

/**
 * Gets list of fields currently available to drag to label build area
 */
function getCurrFields() {
  let currFields = fieldProps;
  let usedFields = document.querySelectorAll('#label-middle .draggable');
  if (usedFields.length > 0) {
    usedFields.forEach((usedField) => {
      currFields = removeObject(currFields, { id: usedField.id });
    });
  }
  return currFields;
}

/**
 * Filters available fields on select option
 */
function filterFields() {
  let value = this.value;
  let filteredFields = '';
  value === 'all'
    ? (filteredFields = getCurrFields())
    : (filteredFields = filterObject(getCurrFields(), { group: value }));
  fieldListDiv.innerHTML = '';
  createFields(filteredFields);
}

/**
 * Creates draggable elements
 * @param {Arr} arr Array with list of currently available fields
 */
function createFields(arr) {
  arr.forEach((field) => {
    let li = document.createElement('li');
    li.innerHTML = field.name;
    li.id = field.id;
    if (field.block === 'labelBlock') {
      li.draggable = 'true';
      li.classList.add('draggable');
      li.dataset.category = field.group;
      li.addEventListener('dragstart', handleDragStart, false);
      li.addEventListener('dragover', handleDragOver, false);
      li.addEventListener('drop', handleDrop, false);
      li.addEventListener('dragend', handleDragEnd, false);
      fieldListDiv.appendChild(li);
    }
  });
}

/**
 * Appends line (fieldBlock) to label builder
 * Binded to button, adds editable div
 */
function addLine() {
  let line = document.createElement('div');
  line.classList.add('field-block', 'container');
  let midBlocks = document.querySelectorAll('#label-middle > .field-block');
  let up = document.createElement('span');
  up.classList.add('material-icons');
  up.innerText = 'keyboard_arrow_up';
  line.appendChild(up);
  let down = document.createElement('span');
  down.classList.add('material-icons');
  down.innerText = 'keyboard_arrow_down';
  line.appendChild(down);
  let lastBlock = midBlocks[midBlocks.length - 1];
  lastBlock.parentNode.insertBefore(line, lastBlock.nextSibling);
  line.draggable = true;
  // Allows items to be added/reordered inside fieldBlock
  line.addEventListener('dragover', (e) => {
    e.preventDefault();
    const dragging = document.querySelector('.dragging');
    dragging !== null ? line.appendChild(dragging) : '';
  });
}

/**
 * Refreshes label preview
 * Triggered every time items are updated
 */
function refreshPreview() {
  let labelList = [];
  let fieldBlocks = document.querySelectorAll('#build-label .field-block');
  // Builds array with directives (labelList)
  fieldBlocks.forEach((block) => {
    let itemsArr = [];
    let items = block.querySelectorAll('li');
    items.forEach((item) => {
      let itemObj = {};
      let className = Array.from(item.classList).filter(isPrintStyle);
      itemObj.field = item.id;
      itemObj.className = className;
      itemObj.prefix = item.dataset.prefix;
      itemObj.suffix = item.dataset.suffix;
      itemsArr.push(itemObj);
    });
    labelList.push(itemsArr);
    let fieldBlockStyles = Array.from(block.classList).filter(isPrintStyle);
    fieldBlockStyles ? (itemsArr.className = fieldBlockStyles) : '';
    let fieldBlockDelim = block.dataset.delimiter;
    fieldBlockDelim
      ? (itemsArr.delimiter = fieldBlockDelim)
      : (itemsArr.delimiter = '');
  });
  // Clears preview div before appending elements
  preview.innerHTML = '';
  // Creates HTML elements and appends to preview div
  labelList.forEach((labelItem, blockIdx) => {
    let blockLen = labelItem.length;
    let fieldBlock = document.createElement('div');
    fieldBlock.classList.add('field-block');
    let labelItemStyles = labelItem.className;
    labelItemStyles.forEach((style) => {
      fieldBlock.classList.add(style);
    });
    preview.appendChild(fieldBlock);
    labelItem.forEach((field, fieldIdx) => {
      createPreviewEl(field, fieldBlock);
      let isLast = fieldIdx == blockLen - 1;
      // Adds delimiter if existing up to last element
      if (!isLast) {
        let preview = document.getElementsByClassName(field.field);
        let delim = document.createElement('span');
        delim.innerText = labelItem.delimiter;
        preview[0].after(delim);
      }
    });
  });

  return labelList;
}

/**
 * Creates elements in preview div, based on controls in build
 * @param {Object} element Field, constructed in `refreshPreview()`
 * @param {DOM Node} parent DOM Node where element will be inserted
 */
function createPreviewEl(element, parent) {
  // Grabs information from fieldProps array to create elements matching on id
  let fieldInfo =
    fieldProps[fieldProps.findIndex((x) => x.id === element.field)];
  let div = document.createElement('div');
  div.innerHTML = fieldInfo.name;
  div.classList.add(fieldInfo.id);
  div.classList.add(...element.className);
  parent.appendChild(div);
  let hasPrefix = element.prefix != undefined;
  let hasSuffix = element.suffix != undefined;
  if (hasPrefix) {
    let currText = div.innerText;
    let prefSpan = `<span>${element.prefix}</span>`;
    div.innerHTML = prefSpan + currText;
  }
  if (hasSuffix) {
    let sufSpan = document.createElement('span');
    sufSpan.innerText = element.suffix;
    div.appendChild(sufSpan);
  }
}

/**
 * Checks if class should be output in JSON
 * @param {String} className found in item
 */
function isPrintStyle(className) {
  const functionalStyles = [
    'draggable',
    'selected',
    'field-block',
    'container',
  ];
  return !functionalStyles.includes(className);
}

/**
 * Generate JSON string for current configurations
 * @param {Array} list Array of fields, built by `refreshPreview()`
 */
function generateJson(list) {
  let labelBlocks = [];
  // Parse nested array
  Object.keys(list).forEach((index) => {
    let fieldBlockObj = {};
    // Joins array of className items for fields
    let fieldItem = list[index];
    fieldItem.map((prop) => {
      prop.className.length > 0
        ? (prop.className = prop.className.join(' '))
        : delete prop.className;
    });
    fieldBlockObj.fieldBlock = fieldItem;
    let fieldBlockDelim = fieldItem.delimiter;
    fieldBlockDelim !== undefined
      ? (fieldBlockObj.delimiter = fieldBlockDelim)
      : '';
    let fieldBlockStyles = fieldItem.className;
    fieldBlockStyles.length > 0
      ? (fieldBlockObj.className = fieldItem.className.join(' '))
      : delete fieldBlockObj.className;
    labelBlocks.push(fieldBlockObj);
  });
  let json = JSON.stringify(labelBlocks, null, 2);
  console.log(json);
  return json;
}

/**
 * Prints JSON in interface
 *
 */
function printJson() {
  let list = refreshPreview();
  let dummy = document.getElementById('dummy');
  let copyBtn = document.getElementById('copyBtn');
  console.log(list);
  console.log(list[0].length);
  let isEmpty = list[0].length == 0;
  let message = '';
  if (isEmpty) {
    dummy.style.display = 'none';
    copyBtn.style.display = 'none';
    alert(
      'Label format is empty! Please drag some items to the build area before trying again'
    );
  } else {
    let json = generateJson(refreshPreview());
    copyBtn.style.display = 'inline-block';
    dummy.value = json;
    dummy.style.display = 'block';
    dummy.style.height = '300px';
    dummy.style.width = '100%';
  }
}

/**
 * Copies JSON output to user's clipboard
 */
function copyJson() {
  dummy.select();
  dummy.setSelectionRange(0, 99999); /* For mobile devices */
  document.execCommand('copy');
  /* Alert the copied text */
  alert('Copied JSON to clipboard');
}

/**
 * Toggles select/deselect clicked element
 * @param {DOM Node} element
 */
function toggleSelect(element) {
  element.classList.toggle('selected');
  let isSelected = element.classList.contains('selected');
  return isSelected;
}

/**
 * Toggles formatting controls based on filter and state
 * @param {String} filter Class of formatting control (field or field-block)
 * @param {Boolean} bool
 */
function activateControls(filter, bool) {
  let filtered = document.querySelectorAll(`[data-group=${filter}]`);
  filtered.forEach((control) => {
    bool ? (control.disabled = false) : (control.disabled = true);
  });
}

/**
 * Deactivates all controls
 */
function deactivateControls() {
  controls.forEach((control) => {
    control.disabled = true;
  });
}

/**
 * Gets selected item state (formatted classes)
 * @param {DOM Node} item Field in build label area
 */
function getState(item) {
  let formatList = Array.from(item.classList);
  // Removes '.draggable' and '.selected' from array
  printableList = formatList.filter(isPrintStyle);

  if (printableList.length > 0) {
    // Render state of each formatting button
    printableList.forEach((formatItem) => {
      // Check if class is a choice in a dropdown by matching first part of class
      let strArr = formatItem.split('-');
      let str = '';
      strArr.length == 3
        ? (str = strArr[0] + '-' + strArr[1])
        : (str = strArr[0]);
      console.log(str);
      // Loop through each item in array
      dropdownsArr.forEach((dropdown) => {
        let isDropdownStyle = str === dropdown.id;
        if (isDropdownStyle) {
          let selDropdown = document.getElementById(str);
          selDropdown.value = formatItem;
        }
      });
      controls.forEach((control) => {
        // Select that format and activate it
        if (formatItem === control.dataset.func) {
          control.classList.add('selected');
        }
      });
    });
  }

  // Get state of prefix/suffix for fields
  let hasPrefix = item.dataset.prefix != null;
  let prefixInput = document.getElementById('prefix');
  hasPrefix ? (prefixInput.value = item.dataset.prefix) : '';
  let hasSuffix = item.dataset.suffix != null;
  let suffixInput = document.getElementById('suffix');
  hasSuffix ? (suffixInput.value = item.dataset.suffix) : '';
}

/**
 * Applies selected control styles to selected items
 * @param {String} control ID of formatting control (button or select)
 * @param {Array} selectedItems Items to be formatted (selected)
 * @param {Boolean} bool If style will be added or removed, depends on state of control (important for buttons)
 */
function toggleStyle(control, selectedItems, bool) {
  selectedItems.forEach((item) => {
    // Double-checking if item is selected
    if (item.classList.contains('selected')) {
      // Deals with buttons
      // if formatting button is selected, add class, else remove
      bool
        ? item.classList.add(control.dataset.func)
        : item.classList.remove(control.dataset.func);
      //
    } else {
      return false;
    }
    refreshPreview();
  });
}

/**
 * Applies selected dropdown styles to selected items
 * @param {String} dropdown ID of dropdown
 * @param {Array} selectedItems Items to be formatted (selected)
 */
function addReplaceStyle(dropdown, selectedItems) {
  // Deals with selection
  dropdown.addEventListener('input', function () {
    selectedItems.forEach((item) => {
      let option = dropdown.value;
      if (option !== '') {
        // Check if item already has styles in this group
        let group = new RegExp(`${dropdown.id}-*`);
        let hasGroup = item.className.split(' ').some(function (c) {
          return group.test(c);
        });
        if (item.classList.contains('selected')) {
          if (!hasGroup) {
            // If not, add it
            item.classList.add(option);
            console.log(`added ${option} to ${item.id}`);
          } else {
            // If yes, replace it
            item.classList.forEach((className) => {
              if (className.startsWith(dropdown.id)) {
                item.classList.remove(className);
              }
            });
            item.classList.add(option);
          }
        }
      }
    });
  });
  refreshPreview();
}

/**
 * Clears/resets controls state
 */
function resetControls() {
  controls.forEach((control) => {
    // Deal with select input
    let isDropdown = control.tagName === 'SELECT';
    isDropdown ? (control.value = '') : '';
    control.classList.remove('selected');
    let isInput = control.tagName === 'INPUT';
    isInput ? (control.value = '') : '';
  });
}

/**
 * Updates optional field content (prefix/suffix)
 * @param {DOM Node} content Optional content input
 * @param {DOM Node} item Field to be modified
 */
function updateFieldContent(content, item) {
  let option = content.id;
  item.setAttribute('data-' + option, content.value);
  console.log(content, item);
}

/**
 * Tags dragging elements and copies their content
 * @param {Event} e
 */
function handleDragStart(e) {
  dragSrcEl = this;
  this.classList.add('dragging');
  e.dataTransfer.effectAllowed = 'move';
}

/**
 * Moves content of dragged element when done moving
 * @param {Event} e
 */
function handleDragOver(e) {
  if (e.preventDefault) {
    e.preventDefault();
  }
  e.dataTransfer.dropEffect = 'move';
  return false;
}

/**
 * Reorders element based on position when dropped
 * @param {Event} e
 */
let dragSrcEl = null;
function handleDrop(e) {
  if (e.stopPropagation) {
    e.stopPropagation();
  }
  if (dragSrcEl != this) {
    this.parentNode.insertBefore(dragSrcEl, this);
  }
  return false;
}

/**
 * Removes tag from dragging element
 * @param {Event} e
 */
function handleDragEnd(e) {
  this.classList.remove('dragging');
  refreshPreview();
  return false;
}

/** Event Listeners
 ******************************
 */
fieldsFilter.onchange = filterFields;

draggables.forEach((draggable) => {
  draggable.addEventListener('dragstart', handleDragStart, false);
  draggable.addEventListener('dragover', handleDragOver, false);
  draggable.addEventListener('drop', handleDrop, false);
  draggable.addEventListener('dragend', handleDragEnd, false);
});

containers.forEach((container) => {
  container.addEventListener('dragover', (e) => {
    e.preventDefault();
    const dragging = document.querySelector('.dragging');
    dragging !== null ? container.appendChild(dragging) : '';
  });
});

// Elements in '#label-middle'
labelMid.addEventListener('click', (e) => {
  if (e.target.matches('.material-icons')) {
    console.log(e.target.innerText);
    if (e.target.innerText === 'keyboard_arrow_up') {
      let first = labelMid.getElementsByClassName('field-block')[0];
      console.log(first);
      let curr = e.target.parentNode;
      // reorder only if item is not first in list already
      if (curr !== first) {
        let prev = e.target.parentNode.previousSibling;
        // move current into prev
        prev.replaceWith(curr);
        // insert current after prev
        curr.parentNode.insertBefore(prev, curr.nextSibling);
      }
    } else if (e.target.innerText === 'keyboard_arrow_down') {
      let next = e.target.parentNode.nextSibling;
      let curr = e.target.parentNode;
      if (next) {
        // move current into next
        curr.replaceWith(next);
        // insert current after next
        next.parentNode.insertBefore(curr, next.nextSibling);
      }
    }
    refreshPreview();
  } else {
    // Toggle select clicked item (on formattables only)
    toggleSelect(e.target);
    // Everytime item is clicked, display list of selected items:
    let selectedItems = build.querySelectorAll('.selected');
    // console.log(selectedItems);

    // When element is selected, activate formatting buttons
    // depends on number of elements in page (at least one selected).
    let isAnySelected = selectedItems.length > 0;

    if (isAnySelected) {
      let itemType = '';
      let numSelected = build.querySelectorAll('.selected');
      // Gets formatting information for individually selected item
      if (numSelected.length > 1) {
        // If there is more than one type of selected items, deactivate controls
        let selected = build.querySelectorAll('.selected');
        let typeArr = [];
        selected.forEach((item) => {
          typeArr.push(Array.from(item.classList).join(' '));
        });
        let uniqueTypeSet = new Set(typeArr);
        // console.log(uniqueTypeSet);
        if (uniqueTypeSet.size > 1) {
          // deactivate controls
          deactivateControls();
        } else {
          (' ');
        }
        resetControls();
      } else if (numSelected.length == 1) {
        // Refreshes buttons according to applied styles in selected item
        let item = build.querySelector('.selected');
        if (item.matches('.draggable')) {
          itemType = 'field';
          // deactivate 'field-block' items
          activateControls(itemType, isAnySelected);
          getState(item);
        } else if (item.matches('.field-block')) {
          itemType = 'field-block';
          // deactivate 'field' items
          activateControls(itemType, isAnySelected);
          getState(item);
        }
      } else {
        return false;
      }
    } else {
      resetControls();
      deactivateControls();
    }
  }
});

// Formatting controls
controlDiv.addEventListener('click', (e) => {
  // Gets selected items to format
  let formatItems = build.querySelectorAll('.selected');
  let isFormatSelected = toggleSelect(e.target);
  let isButton = e.target.tagName === 'BUTTON';
  let isDropdown = e.target.tagName === 'SELECT';
  // Buttons
  if (isButton) {
    toggleStyle(e.target, formatItems, isFormatSelected);
  } else if (isDropdown) {
    addReplaceStyle(e.target, formatItems);
  }
});

// Field and Block options (prefix/suffix, delimiters)
// Listen to input changes
inputs.forEach((input) => {
  input.addEventListener('input', (e) => {
    let formatItem = build.querySelector('.selected');
    updateFieldContent(e.target, formatItem);
    console.log(e.target, formatItem);
    refreshPreview();
  });
});
