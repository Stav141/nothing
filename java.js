
// Disable context menu
document.addEventListener('contextmenu', event => event.preventDefault());


var loader = document.querySelector(".loader")



function vanish() {
  loader.classList.add("disppear");

}


document.querySelector('#firstclick').addEventListener("click", () => {
  loader.style.display = "none";
  firstclick.style.display = "none";

});




document.addEventListener("mousemove", (event) => {
  const mouseX = event.pageX;
  const mouseY = event.pageY;

  let closestButton = null;
  let closestDistance = Infinity;

  buttons.forEach((button) => {
      const rect = button.getBoundingClientRect();
      const buttonX = rect.left + rect.width / 2;
      const buttonY = rect.top + rect.height / 2;

      const distance = Math.sqrt(
          Math.pow(mouseX - buttonX, 2) + Math.pow(mouseY - buttonY, 2)
      );


      if (distance < closestDistance) {
          closestDistance = distance;
          closestButton = button;
      }
  });


  buttons.forEach((button) => {
      button.classList.toggle("closest", button === closestButton);
      
  });


  if (closestButton) {
      closestButton.focus();
  }
});

document.addEventListener("click", () => {
  const focusedButton = document.querySelector(".buttonbox.closest");
  if (focusedButton) {
      focusedButton.click();
  }
});

