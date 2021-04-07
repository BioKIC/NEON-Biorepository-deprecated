/***
 * Sets alert messages in chosen page
 * Version: Apr 2021
 */

/**
 * Adds styled alert messages in page
 * @param {Array} alerts Array with objects
 * Object contains at minimum a property 'alertMsg'
 * 'alertMsg' can be a string or HTML code
 *
 */
function handleAlerts(alerts) {
  const alertDiv = document.getElementById('alert-msgs');
  alertDiv.innerHTML = '';
  alerts.map((alert) => {
    alertDiv.classList.remove('visually-hidden');
    let alertP = document.createElement('p');
    alertP.classList.add('alert');
    alertP.innerHTML = alert.alertMsg + ' Click to dismiss.';
    alertP.onclick = function () {
      alertP.remove();
    };
    alertDiv.appendChild(alertP);
  });
}
