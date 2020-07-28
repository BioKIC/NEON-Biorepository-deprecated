/**
 * Global variables
 */

const url = '../../api/taxonomy/taxasuggest.php?term=';

const taxonInput = document.getElementById('taxa-search');
const matchList = document.getElementById('match-list-container');
const minChars = 2;
let matches = [];

/**
 * Uses text in input to search for taxa in API
 *
 * @param searchText
 */

const searchTaxa = async (searchText) => {
  if (searchText.length >= minChars) {
    // if (searchText.includes(', ')) {
    //   searchText = searchText.split(',');
    //   searchText = searchText[searchText.length - 1].trimStart();
    //   // matches = [];
    //   console.log('Search using this new value: ', searchText);
    //   fetch(url + searchText)
    //     .then((response) => response.json())
    //     .then((data) => matches.push(...data));
    // } else {
    console.log('Search using this value: ', searchText);

    fetch(url + searchText)
      .then((response) => response.json())
      .then((data) => matches.push(...data));

    // if (matches.length == 1) {
    //   return
    // }
    // console.log('This is active: ', document.activeElement);
    // }
  } else {
    console.log('Type more characters to search');
    matches = [];
    matchList.innerHTML = '';
  }
  outputHtml(matches);
  matches = [];
};

/**
 * When user navigates away from taxon input, hides suggestions
 */
function hideSuggestions(element) {
  console.log('This is active: ', document.activeElement);
  element.innerHTML = '';
  element.classList.add('hide');
}

/**
 * With matches found in searchTaxa(), formats and displays them in HTML
 *
 * @param matches
 */
// Show results in HTML
const outputHtml = (matches) => {
  if (matches.length > 0) {
    let html = matches
      //   .map(
      //     (match) => `
      //   <div><span class="suggested">${match}</span></div>
      // `
      //   )
      //   .join('');
      .map((match) => `<option>${match}</option>`)
      .join('');

    html = `<datalist id="match-list">${html}</datalist>`;
    matchList.innerHTML = html;
    // console.log(html);
    // matchList.classList.remove('hide');
  } else {
    matchList.innerHTML = '';
  }
};

/**
 * When an user clicks in a suggestion, the value is added to input
 *
 * @param e
 */
function selectSuggestion(e) {
  // console.log(e.innerText);
  taxonInput.focus();
  // console.log('This is active: ', document.activeElement);
  taxonInput.value = e.innerHTML;
  console.log(taxonInput.value);
  // matchList = document.getElementById('match-list');
  // matchList.innerHTML = '';
  // matchList.classList.toggle('hide');
  hideSuggestions(matchList);
}

/**
 * Event Listeners
 */

// Every time we type, fire off event
taxonInput.addEventListener('input', () => searchTaxa(taxonInput.value));

// taxonInput.addEventListener('focusout', hideSuggestions(matchList));

// taxonInput.addEventListener('blur', function () {
//   console.log('User clicked out of taxonInput');
//   hideSuggestions(matchList);
// });

// document.addEventListener('click', function (e) {
//   // console.log(e.target);
//   // console.log('This is active: ', document.activeElement);
//   if (!e.target.classList.contains('suggested')) {
//     console.log('clicked on element that was not suggestion');
//     hideSuggestions(matchList);
//   } else {
//     console.log('clicked on suggestion');
//     selectSuggestion(e.target);
//   }
// });
