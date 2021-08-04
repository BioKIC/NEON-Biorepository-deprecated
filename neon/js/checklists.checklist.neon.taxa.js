const taxCont = document.querySelectorAll('.taxon-container');

var hasNeon = (el) => el.innerText.includes('[NEON]');

taxCont.forEach((cont) => {
  let taxA = cont.querySelector('.taxon-div a');
  let noteAs = cont.querySelectorAll('.note-div a');
  if (!hasNeon(cont)) {
    taxA.style.color = '#565a5c';
    noteAs.forEach((note) => {
      if (!hasNeon(note)) {
        note.style.color = '#565a5c';
      }
    });
  }
});
