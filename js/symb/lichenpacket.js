/***
 * Lichen Packets Custom Styles
 * Author: Laura Rocha Prado
 * Version: September 2021
 *
 * Features:
 * - Moves custom footer text to top of barcode image
 *
 * OPTIONAL: deactivated by default; remove comments to activate with caveats related to barcode image provider
 * - Replaces standard Symbiota barcode with custom
 * - Barcodes courtesy of barcode.tec-it.com
 ***/
document.addEventListener('DOMContentLoaded', function () {
  let labels = document.querySelectorAll('.label');
  if (labels) {
    labels.forEach((label) => {
      let bc = label.querySelector('.cn-barcode');
      if (bc != null) {
        // Grabs text from footer (when available) and places in barcode div
        let footer = label.querySelector('.label-footer');
        let bc = label.querySelector('.cn-barcode');
        if (footer) {
          footer.className = 'block font-family-arial text-align-center';
          bc.insertBefore(footer, bc.childNodes[0]);
          //bc.appendChild(footer);
        }
        // Grab catnum from img link
        let bcImg = bc.querySelector('.cn-barcode > img');
        let bcImgSrc = bcImg.src;
        let catNum = bcImgSrc.match(/(?<=bctext=).*/)[0].trim();
        // Replace img src with new url
        // let newBcImgSrc = 'https://barcode.tec-it.com/barcode.ashx?data=' + catNum + '&code=Code128&multiplebarcodes=false&translate-esc=true&unit=Px&dpi=300&imagetype=Gif&rotation=0&color%23000000&bgcolor=%23ffffff&codepage=Default&qunit=Mm&quiet=0&hidehrt=True&modulewidth=12';
        // bcImg.src = newBcImgSrc;
        // Adds catalog number below barcode
        // let catNumDiv = document.createElement('span');
        // catNumDiv.innerText = catNum;
        // catNumDiv.className = 'font-family-arial';
        // bc.appendChild(catNumDiv);
      }
    });
  }
});
