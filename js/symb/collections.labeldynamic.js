/**
 * Adds functionality to label page (../../collections/reports/labeldynamic.php)
 * - Allows users to edit content directly in labels
 * - Adds "print/save" button
 * - Controls hidden from printed page via media query
 *
 * Requires modern browsers (HTML5)
 *
 *  Author: Laura Rocha Prado (lauraprado@asu.edu)
 *  Version: Dec 2020
 */

let body = document.querySelector('body');
let page = document.querySelector('.body');

let controls = document.createElement('div');
controls.classList.add('controls');
controls.style.width = '980px';
controls.style.margin = '0 auto';
controls.style.paddingBottom = '30px';

let editBtn = document.createElement('button');
editBtn.innerText = 'Edit Labels Content';
editBtn.id = 'edit';
editBtn.style.fontWeight = 'bold';
editBtn.onclick = toggleEdits;

let printBtn = document.createElement('button');
printBtn.innerText = 'Print/Save PDF';
printBtn.id = 'print';
printBtn.style.marginLeft = '30px';
printBtn.style.fontWeight = 'bold';
printBtn.onclick = function () {
  window.print();
};

controls.appendChild(editBtn);
controls.appendChild(printBtn);
body.prepend(controls);

function toggleEdits() {
  let isEditable = page.contentEditable === 'true';
  let btn = body.querySelector('#edit');
  if (isEditable) {
    console.log(isEditable);
    page.contentEditable = 'false';
    document.querySelector('#edit').innerText = 'Edit Labels Text';
    page.style.border = 'none';
  } else {
    console.log(isEditable);
    page.contentEditable = 'true';
    document.querySelector('#edit').innerText = 'Save';
    page.style.border = '2px solid #03fc88';
  }
}
