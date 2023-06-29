var element = document.getElementsByClassName('affirm')[0];

function masquerElement() {
  element.classList.add('hidden');
  window.location.href='index'
}
setTimeout(masquerElement, 1500);
