/***
 * Sets alert messages in chosen page
 * Version: Apr 2021
 */

// alerts = [
//   {
//     alertMsg:
//       'Try our <a href="./neon/search/index.php">New Occurrence Search Form!</a>',
//   },
// ];

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

// handleAlerts(alerts);
