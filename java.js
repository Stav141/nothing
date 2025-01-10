
// Disable context menu if necessary
document.addEventListener('contextmenu', event => event.preventDefault());


var loader = document.querySelector(".loader")

window.addEventListener("load", vanish);

function vanish() {
  loader.classList.add("disppear");
}

