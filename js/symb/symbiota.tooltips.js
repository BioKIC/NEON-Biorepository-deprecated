/**
 * Fetches tooltip and documentation from external API
 * Symbiota Tooltips (https://github.com/BioKIC/symbiota-tooltips)
 */

async function getTooltip(term, langTag) {
  const apiUrl =
    'https://biokic.github.io/symbiota-tooltips/api' + term + '.json';
  // let tooltipText = '';
  // tooltipText = await (fetch(url));
  let tooltipText = '';
  const res = await fetch(apiUrl);
  if (res.status === 404) {
    console.log('The requested tooltip does not exist.');
  } else {
    const data = await res.json();
    tooltipText = data[0].tooltip[langTag];
  }
  return tooltipText;
}

/**
 * Adds tooltip div adjacent to element
 * @param {*} element
 * @param {*} tooltipText
 * @returns
 */
function addTooltip(element, tooltipText) {
  console.log(tooltipText != '');
  console.log(tooltipText);
  if (tooltipText != '') {
    const tooltip = document.createElement('div');
    tooltip.className = 'tooltip-container';
    tooltip.innerHTML =
      '?<span class="tooltip-text">' + tooltipText + '</span>';
    // element.appendChild(tooltip);
    element.parentNode.insertBefore(tooltip, element.nextSibling);
    console.log(element);
  } else {
    return;
  }
}
