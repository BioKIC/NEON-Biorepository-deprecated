/**
 * Sorts an HTML table.
 *
 * @param {HTML TableElement} table The table to sort
 * @param {*} column The index of column to sort
 * @param {*} asc Determines sorting = ascending
 */
function sortTableByColumn(table, column, asc = true) {
  const sorting = asc ? 1 : -1;
  const tBody = table.tBodies[0];
  const rows = Array.from(tBody.querySelectorAll('tr'));

  // Sorts rows
  const sortedRows = rows.sort((a, b) => {
    const qAS = a.querySelector(`td:nth-child(${column + 1})`).textContent;
    const qBS = b.querySelector(`td:nth-child(${column + 1})`).textContent;

    let aColText = '';
    let bColText = '';

    // Deal with numbers
    if (isNaN(parseInt(qAS))) {
      aColText = qAS.trim().toLowerCase();
    } else {
      aColText = parseInt(qAS);
    }

    if (isNaN(parseInt(qBS))) {
      bColText = qBS.trim().toLowerCase();
    } else {
      bColText = parseInt(qBS);
    }

    // const aColText = qAS.trim();
    // const bColText = qBS.trim();

    return aColText > bColText ? 1 * sorting : -1 * sorting;
  });

  // Remove all existing rows from the table
  while (tBody.firstChild) {
    tBody.removeChild(tBody.firstChild);
  }

  // Re-add sorted rows
  tBody.append(...sortedRows);

  // Remember how column is sorted
  table
    .querySelectorAll('th')
    .forEach((th) => th.classList.remove('th-sort-asc', 'th-sort-desc'));
  table
    .querySelector(`th:nth-child(${column + 1})`)
    .classList.toggle('th-sort-asc', asc);
  table
    .querySelector(`th:nth-child(${column + 1})`)
    .classList.toggle('th-sort-desc', !asc);
}
// sortTableByColumn(document.querySelector("table"), 1, true);
document.querySelectorAll('.table-sortable th').forEach((headerCell) => {
  headerCell.addEventListener('click', () => {
    const tableElement = headerCell.parentElement.parentElement.parentElement;
    const headerIndex = Array.prototype.indexOf.call(
      headerCell.parentElement.children,
      headerCell
    );
    const currentIsAsc = headerCell.classList.contains('th-sort-asc');

    sortTableByColumn(tableElement, headerIndex, !currentIsAsc);
  });
});
