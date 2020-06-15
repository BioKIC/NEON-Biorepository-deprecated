/**
 * Global Variables
 */
const criteriaPanel = document.getElementById("criteria-panel");
const testURL = document.getElementById("test-url");
// const paramsArr = []
//////////////////////////////////////////////////////////////////////////

/**
 * Toggles state of checkboxes in nested lists when there is an "all-selector"
 * Uses jQuery
 */
function toggleSelectorAll() {
  $(this)
    .siblings()
    .find("input:checkbox")
    .prop("checked", this.checked)
    .attr("checked", this.checked)
}

/**
 * Automatically toggles checked/unchecked boxes in nested lists
 * i. e., when there is an "all-selector", unchecking internal
 * checkboxes toggles the "all-selector" to be unchecked as well.
 * Default is all boxes are checked in HTML.
 * @param {String} e.data.element Selector for element containing list,
 * should be passed when binding function to element
 */
function autoToggleSelector(e) {
  // Gets the higher level element for lists
  let element = e.data.element;

  // Figure out where in tree I am before applying checking/unchecking
  // Compare lengths of array with checked vs unchecked elements

  // First checks nearest elements to clicked checkbox
  let allSubChecked = ($(this).closest("ul").find(".child").filter(":checked").length == $(this).closest("ul").find(".child").length);
  $(this).closest("ul").siblings(".all-selector").change().prop("checked", allSubChecked);

  // Then checks most outer "all-selector"
  let allHigherChecked = (
    $(element).siblings().find(".child").filter(":checked").length == $(element).siblings().find(".child").length
  );
  $(element).prop("checked", allHigherChecked);
}

/**
 * Opens modal with id selector
 * @param {String} elementid Selector for modal to be opened
 */
function openModal(elementid) {
  $(elementid).css("display", "block");
};

/**
 * Closes modal with id selector
 * @param {String} elementid Selector for modal to be opened
 */
function closeModal(elementid) {
  $(elementid).css("display", "none");
}

/**
 * Chips
 */

//////// Update chip on event change
const taxaInput = document.getElementsByName('taxa');
taxaInput[0].addEventListener('change', updateChip);
// let taxaChip = document.createElement("p");

// Chip definitions
let inputChip = document.createElement("span"),
  chipBtn = document.createElement("button");
inputChip.setAttribute("class", "chip");
chipBtn.setAttribute("type", "button");
chipBtn.setAttribute("class", "chip-remove-btn");

// Make this function generic? Or adapt function for each criterion?
// How to deal with defaults?
function updateChip(e) {
  // taxaChip.textContent = taxaInput[0].name + ': ' + e.target.value;
  // Deletes current object before appending chips, to avoid redundancy
  let eInput = document.getElementsByName(e.target.name);
  paramsArr.splice(paramsArr[e.target.name], 1);
  let chipArr = getParam(eInput[0].name);
  console.log(chipArr);
  inputChip.textContent = chipArr.taxa;
  inputChip.appendChild(chipBtn);
  criteriaPanel.appendChild(inputChip);
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
  paramsArr["country"] = "USA";

  let element = document.getElementsByName(paramName);
  // Deals with dropdown options
  // const answer = element[0].tagName === "SELECT" ? "it's a dropdown" : "it's not a dropdown";
  // console.log(answer);
  // console.log(element[0].tagName);
  // Deals with inputs
  if (element[0].tagName === "INPUT") {
    // Deals with checkboxes
    if (element[0].getAttribute("type") === "checkbox") {
      // let i = 0;
      let itemsArr = [];
      for (var i = 0; i < element.length; ++i) {
        element[i].checked ? itemsArr.push(element[i].value) : console.log("not checked")
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
        console.log("here");
      }
    }
  } else if (element[0].tagName === "SELECT") {
    let elementValue = element[0].options[element[0].selectedIndex].value;
    // paramsArr.push({
    //   [paramName]: elementValue
    // });
    paramsArr[paramName] = elementValue;
  }
  console.log(paramsArr);
  return paramsArr;
}

/**
 * Creates search URL with parameters
 * Define parameters to be looked for in `paramNames` array
 */
function getSearchUrl() {
  const baseURL = new URL("https://biorepo.neonscience.org/portal/collections/list.php");
  // Clears array temporarily to avoid redundancy
  paramsArr = [];
  const paramNames = [
    'db',
    // 'dataset',
    'state',
    'county',
    'local',
    'elevlow',
    'elevhigh',
    'taxa',
    'taxontype',
    'usethes'
  ];
  // Grabs params from form for each param name
  paramNames.forEach((param, i) => {
    return getParam(paramNames[i]);
  });
  // Appends each key value for each param in search url
  var queryString = Object.keys(paramsArr).map((key) => {
    //   return encodeURIComponent(key) + '=' + encodeURIComponent(paramsArr[key])
    // }).join('&');
    // console.log(baseURL + queryString);
    baseURL.searchParams.append(key, paramsArr[key]);
  })
  console.log(baseURL.href);
  // Appends URL to `testURL` link
  testURL.innerHTML = baseURL.href;
  testURL.href = baseURL.href;
};

//////////////////////////////////////////////////////////////////////////

/**
 * Event Listeners and binders
 */

// Binds function to test button
// $("#teste-btn").click(getSearchParams);
$("#teste-btn").click(function(event) {
  event.preventDefault();
  getSearchUrl();
});

// Nested checkboxes functions
$(".all-selector").click(toggleSelectorAll);
$("#allSites").siblings().find(".child").bind('click', { element: "#allSites" }, autoToggleSelector);
$("#all-neon-colls").siblings().find(".child").bind('click', { element: "#all-neon-colls" }, autoToggleSelector);
$("#neonext-collections-list").find(".child").bind('click', { element: "#neonext-collections-list" }, autoToggleSelector);
$("#ext-collections-list").find(".child").bind('click', { element: "#ext-collections-list" }, autoToggleSelector);

// When checking "all neon collections" box, toggle the property of the modal
$("#all-neon-colls-quick").click(function() {
  let isChecked = $(this).prop("checked");
  $("#all-neon-colls").prop("checked", isChecked);
  $("#all-neon-colls").siblings().find(".child").prop("checked", isChecked);
});

// When clicking in "accept and close button, pass "all-selector" state to this one
$("#neon-modal-close").click(function() {
  let isChecked = $("#all-neon-colls").prop("checked");
  $("#all-neon-colls-quick").prop("checked", isChecked);
});

// Binds expansion function to plus and minus icons in selectors
$(".expansion-icon").click(function() {
  if ($(this).siblings("ul").hasClass("collapsed")) {
    $(this)
      .html("indeterminate_check_box")
      .siblings("ul")
      .removeClass("collapsed")
  } else {
    $(this)
      .html("add_box")
      .siblings("ul")
      .addClass("collapsed")
  }
});

// Listen for open modal click
$("#neon-modal-open").click(function(event) {
  event.preventDefault();
  openModal('#biorepo-collections-list');
});

// Listen for close modal click
$("#neon-modal-close").click(function(event) {
  event.preventDefault();
  closeModal('#biorepo-collections-list');
  // Checks if the "all" selector is checked and toggle main one accordingly
});

//////////////////////////////////////////////////////////////////////////