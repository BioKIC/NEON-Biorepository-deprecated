// Toggles expanded items
function toggleSelectorAll() {
  $(this)
    .siblings()
    .find("input:checkbox")
    .prop("checked", this.checked)
    .attr("checked", this.checked)
    //console.log($(this).siblings().find("input:checkbox"))
}

function autoToggleSelector() {
  // length of subcriteria list:
  // console.log($(this).closest(".subcriteria").find(".child").length);

  // figure out where in tree I am before applying checking/unchecking

  let allSubChecked = ($(this).closest("ul").find(".child").filter(":checked").length == $(this).closest("ul").find(".child").length);
  //console.log("all checked: " + allChecked)
  // if all subcriteria items are checked, then check parent, if not, uncheck
  //console.log($(this).closest("ul").siblings(".all-selector")[0])
  $(this).closest("ul").siblings(".all-selector").change().prop("checked", allSubChecked);
  $(this).closest("ul").siblings(".all-selector").change().attr("checked", allSubChecked);
  // Add a check for ALL items
  //console.log($("#allDomains").siblings().find(".child").filter(":checked").length)
  let allChecked = ($("#allDomains").siblings().find(".child").filter(":checked").length == $("#allDomains").siblings().find(".child").length);
  $("#allDomains").prop("checked", allChecked);
  $("#allDomains").attr("checked", allChecked);
  // Repeat for allCollections div
  let allCollsChecked = ($("#allCollections").siblings().find(".child").filter(":checked").length == $("#allCollections").siblings().find(".child").length);
  $("#allCollections").prop("checked", allCollsChecked);
  $("#allCollections").attr("checked", allCollsChecked);

}

const criteriaPanel = document.getElementById("criteria-panel");
const testURL = document.getElementById("test-url");
var paramsArr = []

//////// Update chip on event change
const taxaInput = document.getElementsByName('taxa');
taxaInput[0].addEventListener('change', updateChip);
let taxaChip = document.createElement("p");

// Make this function generic? Or adapt function for each criterion?
// How to deal with defaults?
function updateChip(e) {
  // taxaChip.textContent = taxaInput[0].name + ': ' + e.target.value;
  // Deletes current object before appending chips, to avoid redundancy
  paramsArr.splice(paramsArr[e.target.name], 1);
  let chipArr = getParam(taxaInput[0].name);
  console.log(chipArr);
  taxaChip.textContent = chipArr.taxa;
  criteriaPanel.appendChild(taxaChip);
}
/////////

function testButton() {
  testURL.innerHTML = "hello";
}


// Function that will go through a group of fields and will capture fields and concatenate them to pass to search array
function getParam(paramName) {
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

function getSearchUrl() {
  const baseURL = new URL("https://biorepo.neonscience.org/portal/collections/list.php");
  // Clears array temporarily to avoid redundancy
  paramsArr = [];
  const paramNames = [
    'taxa',
    'taxontype',
    'db',
    // 'dataset'
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
};

// function getSearchParams() {
//   console.log("Here");
//   const paramsForm = document.getElementById("params-form");
//   let param = "";
//   let i;
//   for (i = 0; i < paramsForm.length; i++) {
//     param = paramsForm.elements[i].getAttribute("name") + "=" + paramsForm.elements[i].value;
//     console.log(param);
//   }
// const sampleSearchWithParams = new URL("https://biorepo.neonscience.org/portal/collections/list.php");

// // Search params for all db
// sampleSearchWithParams.searchParams.append("db", "all");

// // Search params for taxa
// sampleSearchWithParams.searchParams.append("taxa", "Acer");
// sampleSearchWithParams.searchParams.append("usethes", "1");
// sampleSearchWithParams.searchParams.append("taxontype", "2");
// sampleSearchWithParams.searchParams.append("tabindex", "1");

// $("#testUrl").text(sampleSearchWithParams);
// $("#testUrl").attr("href", sampleSearchWithParams);


// These calls work:
// https://biorepo.neonscience.org/portal/collections/list.php?db=all&taxa=Acer&usethes=1&taxontype=2&tabindex=1
// https://biorepo.neonscience.org/portal/collections/list.php?db=allspec&country=USA%3BUnited+States%3BU.S.A.%3BUnited+States+of+America

// https: //biorepo.neonscience.org/portal/collections/list.php?db=all&taxa=Acer&usethes=1&taxontype=2&tabindex=1
// }

// Binds function to test button
// $("#teste-btn").click(getSearchParams);
$("#teste-btn").click(function(event) {
  event.preventDefault();
  getSearchUrl();
  // Loop through all params and create OBJECTS (change from ARRAYS) from each param group, then pass to the URL builder
});
// $("#teste-btn").click(function(event) {
//   event.preventDefault();
//   testButton();
// });

// Binds selector toggles to NEON collections element
$(".all-selector").click(toggleSelectorAll);
$(".child").click(autoToggleSelector);

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